@extends('layouts.main')

@section('title', 'Home - TastePoint')

@section('content')
<!-- Navbar -->
<nav class="navbar">
    <div class="navbar-container">
        <a href="{{ route('home') }}" class="navbar-brand">
            <i class="fas fa-utensils"></i>
            <span>TastePoint</span>
        </a>
        <div class="navbar-user">
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span class="user-name">{{ Auth::user()->name }}</span>
            </div>
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                 @csrf
                <button type="submit" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="main-container">
    <!-- Search Section -->
    <div class="search-section">
        <form action="{{ route('search') }}" method="GET">
            <div class="search-box">
                <input type="text" name="query" class="search-input" 
                       placeholder="Cari restoran atau makanan..." 
                       value="{{ request('query') }}">
                <button type="submit" class="btn-search">
                    <i class="fas fa-search"></i> Cari
                </button>
            </div>
        </form>
    </div>

    <!-- Feature Icons Row -->
    <div class="features-row">
        <!-- Location Feature -->
        <div class="feature-item" onclick="initGeolocation()">
            <div class="feature-icon" style="background: rgba(255, 107, 0, 0.1);">
                <i class="fas fa-location-dot" style="color: var(--primary);"></i>
            </div>
            <div class="feature-content">
                <div class="feature-title">Lokasi</div>
                <div id="locationText" class="feature-text">Klik untuk deteksi</div>
            </div>
        </div>

        <!-- Budget Feature -->
        <div class="feature-item" onclick="toggleBudgetInput()">
            <div class="feature-icon" style="background: rgba(16, 185, 129, 0.1);">
                <i class="fas fa-wallet" style="color: var(--success);"></i>
            </div>
            <div class="feature-content">
                <div class="feature-title">Budget</div>
                <div class="feature-text">Atur budget Anda</div>
            </div>
        </div>

        <!-- Food Detection Feature -->
        <div class="feature-item" onclick="triggerFoodUpload()">
            <div class="feature-icon" style="background: rgba(59, 130, 246, 0.1);">
                <i class="fas fa-camera" style="color: var(--info);"></i>
            </div>
            <div class="feature-content">
                <div class="feature-title">Foto Makanan</div>
                <div class="feature-text">Deteksi dengan AI</div>
            </div>
        </div>
    </div>

    <!-- Budget Input (Hidden) -->
    <div id="budgetInputSection" class="budget-input-section" style="display: none;">
        <h3><i class="fas fa-wallet"></i> Cari Berdasarkan Budget</h3>
        <div class="budget-input-wrapper">
            <input type="number" id="budgetInput" placeholder="Masukkan budget (Rp)" />
            <button onclick="searchByBudget()" class="btn-budget-search">
                <i class="fas fa-search"></i> Cari
            </button>
        </div>
        <div class="budget-presets">
            <button onclick="quickBudgetSearch(25000)">25rb</button>
            <button onclick="quickBudgetSearch(50000)">50rb</button>
            <button onclick="quickBudgetSearch(100000)">100rb</button>
            <button onclick="quickBudgetSearch(200000)">200rb</button>
        </div>
        <div id="budgetResults"></div>
    </div>

    <!-- Hidden File Input -->
    <input type="file" id="foodImageInput" accept="image/*" style="display: none;" onchange="handleFoodImageUpload(event)" />

    <!-- Restaurants Grid -->
    <div class="restaurants-grid">
        @forelse($restaurants as $restaurant)
        <div class="restaurant-card" onclick="window.location.href='{{ route('restaurant.detail', $restaurant->id) }}'">
            @if($restaurant->image_url)
                <img src="{{ $restaurant->image_url }}" 
                     alt="{{ $restaurant->name }}" class="restaurant-image">
            @else
                <div class="restaurant-image-placeholder">
                    <i class="fas fa-utensils"></i>
                </div>
            @endif
            <div class="restaurant-info">
                <h3 class="restaurant-name">{{ $restaurant->name }}</h3>
                
                <div class="restaurant-meta">
                    <span><i class="fas fa-location-dot"></i> {{ Str::limit($restaurant->address, 40) }}</span>
                </div>

                @if($restaurant->opening_hours)
                <div class="restaurant-meta">
                    <span><i class="fas fa-clock"></i> {{ $restaurant->opening_hours }}</span>
                </div>
                @endif
                
                <div class="restaurant-price">
                    <i class="fas fa-money-bill-wave"></i> 
                    {{ $restaurant->price_range }}
                </div>
                
                <div class="restaurant-rating">
                    <i class="fas fa-star"></i>
                    <span>{{ number_format($restaurant->rating, 1) }}</span>
                </div>
                
                <button class="btn-detail" onclick="event.stopPropagation(); window.location.href='{{ route('restaurant.detail', $restaurant->id) }}'">
                    <i class="fas fa-info-circle"></i> Lihat Detail
                </button>
            </div>
        </div>
        @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
            <i class="fas fa-store" style="font-size: 4rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
            <h3 style="color: var(--text-muted); margin-bottom: 0.5rem;">Belum Ada Restoran</h3>
            <p style="color: var(--text-muted);">Admin belum menambahkan restoran. Silakan cek kembali nanti.</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div style="margin-top: 2rem;">
        {{ $restaurants->links() }}
    </div>
