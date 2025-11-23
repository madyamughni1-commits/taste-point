@extends('layouts.admin')

@section('title', 'Kelola Restoran - TastePoint')

@section('content')
<!-- Top Bar -->
<div class="top-bar">
    <div>
        <h1 class="page-title">Kelola Restoran</h1>
        <p style="color: var(--text-muted); margin-top: 0.5rem;">Manajemen data restoran</p>
    </div>
    <div class="top-bar-actions">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <a href="{{ route('admin.restaurants.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Restoran
        </a>
    </div>
</div>

<!-- Alert Messages -->
@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif

<!-- Search & Filter -->
<div class="content-card">
    <form method="GET" action="{{ route('admin.restaurants') }}">
        <div style="display: grid; grid-template-columns: 1fr auto; gap: 1rem;">
            <input type="text" name="search" placeholder="Cari nama restoran atau alamat..." 
                   value="{{ request('search') }}"
                   style="padding: 0.8rem 1rem; background: rgba(255, 255, 255, 0.1); border: 1px solid var(--card-border); border-radius: 10px; color: var(--light);">
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Cari
                </button>
                @if(request('search'))
                <a href="{{ route('admin.restaurants') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Reset
                </a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Restaurants Grid -->
<div class="content-card">
    <div class="content-card-header">
        <h2 class="content-card-title">
            <i class="fas fa-store"></i> Daftar Restoran ({{ $restaurants->total() ?? 0 }})
        </h2>
    </div>

    @if(isset($restaurants) && $restaurants->count() > 0)
        <div class="restaurant-grid">
            @foreach($restaurants as $index => $restaurant)
                <div class="restaurant-card">
                    <!-- Header dengan nomor dan status -->
                    <div class="restaurant-header">
                        <div class="restaurant-number">{{ $restaurants->firstItem() + $index }}</div>
                        @if($restaurant->status === 'active')
                        <span class="restaurant-status status-aktif">
                            <i class="fas fa-check-circle"></i> Aktif
                        </span>
                        @else
                        <span class="restaurant-status status-nonaktif">
                            <i class="fas fa-times-circle"></i> Nonaktif
                        </span>
                        @endif
                    </div>

                    <!-- Restaurant Image -->
                    @if($restaurant->image)
                    <div class="restaurant-image">
                        <img src="{{ asset('storage/' . $restaurant->image) }}" 
                             alt="{{ $restaurant->name }}">
                    </div>
                    @else
                    <div class="restaurant-image-placeholder">
                        <i class="fas fa-store"></i>
                    </div>
                    @endif

                    <!-- Nama Restoran -->
                    <h3 class="restaurant-name">{{ $restaurant->name }}</h3>

                    <!-- Lokasi -->
                    <div class="restaurant-location">
                        {{ Str::limit($restaurant->address, 60) }}
                    </div>

                    <!-- Detail Grid -->
                    <div class="restaurant-details">
                        <!-- Telepon -->
                        <div class="detail-item">
                            <span class="detail-label">Telepon</span>
                            <span class="detail-value" style="font-size: 0.85rem;">
                                {{ $restaurant->phone }}
                            </span>
                        </div>

                        <!-- Range Harga -->
                        <div class="detail-item">
                            <span class="detail-label">Range Harga</span>
                            <span class="detail-value price-range" style="font-size: 0.8rem; line-height: 1.3;">
                                Rp {{ number_format($restaurant->min_price, 0, ',', '.') }}<br>
                                - {{ number_format($restaurant->max_price, 0, ',', '.') }}
                            </span>
                        </div>

                        <!-- Rating -->
                        <div class="detail-item">
                            <span class="detail-label">Rating</span>
                            <div class="rating-display">
                                <span class="rating-star">⭐</span>
                                <span class="rating-number">{{ number_format($restaurant->rating ?? 4.5, 1) }}</span>
                            </div>
                        </div>

                        <!-- Jam Buka -->
                        @if($restaurant->opening_hours)
                        <div class="detail-item">
                            <span class="detail-label">Jam Buka</span>
                            <span class="detail-value" style="font-size: 0.85rem;">
                                {{ Str::limit($restaurant->opening_hours, 15) }}
                            </span>
                        </div>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="restaurant-actions">
                        <a href="{{ route('admin.restaurants.edit', $restaurant->id) }}" class="action-btn btn-edit">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.menus', $restaurant->id) }}" class="action-btn btn-menu">
                            <i class="fas fa-utensils"></i> Menu
                        </a>
                    </div>

                    <!-- Delete Button (Full Width) -->
                    <div style="margin-top: 0.5rem;">
                        <form action="{{ route('admin.restaurants.delete', $restaurant->id) }}" method="POST" 
                              onsubmit="return confirm('Yakin ingin menghapus restoran {{ $restaurant->name }}? Semua menu yang terkait juga akan terhapus!')" 
                              style="width: 100%;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn btn-delete" style="width: 100%;">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($restaurants->hasPages())
        <div style="margin-top: 2rem; display: flex; justify-content: center;">
            {{ $restaurants->links() }}
        </div>
        @endif
    @else
        <div class="empty-state">
            <div class="empty-state-icon">🏪</div>
            <h3 style="color: var(--light); margin-bottom: 0.5rem;">Belum Ada Restoran</h3>
            <p class="empty-state-text">
                @if(request('search'))
                    Tidak ada restoran yang cocok dengan pencarian "{{ request('search') }}"
                    <br>
                    <a href="{{ route('admin.restaurants') }}" style="color: var(--primary);">Lihat semua restoran</a>
                @else
                    Tambahkan restoran pertama untuk memulai!
                    <br>
                    <a href="{{ route('admin.restaurants.create') }}" class="btn btn-primary" style="margin-top: 1rem; display: inline-flex;">
                        <i class="fas fa-plus"></i> Tambah Restoran
                    </a>
                @endif
            </p>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
