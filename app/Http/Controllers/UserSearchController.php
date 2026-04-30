<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SearchUsersRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class UserSearchController extends Controller
{
     // ─────────────────────────────────────────────────────────────
    // FUNCTION 1 — Full-Text Search
    //
    // Returns users whose name/email/city/profession/bio match
    // the query using MySQL MATCH() AGAINST() boolean mode.
    // Uses the full-text index — NOT a LIKE table scan.
    //
    // Min term length: 3 chars (MySQL ft_min_word_len default)
    // ─────────────────────────────────────────────────────────────
 
    public function functionOne_FullTextSearch(string $term): array
    {
        $term = trim($term);
 
        if (mb_strlen($term) < 3) {
            return [
                'error'   => 'Minimum 3 characters required for full-text search.',
                'results' => [],
            ];
        }
 
        $users = User::fullTextSearch($term)
            ->limit(50)       // cap for listing (cursor handles paging)
            ->get();
 
        return [
            'technique'      => 'Full-Text Index (MATCH AGAINST IN BOOLEAN MODE)',
            'query'          => $term,
            'boolean_query'  => collect(explode(' ', $term))
                                  ->filter(fn($w) => strlen($w) >= 3)
                                  ->map(fn($w) => '+' . $w . '*')
                                  ->implode(' '),
            'index_used'     => 'users_fulltext_idx + user_details_fulltext_idx',
            'rows_returned'  => $users->count(),
            'results'        => $users->toArray(),
        ];
    }
 
    // ─────────────────────────────────────────────────────────────
    // FUNCTION 2 — Cursor Pagination
    //
    // HOW IT WORKS vs OFFSET:
    //
    //   OFFSET (BAD at 50M rows):
    //     SELECT * FROM users ORDER BY id LIMIT 15 OFFSET 10000000
    //     MySQL reads and DISCARDS 10M rows, then returns 15.
    //     Gets slower the deeper you go. Page 1000 = very slow.
    //
    //   CURSOR (GOOD at 50M rows):
    //     SELECT * FROM users WHERE id > {last_seen_id} ORDER BY id LIMIT 15
    //     MySQL seeks directly to that id using B-Tree index.
    //     O(1) at any depth. Page 1 = Page 1,000,000 = same speed.
    //
    //   cursor = base64_encode(json({id: last_seen_id}))
    //            passed in request, decoded by Laravel automatically
    // ─────────────────────────────────────────────────────────────
 
    public function functionTwo_CursorPagination(SearchUsersRequest $request): array
    {
        $term    = $request->string('q', '')->value();
        $perPage = (int) $request->get('per_page', 15);

        if (mb_strlen($term) >= 3) {
            // Full-text search — scope handles join + select + ordering
            $query = User::query()->fullTextSearch($term);
        } else {
            // No search term → manual join, newest first
            $query = User::join('user_details', 'users.id', '=', 'user_details.user_id')
                ->select(
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.city',
                    'user_details.profession',
                    'user_details.age',
                    'user_details.experience',
                )
                ->orderByDesc('users.id');
        }
 
        // ② cursorPaginate() — Laravel generates the cursor automatically
        // It encodes the last row's ordering key into a base64 cursor token
        $paginator = $query->cursorPaginate($perPage);
 
        return [
            'technique'       => 'Cursor Pagination (cursorPaginate)',
            'how_cursor_works'=> 'Cursor encodes last row ID as base64. Next page uses WHERE id > {decoded} instead of OFFSET.',
            'per_page'        => $perPage,
            'next_cursor'     => $paginator->nextCursor()?->encode(),
            'prev_cursor'     => $paginator->previousCursor()?->encode(),
            'has_more'        => $paginator->hasMorePages(),
            'data'            => $paginator->items(),
        ];
    }
 
    // ─────────────────────────────────────────────────────────────
    // FUNCTION 3 — Main endpoint (all three combined)
    //
    // The debounce lives in the Blade view (resources/views/users/search.blade.php)
    // On the backend we just receive the debounced request normally.
    //
    // Flow:
    //   User types → [350ms debounce timer] → GET /users/search?q=term
    //   → fullTextSearch() → cursorPaginate() → return JSON/HTML
    // ─────────────────────────────────────────────────────────────
 
    public function functionThree_AllCombined(SearchUsersRequest $request): JsonResponse|View
    {
        $term    = $request->string('q', '')->value();
        $perPage = (int) $request->get('per_page', 15);
 
        // Build query
        if (mb_strlen($term) >= 3) {
            // ① Full-Text Search — scope handles join + select + ordering
            $query = User::query()->fullTextSearch($term);
        } else {
            // No search term → manual join, newest first
            $query = User::join('user_details', 'users.id', '=', 'user_details.user_id')
                ->select(
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.city',
                    'user_details.profession',
                    'user_details.age',
                    'user_details.experience',
                    'user_details.state',
                )
                ->orderByDesc('users.id');
        }
 
        // ② Cursor Pagination — O(1) at any depth
        $paginator = $query->cursorPaginate($perPage);
 
        // ③ Debounced request arrives here (debounce fires in the browser)
        // If this is an AJAX/fetch request → return JSON (for Blade fetch())
        // If it's a normal page load → return the Blade view
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'html'        => view('users._results', ['users' => $paginator, 'term' => $term])->render(),
                'next_cursor' => $paginator->nextCursor()?->encode(),
                'has_more'    => $paginator->hasMorePages(),
                'total_found' => $paginator->count(),
            ]);
        }
 
        return view('users.search', [
            'users'   => $paginator,
            'term'    => $term,
            'perPage' => $perPage,
        ]);
    }
 
    // ─────────────────────────────────────────────────────────────
    // Page entry point — renders the search page on first load
    // ─────────────────────────────────────────────────────────────
 
    public function index(): View
    {
        return view('users.search', [
            'users'      => collect(),
            'term'       => '',
            'perPage'    => 15,
            'totalUsers' => cache()->remember('total_users_count', 3600, fn () => \App\Models\User::count()),
        ]);
    }
}
