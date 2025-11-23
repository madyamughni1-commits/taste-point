// Main JavaScript file for TastePoint - COMPLETE & UPDATED

// Default restaurant data
const defaultRestaurants = [
    {
        id: 1,
        name: "Warung Nusantara",
        type: "restaurant",
        status: "open",
        distance: "1.2 km",
        priceRange: "Rp 25.000 - 75.000",
        image: "fas fa-utensils",
        photo: "https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=400&h=300&fit=crop",
        is24Hours: false,
        avgPrice: 50000,
        location: "Jl. Pendidikan No. 123, Jakarta Pusat",
        description: "Restoran masakan Indonesia tradisional dengan cita rasa autentik dan suasana yang nyaman",
        rating: 4.5,
        views: 156,
        googleMaps: "https://maps.google.com/?q=Jl+Pendidikan+No+123+Jakarta+Pusat",
        menus: [
            { 
                id: 1, 
                name: "Nasi Goreng Spesial", 
                price: 25000, 
                category: "makanan", 
                isPopular: true,
                photo: "https://images.unsplash.com/photo-1551892374-ecf8754cf8b0?w=300&h=200&fit=crop",
                description: "Nasi goreng dengan telur, ayam, dan sayuran segar"
            },
            { 
                id: 2, 
                name: "Ayam Bakar Madu", 
                price: 35000, 
                category: "makanan", 
                isPopular: true,
                photo: "https://images.unsplash.com/photo-1525648199074-cee30ba79a4a?w=300&h=200&fit=crop",
                description: "Ayam bakar dengan bumbu madu spesial"
            },
            { 
                id: 3, 
                name: "Es Jeruk", 
                price: 12000, 
                category: "minuman",
                photo: "https://images.unsplash.com/photo-1621506289937-a8e4df240d0b?w=300&h=200&fit=crop",
                description: "Es jeruk segar dengan potongan jeruk asli"
            }
        ]
    },
    {
        id: 2,
        name: "Kopi Teman",
        type: "cafe",
        status: "soon",
        distance: "0.8 km",
        priceRange: "Rp 15.000 - 50.000",
        image: "fas fa-coffee",
        photo: "https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=400&h=300&fit=crop",
        is24Hours: true,
        avgPrice: 30000,
        location: "Jl. Merdeka No. 45, Jakarta Selatan",
        description: "Tempat nongkrong cozy dengan kopi berkualitas dan makanan ringan yang lezat",
        rating: 4.2,
        views: 89,
        googleMaps: "https://maps.google.com/?q=Jl+Merdeka+No+45+Jakarta+Selatan",
        menus: [
            { 
                id: 4, 
                name: "Kopi Latte", 
                price: 25000, 
                category: "minuman", 
                isPopular: true,
                photo: "https://images.unsplash.com/photo-1561047029-3000c68339ca?w=300&h=200&fit=crop",
                description: "Kopi latte dengan susu segar dan latte art"
            },
            { 
                id: 5, 
                name: "Croissant", 
                price: 20000, 
                category: "snack",
                photo: "https://images.unsplash.com/photo-1555507036-ab794f27d2e9?w=300&h=200&fit=crop",
                description: "Croissant buttery dengan tekstur yang renyah"
            },
            { 
                id: 6, 
                name: "Sandwich", 
                price: 35000, 
                category: "makanan",
                photo: "https://images.unsplash.com/photo-1567234669003-dce7a7a88821?w=300&h=200&fit=crop",
                description: "Sandwich dengan daging asap, sayuran, dan saus spesial"
            }
        ]
    }
];

// User roles
const USER_ROLES = {
    USER: 'user',
    ADMIN: 'admin'
};

// Global state
let currentUser = null;
let currentPage = 'home';
let allRestaurants = [];
let allMenus = [];
let userBudget = null;
let currentLocation = null;
let searchResults = null;
let currentSearchTerm = '';

// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM Loaded - Initializing TastePoint App...');
    initializeApp();
});

function initializeApp() {
    console.log('🚀 Initializing TastePoint App...');
    
    // Initialize data
    initializeAppData();
    
    // Check if user is already logged in
    const savedUser = localStorage.getItem('tastepoint_user');
    if (savedUser) {
        try {
            currentUser = JSON.parse(savedUser);
            console.log('✅ User found in storage:', currentUser);
            showMainApp();
        } catch (e) {
            console.error('❌ Error parsing saved user:', e);
            localStorage.removeItem('tastepoint_user');
            showLoginScreen();
        }
    } else {
        showLoginScreen();
    }

    // Initialize main login form
    initializeMainLogin();
}

function initializeAppData() {
    // Load data from admin system or use defaults
    try {
        const adminRestaurants = JSON.parse(localStorage.getItem('admin_restaurants'));
        const publicData = JSON.parse(localStorage.getItem('public_data'));
        
        if (adminRestaurants && adminRestaurants.length > 0) {
            console.log('📊 Loading data from admin system...');
            allRestaurants = adminRestaurants;
            allMenus = getAllMenusFromRestaurants(adminRestaurants);
        } else if (publicData && publicData.restaurants && publicData.menus) {
            console.log('📊 Loading data from public data...');
            allRestaurants = publicData.restaurants;
            allMenus = publicData.menus;
        } else {
            console.log('📊 Using default data...');
            allRestaurants = [...defaultRestaurants];
            allMenus = getAllMenusFromRestaurants(defaultRestaurants);
        }
    } catch (e) {
        console.error('Error loading data:', e);
        allRestaurants = [...defaultRestaurants];
        allMenus = getAllMenusFromRestaurants(defaultRestaurants);
    }
    
    // Load user preferences
    userBudget = localStorage.getItem('user_budget');
    currentLocation = localStorage.getItem('user_location');
}

function getAllMenusFromRestaurants(restaurants) {
    let menus = [];
    restaurants.forEach(restaurant => {
        restaurant.menus.forEach(menu => {
            menus.push({
                ...menu,
                restaurantName: restaurant.name,
                restaurantId: restaurant.id,
                restaurantImage: restaurant.image,
                restaurantPhoto: restaurant.photo,
                restaurantLocation: restaurant.location,
                restaurantGoogleMaps: restaurant.googleMaps
            });
        });
    });
    return menus;
}

function showLoginScreen() {
    setTimeout(() => {
        document.getElementById('loadingScreen').style.opacity = '0';
        setTimeout(() => {
            document.getElementById('loadingScreen').style.display = 'none';
            document.getElementById('loginScreen').style.display = 'flex';
            console.log('🔐 Showing login screen');
        }, 500);
    }, 2000);
}

function initializeMainLogin() {
    const mainLoginForm = document.getElementById('mainLoginForm');
    const showRegister = document.getElementById('showRegister');

    console.log('🔧 Initializing login form...');

    if (mainLoginForm) {
        mainLoginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('📝 Login form submitted');
            handleMainLogin();
        });

        // Also add click event for submit button
        const submitBtn = mainLoginForm.querySelector('.submit-btn');
        if (submitBtn) {
            submitBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('🖱️ Submit button clicked');
                handleMainLogin();
            });
        }
    } else {
        console.error('❌ Login form not found!');
    }

    if (showRegister) {
        showRegister.addEventListener('click', function(e) {
            e.preventDefault();
            showNotification('Fitur pendaftaran akan datang! Untuk testing, gunakan:<br><br><strong>User:</strong> user@example.com<br><strong>Admin:</strong> admin@example.com', 'info');
        });
    }
}

