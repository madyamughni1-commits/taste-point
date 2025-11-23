@extends('layouts.admin')

@section('title', 'Laporan Aktivitas - TastePoint')

@section('content')
<!-- Top Bar -->
<div class="top-bar">
    <div>
        <h1 class="page-title">
            <i class="fas fa-chart-bar"></i> Laporan Aktivitas Admin
        </h1>
        <p style="color: var(--text-muted); margin-top: 0.5rem;">Tracking aktivitas admin dengan detail tanggal dan jam</p>
    </div>
    <div class="top-bar-actions">
        <form action="{{ route('admin.activity-logs.clear') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus semua log aktivitas?')">
            @csrf
            <button type="submit" class="btn btn-danger btn-sm">
                <i class="fas fa-trash"></i> Hapus Semua Log
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

<!-- Filter Section -->
<div class="content-card">
    <form method="GET" action="{{ route('admin.activity-logs') }}">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
            <div>
                <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.9rem;">
                    <i class="fas fa-filter"></i> Filter Aksi
                </label>
                <select name="action" style="width: 100%; padding: 0.7rem; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--card-border); border-radius: 8px; color: var(--light);">
                    <option value="">Semua Aksi</option>
                    <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Create</option>
                    <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Update</option>
                    <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Delete</option>
                    <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login</option>
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.9rem;">
                    <i class="fas fa-calendar"></i> Filter Tanggal
                </label>
                <input type="date" name="date" value="{{ request('date') }}" style="width: 100%; padding: 0.7rem; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--card-border); border-radius: 8px; color: var(--light);">
            </div>

            <div>
                <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.9rem;">
                    <i class="fas fa-user"></i> Filter Admin
                </label>
                <select name="user" style="width: 100%; padding: 0.7rem; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--card-border); border-radius: 8px; color: var(--light);">
                    <option value="">Semua Admin</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; align-items: flex-end;">
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-search"></i> Filter
                </button>
            </div>
        </div>

        @if(request()->hasAny(['action', 'date', 'user']))
        <div style="margin-top: 1rem;">
            <a href="{{ route('admin.activity-logs') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-times"></i> Reset Filter
            </a>
        </div>
        @endif
    </form>
</div>

<!-- Activity Logs Table -->
<div class="content-card">
    <div class="content-card-header">
        <h2 class="content-card-title">
            <i class="fas fa-list"></i> Daftar Log Aktivitas
        </h2>
        <span style="color: var(--text-muted); font-size: 0.9rem;">
            Total: {{ $logs->total() }} aktivitas
        </span>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tanggal & Jam</th>
                    <th>Admin</th>
                    <th>Aksi</th>
                    <th>Deskripsi</th>
                    <th>IP Address</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $index => $log)
                <tr>
                    <td>{{ $logs->firstItem() + $index }}</td>
                    <td>
                        <div style="display: flex; flex-direction: column; gap: 0.2rem;">
                            <span style="color: var(--light);">
                                <i class="fas fa-calendar"></i> {{ $log->created_at->format('d/m/Y') }}
                            </span>
                            <span style="color: var(--text-muted); font-size: 0.85rem;">
                                <i class="fas fa-clock"></i> {{ $log->created_at->format('H:i:s') }}
                            </span>
                            <span style="color: var(--text-muted); font-size: 0.8rem;">
                                ({{ $log->created_at->diffForHumans() }})
                            </span>
                        </div>
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <div style="width: 30px; height: 30px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">
                                <i class="fas fa-user"></i>
                            </div>
                            <span>{{ $log->user->name ?? 'Deleted User' }}</span>
                        </div>
                    </td>
                    <td>
                        @if($log->action == 'create')
                            <span style="background: rgba(255, 107, 0, 0.2); color: var(--primary); padding: 0.3rem 0.8rem; border-radius: 6px; font-size: 0.85rem;">
                                <i class="fas fa-plus"></i> Create
                            </span>
                        @elseif($log->action == 'update')
                            <span style="background: rgba(16, 185, 129, 0.2); color: var(--success); padding: 0.3rem 0.8rem; border-radius: 6px; font-size: 0.85rem;">
                                <i class="fas fa-edit"></i> Update
                            </span>
                        @elseif($log->action == 'delete')
                            <span style="background: rgba(239, 68, 68, 0.2); color: var(--danger); padding: 0.3rem 0.8rem; border-radius: 6px; font-size: 0.85rem;">
                                <i class="fas fa-trash"></i> Delete
                            </span>
                        @else
                            <span style="background: rgba(59, 130, 246, 0.2); color: var(--info); padding: 0.3rem 0.8rem; border-radius: 6px; font-size: 0.85rem;">
                                <i class="fas fa-info-circle"></i> {{ ucfirst($log->action) }}
                            </span>
                        @endif
                    </td>
                    <td style="max-width: 300px;">{{ $log->description }}</td>
                    <td>
                        <span style="font-family: monospace; color: var(--text-muted);">
                            {{ $log->ip_address ?? '-' }}
                        </span>
                    </td>
                    <td>
                        <form action="{{ route('admin.activity-logs.delete', $log->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus log ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" style="padding: 0.4rem 0.8rem;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 3rem;">
                        <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; display: block; opacity: 0.3;"></i>
                        <h3 style="margin-bottom: 0.5rem;">Belum Ada Log Aktivitas</h3>
                        <p>Log aktivitas admin akan muncul di sini</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($logs->hasPages())
    <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
        {{ $logs->links() }}
    </div>
    @endif
</div>

<!-- Info Box -->
<div class="content-card">
    <h3 style="margin-bottom: 1rem; color: var(--primary);">
        <i class="fas fa-info-circle"></i> Informasi Log Aktivitas
    </h3>
    <div style="display: grid; gap: 1rem;">
        <div style="padding: 1rem; background: rgba(255, 255, 255, 0.02); border-radius: 8px; border-left: 3px solid var(--primary);">
            <strong style="color: var(--light); display: block; margin-bottom: 0.5rem;">
                <i class="fas fa-shield-alt"></i> Keamanan
            </strong>
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                Setiap aktivitas admin dicatat dengan detail termasuk IP address dan user agent untuk keamanan sistem.
            </p>
        </div>
        <div style="padding: 1rem; background: rgba(255, 255, 255, 0.02); border-radius: 8px; border-left: 3px solid var(--success);">
            <strong style="color: var(--light); display: block; margin-bottom: 0.5rem;">
                <i class="fas fa-history"></i> Tracking
            </strong>
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                Log aktivitas mencatat semua perubahan data termasuk data lama dan data baru untuk audit trail.
            </p>
        </div>
        <div style="padding: 1rem; background: rgba(255, 255, 255, 0.02); border-radius: 8px; border-left: 3px solid var(--info);">
            <strong style="color: var(--light); display: block; margin-bottom: 0.5rem;">
                <i class="fas fa-filter"></i> Filter
            </strong>
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                Gunakan filter di atas untuk mencari aktivitas berdasarkan tanggal, jenis aksi, atau admin tertentu.
            </p>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Custom pagination style */
    .pagination {
        display: flex;
        gap: 0.5rem;
        list-style: none;
    }
    
    .page-link {
        padding: 0.5rem 1rem;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--card-border);
        border-radius: 8px;
        color: var(--light);
        text-decoration: none;
        transition: var(--transition);
    }
    
    .page-link:hover {
        background: rgba(255, 255, 255, 0.1);
        color: var(--primary);
    }
    
    .page-item.active .page-link {
        background: var(--primary);
        border-color: var(--primary);
    }
    
    .page-item.disabled .page-link {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>
@endpush