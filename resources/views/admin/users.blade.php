@extends('layouts.admin')

@section('title', 'Kelola Pengguna - TastePoint')

@section('content')
<!-- Top Bar -->
<div class="top-bar">
    <div>
        <h1 class="page-title">Kelola Pengguna</h1>
        <p style="color: var(--text-muted); margin-top: 0.5rem;">Lihat dan kelola pengguna yang terdaftar</p>
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
        <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Total Pengguna</div>
            <div class="stat-value">{{ $users->total() }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <i class="fas fa-user-shield"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Admin</div>
            <div class="stat-value">{{ $users->where('role', 'admin')->count() }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <i class="fas fa-user"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">User Biasa</div>
            <div class="stat-value">{{ $users->where('role', 'user')->count() }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
            <i class="fas fa-user-plus"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Pendaftar Baru (7 Hari)</div>
            <div class="stat-value">{{ $users->where('created_at', '>=', now()->subDays(7))->count() }}</div>
        </div>
    </div>
</div>

<!-- Search & Filter -->
<div class="content-card" style="margin-bottom: 2rem;">
    <form action="{{ route('admin.users') }}" method="GET">
        <div style="display: flex; gap: 1rem; align-items: center;">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Cari nama atau email..."
                   style="flex: 1; padding: 0.8rem 1rem; background: rgba(255, 255, 255, 0.1); border: 1px solid var(--card-border); border-radius: 10px; color: var(--light);">
            
            <select name="role" 
                    style="padding: 0.8rem 1rem; background: rgba(255, 255, 255, 0.1); border: 1px solid var(--card-border); border-radius: 10px; color: var(--light);">
                <option value="">Semua Role</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
            </select>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Cari
            </button>
            
            @if(request('search') || request('role'))
            <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Reset
            </a>
            @endif
        </div>
    </form>
</div>

<!-- Users Table -->
<div class="content-card">
    <div class="content-card-header">
        <h2 class="content-card-title">
            <i class="fas fa-users"></i> Daftar Pengguna
        </h2>
    </div>

    @if($users->count() > 0)
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th style="text-align: center;">Role</th>
                    <th>Tanggal Daftar</th>
                    <th>Login Terakhir</th>
                    <th style="text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $index => $user)
                <tr>
                    <td>{{ $users->firstItem() + $index }}</td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <strong>{{ $user->name }}</strong>
                        </div>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td style="text-align: center;">
                        @if($user->role === 'admin')
                        <span class="badge" style="background: rgba(255, 107, 0, 0.2); color: var(--primary);">
                            <i class="fas fa-shield-alt"></i> Admin
                        </span>
                        @else
                        <span class="badge" style="background: rgba(59, 130, 246, 0.2); color: #3b82f6;">
                            <i class="fas fa-user"></i> User
                        </span>
                        @endif
                    </td>
                    <td>
                        <div>{{ $user->created_at->format('d M Y') }}</div>
                        <small style="color: var(--text-muted);">{{ $user->created_at->format('H:i') }}</small>
                    </td>
                    <td>
                        @if($user->last_login_at)
                        <div>{{ $user->last_login_at->format('d M Y') }}</div>
                        <small style="color: var(--text-muted);">{{ $user->last_login_at->diffForHumans() }}</small>
                        @else
                        <span style="color: var(--text-muted);">Belum pernah login</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if($user->created_at >= now()->subDays(7))
                        <span class="badge badge-success">
                            <i class="fas fa-star"></i> Baru
                        </span>
                        @else
                        <span class="badge" style="background: rgba(107, 114, 128, 0.2); color: #6b7280;">
                            Aktif
                        </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
        {{ $users->links() }}
    </div>
    @else
    <div style="text-align: center; padding: 3rem; color: var(--text-muted);">
        <i class="fas fa-users" style="font-size: 4rem; margin-bottom: 1rem;"></i>
        <h3>Belum Ada Pengguna</h3>
        <p style="margin: 1rem 0;">Belum ada pengguna yang terdaftar</p>
    </div>
    @endif
</div>

<!-- Activity Timeline -->
<div class="content-card" style="margin-top: 2rem;">
    <div class="content-card-header">
        <h2 class="content-card-title">
            <i class="fas fa-history"></i> Aktivitas Terbaru
        </h2>
    </div>

    <div style="padding: 1rem 0;">
        @foreach($users->take(5) as $user)
        <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; border-bottom: 1px solid var(--card-border);">
            <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div style="flex: 1;">
                <div style="font-weight: 600;">{{ $user->name }}</div>
                <small style="color: var(--text-muted);">
                    <i class="fas fa-user-plus"></i> Mendaftar {{ $user->created_at->diffForHumans() }}
                </small>
            </div>
            <div>
                @if($user->role === 'admin')
                <span class="badge" style="background: rgba(255, 107, 0, 0.2); color: var(--primary);">Admin</span>
                @else
                <span class="badge" style="background: rgba(59, 130, 246, 0.2); color: #3b82f6;">User</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@push('styles')
<style>
.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table thead {
    background: rgba(255, 255, 255, 0.05);
}

.data-table th {
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    border-bottom: 2px solid var(--card-border);
}

.data-table td {
    padding: 1rem;
    border-bottom: 1px solid var(--card-border);
}

.data-table tbody tr {
    transition: var(--transition);
}

.data-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.05);
}

.badge {
    display: inline-block;
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 600;
}

.badge-success {
    background: rgba(16, 185, 129, 0.2);
    color: #10b981;
}

/* Responsive */
@media (max-width: 768px) {
    .data-table {
        font-size: 0.85rem;
    }

    .data-table th, .data-table td {
        padding: 0.5rem;
    }

    .stats-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>
@endpush