function handleMainLogin() {
    const email = document.getElementById('mainEmail');
    const password = document.getElementById('mainPassword');
    const remember = document.getElementById('mainRemember');

    if (!email || !password) {
        console.error('❌ Email or password input not found');
        showNotification('Form login tidak lengkap!', 'error');
        return;
    }

    const emailValue = email.value.trim();
    const passwordValue = password.value;
    const rememberValue = remember ? remember.checked : false;

    console.log('🔑 Attempting login with:', { 
        email: emailValue, 
        remember: rememberValue 
    });

    if (!emailValue || !passwordValue) {
        showNotification('Email dan password harus diisi!', 'error');
        return;
    }

    showLoading();

    // Simulate login process
    setTimeout(() => {
        // Check if admin login
        if (emailValue.includes('admin')) {
            if (passwordValue !== 'admin123') {
                showNotification('Password admin salah! Gunakan: admin123', 'error');
                hideLoading();
                return;
            }
            
            currentUser = {
                name: 'Administrator',
                email: emailValue,
                role: USER_ROLES.ADMIN,
                avatar: 'A'
            };
            
            if (rememberValue) {
                localStorage.setItem('tastepoint_user', JSON.stringify(currentUser));
            }
            
            hideLoading();
            console.log('👨‍💼 Admin login successful');
            redirectToAdmin();
        } else {
            // Regular user login
            if (passwordValue !== 'user123') {
                showNotification('Password user salah! Gunakan: user123', 'error');
                hideLoading();
                return;
            }
            
            currentUser = {
                name: emailValue.split('@')[0].charAt(0).toUpperCase() + emailValue.split('@')[0].slice(1),
                email: emailValue,
                role: USER_ROLES.USER,
                avatar: emailValue.split('@')[0].charAt(0).toUpperCase()
            };
            
            if (rememberValue) {
                localStorage.setItem('tastepoint_user', JSON.stringify(currentUser));
            }
            
            hideLoading();
            console.log('👤 User login successful');
            showMainApp();
        }
    }, 1000);
}

function showMainApp() {
    console.log('🖥️ Showing main application');
    
    document.getElementById('loginScreen').style.display = 'none';
    document.getElementById('mainApp').style.display = 'block';
    
    loadHomePage();
}

// PAGE MANAGEMENT
function loadHomePage() {
    currentPage = 'home';
    const mainApp = document.getElementById('mainApp');
    
    mainApp.innerHTML = `
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-custom fixed-top animate__animated animate__fadeInDown">
            <div class="container">
                <a class="navbar-brand" href="#" onclick="navigateTo('home')">
                    <i class="fas fa-utensils me-2"></i>Taste<span>Point</span>
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link active" href="#" onclick="navigateTo('home')">
                                <i class="fas fa-home me-1"></i>Beranda
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="navigateTo('restaurants')">
                                <i class="fas fa-utensils me-1"></i>Restoran
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="navigateTo('cafes')">
                                <i class="fas fa-coffee me-1"></i>Kafe
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="navigateTo('menu')">
                                <i class="fas fa-list me-1"></i>Menu
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="navigateTo('about')">
                                <i class="fas fa-info-circle me-1"></i>Tentang
                            </a>
                        </li>
                    </ul>
                    
                    <div class="navbar-nav">
                        <div class="user-profile animate__animated animate__fadeIn">
                            <div class="user-avatar-circle">
                                <div class="user-avatar">${currentUser.avatar}</div>
                            </div>
                            <div class="user-info">
                                <span class="user-name">${currentUser.name}</span>
                                <small class="user-role">${currentUser.role === USER_ROLES.ADMIN ? 'Administrator' : 'User'}</small>
                            </div>
                            <button class="btn btn-outline-danger btn-sm ms-2" onclick="logout()">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8 mx-auto text-center">
                        <h1 class="hero-title animate__animated animate__fadeInUp">Temukan Rasamu</h1>
                        <p class="hero-subtitle animate__animated animate__fadeInUp animate__delay-1s">
                            Cari restoran dan kafe terdekat sesuai dengan budget dan selera makan Anda
                        </p>
                        
                        <div class="search-container animate__animated animate__fadeInUp animate__delay-2s">
                            <div class="input-group">
                                <input type="text" class="form-control search-input" id="mainSearch" placeholder="Cari makanan atau restoran...">
                                <button class="btn search-btn" id="searchButton">
                                    <i class="fas fa-search me-2"></i>Cari
                                </button>
                            </div>
                        </div>
                        
                        <div class="search-options animate__animated animate__fadeInUp animate__delay-3s">
                            <button class="btn btn-outline-light me-2" id="cameraSearch" data-bs-toggle="modal" data-bs-target="#cameraModal">
                                <i class="fas fa-camera me-2"></i>Foto Makanan
                            </button>
                            <button class="btn btn-outline-light me-2" id="locationSearch" onclick="detectLocation()">
                                <i class="fas fa-location-arrow me-2"></i>Deteksi Lokasi
                            </button>
                            <button class="btn btn-outline-light" id="budgetSearch" data-bs-toggle="modal" data-bs-target="#budgetModal">
                                <i class="fas fa-wallet me-2"></i>Set Budget
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features-section">
            <div class="container">
                <h2 class="section-title animate__animated animate__fadeIn">Fitur Unggulan</h2>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="feature-card animate__animated animate__fadeInUp">
                            <div class="feature-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <h3>Lokasi Terdekat</h3>
                            <p>Temukan restoran dan kafe terdekat dari lokasi Anda dengan mudah</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card animate__animated animate__fadeInUp animate__delay-1s">
                            <div class="feature-icon">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <h3>Sesuai Budget</h3>
                            <p>Filter restoran berdasarkan budget yang Anda miliki</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card animate__animated animate__fadeInUp animate__delay-2s">
                            <div class="feature-icon">
                                <i class="fas fa-camera"></i>
                            </div>
                            <h3>AI Food Detection</h3>
                            <p>Foto makanan dan biarkan AI kami mengenali dan merekomendasikannya</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Restaurant Recommendations -->
        <section class="section-spacing bg-dark">
            <div class="container">
                <h2 class="section-title animate__animated animate__fadeIn">Rekomendasi Untuk Anda</h2>
                <div id="activeFilters" class="active-filters"></div>
                <div class="row g-4" id="restaurantGrid">
                    <!-- Restaurant cards will be loaded here -->
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start">
                        <p>&copy; 2023 TastePoint. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <p>Dibuat dengan ❤️ untuk food lovers</p>
                    </div>
                </div>
            </div>
        </footer>
    `;

    loadRestaurants();
    initializeHomeFeatures();
    updateActiveFilters();
    showNotification(`Selamat datang, ${currentUser.name}! 🎉`, 'success');
}

// NAVIGATION SYSTEM
function navigateTo(page) {
    // Update navigation active state
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => link.classList.remove('active'));
    event.target.classList.add('active');
    
    // Load appropriate page
    switch(page) {
        case 'home':
            loadHomePage();
            break;
        case 'restaurants':
            loadRestaurantsPage();
            break;
        case 'cafes':
            loadCafesPage();
            break;
        case 'menu':
            loadMenuPage();
            break;
        case 'about':
            loadAboutPage();
            break;
    }
}

