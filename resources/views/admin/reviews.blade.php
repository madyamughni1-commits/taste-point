@extends('layouts.admin')

@section('title', 'Kelola Reviews - TastePoint')

@section('content')
<!-- Top Bar -->
<div class="top-bar">
    <div>
        <h1 class="page-title">
            <i class="fas fa-star"></i> Kelola Reviews
        </h1>
        <p style="color: var(--text-muted); margin-top: 0.5rem;">Manajemen review dan rating dari pengguna</p>
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
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-card-title">Total Reviews</div>
            </div>
            <div class="stat-card-icon yellow">
                <i class="fas fa-star"></i>
            </div>
        </div>
        <div class="stat-card-value">{{ $reviews->count() }}</div>
        <div class="stat-card-change positive">
            <i class="fas fa-arrow-up"></i>
            <span>Semua review</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-card-title">Rating Rata-rata</div>
            </div>
            <div class="stat-card-icon orange">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
        <div class="stat-card-value">4.5</div>
        <div class="stat-card-change positive">
            <i class="fas fa-star"></i>
            <span>dari 5 bintang</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-card-title">Review Bulan Ini</div>
            </div>
            <div class="stat-card-icon blue">
                <i class="fas fa-calendar"></i>
            </div>
        </div>
        <div class="stat-card-value">0</div>
        <div class="stat-card-change positive">
            <i class="fas fa-plus"></i>
            <span>Review baru</span>
        </div>
    </div>
</div>

<!-- Info Alert -->
<div class="alert alert-info">
    <i class="fas fa-info-circle"></i> 
    <strong>Catatan:</strong> Fitur review masih dalam pengembangan. Untuk saat ini, halaman ini menampilkan placeholder. 
    Anda dapat mengintegrasikan tabel reviews ketika sudah siap.
</div>

<!-- Reviews Table -->
<div class="content-card">
    <div class="content-card-header">
        <h2 class="content-card-title">
            <i class="fas fa-list"></i> Daftar Reviews
        </h2>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Pengguna</th>
                    <th>Restoran</th>
                    <th>Rating</th>
                    <th>Komentar</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $index => $review)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <div style="width: 30px; height: 30px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">
                                <i class="fas fa-user"></i>
                            </div>
                            <span>{{ $review->user->name ?? 'User' }}</span>
                        </div>
                    </td>
                    <td>{{ $review->restaurant->name ?? 'Restaurant' }}</td>
                    <td>
                        <span style="color: var(--warning);">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= ($review->rating ?? 4))
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                            <span style="margin-left: 0.3rem;">({{ $review->rating ?? 4 }})</span>
                        </span>
                    </td>
                    <td style="max-width: 300px;">
                        {{ Str::limit($review->comment ?? 'No comment', 50) }}
                    </td>
                    <td>{{ $review->created_at->format('d/m/Y') ?? '-' }}</td>
                    <td>
                        <form action="{{ route('admin.reviews.delete', $review->id ?? 1) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus review ini?')">
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
                        <i class="fas fa-star" style="font-size: 3rem; margin-bottom: 1rem; display: block; opacity: 0.3;"></i>
                        <h3 style="margin-bottom: 0.5rem;">Belum Ada Review</h3>
                        <p>Review dari pengguna akan muncul di sini</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Integration Guide -->
<div class="content-card">
    <h3 style="margin-bottom: 1rem; color: var(--primary);">
        <i class="fas fa-code"></i> Panduan Integrasi Review System
    </h3>
    <div style="background: rgba(255, 255, 255, 0.02); border-radius: 8px; padding: 1.5rem; border-left: 3px solid var(--primary);">
        <h4 style="margin-bottom: 1rem; color: var(--light);">Langkah-langkah untuk mengaktifkan fitur review:</h4>
        <ol style="color: var(--text-muted); line-height: 2; padding-left: 1.5rem;">
            <li>Buat migration untuk tabel <code style="background: rgba(255, 107, 0, 0.2); padding: 0.2rem 0.5rem; border-radius: 4px; color: var(--primary);">reviews</code></li>
            <li>Buat model <code style="background: rgba(255, 107, 0, 0.2); padding: 0.2rem 0.5rem; border-radius: 4px; color: var(--primary);">Review.php</code> dengan relasi ke User dan Restaurant</li>
            <li>Tambahkan form review di halaman detail restoran untuk user</li>
            <li>Update controller untuk menampilkan review yang ada di database</li>
            <li>Implementasikan validasi dan filter untuk review yang tidak pantas</li>
        </ol>
        
        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--card-border);">
            <h4 style="margin-bottom: 1rem; color: var(--light);">Contoh struktur tabel reviews:</h4>
            <pre style="background: rgba(0, 0, 0, 0.3); padding: 1rem; border-radius: 8px; overflow-x: auto; color: var(--success); font-size: 0.9rem;">
id | user_id | restaurant_id | rating (1-5) | comment | created_at | updated_at
            </pre>
        </div>
    </div>
</div>

<!-- Features Box -->
<div class="content-card">
    <h3 style="margin-bottom: 1rem; color: var(--primary);">
        <i class="fas fa-star"></i> Fitur Review System (Coming Soon)
    </h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
        <div style="padding: 1rem; background: rgba(255, 255, 255, 0.02); border-radius: 8px; border-left: 3px solid var(--success);">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                <i class="fas fa-star" style="color: var(--success);"></i>
                <strong style="color: var(--light);">Rating System</strong>
            </div>
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                Pengguna dapat memberikan rating 1-5 bintang untuk setiap restoran
            </p>
        </div>

        <div style="padding: 1rem; background: rgba(255, 255, 255, 0.02); border-radius: 8px; border-left: 3px solid var(--info);">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                <i class="fas fa-comment" style="color: var(--info);"></i>
                <strong style="color: var(--light);">Komentar</strong>
            </div>
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                Pengguna dapat menambahkan komentar detail tentang pengalaman mereka
            </p>
        </div>

        <div style="padding: 1rem; background: rgba(255, 255, 255, 0.02); border-radius: 8px; border-left: 3px solid var(--warning);">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                <i class="fas fa-shield-alt" style="color: var(--warning);"></i>
                <strong style="color: var(--light);">Moderasi</strong>
            </div>
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                Admin dapat memoderasi dan menghapus review yang tidak pantas
            </p>
        </div>

        <div style="padding: 1rem; background: rgba(255, 255, 255, 0.02); border-radius: 8px; border-left: 3px solid var(--danger);">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                <i class="fas fa-flag" style="color: var(--danger);"></i>
                <strong style="color: var(--light);">Report System</strong>
            </div>
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                Pengguna dapat melaporkan review yang bermasalah untuk ditinjau admin
            </p>
        </div>
    </div>
</div>
@endsection