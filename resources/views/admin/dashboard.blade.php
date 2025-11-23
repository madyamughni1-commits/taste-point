@extends('layouts.admin')

@section('title', 'Dashboard Admin - TastePoint')

@section('content')
<!-- Top Bar -->
<div class="top-bar">
    <div>
        <h1 class="page-title">Dashboard Admin</h1>
        <p style="color: var(--text-muted); margin-top: 0.5rem;">Selamat datang kembali, {{ Auth::user()->name }}!</p>
    </div>
    <div class="top-bar-actions">
        <div class="user-info">
            <div class="user-avatar">
                <i class="fas fa-user"></i>
            </div>
            <span>{{ Auth::user()->name }}</span>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
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

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-card-title">Total Restoran</div>
            </div>
            <div class="stat-card-icon orange">
                <i class="fas fa-store"></i>
            </div>
        </div>
        <div class="stat-card-value">{{ $stats['total_restaurants'] }}</div>
        <div class="stat-card-change positive">
            <i class="fas fa-arrow-up"></i>
            <span>12% dari bulan lalu</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-card-title">Total Menu</div>
            </div>
            <div class="stat-card-icon green">
                <i class="fas fa-utensils"></i>
            </div>
        </div>
        <div class="stat-card-value">{{ $stats['total_menus'] }}</div>
        <div class="stat-card-change positive">
            <i class="fas fa-arrow-up"></i>
            <span>8% dari bulan lalu</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-card-title">Total Pengguna</div>
            </div>
            <div class="stat-card-icon blue">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="stat-card-value">{{ $stats['total_users'] }}</div>
        <div class="stat-card-change positive">
            <i class="fas fa-arrow-up"></i>
            <span>24% dari bulan lalu</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-card-title">Rata-rata Rating</div>
            </div>
            <div class="stat-card-icon yellow">
                <i class="fas fa-star"></i>
            </div>
        </div>
        <div class="stat-card-value">{{ $stats['avg_rating'] }}</div>
        <div class="stat-card-change positive">
            <i class="fas fa-arrow-up"></i>
            <span>0.3 dari bulan lalu</span>
        </div>
    </div>
</div>

<!-- Recent Restaurants with Grid Card -->
<div class="content-card" style="margin-top: 2rem;">
    <div class="content-card-header">
        <h2 class="content-card-title">
            <i class="fas fa-store"></i> Restoran Terbaru
        </h2>
        <a href="{{ route('admin.restaurants') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-eye"></i> Lihat Semua
        </a>
    </div>

    @if($recent_restaurants && $recent_restaurants->count() > 0)
        <div class="restaurant-grid">
            @foreach($recent_restaurants as $index => $restaurant)
                <div class="restaurant-card">
                    <!-- Header dengan nomor dan status -->
                    <div class="restaurant-header">
                        <div class="restaurant-number">{{ $index + 1 }}</div>
                        <span class="restaurant-status status-aktif">
                            <i class="fas fa-check-circle"></i> Aktif
                        </span>
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
                        {{ Str::limit($restaurant->address, 50) }}
                    </div>

                    <!-- Detail Grid -->
                    <div class="restaurant-details">
                        <!-- Range Harga -->
                        <div class="detail-item">
                            <span class="detail-label">Range Harga</span>
                            <span class="detail-value price-range">
                                Rp {{ number_format($restaurant->min_price, 0, ',', '.') }} - 
                                {{ number_format($restaurant->max_price, 0, ',', '.') }}
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
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <div class="empty-state-icon">🏪</div>
            <p class="empty-state-text">Belum ada restoran. <a href="{{ route('admin.restaurants.create') }}" style="color: var(--primary);">Tambahkan restoran pertama</a></p>
        </div>
    @endif
</div>