function loadRestaurantsPage() {
    currentPage = 'restaurants';
    const mainApp = document.getElementById('mainApp');
    
    mainApp.innerHTML = `
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
            <div class="container">
                <a class="navbar-brand" href="#" onclick="navigateTo('home')">
                    <i class="fas fa-utensils me-2"></i>Taste<span>Point</span>
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="navigateTo('home')">
                                <i class="fas fa-home me-1"></i>Beranda
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="#" onclick="navigateTo('restaurants')">
                                <i class="fas fa-utensils me-1"></i>Restoran
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="navigateTo('cafes')">
                                <i class="fas fa-coffee me-1"></i>Kafe
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="navigateTo('menu')">
                                <i class="fas fa-list me-1"></i>Menu
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="navigateTo('about')">
                                <i class="fas fa-info-circle me-1"></i>Tentang
                            </a>
                        </li>
                    </ul>
                    
                    <div class="navbar-nav">
                        <div class="user-profile">
                            <div class="user-avatar-circle">
                                <div class="user-avatar">${currentUser.avatar}</div>
                            </div>
                            <div class="user-info">
                                <span class="user-name">${currentUser.name}</span>
                                <small class="user-role">${currentUser.role === USER_ROLES.ADMIN ? 'Administrator' : 'User'}</small>
                            </div>
                            <button class="btn btn-outline-danger btn-sm ms-2" onclick="logout()">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Restaurants Header -->
        <section class="hero-section" style="padding: 6rem 0 4rem; background: linear-gradient(135deg, rgba(255,107,0,0.2) 0%, rgba(18,18,18,0.95) 100%);">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 mx-auto text-center">
                        <h1 class="hero-title animate__animated animate__fadeInUp">Semua Restoran</h1>
                        <p class="hero-subtitle animate__animated animate__fadeInUp animate__delay-1s">
                            Temukan restoran terbaik di sekitar Anda
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Restaurants Content -->
        <section class="section-spacing">
            <div class="container">
                <div class="row mb-5">
                    <div class="col-md-8">
                        <h3 class="animate__animated animate__fadeIn">
                            <i class="fas fa-utensils me-2"></i>Daftar Restoran
                        </h3>
                        <p class="text-muted animate__animated animate__fadeIn animate__delay-1s">
                            ${allRestaurants.filter(r => r.type === 'restaurant').length} restoran tersedia
                        </p>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group animate__animated animate__fadeIn animate__delay-2s">
                            <input type="text" class="form-control" id="restaurantSearch" placeholder="Cari restoran...">
                            <button class="btn btn-primary" onclick="searchRestaurants()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row g-4" id="restaurantsGrid">
                    <!-- Restaurants will be loaded here -->
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start">
                        <p>&copy; 2023 TastePoint. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <p>Dibuat dengan ❤️ untuk food lovers</p>
                    </div>
                </div>
            </div>
        </footer>
    `;

    loadAllRestaurants();
    initializeRestaurantSearch();
}

function loadCafesPage() {
    currentPage = 'cafes';
    const mainApp = document.getElementById('mainApp');
    
    mainApp.innerHTML = `
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
            <div class="container">
                <a class="navbar-brand" href="#" onclick="navigateTo('home')">
                    <i class="fas fa-utensils me-2"></i>Taste<span>Point</span>
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="navigateTo('home')">
                                <i class="fas fa-home me-1"></i>Beranda
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="navigateTo('restaurants')">
                                <i class="fas fa-utensils me-1"></i>Restoran
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="#" onclick="navigateTo('cafes')">
                                <i class="fas fa-coffee me-1"></i>Kafe
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="navigateTo('menu')">
                                <i class="fas fa-list me-1"></i>Menu
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="navigateTo('about')">
                                <i class="fas fa-info-circle me-1"></i>Tentang
                            </a>
                        </li>
                    </ul>
                    
                    <div class="navbar-nav">
                        <div class="user-profile">
                            <div class="user-avatar-circle">
                                <div class="user-avatar">${currentUser.avatar}</div>
                            </div>
                            <div class="user-info">
                                <span class="user-name">${currentUser.name}</span>
                                <small class="user-role">${currentUser.role === USER_ROLES.ADMIN ? 'Administrator' : 'User'}</small>
                            </div>
                            <button class="btn btn-outline-danger btn-sm ms-2" onclick="logout()">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Cafes Header -->
        <section class="hero-section" style="padding: 6rem 0 4rem; background: linear-gradient(135deg, rgba(139,69,19,0.2) 0%, rgba(18,18,18,0.95) 100%);">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 mx-auto text-center">
                        <h1 class="hero-title animate__animated animate__fadeInUp">Kafe & Coffee Shop</h1>
                        <p class="hero-subtitle animate__animated animate__fadeInUp animate__delay-1s">
                            Temukan tempat nongkrong terbaik dengan kopi berkualitas
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Cafes Content -->
        <section class="section-spacing">
            <div class="container">
                <div class="row mb-5">
                    <div class="col-md-8">
                        <h3 class="animate__animated animate__fadeIn">
                            <i class="fas fa-coffee me-2"></i>Daftar Kafe
                        </h3>
                        <p class="text-muted animate__animated animate__fadeIn animate__delay-1s">
                            ${allRestaurants.filter(r => r.type === 'cafe').length} kafe tersedia
                        </p>
                    </div>
                </div>

                <div class="row g-4" id="cafesGrid">
                    <!-- Cafes will be loaded here -->
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start">
                        <p>&copy; 2023 TastePoint. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <p>Dibuat dengan ❤️ untuk food lovers</p>
                    </div>
                </div>
            </div>
        </footer>
    `;

    loadCafes();
}

function loadMenuPage() {
    currentPage = 'menu';
    const mainApp = document.getElementById('mainApp');
    
    mainApp.innerHTML = `
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
            <div class="container">
                <a class="navbar-brand" href="#" onclick="navigateTo('home')">
                    <i class="fas fa-utensils me-2"></i>Taste<span>Point</span>
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="navigateTo('home')">
                                <i class="fas fa-home me-1"></i>Beranda
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="navigateTo('restaurants')">
                                <i class="fas fa-utensils me-1"></i>Restoran
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="navigateTo('cafes')">
                                <i class="fas fa-coffee me-1"></i>Kafe
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="#" onclick="navigateTo('menu')">
                                <i class="fas fa-list me-1"></i>Menu
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="navigateTo('about')">
                                <i class="fas fa-info-circle me-1"></i>Tentang
                            </a>
                        </li>
                    </ul>
                    
                    <div class="navbar-nav">
                        <div class="user-profile">
                            <div class="user-avatar-circle">
                                <div class="user-avatar">${currentUser.avatar}</div>
                            </div>
                            <div class="user-info">
                                <span class="user-name">${currentUser.name}</span>
                                <small class="user-role">${currentUser.role === USER_ROLES.ADMIN ? 'Administrator' : 'User'}</small>
                            </div>
                            <button class="btn btn-outline-danger btn-sm ms-2" onclick="logout()">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Menu Header -->
        <section class="hero-section" style="padding: 6rem 0 4rem; background: linear-gradient(135deg, rgba(76,175,80,0.2) 0%, rgba(18,18,18,0.95) 100%);">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 mx-auto text-center">
                        <h1 class="hero-title animate__animated animate__fadeInUp">Semua Menu</h1>
                        <p class="hero-subtitle animate__animated animate__fadeInUp animate__delay-1s">
                            Jelajahi berbagai pilihan makanan dan minuman
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Menu Content -->
        <section class="section-spacing">
            <div class="container">
                <div class="row mb-5">
                    <div class="col-md-8">
                        <h3 class="animate__animated animate__fadeIn">
                            <i class="fas fa-list me-2"></i>Daftar Menu
                        </h3>
                        <p class="text-muted animate__animated animate__fadeIn animate__delay-1s">
                            ${allMenus.length} menu tersedia
                        </p>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group animate__animated animate__fadeIn animate__delay-2s">
                            <input type="text" class="form-control" id="menuSearch" placeholder="Cari menu...">
                            <button class="btn btn-primary" onclick="searchMenus()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row g-4" id="menusGrid">
                    <!-- Menus will be loaded here -->
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start">
                        <p>&copy; 2023 TastePoint. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <p>Dibuat dengan ❤️ untuk food lovers</p>
                    </div>
                </div>
            </div>
        </footer>
    `;

    loadAllMenus();
    initializeMenuSearch();
}

