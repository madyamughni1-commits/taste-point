<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TastePoint')</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <style>
        :root {
            --primary: #964207;
            --primary-dark: #763d16;
            --primary-light: #744327;
            --darker: #fa5b06;
            --dark: #000000;
            --card-bg: rgba(255, 255, 255, 0.05);
            --card-border: rgba(255, 255, 255, 0.1);
            --light: #ffffff;
            --text-muted: #aaa;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #000000 0%, #a36820 100%);

            min-height: 100vh;
            color: var(--light);
        }

        /* Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--light);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-brand i {
            color: var(--primary);
        }

        .navbar-brand span {
            background: linear-gradient(to right, var(--primary), var(--primary-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .navbar-user {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-muted);
        }

        .user-name {
            font-weight: 600;
            color: var(--light);
        }

        .btn-logout {
            background: rgba(255, 107, 107, 0.2);
            color: #ff6b6b;
            border: 1px solid rgba(255, 107, 107, 0.3);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
        }

        .btn-logout:hover {
            background: rgba(255, 107, 107, 0.3);
            transform: translateY(-2px);
        }

        /* Main Container */
        .main-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        /* Search Section */
        .search-section {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .search-box {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .search-input {
            flex: 1;
            padding: 1rem 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            color: var(--light);
            font-size: 1rem;
        }

        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
        }

        .btn-search {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 107, 0, 0.4);
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .action-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--light);
            padding: 0.8rem 1.5rem;
            border-radius: 12px;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }

        .action-btn i {
            color: var(--primary);
        }

        .action-btn:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        /* Budget Section */
        .budget-section {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .budget-section h3 {
            margin-bottom: 1rem;
            color: var(--light);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .budget-section h3 i {
            color: var(--primary);
        }

        .budget-input {
            width: 100%;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            color: var(--light);
            margin-bottom: 1rem;
        }

        .budget-input:focus {
            outline: none;
            border-color: var(--primary);
        }

        .budget-presets {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .budget-preset {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--light);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
        }

        .budget-preset:hover,
        .budget-preset.active {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-color: var(--primary);
        }

        .budget-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn-apply {
            flex: 1;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            padding: 0.8rem;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-apply:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 107, 0, 0.4);
        }

        .btn-reset {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--light);
            padding: 0.8rem 1.5rem;
            border-radius: 12px;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-reset:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        /* Restaurant Cards */
        .restaurants-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .restaurant-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            overflow: hidden;
            transition: var(--transition);
            cursor: pointer;
        }

        .restaurant-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(255, 107, 0, 0.2);
            border-color: var(--primary);
        }

        .restaurant-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .restaurant-info {
            padding: 1.5rem;
        }

        .restaurant-name {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--light);
        }

        .restaurant-meta {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: black;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .restaurant-meta i {
            color: black;
        }

        .restaurant-price {
            color: black;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .restaurant-rating {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: black;
        }

        .btn-detail {
            width: 100%;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--light);
            padding: 0.8rem;
            border-radius: 12px;
            margin-top: 1rem;
            cursor: pointer;
            transition: var(--transition);
            font-weight: 600;
        }

        .btn-detail:hover {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-color: var(--primary);
        }

        /* Food Detection Section */
        .detection-section {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .detection-section h3 {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detection-section h3 i {
            color: var(--primary);
        }

        .detection-info {
            color: var(--text-muted);
            margin-bottom: 1.5rem;
        }

        .btn-simulate {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 12px;
            cursor: pointer;
            transition: var(--transition);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-simulate:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 107, 0, 0.4);
        }

        /* Responsive */
       /* Default (Desktop / Laptop) */
.navbar {
    padding: 1.5rem 2rem;
}

.navbar-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 2rem;
}

.navbar-brand {
    font-size: 2rem;
}

.main-container {
    padding: 0 3rem;
}

.search-box {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.action-buttons {
    display: flex;
    gap: 1rem;
}

.restaurants-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.budget-presets {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-start;
}

/* Tablet (max-width: 768px) */
@media (max-width: 768px) {
    .navbar {
        padding: 1rem;
    }

    .navbar-container {
        flex-direction: column;
        gap: 1rem;
    }

    .navbar-brand {
        font-size: 1.6rem;
    }

    .main-container {
        padding: 0 1rem;
    }

    .search-box {
        flex-direction: column;
        width: 100%;
    }

    .action-buttons {
        flex-direction: column;
        align-items: stretch;
    }

    .restaurants-grid {
        grid-template-columns: 1fr 1fr;
    }

    .budget-presets {
        justify-content: center;
    }
}

/* Mobile Landscape (max-width: 600px) */
@media (max-width: 600px) {
    .navbar-brand {
        font-size: 1.4rem;
    }

    .restaurants-grid {
        grid-template-columns: 1fr;
    }

    .search-box,
    .action-buttons {
        width: 100%;
        gap: 0.8rem;
    }

    .main-container {
        padding: 0 0.8rem;
    }
}

/* Mobile Portrait (max-width: 480px) */
@media (max-width: 480px) {
    .navbar {
        padding: 0.8rem;
    }

    .navbar-container {
        gap: 0.8rem;
    }

    .navbar-brand {
        font-size: 1.2rem;
    }

    .restaurants-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .search-box,
    .action-buttons {
        flex-direction: column;
        width: 100%;
    }

    .budget-presets {
        flex-wrap: wrap;
        justify-content: center;
    }
}

/* Very Small Devices (max-width: 360px) */
@media (max-width: 360px) {
    .navbar-brand {
        font-size: 1rem;
    }

    .restaurants-grid {
        gap: 0.5rem;
    }

    .main-container {
        padding: 0 0.5rem;
    }
}

    </style>

    @stack('styles')
</head>
<body>
    @yield('content')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @stack('scripts')
</body>
</html>