<!-- Quick Actions - SPACING DITAMBAHKAN -->
<div class="content-card" style="margin-top: 3rem;">
    <div class="content-card-header">
        <h2 class="content-card-title">
            <i class="fas fa-bolt"></i> Aksi Cepat
        </h2>
    </div>

    <div class="quick-actions-grid">
        <a href="{{ route('admin.restaurants.create') }}" class="quick-action-btn btn-primary-action">
            <div class="action-icon">
                <i class="fas fa-plus"></i>
            </div>
            <div class="action-content">
                <h4>Tambah Restoran</h4>
                <p>Tambahkan restoran baru ke database</p>
            </div>
        </a>

        <a href="{{ route('admin.bulk-menu-upload') }}" class="quick-action-btn btn-success-action">
            <div class="action-icon">
                <i class="fas fa-cloud-upload-alt"></i>
            </div>
            <div class="action-content">
                <h4>Bulk Upload Menu</h4>
                <p>Upload menu dari foto dengan AI</p>
            </div>
        </a>

        <a href="{{ route('admin.restaurants') }}" class="quick-action-btn btn-secondary-action">
            <div class="action-icon">
                <i class="fas fa-store"></i>
            </div>
            <div class="action-content">
                <h4>Kelola Restoran</h4>
                <p>Lihat dan edit semua restoran</p>
            </div>
        </a>

        <a href="{{ route('admin.activity-logs') }}" class="quick-action-btn btn-info-action">
            <div class="action-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div class="action-content">
                <h4>Lihat Laporan</h4>
                <p>Log aktivitas dan statistik</p>
            </div>
        </a>
    </div>
</div>

<!-- Activity Log - SPACING DITAMBAHKAN -->
<div class="content-card" style="margin-top: 3rem;">
    <div class="content-card-header">
        <h2 class="content-card-title">
            <i class="fas fa-history"></i> Aktivitas Terbaru
        </h2>
        <a href="{{ route('admin.activity-logs') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-eye"></i> Lihat Semua
        </a>
    </div>

    <div class="activity-timeline">
        @forelse($recent_activities ?? [] as $activity)
        <div class="activity-item">
            <div class="activity-icon 
                @if($activity->action == 'create') icon-create
                @elseif($activity->action == 'update') icon-update
                @elseif($activity->action == 'delete') icon-delete
                @else icon-info
                @endif">
                @if($activity->action == 'create')
                    <i class="fas fa-plus"></i>
                @elseif($activity->action == 'update')
                    <i class="fas fa-edit"></i>
                @elseif($activity->action == 'delete')
                    <i class="fas fa-trash"></i>
                @else
                    <i class="fas fa-info-circle"></i>
                @endif
            </div>
            <div class="activity-content">
                <div class="activity-header">
                    <strong>{{ $activity->description }}</strong>
                    <span class="activity-time">{{ $activity->created_at->diffForHumans() }}</span>
                </div>
                <div class="activity-meta">
                    <span class="activity-user">
                        <i class="fas fa-user"></i> {{ $activity->user->name ?? 'Unknown' }}
                    </span>
                    <span class="activity-separator">•</span>
                    <span class="activity-date">
                        <i class="fas fa-clock"></i> {{ $activity->created_at->format('d/m/Y H:i') }}
                    </span>
                </div>
            </div>
        </div>
        @empty
        <div class="empty-activity">
            <i class="fas fa-history"></i>
            <p>Belum ada aktivitas tercatat</p>
        </div>
        @endforelse
    </div>
</div>
@endsection

@push('styles')
<style>
/* ===== GRID CARD STYLE untuk Restoran Terbaru ===== */

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

.restaurant-location {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.9rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    line-height: 1.4;
}

.restaurant-location::before {
    content: '📍';
    font-size: 1rem;
}

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
    font-size: 0.85rem;
    line-height: 1.3;
}

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

.empty-state {
    text-align: center;
    padding: 3rem 1.5rem;
    color: rgba(255, 255, 255, 0.5);
}

.empty-state-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.4;
}

.empty-state-text {
    font-size: 1rem;
    line-height: 1.6;
}

/* ===== QUICK ACTIONS GRID ===== */