function loadAboutPage() {
    currentPage = 'about';
    const mainApp = document.getElementById('mainApp');
    
    mainApp.innerHTML = `
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
            <div class="container">
                <a class="navbar-brand" href="#" onclick="navigateTo('home')">
                    <i class="fas fa-utensils me-2"></i>Taste<span>Point</span>
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="navigateTo('home')">
                                <i class="fas fa-home me-1"></i>Beranda
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="navigateTo('restaurants')">
                                <i class="fas fa-utensils me-1"></i>Restoran
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="navigateTo('cafes')">
                                <i class="fas fa-coffee me-1"></i>Kafe
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="navigateTo('menu')">
                                <i class="fas fa-list me-1"></i>Menu
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="#" onclick="navigateTo('about')">
                                <i class="fas fa-info-circle me-1"></i>Tentang
                            </a>
                        </li>
                    </ul>
                    
                    <div class="navbar-nav">
                        <div class="user-profile">
                            <div class="user-avatar-circle">
                                <div class="user-avatar">${currentUser.avatar}</div>
                            </div>
                            <div class="user-info">
                                <span class="user-name">${currentUser.name}</span>
                                <small class="user-role">${currentUser.role === USER_ROLES.ADMIN ? 'Administrator' : 'User'}</small>
                            </div>
                            <button class="btn btn-outline-danger btn-sm ms-2" onclick="logout()">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- About Header -->
        <section class="hero-section" style="padding: 6rem 0 4rem; background: linear-gradient(135deg, rgba(33,150,243,0.2) 0%, rgba(18,18,18,0.95) 100%);">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 mx-auto text-center">
                        <h1 class="hero-title animate__animated animate__fadeInUp">Tentang TastePoint</h1>
                        <p class="hero-subtitle animate__animated animate__fadeInUp animate__delay-1s">
                            Menghubungkan Anda dengan pengalaman kuliner terbaik
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Content -->
        <section class="section-spacing">
            <div class="container">
                <div class="row align-items-center mb-5">
                    <div class="col-lg-6">
                        <h2 class="mb-4 animate__animated animate__fadeIn">Our Story</h2>
                        <p class="lead animate__animated animate__fadeIn animate__delay-1s">
                            TastePoint lahir dari passion untuk menghubungkan pecinta makanan dengan restoran dan kafe terbaik di sekitar mereka.
                        </p>
                        <p class="animate__animated animate__fadeIn animate__delay-2s">
                            Dengan teknologi AI yang canggih, kami memudahkan Anda menemukan makanan yang sesuai dengan selera dan budget.
                        </p>
                    </div>
                    <div class="col-lg-6 text-center">
                        <div class="about-icon animate__animated animate__bounceIn animate__delay-3s" style="font-size: 8rem; color: var(--primary);">
                            <i class="fas fa-utensils"></i>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 text-center">
                        <h3 class="mb-4 animate__animated animate__fadeIn">Tim Kami</h3>
                        <p class="animate__animated animate__fadeIn animate__delay-1s">
                            Dikembangkan oleh tim passionate yang mencintai makanan dan teknologi
                        </p>
                        <div class="mt-4 animate__animated animate__fadeIn animate__delay-2s">
                            <button class="btn btn-primary me-2" onclick="navigateTo('home')">
                                <i class="fas fa-home me-2"></i>Kembali ke Beranda
                            </button>
                            <button class="btn btn-outline-primary" onclick="showNotification('Hubungi kami: info@tastepoint.com', 'info')">
                                <i class="fas fa-envelope me-2"></i>Hubungi Kami
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start">
                        <p>&copy; 2023 TastePoint. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <p>Dibuat dengan ❤️ untuk food lovers</p>
                    </div>
                </div>
            </div>
        </footer>
    `;
}

// DATA LOADING FUNCTIONS
function loadRestaurants() {
    const restaurantGrid = document.getElementById('restaurantGrid');
    if (!restaurantGrid) return;

    restaurantGrid.innerHTML = '';

    // Filter restaurants based on budget if set
    let filteredRestaurants = allRestaurants;
    if (userBudget) {
        filteredRestaurants = allRestaurants.filter(restaurant => restaurant.avgPrice <= userBudget);
    }

    if (filteredRestaurants.length === 0) {
        restaurantGrid.innerHTML = `
            <div class="col-12 text-center">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <h4>Tidak ada restoran yang sesuai dengan budget Anda</h4>
                    <p>Coba atur budget yang lebih tinggi atau hapus filter budget</p>
                    <button class="btn btn-primary mt-2" onclick="clearBudget()">Hapus Filter Budget</button>
                </div>
            </div>
        `;
        return;
    }

    filteredRestaurants.forEach((restaurant, index) => {
        const card = createRestaurantCard(restaurant, index);
        restaurantGrid.appendChild(card);
    });
}

function loadAllRestaurants() {
    const restaurantsGrid = document.getElementById('restaurantsGrid');
    if (!restaurantsGrid) return;

    const restaurants = allRestaurants.filter(r => r.type === 'restaurant');
    
    restaurantsGrid.innerHTML = '';

    if (restaurants.length === 0) {
        restaurantsGrid.innerHTML = `
            <div class="col-12 text-center">
                <div class="alert alert-info">
                    <i class="fas fa-utensils fa-2x mb-3"></i>
                    <h4>Belum ada restoran</h4>
                    <p>Silakan tambahkan restoran melalui panel admin</p>
                </div>
            </div>
        `;
        return;
    }

    restaurants.forEach((restaurant, index) => {
        const col = document.createElement('div');
        col.className = 'col-md-6 col-lg-4';
        col.innerHTML = `
            <div class="restaurant-card h-100 animate__animated animate__fadeInUp" style="animation-delay: ${index * 0.1}s">
                <div class="restaurant-image">
                    ${restaurant.photo ? 
                        `<img src="${restaurant.photo}" alt="${restaurant.name}" class="restaurant-photo">` :
                        `<i class="${restaurant.image}"></i>`
                    }
                </div>
                <div class="restaurant-info">
                    <h3 class="restaurant-name">${restaurant.name}</h3>
                    <div class="restaurant-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>${restaurant.location}</span>
                    </div>
                    <div class="restaurant-status status-${restaurant.status}">
                        ${getStatusText(restaurant.status)}
                    </div>
                    <div class="restaurant-details">
                        <span><i class="fas fa-map-marker-alt"></i> ${restaurant.distance}</span>
                        <span class="restaurant-price">${restaurant.priceRange}</span>
                    </div>
                    <p class="text-muted small">${restaurant.description}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="rating">
                            <i class="fas fa-star text-warning"></i>
                            <span>${restaurant.rating}</span>
                        </div>
                        <button class="btn btn-primary btn-sm" onclick="viewRestaurantDetail(${restaurant.id})">
                            Lihat Menu
                        </button>
                    </div>
                </div>
            </div>
        `;
        restaurantsGrid.appendChild(col);
    });
}