</div>

<!-- Food Detection Modal -->
<div id="foodDetectionModal">
    <div class="modal-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="color: var(--primary); margin: 0;">
                <i class="fas fa-camera"></i> Deteksi Makanan
            </h2>
            <button onclick="closeFoodDetectionModal()" style="background: transparent; border: none; color: var(--light); font-size: 1.5rem; cursor: pointer; padding: 0.5rem;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div id="foodImagePreview" style="text-align: center;"></div>
        
        <div id="detectionStatus" style="background: rgba(255, 255, 255, 0.05); padding: 1rem; border-radius: 8px; margin-bottom: 1rem; text-align: center;">
            <i class="fas fa-spinner fa-spin"></i> Mendeteksi makanan dengan Google Vision AI...
        </div>
        
        <div id="detectionResults" style="display: none; margin-top: 1rem;"></div>
        
        <div style="text-align: center; margin-top: 1.5rem;">
            <button onclick="closeFoodDetectionModal()" class="btn btn-secondary">
                <i class="fas fa-times"></i> Tutup
            </button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Features Row */
.features-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.feature-item {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--card-border);
    border-radius: 12px;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    cursor: pointer;
    transition: var(--transition);
}

.feature-item:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: var(--primary);
    transform: translateY(-2px);
}

.feature-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.feature-content {
    flex: 1;
}

.feature-title {
    font-weight: 600;
    color: var(--light);
    margin-bottom: 0.25rem;
}

.feature-text {
    font-size: 0.85rem;
    color: var(--text-muted);
}

/* Budget Input Section */
.budget-input-section {
    background: rgba(16, 185, 129, 0.05);
    border: 1px solid rgba(16, 185, 129, 0.3);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.budget-input-section h3 {
    color: var(--success);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.budget-input-wrapper {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.budget-input-wrapper input {
    flex: 1;
    padding: 0.8rem;
    background: rgba(0, 0, 0, 0.3);
    border: 1px solid var(--card-border);
    border-radius: 8px;
    color: var(--light);
    font-size: 1rem;
}

.btn-budget-search {
    padding: 0.8rem 1.5rem;
    background: var(--success);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: var(--transition);
}

.btn-budget-search:hover {
    background: #0d9488;
}

.budget-presets {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.budget-presets button {
    padding: 0.5rem 1rem;
    background: rgba(16, 185, 129, 0.1);
    border: 1px solid rgba(16, 185, 129, 0.3);
    border-radius: 6px;
    color: var(--success);
    cursor: pointer;
    transition: var(--transition);
}

.budget-presets button:hover {
    background: rgba(16, 185, 129, 0.2);
}

/* Restaurant Grid - STYLE RAPI DENGAN BORDER JELAS */
.restaurants-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
    margin: 0 auto 3rem;
}

.restaurant-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(20px);
    border: 2px solid var(--card-border); /* BORDER LEBIH TEBAL */
    border-radius: 15px;
    overflow: hidden;
    cursor: pointer;
    transition: var(--transition);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3); /* TAMBAH SHADOW */
}

.restaurant-card:hover {
    transform: translateY(-5px);
    border-color: var(--primary);
    box-shadow: 0 10px 30px rgba(255, 107, 0, 0.4);
}

.restaurant-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-bottom: 2px solid var(--card-border); /* BORDER BAWAH GAMBAR */
}

.restaurant-image-placeholder {
    width: 100%;
    height: 200px;
    background: linear-gradient(135deg, rgba(255, 107, 0, 0.2), rgba(255, 107, 0, 0.05));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: var(--text-muted);
    border-bottom: 2px solid var(--card-border);
}

.restaurant-info {
    padding: 1.2rem;
    background: rgba(0, 0, 0, 0.2); /* BACKGROUND INFO LEBIH GELAP */
}

.restaurant-name {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 0.8rem;
    color: var(--light);
}

.restaurant-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    color: var(--text-muted);
    font-size: 0.9rem;
    padding: 0.3rem 0;
}

.restaurant-price {
    color: var(--success);
    font-weight: 600;
    margin-bottom: 0.5rem;
    padding: 0.5rem;
    background: rgba(16, 185, 129, 0.1);
    border-radius: 6px;
    display: inline-block;
}

