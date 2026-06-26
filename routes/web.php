<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserSearchController;


Route::get('/', function () {
    return view('welcome');
});



// ─────────────────────────────────────────────────────────────
// Main page + debounced search endpoint (Function 3)
// ─────────────────────────────────────────────────────────────
Route::get('/users/search', [UserSearchController::class, 'index'])->name('users.search');
Route::get('/users/search/query', [UserSearchController::class, 'functionThree_AllCombined'])->name('users.search.query');
 
// ─────────────────────────────────────────────────────────────
// Function 1 — Full-Text Search only (for testing/debugging)
// Usage: GET /users/search/fulltext?q=john
// ─────────────────────────────────────────────────────────────
Route::get('/users/search/fulltext', function (\Illuminate\Http\Request $request) {
    $controller = app(UserSearchController::class);
    $result     = $controller->functionOne_FullTextSearch($request->get('q', ''));
    return response()->json($result, 200, [], JSON_PRETTY_PRINT);
})->name('users.search.fulltext');
 
// ─────────────────────────────────────────────────────────────
// Function 2 — Cursor Pagination only (for testing/debugging)
// Usage: GET /users/search/cursor?q=john&cursor=eyJpZCI6MTV9
// ─────────────────────────────────────────────────────────────
Route::get('/users/search/cursor', [UserSearchController::class, 'functionTwo_CursorPagination'])
    ->name('users.search.cursor');