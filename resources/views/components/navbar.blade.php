<nav class="navbar">
    <div class="navbar-container">
        <!-- Logo & Brand -->
        <div class="navbar-brand">
            <a href="{{ route('home') }}" class="brand-link">
                <span class="brand-icon">🍴</span>
                <span class="brand-text">TastePoint</span>
            </a>
        </div>

        <!-- Navigation Menu (Optional) -->
        <div class="navbar-menu">
            <a href="{{ route('home') }}" class="nav-link">Home</a>
            <a href="#" class="nav-link">Restaurants</a>
            <a href="#" class="nav-link">Reviews</a>
        </div>

        <!-- User Info & Logout -->
        <div class="navbar-user">
            @auth
                <div class="user-info">
                    <span class="user-icon">👤</span>
                    <span class="user-name">{{ Auth::user()->name }}</span>
                </div>
                
                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="btn-logout">
                        🚪 Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn-login">Login</a>
            @endauth
        </div>
    </div>
</nav>