/* ===== GRID CARD STYLE untuk Kelola Restoran ===== */

.restaurant-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.restaurant-card {
    background: linear-gradient(135deg, rgba(45, 35, 25, 0.9) 0%, rgba(35, 30, 25, 0.95) 100%);
    border-radius: 15px;
    padding: 1.5rem;
    border: 1px solid rgba(255, 136, 0, 0.2);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.restaurant-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #ff8800 0%, #ffaa33 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.restaurant-card:hover {
    transform: translateY(-8px);
    border-color: rgba(255, 136, 0, 0.5);
    box-shadow: 0 12px 30px rgba(255, 136, 0, 0.25);
}

.restaurant-card:hover::before {
    opacity: 1;
}

/* Header dengan Badge */
.restaurant-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.restaurant-number {
    background: linear-gradient(135deg, #ff8800 0%, #ff6600 100%);
    color: #ffffff;
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 16px;
    box-shadow: 0 4px 15px rgba(255, 136, 0, 0.3);
}

/* Status Badge */
.restaurant-status {
    padding: 0.4rem 0.8rem;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.status-aktif {
    background: rgba(34, 197, 94, 0.2);
    color: #22c55e;
    border: 1px solid rgba(34, 197, 94, 0.3);
}

.status-nonaktif {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

/* Restaurant Image */
.restaurant-image {
    width: 100%;
    height: 180px;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 1rem;
}

.restaurant-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.restaurant-card:hover .restaurant-image img {
    transform: scale(1.05);
}

.restaurant-image-placeholder {
    width: 100%;
    height: 180px;
    background: linear-gradient(135deg, rgba(255, 107, 0, 0.1), rgba(255, 107, 0, 0.05));
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
}

.restaurant-image-placeholder i {
    font-size: 3rem;
    color: var(--text-muted);
}

/* Restaurant Name */
.restaurant-name {
    color: #ffffff;
    font-size: 1.3rem;
    font-weight: 700;
    margin: 0 0 0.8rem 0;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Location */
.restaurant-location {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.9rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    line-height: 1.4;
    min-height: 40px;
}

.restaurant-location::before {
    content: '📍';
    font-size: 1rem;
    flex-shrink: 0;
}

/* Details Grid - 2x2 untuk kelola restoran */
.restaurant-details {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(255, 136, 0, 0.15);
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.detail-label {
    color: rgba(255, 255, 255, 0.5);
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

.detail-value {
    color: #ffffff;
    font-size: 0.9rem;
    font-weight: 600;
}

.price-range {
    color: #ff8800;
    font-weight: 700;
}

/* Rating Display */
.rating-display {
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.rating-star {
    font-size: 1.1rem;
}

.rating-number {
    color: #ffffff;
    font-weight: 700;
    font-size: 1rem;
}

/* Action Buttons */
.restaurant-actions {
    display: flex;
    gap: 0.8rem;
}

.action-btn {
    flex: 1;
    padding: 0.7rem;
    border-radius: 8px;
    border: none;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    text-decoration: none;
}

.btn-edit {
    background: rgba(255, 136, 0, 0.2);
    color: #ff8800;
    border: 1px solid rgba(255, 136, 0, 0.3);
}

.btn-edit:hover {
    background: rgba(255, 136, 0, 0.3);
    border-color: rgba(255, 136, 0, 0.5);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 136, 0, 0.3);
}

.btn-menu {
    background: rgba(34, 197, 94, 0.2);
    color: #22c55e;
    border: 1px solid rgba(34, 197, 94, 0.3);
}

.btn-menu:hover {
    background: rgba(34, 197, 94, 0.3);
    border-color: rgba(34, 197, 94, 0.5);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
}

.btn-delete {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.btn-delete:hover {
    background: rgba(239, 68, 68, 0.3);
    border-color: rgba(239, 68, 68, 0.5);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 1.5rem;
    color: rgba(255, 255, 255, 0.5);
}

.empty-state-icon {
    font-size: 5rem;
    margin-bottom: 1.5rem;
    opacity: 0.4;
}

.empty-state-text {
    font-size: 1rem;
    line-height: 1.8;
}

/* Responsive */
@media (max-width: 1200px) {
    .restaurant-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }
}

@media (max-width: 768px) {
    .restaurant-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .restaurant-card {
        padding: 1.2rem;
    }
    
    .restaurant-details {
        gap: 0.8rem;
    }

    .restaurant-actions {
        flex-direction: column;
    }

    .action-btn {
        width: 100%;
    }
}

/* Pagination Style */
.pagination {
    display: flex;
    gap: 0.5rem;
    list-style: none;
    justify-content: center;
    align-items: center;
}

.page-item {
    margin: 0;
}

.page-link {
    padding: 0.6rem 1rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--card-border);
    border-radius: 8px;
    color: var(--light);
    text-decoration: none;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
}

.page-link:hover {
    background: rgba(255, 255, 255, 0.1);
    color: var(--primary);
    border-color: var(--primary);
}

.page-item.active .page-link {
    background: var(--primary);
    border-color: var(--primary);
    color: #ffffff;
}

.page-item.disabled .page-link {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
}
</style>
@endpush