function loadCafes() {
    const cafesGrid = document.getElementById('cafesGrid');
    if (!cafesGrid) return;

    const cafes = allRestaurants.filter(r => r.type === 'cafe');
    
    cafesGrid.innerHTML = '';

    if (cafes.length === 0) {
        cafesGrid.innerHTML = `
            <div class="col-12 text-center">
                <div class="alert alert-info">
                    <i class="fas fa-coffee fa-2x mb-3"></i>
                    <h4>Belum ada kafe</h4>
                    <p>Silakan tambahkan kafe melalui panel admin</p>
                </div>
            </div>
        `;
        return;
    }

    cafes.forEach((cafe, index) => {
        const col = document.createElement('div');
        col.className = 'col-md-6 col-lg-4';
        col.innerHTML = `
            <div class="restaurant-card h-100 animate__animated animate__fadeInUp" style="animation-delay: ${index * 0.1}s">
                <div class="restaurant-image">
                    ${cafe.photo ? 
                        `<img src="${cafe.photo}" alt="${cafe.name}" class="restaurant-photo">` :
                        `<i class="${cafe.image}"></i>`
                    }
                </div>
                <div class="restaurant-info">
                    <h3 class="restaurant-name">${cafe.name}</h3>
                    <div class="restaurant-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>${cafe.location}</span>
                    </div>
                    <div class="restaurant-status status-${cafe.status}">
                        ${getStatusText(cafe.status)}
                    </div>
                    <div class="restaurant-details">
                        <span><i class="fas fa-map-marker-alt"></i> ${cafe.distance}</span>
                        <span class="restaurant-price">${cafe.priceRange}</span>
                    </div>
                    <p class="text-muted small">${cafe.description}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="rating">
                            <i class="fas fa-star text-warning"></i>
                            <span>${cafe.rating}</span>
                        </div>
                        <button class="btn btn-primary btn-sm" onclick="viewRestaurantDetail(${cafe.id})">
                            Lihat Menu
                        </button>
                    </div>
                </div>
            </div>
        `;
        cafesGrid.appendChild(col);
    });
}

function loadAllMenus() {
    const menusGrid = document.getElementById('menusGrid');
    if (!menusGrid) return;

    menusGrid.innerHTML = '';

    if (allMenus.length === 0) {
        menusGrid.innerHTML = `
            <div class="col-12 text-center">
                <div class="alert alert-info">
                    <i class="fas fa-list fa-2x mb-3"></i>
                    <h4>Belum ada menu</h4>
                    <p>Silakan tambahkan menu melalui panel admin</p>
                </div>
            </div>
        `;
        return;
    }

    allMenus.forEach((menu, index) => {
        const col = document.createElement('div');
        col.className = 'col-md-6 col-lg-4';
        col.innerHTML = `
            <div class="restaurant-card h-100 animate__animated animate__fadeInUp" style="animation-delay: ${index * 0.1}s">
                <div class="restaurant-image">
                    ${menu.photo ? 
                        `<img src="${menu.photo}" alt="${menu.name}" class="restaurant-photo">` :
                        `<i class="${menu.restaurantImage || 'fas fa-utensils'}"></i>`
                    }
                </div>
                <div class="restaurant-info">
                    <h3 class="restaurant-name">${menu.name}</h3>
                    <div class="restaurant-details">
                        <span class="restaurant-price">Rp ${menu.price.toLocaleString()}</span>
                    </div>
                    <p class="text-muted small">Tersedia di: ${menu.restaurantName}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-primary">${menu.category}</span>
                        <button class="btn btn-primary btn-sm" onclick="viewRestaurantDetail(${menu.restaurantId})">
                            Lihat Restoran
                        </button>
                    </div>
                </div>
            </div>
        `;
        menusGrid.appendChild(col);
    });
}

// HELPER FUNCTIONS
function createRestaurantCard(restaurant, index) {
    const col = document.createElement('div');
    col.className = 'col-md-6 col-lg-4';
    col.innerHTML = `
        <div class="restaurant-card h-100 animate__animated animate__fadeInUp" style="animation-delay: ${index * 0.2}s">
            <div class="restaurant-image">
                ${restaurant.photo ? 
                    `<img src="${restaurant.photo}" alt="${restaurant.name}" class="restaurant-photo">` :
                    `<i class="${restaurant.image}"></i>`
                }
            </div>
            <div class="restaurant-info">
                <h3 class="restaurant-name">${restaurant.name}</h3>
                <div class="restaurant-location">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>${restaurant.location}</span>
                </div>
                <div class="restaurant-status status-${restaurant.status}">
                    ${getStatusText(restaurant.status)}
                </div>
                <div class="restaurant-details">
                    <span><i class="fas fa-map-marker-alt"></i> ${restaurant.distance}</span>
                    <span class="restaurant-price">${restaurant.priceRange}</span>
                </div>
                <button class="view-menu-btn" onclick="viewRestaurantDetail(${restaurant.id})">
                    <i class="fas fa-list me-2"></i>Lihat Menu
                </button>
            </div>
        </div>
    `;
    return col;
}

function getStatusText(status) {
    switch (status) {
        case 'open': return 'Buka';
        case 'closed': return 'Tutup';
        case 'soon': return 'Hampir Tutup';
        default: return 'Tutup';
    }
}

function viewRestaurantDetail(restaurantId) {
    const restaurant = allRestaurants.find(r => r.id === restaurantId);
    if (restaurant) {
        showRestaurantDetailModal(restaurant);
    }
}

