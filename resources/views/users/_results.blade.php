{{--
    _results.blade.php
    Rendered server-side by Laravel and returned as HTML string inside JSON.
    The JS in search.blade.php injects this into the DOM.

    $users = CursorPaginator instance
    $term  = search query string (for highlighting)
--}}

@forelse ($users as $user)
    @php
        // Generate a deterministic avatar color from user id
        $hue = ($user->id * 47) % 360;
        $initial = strtoupper(substr($user->name, 0, 1));

        // ① Relevance score only present when full-text search was used
        $score = $user->relevance_score ?? null;
    @endphp

    <li class="user-card">
        {{-- Avatar --}}
        <div class="avatar" style="
            background: hsl({{ $hue }}, 50%, 20%);
            border: 2px solid hsl({{ $hue }}, 60%, 45%);
            color: hsl({{ $hue }}, 80%, 75%);
        ">{{ $initial }}</div>

        {{-- Info --}}
        <div class="user-info">
            <div class="user-name">
                {{-- Highlight matching term in name --}}
                @if ($term)
                    {!! preg_replace(
                        '/(' . preg_quote(e($term), '/') . ')/iu',
                        '<mark>$1</mark>',
                        e($user->name)
                    ) !!}
                @else
                    {{ $user->name }}
                @endif

                {{-- ① Relevance score badge (only for full-text results) --}}
                @if ($score !== null)
                    <span class="tag tag-orange">① score: {{ number_format($score, 2) }}</span>
                @endif

                <span class="tag tag-muted">#{{ $user->id }}</span>
            </div>

            <div class="user-email">
                @if ($term)
                    {!! preg_replace(
                        '/(' . preg_quote(e($term), '/') . ')/iu',
                        '<mark>$1</mark>',
                        e($user->email)
                    ) !!}
                @else
                    {{ $user->email }}
                @endif
            </div>

            <div class="user-tags">
                <span class="tag tag-green">
                    @if ($term)
                        {!! preg_replace('/(' . preg_quote(e($term), '/') . ')/iu', '<mark>$1</mark>', e($user->city)) !!}
                    @else
                        {{ $user->city }}
                    @endif
                </span>

                @if (isset($user->profession))
                    <span class="tag tag-purple">
                        @if ($term)
                            {!! preg_replace('/(' . preg_quote(e($term), '/') . ')/iu', '<mark>$1</mark>', e($user->profession)) !!}
                        @else
                            {{ $user->profession }}
                        @endif
                    </span>
                @endif

                @if (isset($user->age))
                    <span class="tag tag-blue">Age {{ $user->age }}</span>
                @endif

                @if (isset($user->experience))
                    <span class="tag tag-muted">{{ $user->experience }}yr exp</span>
                @endif

                @if (isset($user->state))
                    <span class="tag tag-muted">{{ $user->state }}</span>
                @endif
            </div>
        </div>
    </li>

@empty
    <li class="state-msg">
        <span class="icon">🔍</span>
        <span>No users found for "<strong>{{ $term }}</strong>"</span>
        <span style="font-size:11px; color:#8b949e; font-family:monospace">
            Full-Text Index returned 0 matches. Try a different term.
        </span>
    </li>
@endforelse