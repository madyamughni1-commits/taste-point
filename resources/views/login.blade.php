@extends('layouts.app')

@section('title', 'Login - TastePoint')

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

<!-- Login Screen -->
<div class="login-screen" id="loginScreen" style="display: none;">
    <div class="login-container animate__animated animate__fadeInUp">
        <div class="login-logo">
            <i class="fas fa-utensils"></i>
            <span>TastePoint</span>
        </div>
        
        <h2>Selamat Datang</h2>
        <p>Silakan masuk untuk melanjutkan</p>
        
        <!-- Alert Messages -->
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
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
        
        <form class="login-form" method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" class="form-input" name="email" id="email" 
                       placeholder="Masukkan email Anda" value="{{ old('email') }}" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Kata Sandi</label>
                <input type="password" class="form-input" name="password" id="password" 
                       placeholder="Masukkan kata sandi" required>
            </div>
            
            <div class="form-options">
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Ingat saya</label>
                </div>
            </div>
            
            <button type="submit" class="submit-btn">
                <i class="fas fa-sign-in-alt"></i>Masuk
            </button>
        </form>

        <div class="login-footer">
            <p>Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
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
            document.getElementById('loginScreen').style.display = 'flex';
        }, 500);
    }, 2000);

    // Form will submit normally to the server (no AJAX needed)
});
</script>
@endpush