function showRestaurantDetailModal(restaurant) {
    const modalTitle = document.getElementById('restaurantDetailTitle');
    const modalContent = document.getElementById('restaurantDetailContent');
    
    modalTitle.innerHTML = `<i class="fas fa-utensils me-2"></i>${restaurant.name}`;
    
    modalContent.innerHTML = `
        <div class="restaurant-detail-header mb-4">
            <div class="row">
                <div class="col-md-4">
                    <div class="restaurant-image-large text-center mb-3">
                        ${restaurant.photo ? 
                            `<img src="${restaurant.photo}" alt="${restaurant.name}" class="img-fluid rounded" style="max-height: 200px;">` :
                            `<i class="${restaurant.image}" style="font-size: 4rem; color: var(--primary);"></i>`
                        }
                    </div>
                </div>
                <div class="col-md-8">
                    <h3>${restaurant.name}</h3>
                    <div class="restaurant-location-detail">
                        <i class="fas fa-map-marker-alt"></i>
                        <span class="text-dark fw-bold">${restaurant.location}</span>
                    </div>
                    <div class="restaurant-meta mb-3">
                        <div class="meta-item">
                            <i class="fas fa-clock"></i>
                            <span>${restaurant.is24Hours ? 'Buka 24 Jam' : '10:00 - 22:00'}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-star text-warning"></i>
                            <span>${restaurant.rating} (${restaurant.views} reviews)</span>
                        </div>
                    </div>
                    <div class="restaurant-status status-${restaurant.status} d-inline-block">
                        ${getStatusText(restaurant.status)}
                    </div>
                </div>
            </div>
        </div>

        <div class="restaurant-description mb-4">
            <h5>Deskripsi</h5>
            <p>${restaurant.description}</p>
        </div>

        <div class="restaurant-menus-section">
            <h5 class="mb-3">Menu</h5>
            <div class="row g-3" id="restaurantMenus">
                ${restaurant.menus.length > 0 ? restaurant.menus.map(menu => `
                    <div class="col-md-6">
                        <div class="menu-card">
                            <div class="d-flex align-items-start">
                                ${menu.photo ? 
                                    `<img src="${menu.photo}" alt="${menu.name}" class="menu-photo me-3">` :
                                    `<div class="menu-icon me-3">
                                        <i class="fas fa-utensils"></i>
                                    </div>`
                                }
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="menu-name">${menu.name}</h6>
                                        <span class="menu-price">Rp ${menu.price.toLocaleString()}</span>
                                    </div>
                                    <p class="menu-description">${menu.description}</p>
                                    <div>
                                        <span class="badge bg-primary">${menu.category}</span>
                                        ${menu.isPopular ? '<span class="badge bg-warning ms-1">Popular</span>' : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('') : `
                    <div class="col-12 text-center">
                        <p class="text-muted">Belum ada menu untuk restoran ini</p>
                    </div>
                `}
            </div>
        </div>

        <div class="restaurant-actions-modal mt-4 pt-3">
            <div class="d-flex gap-2">
                ${restaurant.googleMaps ? `
                    <a href="${restaurant.googleMaps}" target="_blank" class="btn btn-primary flex-fill">
                        <i class="fas fa-directions me-2"></i>Petunjuk Arah
                    </a>
                ` : `
                    <button class="btn btn-primary flex-fill" onclick="showNotification('Link Google Maps belum tersedia', 'info')">
                        <i class="fas fa-directions me-2"></i>Petunjuk Arah
                    </button>
                `}
                <button class="btn btn-outline-primary" onclick="shareRestaurant('${restaurant.name}', '${restaurant.location}')">
                    <i class="fas fa-share-alt me-2"></i>Bagikan
                </button>
            </div>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('restaurantDetailModal'));
    modal.show();
}

function shareRestaurant(name, location) {
    if (navigator.share) {
        navigator.share({
            title: name,
            text: `Cek ${name} di ${location}`,
            url: window.location.href
        });
    } else {
        showNotification('Link berhasil disalin ke clipboard!', 'success');
    }
}

// FEATURE INITIALIZATION
function initializeHomeFeatures() {
    // Search functionality
    const searchButton = document.getElementById('searchButton');
    const mainSearch = document.getElementById('mainSearch');
    
    if (searchButton && mainSearch) {
        searchButton.addEventListener('click', function() {
            const searchTerm = mainSearch.value.trim();
            if (searchTerm) {
                performSearch(searchTerm);
            } else {
                showNotification('Masukkan kata pencarian', 'error');
            }
        });
        
        mainSearch.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchButton.click();
            }
        });
    }

    // Budget modal functionality
    const budgetSearch = document.getElementById('budgetSearch');
    const applyBudget = document.getElementById('applyBudget');
    const budgetChips = document.querySelectorAll('.chip');

    if (budgetSearch) {
        budgetSearch.addEventListener('click', function() {
            // Modal will be handled by Bootstrap
        });
    }

    if (applyBudget) {
        applyBudget.addEventListener('click', function() {
            const budgetAmount = document.getElementById('budgetAmount').value;
            if (budgetAmount) {
                setUserBudget(parseInt(budgetAmount));
                const modal = bootstrap.Modal.getInstance(document.getElementById('budgetModal'));
                modal.hide();
            } else {
                showNotification('Masukkan jumlah budget', 'error');
            }
        });
    }

    budgetChips.forEach(chip => {
        chip.addEventListener('click', function() {
            const budget = this.getAttribute('data-budget');
            document.getElementById('budgetAmount').value = budget;
        });
    });

    // Initialize camera functionality
    initializeCamera();
}

function initializeRestaurantSearch() {
    const restaurantSearch = document.getElementById('restaurantSearch');
    if (restaurantSearch) {
        restaurantSearch.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchRestaurants();
            }
        });
    }
}

function initializeMenuSearch() {
    const menuSearch = document.getElementById('menuSearch');
    if (menuSearch) {
        menuSearch.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchMenus();
            }
        });
    }
}

// SEARCH FUNCTIONS - ENHANCED
function performSearch(searchTerm) {
    currentSearchTerm = searchTerm;
    showNotification(`Mencari: ${searchTerm}`, 'info');
    
    // Smooth scroll to results
    const restaurantSection = document.querySelector('.section-spacing');
    if (restaurantSection) {
        restaurantSection.scrollIntoView({ behavior: 'smooth' });
    }
    
    // Filter restaurants and menus based on search term
    const filteredRestaurants = allRestaurants.filter(restaurant => 
        restaurant.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
        restaurant.description.toLowerCase().includes(searchTerm.toLowerCase()) ||
        restaurant.location.toLowerCase().includes(searchTerm.toLowerCase()) ||
        restaurant.menus.some(menu => menu.name.toLowerCase().includes(searchTerm.toLowerCase()))
    );

    // Update UI
    if (currentPage === 'home') {
        const restaurantGrid = document.getElementById('restaurantGrid');
        if (restaurantGrid) {
            restaurantGrid.innerHTML = '';
            
            if (filteredRestaurants.length > 0) {
                filteredRestaurants.forEach((restaurant, index) => {
                    const card = createRestaurantCard(restaurant, index);
                    restaurantGrid.appendChild(card);
                });
            } else {
                restaurantGrid.innerHTML = `
                    <div class="col-12 text-center">
                        <div class="alert alert-info">
                            <i class="fas fa-search fa-2x mb-3"></i>
                            <h4>Tidak ada hasil ditemukan untuk "${searchTerm}"</h4>
                            <p>Coba gunakan kata kunci yang berbeda atau gunakan fitur foto makanan</p>
                        </div>
                    </div>
                `;
            }
        }
    }
}

