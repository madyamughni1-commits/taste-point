@extends('layouts.admin')

@section('title', 'Pengaturan - TastePoint')

@section('content')
<!-- Top Bar -->
<div class="top-bar">
    <div>
        <h1 class="page-title">
            <i class="fas fa-cog"></i> Pengaturan Akun
        </h1>
        <p style="color: var(--text-muted); margin-top: 0.5rem;">Kelola profil dan keamanan akun admin Anda</p>
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

@if($errors->any())
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <ul style="margin: 0; padding-left: 1.5rem;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Profile Settings -->
<div class="content-card">
    <div class="content-card-header">
        <h2 class="content-card-title">
            <i class="fas fa-user"></i> Informasi Profil
        </h2>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div style="display: grid; gap: 1.5rem;">
            <!-- Name -->
            <div>
                <label style="display: block; margin-bottom: 0.5rem; color: var(--light); font-weight: 500;">
                    <i class="fas fa-user"></i> Nama Lengkap
                </label>
                <input 
                    type="text" 
                    name="name" 
                    value="{{ old('name', $user->name) }}"
                    required
                    style="width: 100%; padding: 0.8rem; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--card-border); border-radius: 8px; color: var(--light); font-size: 1rem;"
                    placeholder="Masukkan nama lengkap"
                >
            </div>

            <!-- Email -->
            <div>
                <label style="display: block; margin-bottom: 0.5rem; color: var(--light); font-weight: 500;">
                    <i class="fas fa-envelope"></i> Email
                </label>
                <input 
                    type="email" 
                    name="email" 
                    value="{{ old('email', $user->email) }}"
                    required
                    style="width: 100%; padding: 0.8rem; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--card-border); border-radius: 8px; color: var(--light); font-size: 1rem;"
                    placeholder="Masukkan email"
                >
            </div>

            <!-- Role (Read Only) -->
            <div>
                <label style="display: block; margin-bottom: 0.5rem; color: var(--light); font-weight: 500;">
                    <i class="fas fa-shield-alt"></i> Role
                </label>
                <input 
                    type="text" 
                    value="{{ ucfirst($user->role) }}"
                    readonly
                    style="width: 100%; padding: 0.8rem; background: rgba(255, 255, 255, 0.02); border: 1px solid var(--card-border); border-radius: 8px; color: var(--text-muted); font-size: 1rem; cursor: not-allowed;"
                >
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.5rem;">
                    <i class="fas fa-info-circle"></i> Role tidak dapat diubah sendiri
                </p>
            </div>

            <button type="submit" class="btn btn-primary" style="width: fit-content;">
                <i class="fas fa-save"></i> Simpan Perubahan Profil
            </button>
        </div>
    </form>
</div>

<!-- Security Settings -->
<div class="content-card">
    <div class="content-card-header">
        <h2 class="content-card-title">
            <i class="fas fa-lock"></i> Keamanan
        </h2>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Keep current profile data -->
        <input type="hidden" name="name" value="{{ $user->name }}">
        <input type="hidden" name="email" value="{{ $user->email }}">

        <div style="display: grid; gap: 1.5rem;">
            <!-- Current Password -->
            <div>
                <label style="display: block; margin-bottom: 0.5rem; color: var(--light); font-weight: 500;">
                    <i class="fas fa-key"></i> Password Saat Ini
                </label>
                <input 
                    type="password" 
                    name="current_password"
                    style="width: 100%; padding: 0.8rem; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--card-border); border-radius: 8px; color: var(--light); font-size: 1rem;"
                    placeholder="Masukkan password saat ini"
                >
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.5rem;">
                    <i class="fas fa-info-circle"></i> Wajib diisi jika ingin mengubah password
                </p>
            </div>

            <!-- New Password -->
            <div>
                <label style="display: block; margin-bottom: 0.5rem; color: var(--light); font-weight: 500;">
                    <i class="fas fa-lock"></i> Password Baru
                </label>
                <input 
                    type="password" 
                    name="new_password"
                    style="width: 100%; padding: 0.8rem; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--card-border); border-radius: 8px; color: var(--light); font-size: 1rem;"
                    placeholder="Masukkan password baru (minimal 8 karakter)"
                >
            </div>

            <!-- Confirm New Password -->
            <div>
                <label style="display: block; margin-bottom: 0.5rem; color: var(--light); font-weight: 500;">
                    <i class="fas fa-lock"></i> Konfirmasi Password Baru
                </label>
                <input 
                    type="password" 
                    name="new_password_confirmation"
                    style="width: 100%; padding: 0.8rem; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--card-border); border-radius: 8px; color: var(--light); font-size: 1rem;"
                    placeholder="Masukkan ulang password baru"
                >
            </div>

            <button type="submit" class="btn btn-primary" style="width: fit-content;">
                <i class="fas fa-save"></i> Update Password
            </button>
        </div>
    </form>
</div>

