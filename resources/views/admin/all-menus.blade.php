@extends('layouts.admin')

@section('title', 'Semua Menu Makanan - TastePoint')

@section('content')
<!-- Top Bar -->
<div class="top-bar">
    <div>
        <h1 class="page-title">Semua Menu Makanan</h1>
        <p style="color: var(--text-muted); margin-top: 0.5rem;">Lihat semua menu dari semua restoran</p>
    </div>
    <div class="top-bar-actions">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid" style="margin-bottom: 2rem;">
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <i class="fas fa-utensils"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Total Menu</div>
            <div class="stat-value">{{ $menus->total() }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Menu Tersedia</div>
            <div class="stat-value">{{ $menus->where('status', 'available')->count() }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <i class="fas fa-store"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Restoran</div>
            <div class="stat-value">{{ $menus->pluck('restaurant_id')->unique()->count() }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
            <i class="fas fa-tags"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Kategori</div>
            <div class="stat-value">{{ $menus->pluck('category')->filter()->unique()->count() }}</div>
        </div>
    </div>
</div>

<!-- Search & Filter -->
<div class="content-card" style="margin-bottom: 2rem;">
    <form action="{{ route('admin.all-menus') }}" method="GET">
        <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Cari nama menu..."
                   style="flex: 1; min-width: 200px; padding: 0.8rem 1rem; background: rgba(255, 255, 255, 0.1); border: 1px solid var(--card-border); border-radius: 10px; color: var(--light);">
            
            <select name="category" 
                    style="padding: 0.8rem 1rem; background: rgba(255, 255, 255, 0.1); border: 1px solid var(--card-border); border-radius: 10px; color: var(--light);">
                <option value="">Semua Kategori</option>
                @foreach($menus->pluck('category')->filter()->unique() as $cat)
                <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>

            <select name="restaurant" 
                    style="padding: 0.8rem 1rem; background: rgba(255, 255, 255, 0.1); border: 1px solid var(--card-border); border-radius: 10px; color: var(--light);">
                <option value="">Semua Restoran</option>
                @foreach($restaurants as $restaurant)
                <option value="{{ $restaurant->id }}" {{ request('restaurant') == $restaurant->id ? 'selected' : '' }}>
                    {{ $restaurant->name }}
                </option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Cari
            </button>
            
            @if(request()->hasAny(['search', 'category', 'restaurant']))
            <a href="{{ route('admin.all-menus') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Reset
            </a>
            @endif
        </div>
    </form>
</div>

<!-- Menus Grid -->
<div class="content-card">
    <div class="content-card-header">
        <h2 class="content-card-title">
            <i class="fas fa-list"></i> Daftar Menu
        </h2>
    </div>

    @if($menus->count() > 0)
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; padding: 1rem 0;">
            @foreach($menus as $menu)
            <div class="menu-card" style="background: rgba(255, 255, 255, 0.05); border: 1px solid var(--card-border); border-radius: 15px; overflow: hidden; transition: var(--transition);">
                <!-- Menu Image -->
                @if($menu->image)
                <img src="{{ asset('storage/' . $menu->image) }}" 
                     alt="{{ $menu->name }}" 
                     style="width: 100%; height: 180px; object-fit: cover;">
                @else
                <div style="width: 100%; height: 180px; background: linear-gradient(135deg, rgba(255, 107, 0, 0.2), rgba(255, 107, 0, 0.05)); display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-utensils" style="font-size: 3rem; color: var(--text-muted);"></i>
                </div>
                @endif

                <!-- Menu Info -->
                <div style="padding: 1.5rem;">
                    <!-- Category Badge -->
                    @if($menu->category)
                    <span style="background: rgba(255, 107, 0, 0.2); color: var(--primary); padding: 0.3rem 0.8rem; border-radius: 6px; font-size: 0.85rem; display: inline-block; margin-bottom: 0.8rem;">
                        {{ $menu->category }}
                    </span>
                    @endif

                    <!-- Menu Name -->
                    <h3 style="color: var(--light); margin-bottom: 0.5rem; font-size: 1.1rem;">
                        {{ $menu->name }}
                    </h3>
                    
                    <!-- Restaurant Name -->
                    <div style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.8rem;">
                        <i class="fas fa-store"></i> {{ $menu->restaurant->name }}
                    </div>

                    <!-- Description -->
                    @if($menu->description)
                    <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1rem; line-height: 1.4;">
                        {{ Str::limit($menu->description, 60) }}
                    </p>
                    @endif

                    <!-- Price -->
                    <div style="color: var(--primary); font-weight: 600; font-size: 1.2rem; margin-bottom: 1rem;">
                        Rp {{ number_format($menu->price, 0, ',', '.') }}
                    </div>

                    <!-- Status & Actions -->
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                        @if($menu->status === 'available')
                        <span class="badge badge-success" style="flex: 1;">
                            <i class="fas fa-check"></i> Tersedia
                        </span>
                        @else
                        <span class="badge" style="background: rgba(239, 68, 68, 0.2); color: #ef4444; flex: 1;">
                            <i class="fas fa-times"></i> Habis
                        </span>
                        @endif

                        <a href="{{ route('admin.menus', $menu->restaurant_id) }}" 
                           class="btn btn-sm btn-secondary"
                           title="Kelola Menu">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
            {{ $menus->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 3rem; color: var(--text-muted);">
            <i class="fas fa-utensils" style="font-size: 4rem; margin-bottom: 1rem;"></i>
            <h3>Belum Ada Menu</h3>
            <p style="margin: 1rem 0;">Tambahkan menu di halaman kelola restoran</p>
            <a href="{{ route('admin.restaurants') }}" class="btn btn-primary">
                <i class="fas fa-store"></i> Ke Halaman Restoran
            </a>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.menu-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(255, 107, 0, 0.2);
    border-color: var(--primary);
}

.badge {
    display: inline-block;
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 600;
    text-align: center;
}

.badge-success {
    background: rgba(16, 185, 129, 0.2);
    color: #10b981;
}

/* Responsive */
@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>
@endpush