.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.quick-action-btn {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.quick-action-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.quick-action-btn:hover::before {
    opacity: 1;
}

.quick-action-btn:hover {
    transform: translateY(-5px);
}

.btn-primary-action {
    background: rgba(255, 107, 0, 0.15);
    border: 1px solid rgba(255, 107, 0, 0.3);
}

.btn-primary-action::before {
    background: linear-gradient(90deg, #ff8800 0%, #ffaa33 100%);
}

.btn-primary-action:hover {
    box-shadow: 0 8px 20px rgba(255, 107, 0, 0.3);
    border-color: rgba(255, 107, 0, 0.5);
}

.btn-success-action {
    background: rgba(34, 197, 94, 0.15);
    border: 1px solid rgba(34, 197, 94, 0.3);
}

.btn-success-action::before {
    background: linear-gradient(90deg, #22c55e 0%, #10b981 100%);
}

.btn-success-action:hover {
    box-shadow: 0 8px 20px rgba(34, 197, 94, 0.3);
    border-color: rgba(34, 197, 94, 0.5);
}

.btn-secondary-action {
    background: rgba(107, 114, 128, 0.15);
    border: 1px solid rgba(107, 114, 128, 0.3);
}

.btn-secondary-action::before {
    background: linear-gradient(90deg, #6b7280 0%, #4b5563 100%);
}

.btn-secondary-action:hover {
    box-shadow: 0 8px 20px rgba(107, 114, 128, 0.3);
    border-color: rgba(107, 114, 128, 0.5);
}

.btn-info-action {
    background: rgba(59, 130, 246, 0.15);
    border: 1px solid rgba(59, 130, 246, 0.3);
}

.btn-info-action::before {
    background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%);
}

.btn-info-action:hover {
    box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
    border-color: rgba(59, 130, 246, 0.5);
}

.action-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.btn-primary-action .action-icon {
    background: rgba(255, 107, 0, 0.2);
    color: #ff8800;
}

.btn-success-action .action-icon {
    background: rgba(34, 197, 94, 0.2);
    color: #22c55e;
}

.btn-secondary-action .action-icon {
    background: rgba(107, 114, 128, 0.2);
    color: #9ca3af;
}

.btn-info-action .action-icon {
    background: rgba(59, 130, 246, 0.2);
    color: #3b82f6;
}

.action-content h4 {
    color: var(--light);
    margin: 0 0 0.3rem 0;
    font-size: 1.1rem;
}

.action-content p {
    color: var(--text-muted);
    margin: 0;
    font-size: 0.9rem;
}

/* ===== ACTIVITY TIMELINE ===== */

.activity-timeline {
    margin-top: 1.5rem;
}

.activity-item {
    display: flex;
    gap: 1rem;
    padding: 1.2rem;
    background: rgba(255, 255, 255, 0.02);
    border-radius: 10px;
    margin-bottom: 1rem;
    border-left: 3px solid transparent;
    transition: all 0.3s ease;
}

.activity-item:hover {
    background: rgba(255, 255, 255, 0.05);
    transform: translateX(5px);
}

.activity-icon {
    width: 45px;
    height: 45px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.icon-create {
    background: rgba(255, 107, 0, 0.2);
    color: #ff8800;
}

.activity-item:has(.icon-create) {
    border-left-color: #ff8800;
}

.icon-update {
    background: rgba(34, 197, 94, 0.2);
    color: #22c55e;
}

.activity-item:has(.icon-update) {
    border-left-color: #22c55e;
}

.icon-delete {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
}

.activity-item:has(.icon-delete) {
    border-left-color: #ef4444;
}

.icon-info {
    background: rgba(59, 130, 246, 0.2);
    color: #3b82f6;
}

.activity-item:has(.icon-info) {
    border-left-color: #3b82f6;
}

.activity-content {
    flex: 1;
}

.activity-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 0.5rem;
    gap: 1rem;
}

.activity-header strong {
    color: var(--light);
    font-size: 1rem;
}

.activity-time {
    color: var(--text-muted);
    font-size: 0.85rem;
    flex-shrink: 0;
}

.activity-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-muted);
    font-size: 0.9rem;
}

.activity-user,
.activity-date {
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.activity-separator {
    color: rgba(255, 255, 255, 0.3);
}

.empty-activity {
    text-align: center;
    padding: 3rem;
    color: var(--text-muted);
}

.empty-activity i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.3;
    display: block;
}

/* Responsive */
@media (max-width: 1024px) {
    .restaurant-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }

    .quick-actions-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
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
        grid-template-columns: 1fr;
        gap: 0.8rem;
    }

    .restaurant-actions {
        flex-direction: column;
    }

    .action-btn {
        width: 100%;
    }

    .quick-actions-grid {
        grid-template-columns: 1fr;
    }

    .activity-item {
        flex-direction: column;
        align-items: start;
    }

    .activity-header {
        flex-direction: column;
        gap: 0.5rem;
    }
}
</style>
@endpush