<!-- Account Info -->
<div class="content-card">
    <div class="content-card-header">
        <h2 class="content-card-title">
            <i class="fas fa-info-circle"></i> Informasi Akun
        </h2>
    </div>

    <div style="display: grid; gap: 1rem;">
        <div style="display: flex; justify-content: space-between; padding: 1rem; background: rgba(255, 255, 255, 0.02); border-radius: 8px;">
            <span style="color: var(--text-muted);">
                <i class="fas fa-calendar-plus"></i> Tanggal Bergabung
            </span>
            <span style="color: var(--light); font-weight: 500;">
                {{ $user->created_at->format('d F Y') }}
            </span>
        </div>

        <div style="display: flex; justify-content: space-between; padding: 1rem; background: rgba(255, 255, 255, 0.02); border-radius: 8px;">
            <span style="color: var(--text-muted);">
                <i class="fas fa-clock"></i> Terakhir Diperbarui
            </span>
            <span style="color: var(--light); font-weight: 500;">
                {{ $user->updated_at->format('d F Y, H:i') }}
            </span>
        </div>

        <div style="display: flex; justify-content: space-between; padding: 1rem; background: rgba(255, 255, 255, 0.02); border-radius: 8px;">
            <span style="color: var(--text-muted);">
                <i class="fas fa-user-tag"></i> Status Akun
            </span>
            <span style="background: rgba(16, 185, 129, 0.2); color: var(--success); padding: 0.3rem 0.8rem; border-radius: 6px; font-size: 0.85rem; font-weight: 500;">
                <i class="fas fa-check-circle"></i> Aktif
            </span>
        </div>
    </div>
</div>

<!-- Security Tips -->
<div class="content-card">
    <h3 style="margin-bottom: 1rem; color: var(--primary);">
        <i class="fas fa-shield-alt"></i> Tips Keamanan
    </h3>
    <div style="display: grid; gap: 1rem;">
        <div style="padding: 1rem; background: rgba(255, 255, 255, 0.02); border-radius: 8px; border-left: 3px solid var(--success);">
            <strong style="color: var(--light); display: block; margin-bottom: 0.5rem;">
                <i class="fas fa-check"></i> Password yang Kuat
            </strong>
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol. Minimal 8 karakter.
            </p>
        </div>

        <div style="padding: 1rem; background: rgba(255, 255, 255, 0.02); border-radius: 8px; border-left: 3px solid var(--info);">
            <strong style="color: var(--light); display: block; margin-bottom: 0.5rem;">
                <i class="fas fa-key"></i> Jangan Bagikan Password
            </strong>
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                Jangan pernah membagikan password Anda kepada siapapun, termasuk sesama admin.
            </p>
        </div>

        <div style="padding: 1rem; background: rgba(255, 255, 255, 0.02); border-radius: 8px; border-left: 3px solid var(--warning);">
            <strong style="color: var(--light); display: block; margin-bottom: 0.5rem;">
                <i class="fas fa-sync"></i> Update Berkala
            </strong>
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                Ubah password Anda secara berkala, minimal 3 bulan sekali untuk keamanan optimal.
            </p>
        </div>

        <div style="padding: 1rem; background: rgba(255, 255, 255, 0.02); border-radius: 8px; border-left: 3px solid var(--danger);">
            <strong style="color: var(--light); display: block; margin-bottom: 0.5rem;">
                <i class="fas fa-sign-out-alt"></i> Logout Setelah Selesai
            </strong>
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                Selalu logout setelah selesai menggunakan sistem, terutama di komputer umum.
            </p>
        </div>
    </div>
</div>

<!-- System Info -->
<div class="content-card">
    <div class="content-card-header">
        <h2 class="content-card-title">
            <i class="fas fa-server"></i> Informasi Sistem
        </h2>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <div style="text-align: center; padding: 1.5rem; background: rgba(255, 107, 0, 0.1); border-radius: 8px;">
            <div style="font-size: 2rem; color: var(--primary); margin-bottom: 0.5rem;">
                <i class="fas fa-code"></i>
            </div>
            <div style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.3rem;">
                Laravel Version
            </div>
            <div style="color: var(--light); font-weight: 600; font-size: 1.1rem;">
                {{ app()->version() }}
            </div>
        </div>

        <div style="text-align: center; padding: 1.5rem; background: rgba(16, 185, 129, 0.1); border-radius: 8px;">
            <div style="font-size: 2rem; color: var(--success); margin-bottom: 0.5rem;">
                <i class="fab fa-php"></i>
            </div>
            <div style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.3rem;">
                PHP Version
            </div>
            <div style="color: var(--light); font-weight: 600; font-size: 1.1rem;">
                {{ PHP_VERSION }}
            </div>
        </div>

        <div style="text-align: center; padding: 1.5rem; background: rgba(59, 130, 246, 0.1); border-radius: 8px;">
            <div style="font-size: 2rem; color: var(--info); margin-bottom: 0.5rem;">
                <i class="fas fa-server"></i>
            </div>
            <div style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.3rem;">
                Environment
            </div>
            <div style="color: var(--light); font-weight: 600; font-size: 1.1rem;">
                {{ config('app.env') }}
            </div>
        </div>
    </div>
</div>
@endsection