function searchRestaurants() {
    const searchTerm = document.getElementById('restaurantSearch').value.trim().toLowerCase();
    if (!searchTerm) {
        loadAllRestaurants();
        return;
    }

    const filtered = allRestaurants.filter(restaurant => 
        restaurant.name.toLowerCase().includes(searchTerm) ||
        restaurant.location.toLowerCase().includes(searchTerm) ||
        restaurant.description.toLowerCase().includes(searchTerm) ||
        restaurant.menus.some(menu => menu.name.toLowerCase().includes(searchTerm))
    );

    const restaurantsGrid = document.getElementById('restaurantsGrid');
    if (restaurantsGrid) {
        restaurantsGrid.innerHTML = '';
        filtered.forEach((restaurant, index) => {
            const col = document.createElement('div');
            col.className = 'col-md-6 col-lg-4';
            col.innerHTML = `
                <div class="restaurant-card h-100 animate__animated animate__fadeInUp" style="animation-delay: ${index * 0.1}s">
                    <div class="restaurant-image">
                        ${restaurant.photo ? 
                            `<img src="${restaurant.photo}" alt="${restaurant.name}" class="restaurant-photo">` :
                            `<i class="${restaurant.image}"></i>`
                        }
                    </div>
                    <div class="restaurant-info">
                        <h3 class="restaurant-name">${restaurant.name}</h3>
                        <div class="restaurant-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>${restaurant.location}</span>
                        </div>
                        <div class="restaurant-status status-${restaurant.status}">
                            ${getStatusText(restaurant.status)}
                        </div>
                        <div class="restaurant-details">
                            <span><i class="fas fa-map-marker-alt"></i> ${restaurant.distance}</span>
                            <span class="restaurant-price">${restaurant.priceRange}</span>
                        </div>
                        <p class="text-muted small">${restaurant.description}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="rating">
                                <i class="fas fa-star text-warning"></i>
                                <span>${restaurant.rating}</span>
                            </div>
                            <button class="btn btn-primary btn-sm" onclick="viewRestaurantDetail(${restaurant.id})">
                                Lihat Detail
                            </button>
                        </div>
                    </div>
                </div>
            `;
            restaurantsGrid.appendChild(col);
        });

        if (filtered.length === 0) {
            restaurantsGrid.innerHTML = `
                <div class="col-12 text-center">
                    <p class="text-muted">Tidak ada restoran yang ditemukan untuk "${searchTerm}"</p>
                </div>
            `;
        }
    }
}

function searchMenus() {
    const searchTerm = document.getElementById('menuSearch').value.trim().toLowerCase();
    if (!searchTerm) {
        loadAllMenus();
        return;
    }

    const filtered = allMenus.filter(menu => 
        menu.name.toLowerCase().includes(searchTerm) ||
        menu.category.toLowerCase().includes(searchTerm) ||
        menu.restaurantName.toLowerCase().includes(searchTerm)
    );

    const menusGrid = document.getElementById('menusGrid');
    if (menusGrid) {
        menusGrid.innerHTML = '';
        filtered.forEach((menu, index) => {
            const col = document.createElement('div');
            col.className = 'col-md-6 col-lg-4';
            col.innerHTML = `
                <div class="restaurant-card h-100 animate__animated animate__fadeInUp" style="animation-delay: ${index * 0.1}s">
                    <div class="restaurant-image">
                        ${menu.photo ? 
                            `<img src="${menu.photo}" alt="${menu.name}" class="restaurant-photo">` :
                            `<i class="${menu.restaurantImage || 'fas fa-utensils'}"></i>`
                        }
                    </div>
                    <div class="restaurant-info">
                        <h3 class="restaurant-name">${menu.name}</h3>
                        <div class="restaurant-details">
                            <span class="restaurant-price">Rp ${menu.price.toLocaleString()}</span>
                        </div>
                        <p class="text-muted small">Tersedia di: ${menu.restaurantName}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-primary">${menu.category}</span>
                            <button class="btn btn-primary btn-sm" onclick="viewRestaurantDetail(${menu.restaurantId})">
                                Lihat Restoran
                            </button>
                        </div>
                    </div>
                </div>
            `;
            menusGrid.appendChild(col);
        });

        if (filtered.length === 0) {
            menusGrid.innerHTML = `
                <div class="col-12 text-center">
                    <p class="text-muted">Tidak ada menu yang ditemukan untuk "${searchTerm}"</p>
                </div>
            `;
        }
    }
}

// CAMERA FUNCTIONALITY
function initializeCamera() {
    const cameraModal = document.getElementById('cameraModal');
    if (cameraModal) {
        cameraModal.addEventListener('show.bs.modal', function() {
            startCamera();
        });
        
        cameraModal.addEventListener('hide.bs.modal', function() {
            stopCamera();
        });
    }

    const captureBtn = document.getElementById('captureBtn');
    const retakeBtn = document.getElementById('retakeBtn');
    const uploadBtn = document.getElementById('uploadBtn');

    if (captureBtn) {
        captureBtn.addEventListener('click', capturePhoto);
    }
    if (retakeBtn) {
        retakeBtn.addEventListener('click', retakePhoto);
    }
    if (uploadBtn) {
        uploadBtn.addEventListener('click', analyzeFood);
    }
}

let cameraStream = null;

async function startCamera() {
    try {
        const video = document.getElementById('cameraPreview');
        cameraStream = await navigator.mediaDevices.getUserMedia({ 
            video: { facingMode: 'environment' } 
        });
        video.srcObject = cameraStream;
        
        // Reset UI
        document.getElementById('capturedImage').classList.add('d-none');
        document.getElementById('cameraPreview').classList.remove('d-none');
        document.getElementById('captureBtn').classList.remove('d-none');
        document.getElementById('retakeBtn').classList.add('d-none');
        document.getElementById('uploadBtn').classList.add('d-none');
        document.getElementById('foodDetectionResult').classList.add('d-none');
    } catch (error) {
        console.error('Error accessing camera:', error);
        showNotification('Tidak dapat mengakses kamera', 'error');
    }
}

function stopCamera() {
    if (cameraStream) {
        cameraStream.getTracks().forEach(track => track.stop());
        cameraStream = null;
    }
}

function capturePhoto() {
    const video = document.getElementById('cameraPreview');
    const canvas = document.getElementById('photoCanvas');
    const capturedImage = document.getElementById('capturedImage');
    
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    
    capturedImage.src = canvas.toDataURL('image/jpeg');
    capturedImage.classList.remove('d-none');
    video.classList.add('d-none');
    
    document.getElementById('captureBtn').classList.add('d-none');
    document.getElementById('retakeBtn').classList.remove('d-none');
    document.getElementById('uploadBtn').classList.remove('d-none');
}

function retakePhoto() {
    document.getElementById('capturedImage').classList.add('d-none');
    document.getElementById('cameraPreview').classList.remove('d-none');
    document.getElementById('captureBtn').classList.remove('d-none');
    document.getElementById('retakeBtn').classList.add('d-none');
    document.getElementById('uploadBtn').classList.add('d-none');
    document.getElementById('foodDetectionResult').classList.add('d-none');
}

