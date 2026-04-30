<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

use Laravel\Passport\HasApiTokens;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
       protected $fillable = ['name', 'email', 'phone', 'city'];


       public function detail(): HasOne
    {
        return $this->hasOne(UserDetail::class);
    }



    // ─────────────────────────────────────────────────────────────
    // ① FULL-TEXT SEARCH SCOPE
    //
    // HOW IT WORKS:
    //   MySQL builds an inverted index:
    //     "john"  → [row_ids: 1, 45, 230, 10023 ...]
    //     "delhi" → [row_ids: 45, 1002, 33000 ...]
    //   MATCH() AGAINST() does O(log n) index lookup — NOT a full scan.
    //   LIKE '%john%' would do a full 50M row table scan (~30s).
    //   MATCH() AGAINST() returns results in <100ms on 50M rows.
    // ─────────────────────────────────────────────────────────────
 
    public function scopeFullTextSearch(Builder $query, string $term): Builder
    {
        $trimmed = trim($term);

        // ── EMAIL EXACT MATCH ───────────────────────────────────
        // If input looks like an email → use exact WHERE lookup
        // Full-text can't do exact match ("+kiran* +outlook*" matches many)
        // WHERE email = ? uses B-Tree index → O(log n) → returns 1 row
        if (filter_var($trimmed, FILTER_VALIDATE_EMAIL)) {
            return $query
                ->join('user_details', 'users.id', '=', 'user_details.user_id')
                ->where('users.email', $trimmed)
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
                ->orderBy('users.id');
        }

        $booleanQuery = $this->toBooleanQuery($trimmed);

        // If no valid search words (all < 3 chars), return nothing instantly
        if (empty($booleanQuery)) {
            return $query->whereRaw('1 = 0');
        }

        // ── STEP 1: Two fast independent index lookups ──────────
        // Each MATCH uses its own full-text index → O(log n) each
        // Runs in PHP, not as a slow SQL subquery
        $userIds = DB::table('users')
            ->whereRaw('MATCH(name, email, city) AGAINST(? IN BOOLEAN MODE)', [$booleanQuery])
            ->limit(500)
            ->pluck('id');

        $detailUserIds = DB::table('user_details')
            ->whereRaw('MATCH(profession, bio, state) AGAINST(? IN BOOLEAN MODE)', [$booleanQuery])
            ->limit(500)
            ->pluck('user_id');

        $matchedIds = $userIds->merge($detailUserIds)->unique()->sort()->values()->all();

        // ── STEP 2: No results? Return empty INSTANTLY ──────────
        // No 20-second wait — we know immediately
        if (empty($matchedIds)) {
            return $query->whereRaw('1 = 0');
        }

        // ── STEP 3: Fetch full data only for matched IDs ────────
        // whereIn with a small set of IDs = instant via primary key
        return $query
            ->join('user_details', 'users.id', '=', 'user_details.user_id')
            ->whereIn('users.id', $matchedIds)
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
            ->orderBy('users.id');
    }
 
    // ─────────────────────────────────────────────────────────────
    // Convert plain text to MySQL Boolean Full-Text query
    //
    //  "john doe new"  →  "+john* +doe* +new*"
    //
    //  Rules:
    //   - Minimum 3 chars (MySQL ft_min_word_len default)
    //   - + prefix = row MUST contain this word
    //   - * suffix = match any word that starts with this prefix
    // ─────────────────────────────────────────────────────────────
 
    // MySQL InnoDB default stopwords — these are NEVER indexed,
    // so including them in a +required query guarantees 0 results
    private const STOPWORDS = [
        'a','about','an','are','as','at','be','by','com','de','en',
        'for','from','how','i','in','is','it','la','of','on','or',
        'that','the','this','to','was','what','when','where','who',
        'will','with','und','www',
    ];

    private function toBooleanQuery(string $term): string
    {
        return collect(preg_split('/[^a-zA-Z0-9]+/', trim($term)))
            ->map(fn(string $word) => trim($word))
            ->filter(fn(string $word) => mb_strlen($word) >= 3)
            ->filter(fn(string $word) => !in_array(strtolower($word), self::STOPWORDS))
            ->map(fn(string $word) => '+' . $word . '*')
            ->implode(' ');
    }


    
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
