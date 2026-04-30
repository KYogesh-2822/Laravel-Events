<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel Search — 50M Users</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:       #0d1117;
            --surface:  #161b22;
            --border:   #30363d;
            --text:     #e6edf3;
            --muted:    #8b949e;
            --green:    #3fb950;
            --blue:     #58a6ff;
            --orange:   #f0883e;
            --purple:   #a371f7;
            --red:      #f85149;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Segoe UI', system-ui, sans-serif;
            font-size: 14px;
            line-height: 1.5;
        }

        /* ── Layout ── */
        .header {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 14px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .header h1 { font-size: 16px; font-weight: 700; color: var(--text); }
        .header h1 span { color: var(--green); }
        .badge {
            background: #1f3124;
            color: var(--green);
            border: 1px solid #2ea04380;
            border-radius: 20px;
            padding: 2px 10px;
            font-size: 11px;
            font-weight: 600;
        }

        .layout { display: grid; grid-template-columns: 320px 1fr; min-height: calc(100vh - 53px); }

        /* ── Sidebar (technique panel) ── */
        .sidebar {
            background: var(--surface);
            border-right: 1px solid var(--border);
            padding: 20px;
            overflow-y: auto;
        }
        .sidebar h2 {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--muted);
            margin-bottom: 16px;
        }

        /* Technique cards */
        .technique-card {
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 14px;
            margin-bottom: 12px;
            background: var(--bg);
        }
        .technique-card .t-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }
        .t-num {
            width: 22px; height: 22px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 800;
            flex-shrink: 0;
        }
        .t-num.green  { background: #1f3124; color: var(--green);  border: 1px solid var(--green); }
        .t-num.blue   { background: #0d2044; color: var(--blue);   border: 1px solid var(--blue);  }
        .t-num.orange { background: #2d1f0a; color: var(--orange); border: 1px solid var(--orange);}

        .t-title { font-size: 13px; font-weight: 700; }
        .t-desc  { font-size: 12px; color: var(--muted); margin-bottom: 8px; }
        .t-code  {
            background: #010409;
            border: 1px solid var(--border);
            border-radius: 4px;
            padding: 8px 10px;
            font-family: 'Courier New', monospace;
            font-size: 11px;
            color: var(--green);
            word-break: break-all;
        }
        .t-code .comment { color: var(--muted); }
        .t-code .keyword { color: var(--blue); }
        .t-code .string  { color: var(--orange); }

        /* Live stats panel */
        .stats-panel {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 14px;
            margin-top: 16px;
        }
        .stats-panel h3 {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--muted);
            margin-bottom: 12px;
        }
        .stat-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid var(--border);
            font-size: 12px;
        }
        .stat-row:last-child { border-bottom: none; }
        .stat-row .label { color: var(--muted); }
        .stat-row .value { font-weight: 700; font-family: monospace; }
        .val-green  { color: var(--green);  }
        .val-blue   { color: var(--blue);   }
        .val-orange { color: var(--orange); }
        .val-red    { color: var(--red);    }
        .val-purple { color: var(--purple); }

        /* ── Main content ── */
        .main { display: flex; flex-direction: column; }

        .search-area {
            padding: 20px 24px 16px;
            border-bottom: 1px solid var(--border);
            background: var(--surface);
        }

        .search-wrap {
            position: relative;
            max-width: 600px;
        }
        .search-icon {
            position: absolute;
            left: 14px; top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 16px;
            pointer-events: none;
        }
        #search-input {
            width: 100%;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px 14px 10px 40px;
            font-size: 15px;
            color: var(--text);
            outline: none;
            transition: border-color 0.15s;
        }
        #search-input:focus { border-color: var(--blue); }

        /* ③ DEBOUNCE INDICATOR */
        .debounce-bar-wrap {
            max-width: 600px;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 20px;
        }
        .debounce-label {
            font-size: 11px;
            color: var(--muted);
            white-space: nowrap;
            font-family: monospace;
        }
        .debounce-bar-outer {
            flex: 1;
            height: 4px;
            background: var(--border);
            border-radius: 2px;
            overflow: hidden;
        }
        .debounce-bar-inner {
            height: 100%;
            background: var(--orange);
            border-radius: 2px;
            width: 0%;
            transition: width 0.05s linear;
        }

        /* ② CURSOR indicator */
        .cursor-info {
            display: flex;
            gap: 8px;
            margin-top: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        .cursor-token {
            font-family: monospace;
            font-size: 11px;
            background: #0d2044;
            color: var(--blue);
            border: 1px solid #58a6ff40;
            border-radius: 4px;
            padding: 2px 8px;
            max-width: 260px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Results */
        .results-wrap {
            flex: 1;
            overflow-y: auto;
            padding: 0;
        }

        #results-list { list-style: none; }

        .user-card {
            display: flex;
            gap: 14px;
            padding: 14px 24px;
            border-bottom: 1px solid var(--border);
            align-items: flex-start;
            transition: background 0.1s;
        }
        .user-card:hover { background: var(--surface); }

        .avatar {
            width: 38px; height: 38px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 15px; flex-shrink: 0;
        }
        .user-info { flex: 1; min-width: 0; }
        .user-name {
            font-size: 14px;
            font-weight: 700;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .user-email { color: var(--muted); font-size: 12px; margin-top: 2px; }
        .user-tags  { display: flex; gap: 6px; margin-top: 8px; flex-wrap: wrap; }
        .tag {
            font-size: 11px;
            border-radius: 4px;
            padding: 1px 8px;
            font-weight: 600;
            border-width: 1px;
            border-style: solid;
        }
        .tag-green  { background:#1f3124; color:var(--green);  border-color:#3fb95040; }
        .tag-blue   { background:#0d2044; color:var(--blue);   border-color:#58a6ff40; }
        .tag-purple { background:#1e1240; color:var(--purple); border-color:#a371f740; }
        .tag-orange { background:#2d1f0a; color:var(--orange); border-color:#f0883e40; }
        .tag-muted  { background:#21262d; color:var(--muted);  border-color:#30363d;   }

        mark {
            background: #f0883e33;
            color: var(--orange);
            border-radius: 2px;
        }

        /* Load more button */
        #load-more-wrap {
            padding: 16px 24px;
            text-align: center;
        }
        #load-more-btn {
            background: var(--surface);
            color: var(--blue);
            border: 1px solid var(--blue);
            border-radius: 6px;
            padding: 8px 24px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s;
        }
        #load-more-btn:hover   { background: #0d2044; }
        #load-more-btn:disabled{ opacity: 0.5; cursor: not-allowed; }

        /* Empty / loading states */
        .state-msg {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 24px;
            color: var(--muted);
            gap: 8px;
            text-align: center;
        }
        .state-msg .icon { font-size: 32px; }
        .spinner {
            width: 20px; height: 20px;
            border: 2px solid var(--border);
            border-top-color: var(--blue);
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>

{{-- HEADER --}}
<div class="header">
    <h1>Laravel <span>Search Engine</span></h1>
    <span class="badge">{{ number_format($totalUsers) }} Users</span>
</div>

<div class="layout">

    {{-- ── SIDEBAR: Technique explainer + live stats ── --}}
    <aside class="sidebar">
        <h2>How It Works</h2>

        <div class="technique-card">
            <div class="t-header">
                <span class="t-num green">1</span>
                <span class="t-title">Full-Text Search</span>
            </div>
            <div class="t-desc">MATCH() AGAINST() in boolean mode — O(log n) via index, not LIKE table scan.</div>
            <div class="t-code"><span class="keyword">MATCH</span>(name,email,city) <span class="keyword">AGAINST</span>(<span class="string">'+term*'</span> <span class="keyword">IN BOOLEAN MODE</span>)</div>
        </div>

        <div class="technique-card">
            <div class="t-header">
                <span class="t-num blue">2</span>
                <span class="t-title">Cursor Pagination</span>
            </div>
            <div class="t-desc">WHERE id &gt; last_seen_id — O(1) at any depth. No offset scanning.</div>
            <div class="t-code"><span class="keyword">WHERE</span> id &gt; <span class="string">{cursor}</span> <span class="keyword">ORDER BY</span> id <span class="keyword">LIMIT</span> 15</div>
        </div>

        <div class="technique-card">
            <div class="t-header">
                <span class="t-num orange">3</span>
                <span class="t-title">Debounce (350ms)</span>
            </div>
            <div class="t-desc">Waits 350ms after last keystroke before firing API call. 10 keystrokes = 1 request.</div>
            <div class="t-code"><span class="comment">// clear → restart timer each keystroke</span><br><span class="keyword">clearTimeout</span>(timer);<br>timer = <span class="keyword">setTimeout</span>(fetch, <span class="string">350</span>);</div>
        </div>

        <div class="stats-panel">
            <h3>Live Stats</h3>
            <div class="stat-row">
                <span class="label">Keystrokes</span>
                <span class="value val-orange" id="stat-keystrokes">0</span>
            </div>
            <div class="stat-row">
                <span class="label">API Calls</span>
                <span class="value val-green" id="stat-api-calls">0</span>
            </div>
            <div class="stat-row">
                <span class="label">Calls Saved</span>
                <span class="value val-red" id="stat-saved">0</span>
            </div>
            <div class="stat-row">
                <span class="label">Results Shown</span>
                <span class="value val-blue" id="stat-results">0</span>
            </div>
            <div class="stat-row">
                <span class="label">Pages Loaded</span>
                <span class="value val-purple" id="stat-pages">0</span>
            </div>
            <div class="stat-row">
                <span class="label">Cursor</span>
                <span class="value val-blue" id="stat-cursor">none</span>
            </div>
        </div>
    </aside>

    {{-- ── MAIN CONTENT ── --}}
    <main class="main">

        {{-- Search bar --}}
        <div class="search-area">
            <div class="search-wrap">
                <span class="search-icon">🔍</span>
                {{-- ③ DEBOUNCE: Input event triggers JS debounce timer --}}
                <input
                    type="text"
                    id="search-input"
                    placeholder="Search {{ number_format($totalUsers) }} users by name, city, profession..."
                    autocomplete="off"
                    value="{{ $term }}"
                >
            </div>

            {{-- ③ Debounce progress bar --}}
            <div class="debounce-bar-wrap" id="debounce-wrap" style="display:none">
                <span class="debounce-label">⏱ debounce: <span id="debounce-ms">350</span>ms</span>
                <div class="debounce-bar-outer">
                    <div class="debounce-bar-inner" id="debounce-bar"></div>
                </div>
                <span class="debounce-label" style="color:var(--orange)">waiting...</span>
            </div>

            {{-- ② Cursor token display --}}
            <div class="cursor-info" id="cursor-info" style="display:none">
                <span style="font-size:11px; color:var(--muted)">② next_cursor:</span>
                <span class="cursor-token" id="cursor-display"></span>
                <span style="font-size:11px; color:var(--muted)">→ decoded: WHERE id &gt; <span id="cursor-decoded" style="color:var(--blue)"></span></span>
            </div>
        </div>

        {{-- Results --}}
        <div class="results-wrap">
            <ul id="results-list">
                <li class="state-msg" id="empty-state">
                    <span class="icon">👤</span>
                    <span>Type at least 3 characters to search {{ number_format($totalUsers) }} users</span>
                    <span style="font-size:11px; font-family:monospace">Uses: MATCH(name,email,city,profession,bio) AGAINST('+term*' IN BOOLEAN MODE)</span>
                </li>
            </ul>

            <div id="load-more-wrap" style="display:none">
                {{-- ② CURSOR PAGINATION: Load more uses cursor token, not page number --}}
                <button id="load-more-btn">Load more results ↓</button>
            </div>
        </div>

    </main>
</div>

{{-- ══════════════════════════════════════════════════════════════
     VANILLA JS — No framework, no npm, just the browser
     ══════════════════════════════════════════════════════════════ --}}
<script>
(function () {
    'use strict';

    // ── State ──────────────────────────────────────────────────
    let nextCursor    = null;
    let currentQuery  = '';
    let debounceTimer = null;
    let debounceInterval = null;
    let keystrokeCount = 0;
    let apiCallCount   = 0;
    let resultCount    = 0;
    let pageCount      = 0;

    // ── DOM refs ───────────────────────────────────────────────
    const input       = document.getElementById('search-input');
    const resultsList = document.getElementById('results-list');
    const emptyState  = document.getElementById('empty-state');
    const loadMoreWrap= document.getElementById('load-more-wrap');
    const loadMoreBtn = document.getElementById('load-more-btn');
    const debounceWrap= document.getElementById('debounce-wrap');
    const debounceBar = document.getElementById('debounce-bar');
    const debounceMs  = document.getElementById('debounce-ms');
    const cursorInfo  = document.getElementById('cursor-info');
    const cursorDisp  = document.getElementById('cursor-display');
    const cursorDecod = document.getElementById('cursor-decoded');

    // Stat elements
    const statKeystrokes = document.getElementById('stat-keystrokes');
    const statApiCalls   = document.getElementById('stat-api-calls');
    const statSaved      = document.getElementById('stat-saved');
    const statResults    = document.getElementById('stat-results');
    const statPages      = document.getElementById('stat-pages');
    const statCursor     = document.getElementById('stat-cursor');

    // ── ③ DEBOUNCE — The core technique ────────────────────────
    //
    //  Every keystroke:
    //    1. Increments keystroke counter
    //    2. Clears any pending timer (cancels previous scheduled call)
    //    3. Starts a NEW 350ms timer
    //    4. When timer fires → one API call
    //
    //  Result: 10 keystrokes = 1 API call (not 10)
    // ──────────────────────────────────────────────────────────

    input.addEventListener('input', function () {
        const term = this.value.trim();
        keystrokeCount++;
        updateStats();

        // Clear previous debounce timer (this is the debounce mechanism)
        clearTimeout(debounceTimer);
        clearInterval(debounceInterval);

        if (!term) {
            resetResults();
            debounceWrap.style.display = 'none';
            return;
        }

        // Show debounce progress bar
        debounceWrap.style.display = 'flex';
        let remaining = 350;
        debounceBar.style.width = '100%';

        // Animate bar draining down
        debounceInterval = setInterval(function () {
            remaining -= 30;
            const pct = Math.max(0, (remaining / 350) * 100);
            debounceBar.style.width = pct + '%';
            debounceMs.textContent = Math.max(0, remaining);
            if (remaining <= 0) clearInterval(debounceInterval);
        }, 30);

        // Schedule the actual API call — only fires if user stops typing
        debounceTimer = setTimeout(function () {
            clearInterval(debounceInterval);
            debounceWrap.style.display = 'none';

            // This is the one API call that finally fires
            apiCallCount++;
            currentQuery = term;
            nextCursor   = null;
            resultCount  = 0;
            pageCount    = 0;
            fetchUsers(term, null);
        }, 350);
    });

    // ── ① + ② FETCH: Full-Text Search + Cursor Pagination ─────
    //
    //  GET /users/search/query?q=term&cursor=TOKEN&per_page=15
    //
    //  Laravel controller does:
    //    ① User::fullTextSearch($term)        — MATCH AGAINST
    //    ② ->cursorPaginate(15)               — WHERE id > cursor
    //  Returns JSON with { html, next_cursor, has_more }
    // ──────────────────────────────────────────────────────────

    function fetchUsers(term, cursor) {
        loadMoreBtn.disabled = true;
        if (!cursor) showLoadingState();

        const params = new URLSearchParams({ q: term, per_page: 15 });
        if (cursor) params.append('cursor', cursor);

        fetch('{{ route("users.search.query") }}?' + params.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(function (res) { return res.json(); })
        .then(function (json) {
            pageCount++;

            if (!cursor) {
                // Fresh search — replace results
                resultsList.innerHTML = json.html;
                emptyState.remove && emptyState.remove();
            } else {
                // Load more — append results
                const tmp = document.createElement('div');
                tmp.innerHTML = json.html;
                while (tmp.firstChild) resultsList.appendChild(tmp.firstChild);
            }

            // ② Update cursor for next "load more" call
            nextCursor = json.next_cursor;
            resultCount += (json.total_found || 0);

            // Show/hide cursor info
            if (nextCursor) {
                cursorInfo.style.display = 'flex';
                cursorDisp.textContent   = nextCursor;
                // Decode base64 to show the raw id
                try {
                    const decoded = JSON.parse(atob(nextCursor));
                    cursorDecod.textContent = decoded.id || '?';
                } catch (e) {
                    cursorDecod.textContent = '…';
                }
            } else {
                cursorInfo.style.display = 'none';
            }

            // Show/hide load more button
            if (json.has_more && nextCursor) {
                loadMoreWrap.style.display = 'block';
                loadMoreBtn.disabled = false;
            } else {
                loadMoreWrap.style.display = 'none';
            }

            updateStats();
        })
        .catch(function (err) {
            console.error('Search error:', err);
            resultsList.innerHTML = '<li class="state-msg"><span>Something went wrong. Check your server.</span></li>';
        });
    }

    // ② Load more — uses stored cursor token
    loadMoreBtn.addEventListener('click', function () {
        if (!nextCursor || !currentQuery) return;
        apiCallCount++;
        updateStats();
        fetchUsers(currentQuery, nextCursor);
    });

    function resetResults() {
        resultsList.innerHTML = '<li class="state-msg" id="empty-state"><span class="icon">👤</span><span>Type at least 3 characters to search 50 million users</span></li>';
        nextCursor   = null;
        currentQuery = '';
        resultCount  = 0;
        pageCount    = 0;
        loadMoreWrap.style.display = 'none';
        cursorInfo.style.display   = 'none';
        updateStats();
    }

    function showLoadingState() {
        resultsList.innerHTML = '<li class="state-msg"><div class="spinner"></div><span>Searching with Full-Text Index...</span></li>';
    }

    function updateStats() {
        statKeystrokes.textContent = keystrokeCount;
        statApiCalls.textContent   = apiCallCount;
        statSaved.textContent      = Math.max(0, keystrokeCount - apiCallCount);
        statResults.textContent    = document.querySelectorAll('.user-card').length;
        statPages.textContent      = pageCount;
        statCursor.textContent     = nextCursor ? nextCursor.substring(0, 20) + '…' : 'none';
    }

})();
</script>

</body>
</html>