async function analyzeFood() {
    showLoading();
    const resultDiv = document.getElementById('foodDetectionResult');
    resultDiv.classList.remove('d-none');
    resultDiv.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p>Menganalisis makanan...</p>
        </div>
    `;

    try {
        // Simulate AI food detection with realistic foods
        const foodItems = [
            { name: "Nasi Goreng", price: 25000, image: "🍛" },
            { name: "Ayam Bakar", price: 35000, image: "🍗" },
            { name: "Gado-gado", price: 20000, image: "🥗" },
            { name: "Sate Ayam", price: 30000, image: "🍢" },
            { name: "Rendang", price: 40000, image: "🍖" },
            { name: "Bakso", price: 25000, image: "🍡" },
            { name: "Mie Ayam", price: 20000, image: "🍜" },
            { name: "Martabak", price: 35000, image: "🥞" }
        ];
        
        const randomFood = foodItems[Math.floor(Math.random() * foodItems.length)];
        
        // Simulate API delay
        await new Promise(resolve => setTimeout(resolve, 2000));
        
        resultDiv.innerHTML = `
            <div class="alert alert-success">
                <h5><i class="fas fa-check-circle me-2"></i>Makanan Terdeteksi!</h5>
                <p class="mb-2">Kami mendeteksi: <strong>${randomFood.name} ${randomFood.image}</strong></p>
                <p class="mb-3">Harga estimasi: <strong>Rp ${randomFood.price.toLocaleString()}</strong></p>
                <button class="btn btn-primary btn-sm" onclick="selectDetectedFood('${randomFood.name}', ${randomFood.price})">
                    <i class="fas fa-search me-2"></i>Cari Restoran yang Menjual ${randomFood.name}
                </button>
            </div>
        `;
        
        hideLoading();
    } catch (error) {
        console.error('Error analyzing food:', error);
        resultDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Gagal menganalisis gambar. Silakan coba lagi.
            </div>
        `;
        hideLoading();
    }
}

// Global function for food detection
function selectDetectedFood(foodName, price) {
    const searchInput = document.getElementById('mainSearch');
    if (searchInput) {
        searchInput.value = foodName;
    }
    
    // Close camera modal
    const cameraModal = bootstrap.Modal.getInstance(document.getElementById('cameraModal'));
    if (cameraModal) {
        cameraModal.hide();
    }
    
    showNotification(`🔍 Mencari restoran yang menjual: ${foodName}`, 'info');
    
    // Smooth scroll to results
    setTimeout(() => {
        const restaurantSection = document.querySelector('.section-spacing');
        if (restaurantSection) {
            restaurantSection.scrollIntoView({ behavior: 'smooth' });
        }
    }, 500);
    
    // Perform search
    performSearch(foodName);
}

// LOCATION FUNCTIONALITY
function detectLocation() {
    showNotification('Mendeteksi lokasi Anda...', 'info');
    
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            async (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                try {
                    // Use simulated address for demo
                    const address = await getAddressFromCoordinates(lat, lng);
                    currentLocation = address;
                    localStorage.setItem('user_location', currentLocation);
                    
                    showNotification(`📍 Lokasi terdeteksi: ${address}`, 'success');
                    updateActiveFilters();
                } catch (error) {
                    console.error('Error getting address:', error);
                    // Fallback to approximate location
                    const locations = ["Jl. Sudirman No. 123, Jakarta Pusat", "Jl. Asia Afrika No. 67, Bandung", "Jl. Tunjungan No. 45, Surabaya"];
                    const randomLocation = locations[Math.floor(Math.random() * locations.length)];
                    currentLocation = randomLocation;
                    localStorage.setItem('user_location', currentLocation);
                    showNotification(`📍 Lokasi terdeteksi: ${randomLocation}`, 'success');
                    updateActiveFilters();
                }
            },
            (error) => {
                console.error('Error getting location:', error);
                showNotification('Tidak dapat mendeteksi lokasi. Pastikan izin lokasi diaktifkan.', 'error');
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 60000
            }
        );
    } else {
        showNotification('Browser tidak mendukung geolocation', 'error');
    }
}

async function getAddressFromCoordinates(lat, lng) {
    // In a real application, you would use Google Maps Geocoding API
    // For demo purposes, we'll simulate the API call
    return new Promise((resolve) => {
        setTimeout(() => {
            const addresses = [
                "Jl. Sudirman No. 123, Jakarta Pusat",
                "Jl. Asia Afrika No. 67, Bandung",
                "Jl. Tunjungan No. 45, Surabaya",
                "Jl. Legian No. 88, Kuta, Bali"
            ];
            const randomAddress = addresses[Math.floor(Math.random() * addresses.length)];
            resolve(randomAddress);
        }, 1500);
    });
}

// BUDGET FUNCTIONALITY
function setUserBudget(amount) {
    userBudget = amount;
    localStorage.setItem('user_budget', amount);
    
    showNotification(`💰 Budget diatur: Rp ${amount.toLocaleString()}`, 'success');
    
    // Smooth scroll to results
    const restaurantSection = document.querySelector('.section-spacing');
    if (restaurantSection) {
        restaurantSection.scrollIntoView({ behavior: 'smooth' });
    }
    
    loadRestaurants();
    updateActiveFilters();
}

function updateActiveFilters() {
    const activeFilters = document.getElementById('activeFilters');
    if (!activeFilters) return;

    activeFilters.innerHTML = '';

    if (userBudget) {
        const budgetFilter = document.createElement('div');
        budgetFilter.className = 'filter-tag';
        budgetFilter.innerHTML = `
            <i class="fas fa-wallet"></i>
            Budget: Rp ${userBudget.toLocaleString()}
            <button class="remove" onclick="clearBudget()">&times;</button>
        `;
        activeFilters.appendChild(budgetFilter);
    }

    if (currentLocation) {
        const locationFilter = document.createElement('div');
        locationFilter.className = 'filter-tag';
        locationFilter.innerHTML = `
            <i class="fas fa-map-marker-alt"></i>
            Lokasi: ${currentLocation}
            <button class="remove" onclick="clearLocation()">&times;</button>
        `;
        activeFilters.appendChild(locationFilter);
    }
}

function clearBudget() {
    userBudget = null;
    localStorage.removeItem('user_budget');
    loadRestaurants();
    updateActiveFilters();
    showNotification('Budget dihapus', 'info');
}

function clearLocation() {
    currentLocation = null;
    localStorage.removeItem('user_location');
    updateActiveFilters();
    showNotification('Lokasi dihapus', 'info');
}

// UTILITY FUNCTIONS
function showLoading() {
    const loadingScreen = document.getElementById('loadingScreen');
    if (loadingScreen) {
        loadingScreen.style.display = 'flex';
        loadingScreen.style.opacity = '1';
    }
}

function hideLoading() {
    const loadingScreen = document.getElementById('loadingScreen');
    if (loadingScreen) {
        loadingScreen.style.opacity = '0';
        setTimeout(() => {
            loadingScreen.style.display = 'none';
        }, 500);
    }
}

function showNotification(message, type = 'success') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.custom-notification');
    existingNotifications.forEach(notification => notification.remove());

    // Create notification element
    const notification = document.createElement('div');
    notification.className = `custom-notification alert alert-${type === 'error' ? 'danger' : type === 'info' ? 'info' : 'success'} alert-dismissible fade show`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        min-width: 300px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        border-radius: 15px;
        border: none;
    `;
    
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
            <div>${message}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

function redirectToAdmin() {
    document.getElementById('loginScreen').style.display = 'none';
    document.getElementById('mainApp').style.display = 'block';
    
    // Initialize admin dashboard
    if (typeof adminDashboard !== 'undefined') {
        adminDashboard.loadDashboard();
    } else {
        // Load admin.js if not already loaded
        const script = document.createElement('script');
        script.src = 'js/admin.js';
        script.onload = function() {
            adminDashboard.loadDashboard();
        };
        document.head.appendChild(script);
    }
}

function logout() {
    if (confirm('Apakah Anda yakin ingin logout?')) {
        localStorage.removeItem('tastepoint_user');
        localStorage.removeItem('user_budget');
        localStorage.removeItem('user_location');
        location.reload();
    }
}