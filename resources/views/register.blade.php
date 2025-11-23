@extends('layouts.app')

@section('title', 'Daftar - TastePoint')

@section('content')
<!-- Loading Screen -->
<div class="loading-screen" id="loadingScreen">
    <div class="loading-logo animate__animated animate__pulse">
        <i class="fas fa-utensils me-2"></i>TASTEPOINT
    </div>
    <div class="loading-bar">
        <div class="loading-progress"></div>
    </div>
</div>

<!-- Register Screen -->
<div class="login-screen" id="registerScreen" style="display: none;">
    <div class="login-container animate__animated animate__fadeInUp">
        <div class="login-logo">
            <i class="fas fa-utensils"></i>
            <span>TastePoint</span>
        </div>
        
        <h2>Daftar Akun Baru</h2>
        <p>Buat akun untuk mulai menjelajah</p>
        
        <!-- Alert Messages -->
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form class="login-form" method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" class="form-input" name="name" 
                       placeholder="Masukkan nama lengkap Anda" value="{{ old('name') }}" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" class="form-input" name="email" 
                       placeholder="Masukkan email Anda" value="{{ old('email') }}" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Kata Sandi</label>
                <input type="password" class="form-input" name="password" 
                       placeholder="Minimal 6 karakter" required>
                <small style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.3rem; display: block;">
                    <i class="fas fa-info-circle"></i> Minimal 6 karakter
                </small>
            </div>
            
            <div class="form-group">
                <label class="form-label">Konfirmasi Kata Sandi</label>
                <input type="password" class="form-input" name="password_confirmation" 
                       placeholder="Ulangi kata sandi" required>
            </div>
            
            <button type="submit" class="submit-btn">
                <i class="fas fa-user-plus"></i>Daftar Sekarang
            </button>
        </form>

        <div class="login-footer">
            <p>Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hide loading screen after 2 seconds
    setTimeout(() => {
        document.getElementById('loadingScreen').style.opacity = '0';
        setTimeout(() => {
            document.getElementById('loadingScreen').style.display = 'none';
            document.getElementById('registerScreen').style.display = 'flex';
        }, 500);
    }, 2000);
});
</script>
@endpush