.restaurant-rating {
    color: var(--warning);
    font-weight: 600;
    margin-bottom: 1rem;
    padding: 0.5rem;
    background: rgba(245, 158, 11, 0.1);
    border-radius: 6px;
    display: inline-block;
}

.btn-detail {
    width: 100%;
    padding: 0.8rem;
    background: var(--primary);
    color: white;
    border: 2px solid var(--primary);
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: var(--transition);
}

.btn-detail:hover {
    background: transparent;
    color: var(--primary);
}

/* Food Detection Modal */
#foodDetectionModal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.9);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

#foodDetectionModal .modal-content {
    background: linear-gradient(135deg, rgba(26, 26, 46, 0.98), rgba(40, 40, 60, 0.98));
    border: 2px solid rgba(255, 107, 0, 0.3);
    border-radius: 15px;
    padding: 2rem;
    max-width: 600px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
}

/* Input Number - Remove Spinner */
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type="number"] {
    -moz-appearance: textfield;
}

/* Responsive */
@media (max-width: 768px) {
    .features-row {
        grid-template-columns: 1fr;
    }

    .restaurants-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Global Variables
let userLocation = {
    latitude: null,
    longitude: null,
    city: null,
    street: null
};

// ========================
// GEOLOCATION
// ========================
function initGeolocation() {
    if ("geolocation" in navigator) {
        document.getElementById('locationText').innerHTML = 'Mendeteksi...';
        
        navigator.geolocation.getCurrentPosition(
            (position) => {
                userLocation.latitude = position.coords.latitude;
                userLocation.longitude = position.coords.longitude;
                
                // Show coordinate first
                document.getElementById('locationText').innerHTML = 'Mendapatkan alamat...';
                
                // Reverse geocode dengan timeout
                const timeoutId = setTimeout(() => {
                    // Fallback if API takes too long
                    document.getElementById('locationText').innerHTML = 'Makassar';
                }, 5000);
                
                fetch("{{ route('update.location') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        latitude: userLocation.latitude,
                        longitude: userLocation.longitude
                    })
                })
                .then(response => response.json())
                .then(data => {
                    clearTimeout(timeoutId);
                    if (data.success) {
                        userLocation.city = data.location.city;
                        userLocation.street = data.location.street;
                        
                        document.getElementById('locationText').innerHTML = 
                            `${data.location.city}`;
                    } else {
                        document.getElementById('locationText').innerHTML = 'Makassar';
                    }
                })
                .catch(error => {
                    clearTimeout(timeoutId);
                    console.error('Location error:', error);
                    document.getElementById('locationText').innerHTML = 'Makassar';
                });
            },
            (error) => {
                console.error('Geolocation error:', error);
                document.getElementById('locationText').innerHTML = 'Izinkan akses lokasi';
            },
            { timeout: 5000, enableHighAccuracy: false }
        );
    } else {
        document.getElementById('locationText').innerHTML = 'Browser tidak support';
    }
}

// ========================
// BUDGET
// ========================
function toggleBudgetInput() {
    const section = document.getElementById('budgetInputSection');
    section.style.display = section.style.display === 'none' ? 'block' : 'none';
}

function quickBudgetSearch(amount) {
    document.getElementById('budgetInput').value = amount;
    searchByBudget();
}

function searchByBudget() {
    const budget = document.getElementById('budgetInput').value;
    
    if (!budget || budget <= 0) {
        alert('Masukkan budget yang valid');
        return;
    }
    
    fetch("{{ route('search.budget') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ budget: budget })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayBudgetResults(data);
        }
    })
    .catch(error => {
        console.error('Budget search error:', error);
        alert('Terjadi kesalahan');
    });
}

function displayBudgetResults(data) {
    const resultsDiv = document.getElementById('budgetResults');
    
    if (data.noResults) {
        resultsDiv.innerHTML = `
            <div style="text-align: center; padding: 2rem; margin-top: 1rem; background: rgba(239, 68, 68, 0.1); border-radius: 8px; border: 1px solid rgba(239, 68, 68, 0.3);">
                <i class="fas fa-info-circle" style="font-size: 2rem; color: #ef4444; margin-bottom: 0.5rem;"></i>
                <p style="color: #aaa;">Tidak ada hasil dengan budget Rp ${new Intl.NumberFormat('id-ID').format(data.budget)}</p>
            </div>
        `;
        return;
    }
    
    let html = `
        <div style="margin-top: 1.5rem; padding: 1rem; background: rgba(255, 255, 255, 0.03); border-radius: 8px; border: 1px solid var(--card-border);">
            <h4 style="color: var(--primary); margin-bottom: 1rem;">
                <i class="fas fa-check-circle"></i> 
                ${data.total_restaurants} Restoran & ${data.total_menus} Menu
            </h4>
    `;
    
    if (data.restaurants.length > 0) {
        html += '<div style="display: grid; gap: 0.5rem;">';
        data.restaurants.forEach(restaurant => {
            html += `
                <div style="background: rgba(255, 255, 255, 0.05); padding: 1rem; border-radius: 8px; border: 1px solid var(--card-border); display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h5 style="color: var(--light); margin-bottom: 0.3rem;">${restaurant.name}</h5>
                        <p style="color: #aaa; font-size: 0.85rem;">Rp ${new Intl.NumberFormat('id-ID').format(restaurant.min_price)} - ${new Intl.NumberFormat('id-ID').format(restaurant.max_price)}</p>
                    </div>
                    <a href="/restaurant/${restaurant.id}" class="btn btn-sm btn-primary" style="padding: 0.5rem 1rem; text-decoration: none;">Detail</a>
                </div>
            `;
        });
        html += '</div>';
    }
    
    html += '</div>';
    resultsDiv.innerHTML = html;
}

