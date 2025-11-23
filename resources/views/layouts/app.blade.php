<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TastePoint')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <style>
        /* ===== VIDEO BACKGROUND ===== */
        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .video-background video {
            position: absolute;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            transform: translate(-50%, -50%);
            object-fit: cover;
        }

        /* Overlay lebih gelap untuk login page */
        .video-background::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                135deg,
                rgba(15, 10, 5, 0.94) 0%,
                rgba(25, 15, 10, 0.92) 50%,
                rgba(20, 12, 8, 0.94) 100%
            );
            pointer-events: none;
        }

        /* ===== EXISTING LOGIN STYLES ===== */
        :root {
            --primary: #ff6b00;
            --primary-dark: #e66000;
            --success: #10b981;
            --danger: #ef4444;
            --light: #f3f4f6;
            --text-muted: rgba(255, 255, 255, 0.6);
            --card-border: rgba(255, 136, 0, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            position: relative;
            z-index: 1;
            overflow-x: hidden;
        }

        /* Loading Screen */
        .loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #1a1a1a 0%, #0d0d0d 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.5s ease;
        }

        .loading-logo {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .loading-bar {
            width: 300px;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        .loading-progress {
            width: 0;
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--primary-dark));
            animation: loading 2s ease-in-out;
            border-radius: 10px;
        }

        @keyframes loading {
            0% { width: 0%; }
            100% { width: 100%; }
        }

        /* Login Screen */
        .login-screen {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-container {
            background: rgba(30, 30, 30, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            padding: 3rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }

        .login-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
            margin-bottom: 2rem;
        }

        .login-logo i {
            font-size: 2.5rem;
            color: var(--primary);
        }

        .login-logo span {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
        }

        .login-container h2 {
            color: var(--light);
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .login-container > p {
            color: var(--text-muted);
            text-align: center;
            margin-bottom: 2rem;
        }

        /* Alert Messages */
        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #ef4444;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #10b981;
        }

        .alert ul {
            margin: 0;
            padding-left: 1.2rem;
        }

        /* Login Form */
        .login-form {
            margin-top: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            color: var(--light);
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .form-input {
            width: 100%;
            padding: 0.9rem 1rem;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: var(--light);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.12);
            box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.1);
        }

        .form-input::placeholder {
            color: var(--text-muted);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--light);
            font-size: 0.9rem;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--primary);
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 107, 0, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        /* Login Footer */
        .login-footer {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }

        .login-footer p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .login-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .login-footer a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        /* Video Toggle Button */
        .video-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 999;
            padding: 12px 16px;
            background: rgba(255, 136, 0, 0.9);
            color: white;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(255, 136, 0, 0.4);
            transition: all 0.3s ease;
        }

        .video-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(255, 136, 0, 0.5);
        }

        /* Responsive */
        @media (max-width: 576px) {
            .login-container {
                padding: 2rem 1.5rem;
            }

            .loading-logo {
                font-size: 2rem;
            }

            .loading-bar {
                width: 200px;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Video Background -->
    <div class="video-background">
        <video autoplay loop muted playsinline preload="auto" id="bgVideo">
            <source src="{{ asset('videos/background.mp4') }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>

    <!-- Optional: Video Toggle Button -->
    <button class="video-toggle" id="videoToggle" title="Toggle Background Video">
        <i class="fas fa-video"></i>
    </button>

    <!-- Content -->
    @yield('content')

    <!-- Video Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const video = document.getElementById('bgVideo');
            const toggleBtn = document.getElementById('videoToggle');
            
            if (video && toggleBtn) {
                let isPlaying = true;
                
                toggleBtn.addEventListener('click', function() {
                    if (isPlaying) {
                        video.pause();
                        toggleBtn.innerHTML = '<i class="fas fa-video-slash"></i>';
                        toggleBtn.style.background = 'rgba(107, 114, 128, 0.9)';
                    } else {
                        video.play();
                        toggleBtn.innerHTML = '<i class="fas fa-video"></i>';
                        toggleBtn.style.background = 'rgba(255, 136, 0, 0.9)';
                    }
                    isPlaying = !isPlaying;
                });
            }

            // Save video state to localStorage
            const savedState = localStorage.getItem('videoPlaying');
            if (savedState === 'false' && video) {
                video.pause();
                if (toggleBtn) {
                    toggleBtn.innerHTML = '<i class="fas fa-video-slash"></i>';
                    toggleBtn.style.background = 'rgba(107, 114, 128, 0.9)';
                }
            }

            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    localStorage.setItem('videoPlaying', video.paused ? 'false' : 'true');
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>