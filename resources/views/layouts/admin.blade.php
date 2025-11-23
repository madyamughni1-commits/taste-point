<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - TastePoint')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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

        /* Overlay untuk readability - ADJUST SESUAI KEBUTUHAN */
        .video-background::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                135deg,
                rgba(20, 15, 10, 0.92) 0%,
                rgba(30, 20, 15, 0.90) 50%,
                rgba(25, 18, 12, 0.92) 100%
            );
            pointer-events: none;
        }

        /* ===== EXISTING ADMIN STYLES ===== */
        :root {
            --bg-dark: rgba(18, 18, 18, 0.95);
            --bg-card: rgba(30, 30, 30, 0.95);
            --card-border: rgba(255, 136, 0, 0.15);
            --primary: #ff6b00;
            --primary-dark: #e66000;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --light: #f3f4f6;
            --text-muted: rgba(255, 255, 255, 0.6);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: transparent;
            color: var(--light);
            overflow-x: hidden;
            position: relative;
            z-index: 1;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 260px;
            height: 100vh;
            background: rgba(20, 20, 20, 0.95);
            backdrop-filter: blur(10px);
            border-right: 1px solid var(--card-border);
            padding: 1.5rem 0;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 3px;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0 1.5rem 1.5rem;
            border-bottom: 1px solid var(--card-border);
            margin-bottom: 1.5rem;
        }

        .sidebar-brand i {
            font-size: 1.8rem;
            color: var(--primary);
        }

        .sidebar-brand span {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }

        .sidebar-menu {
            list-style: none;
            padding: 0 0.8rem;
        }

        .sidebar-menu li {
            margin-bottom: 0.3rem;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.9rem 1rem;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 10px;
            transition: var(--transition);
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255, 107, 0, 0.15);
            color: var(--primary);
            transform: translateX(5px);
        }

        .sidebar-menu a i {
            font-size: 1.2rem;
            width: 24px;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            padding: 2rem;
            position: relative;
        }

        /* Top Bar */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 2rem;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--light);
            margin: 0;
        }

        .top-bar-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* User Info */
        .user-info {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.6rem 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            border: 1px solid var(--card-border);
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.7rem 1.2rem;
            border-radius: 10px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            font-size: 0.95rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 0, 0.3);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: var(--light);
            border: 1px solid var(--card-border);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .btn-logout {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
            padding: 0.6rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-logout:hover {
            background: rgba(239, 68, 68, 0.3);
        }

        /* Content Card */
        .content-card {
            background: rgba(30, 30, 30, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid var(--card-border);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .content-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--card-border);
        }

        .content-card-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--light);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(30, 30, 30, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid var(--card-border);
            border-radius: 15px;
            padding: 1.5rem;
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(255, 107, 0, 0.15);
        }

        /* Alerts */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: var(--success);
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: var(--danger);
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table thead {
            background: rgba(255, 255, 255, 0.05);
        }

        table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid var(--card-border);
        }

        table td {
            padding: 1rem;
            border-bottom: 1px solid var(--card-border);
        }

        table tbody tr {
            transition: var(--transition);
        }

        table tbody tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .table-responsive {
            overflow-x: auto;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            .top-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Video Toggle Button (Optional) */
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
            transition: var(--transition);
        }

        .video-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(255, 136, 0, 0.5);
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

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-utensils"></i>
            <span>TastePoint</span>
        </div>

        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.restaurants') }}" class="{{ request()->routeIs('admin.restaurants*') ? 'active' : '' }}">
                    <i class="fas fa-store"></i>
                    <span>Restoran</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.all-menus') }}" class="{{ request()->routeIs('admin.all-menus') || request()->routeIs('admin.menus*') ? 'active' : '' }}">
                    <i class="fas fa-utensils"></i>
                    <span>Menu Makanan</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.bulk-menu-upload') }}" class="{{ request()->routeIs('admin.bulk-menu*') ? 'active' : '' }}">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <span>Bulk Upload Menu</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Pengguna</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.reviews') }}" class="{{ request()->routeIs('admin.reviews') ? 'active' : '' }}">
                    <i class="fas fa-star"></i>
                    <span>Reviews</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.activity-logs') }}" class="{{ request()->routeIs('admin.activity-logs') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i>
                    <span>Laporan Aktivitas</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i>
                    <span>Pengaturan</span>
                </a>
            </li>
        </ul>

        <ul class="sidebar-menu" style="margin-top: auto; padding-top: 2rem; border-top: 1px solid var(--card-border);">
            <li>
                <a href="{{ route('home') }}">
                    <i class="fas fa-home"></i>
                    <span>Ke Halaman User</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

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