// ========================
// FOOD DETECTION
// ========================
function triggerFoodUpload() {
    document.getElementById('foodImageInput').click();
}

function handleFoodImageUpload(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    if (!file.type.match('image.*')) {
        alert('File harus berupa gambar');
        return;
    }
    
    if (file.size > 5 * 1024 * 1024) {
        alert('Ukuran file maksimal 5MB');
        return;
    }
    
    // Show preview
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('foodImagePreview').innerHTML = `
            <img src="${e.target.result}" style="max-width: 300px; max-height: 300px; border-radius: 10px; margin: 1rem 0; border: 2px solid var(--card-border);" />
        `;
    };
    reader.readAsDataURL(file);
    
    // Show modal
    document.getElementById('foodDetectionModal').style.display = 'flex';
    
    // Detect
    detectFoodFromImage(file);
}

function detectFoodFromImage(file) {
    const formData = new FormData();
    formData.append('food_image', file);
    
    document.getElementById('detectionStatus').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mendeteksi makanan dengan Google Vision AI...';
    document.getElementById('detectionResults').style.display = 'none';
    
    fetch("{{ route('detect.food') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayFoodDetectionResults(data);
        } else {
            document.getElementById('detectionStatus').innerHTML = `
                <i class="fas fa-times-circle" style="color: #ef4444;"></i> 
                ${data.message || 'Gagal mendeteksi makanan'}
            `;
        }
    })
    .catch(error => {
        console.error('Food detection error:', error);
        document.getElementById('detectionStatus').innerHTML = `
            <i class="fas fa-times-circle" style="color: #ef4444;"></i> 
            Terjadi kesalahan saat mendeteksi
        `;
    });
}

function displayFoodDetectionResults(data) {
    document.getElementById('detectionStatus').innerHTML = `
        <i class="fas fa-check-circle" style="color: #10b981;"></i> 
        <strong>Terdeteksi: ${data.detected_food}</strong>
    `;
    
    const resultsDiv = document.getElementById('detectionResults');
    resultsDiv.style.display = 'block';
    
    if (data.total_found === 0) {
        resultsDiv.innerHTML = `
            <p style="text-align: center; color: #aaa; padding: 2rem; background: rgba(255, 255, 255, 0.03); border-radius: 8px; border: 1px solid var(--card-border);">
                <i class="fas fa-info-circle"></i><br/>
                Tidak ditemukan restoran yang menyediakan <strong>${data.detected_food}</strong>
            </p>
        `;
        return;
    }
    
    let html = `<h4 style="color: var(--primary); margin-bottom: 1rem;">Ditemukan ${data.total_found} hasil:</h4>`;
    
    if (data.restaurants.length > 0) {
        html += '<div style="display: grid; gap: 1rem;">';
        data.restaurants.forEach(restaurant => {
            html += `
                <div style="background: rgba(255, 255, 255, 0.05); padding: 1rem; border-radius: 10px; border: 2px solid var(--card-border);">
                    <h5 style="color: var(--light); margin-bottom: 0.5rem;">${restaurant.name}</h5>
                    <p style="color: #aaa; font-size: 0.9rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-map-marker-alt"></i> ${restaurant.address}
                    </p>
                    <a href="/restaurant/${restaurant.id}" class="btn btn-primary btn-sm" style="text-decoration: none;">Lihat Detail</a>
                </div>
            `;
        });
        html += '</div>';
    }
    
    resultsDiv.innerHTML = html;
}

function closeFoodDetectionModal() {
    document.getElementById('foodDetectionModal').style.display = 'none';
    document.getElementById('foodImageInput').value = '';
    document.getElementById('foodImagePreview').innerHTML = '';
    document.getElementById('detectionResults').innerHTML = '';
}
</script>
@endpush