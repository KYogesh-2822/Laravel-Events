<!-- Header & Navigation -->
    <header>
        <div class="logo">FrontendLab</div>
        <nav>
            <ul>
                <li><a href="#home">Home</a></li>
                <li><a href="#features">Features</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </nav>

        <!-- Header Actions (Toggle + CTA) -->
        <div class="header-actions">
            <button id="theme-toggle" class="theme-toggle-btn" aria-label="Toggle Dark Mode">🌙</button>
            @if(auth()->check())
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-default btn-flat float-end">Sign out</button>
                  </form>
            @else
            <a href="{{ route('user.register') }}"><button class="btn nav-btn">Sign Up</button></a>
            @endif
            
            <!-- Hamburger Menu Button -->
            <button class="hamburger" aria-label="Open Menu">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
        </div>
    </header>
