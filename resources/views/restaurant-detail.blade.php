@extends('layouts.main')

@section('title', $restaurant->name . ' - TastePoint')

@section('content')
<!-- Navbar -->
<nav class="navbar">
    <div class="navbar-container">
        <a href="{{ route('home') }}" class="navbar-brand">
            <i class="fas fa-utensils"></i>
            <span>TastePoint</span>
        </a>
        <div class="navbar-user">
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span class="user-name">{{ Auth::user()->name }}</span>
            </div>
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="main-container">
    <!-- Back Button -->
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('home') }}" class="btn btn-secondary" style="text-decoration: none;">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Restaurant Header -->
    <div class="content-card">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <!-- Restaurant Image -->
            <div>
                <img src="{{ $restaurant->image_url }}" 
                     alt="{{ $restaurant->name }}" 
                     style="width: 100%; height: 400px; object-fit: cover; border-radius: 15px;">
            </div>

            <!-- Restaurant Info -->
            <div>
                <h1 style="font-size: 2.5rem; margin-bottom: 1rem; color: var(--light);">
                    {{ $restaurant->name }}
                </h1>

                <!-- Rating -->
                <div style="margin-bottom: 1.5rem;">
                    <div class="restaurant-rating" style="font-size: 1.3rem;">
                        <i class="fas fa-star"></i>
                        <span>{{ number_format($restaurant->rating, 1) }}</span>
                        <span style="color: var(--text-muted); font-size: 1rem;">({{ $restaurant->rating }}/5.0)</span>
                    </div>
                </div>

                <!-- Address -->
                <div style="margin-bottom: 1.5rem;">
                    <h3 style="color: var(--primary); margin-bottom: 0.5rem;">
                        <i class="fas fa-location-dot"></i> Alamat
                    </h3>
                    <p style="color: var(--light); line-height: 1.6;">{{ $restaurant->address }}</p>
                    
                    <!-- Google Maps Link -->
                    @if($restaurant->google_maps_link)
                    <a href="{{ $restaurant->google_maps_link }}" target="_blank" class="btn btn-primary" style="margin-top: 0.5rem; text-decoration: none;">
                        <i class="fas fa-map-marked-alt"></i> Buka di Google Maps
                    </a>
                    @endif
                </div>

                <!-- Phone -->
                <div style="margin-bottom: 1.5rem;">
                    <h3 style="color: var(--primary); margin-bottom: 0.5rem;">
                        <i class="fas fa-phone"></i> Telepon
                    </h3>
                    <p style="color: var(--light);">
                        <a href="tel:{{ $restaurant->phone }}" style="color: var(--light); text-decoration: none;">
                            {{ $restaurant->phone }}
                        </a>
                    </p>
                </div>

                <!-- Opening Hours -->
                @if($restaurant->opening_hours || $restaurant->opening_days)
                <div style="margin-bottom: 1.5rem;">
                    <h3 style="color: var(--primary); margin-bottom: 0.5rem;">
                        <i class="fas fa-clock"></i> Jam Buka
                    </h3>
                    @if($restaurant->opening_days)
                    <p style="color: var(--light); margin-bottom: 0.3rem;">{{ $restaurant->opening_days }}</p>
                    @endif
                    @if($restaurant->opening_hours)
                    <p style="color: var(--light);">{{ $restaurant->opening_hours }}</p>
                    @endif
                </div>
                @endif

                <!-- Price Range -->
                <div style="margin-bottom: 1.5rem;">
                    <h3 style="color: var(--primary); margin-bottom: 0.5rem;">
                        <i class="fas fa-money-bill-wave"></i> Range Harga
                    </h3>
                    <p style="color: var(--light); font-size: 1.2rem; font-weight: 600;">
                        {{ $restaurant->price_range }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Description -->
        @if($restaurant->description)
        <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--card-border);">
            <h3 style="color: var(--primary); margin-bottom: 1rem;">
                <i class="fas fa-info-circle"></i> Tentang Restoran
            </h3>
            <p style="color: var(--light); line-height: 1.8;">{{ $restaurant->description }}</p>
        </div>
        @endif
    </div>

    <!-- Menus Section -->
    <div class="content-card" style="margin-top: 2rem;">
        <div class="content-card-header">
            <h2 class="content-card-title">
                <i class="fas fa-utensils"></i> Menu Tersedia
            </h2>
        </div>

        @if($restaurant->menus->count() > 0)
            <!-- Group by category -->
            @php
                $menusByCategory = $restaurant->menus->groupBy('category');
            @endphp

            @foreach($menusByCategory as $category => $menus)
                <div style="margin-bottom: 2rem;">
                    <h3 style="color: var(--primary); margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--card-border);">
                        {{ $category ?: 'Menu Lainnya' }}
                    </h3>

                    <div class="menus-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem;">
                        @foreach($menus as $menu)
                        <div class="menu-card" style="background: rgba(255, 255, 255, 0.05); border: 1px solid var(--card-border); border-radius: 15px; overflow: hidden; transition: var(--transition);">
                            <img src="{{ $menu->image_url }}" 
                                 alt="{{ $menu->name }}" 
                                 style="width: 100%; height: 150px; object-fit: cover;">
                            <div style="padding: 1rem;">
                                <h4 style="color: var(--light); margin-bottom: 0.5rem; font-size: 1.1rem;">
                                    {{ $menu->name }}
                                </h4>
                                @if($menu->description)
                                <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.8rem; line-height: 1.4;">
                                    {{ Str::limit($menu->description, 80) }}
                                </p>
                                @endif
                                <div style="color: var(--primary); font-weight: 600; font-size: 1.1rem;">
                                    {{ $menu->formatted_price }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @else
            <div style="text-align: center; padding: 3rem; color: var(--text-muted);">
                <i class="fas fa-utensils" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                <p>Belum ada menu tersedia untuk restoran ini.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.menu-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(255, 107, 0, 0.2);
    border-color: var(--primary);
}

@media (max-width: 768px) {
    .content-card > div:first-child {
        grid-template-columns: 1fr !important;
    }
    
    h1 {
        font-size: 1.8rem !important;
    }
    
    .menus-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>
@endpush