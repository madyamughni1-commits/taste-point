@extends('layouts.app')

@section('title', 'Home - TastePoint')

@section('content')
<div class="main-app">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-utensils me-2"></i>TastePoint
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}
                </span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-sign-out-alt me-1"></i>Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5">
        <!-- Search Bar -->
        <div class="row mb-4">
            <div class="col-md-8 mx-auto">
                <div class="input-group">
                    <input type="text" class="form-control form-control-lg" id="searchInput" placeholder="Cari restoran atau makanan...">
                    <button class="btn btn-primary" type="button" id="searchBtn">
                        <i class="fas fa-search me-2"></i>Cari
                    </button>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mb-4">
            <div class="col-md-8 mx-auto">
                <div class="d-flex gap-2 flex-wrap justify-content-center">
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#budgetModal">
                        <i class="fas fa-wallet me-2"></i>Atur Budget
                    </button>
                    <button class="btn btn-outline-success" id="detectLocationBtn">
                        <i class="fas fa-map-marker-alt me-2"></i>Deteksi Lokasi
                    </button>
                    <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#cameraModal">
                        <i class="fas fa-camera me-2"></i>Foto Makanan
                    </button>
                </div>
            </div>
        </div>

        <!-- Restaurants List -->
        <div class="row" id="restaurantsList">
            @foreach($restaurants as $restaurant)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card bg-dark text-light h-100">
                    <img src="{{ $restaurant->photo }}" class="card-img-top" alt="{{ $restaurant->name }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $restaurant->name }}</h5>
                        <p class="card-text">
                            <small><i class="fas fa-map-marker-alt me-1"></i>{{ $restaurant->distance }}</small><br>
                            <small><i class="fas fa-money-bill me-1"></i>{{ $restaurant->price_range }}</small>
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-primary">
                                <i class="fas fa-star me-1"></i>{{ $restaurant->rating }}
                            </span>
                            <button class="btn btn-sm btn-outline-primary" onclick="viewRestaurant({{ $restaurant->id }})">
                                Lihat Detail
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Budget Modal -->
<div class="modal fade" id="budgetModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header border-secondary">
                <h5 class="modal-title"><i class="fas fa-wallet me-2"></i>Atur Budget</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="number" class="form-control" id="budgetAmount" placeholder="Masukkan budget (Rp)">
                <div class="mt-3">
                    <button class="btn btn-outline-primary btn-sm me-2" onclick="setBudget(25000)">25K</button>
                    <button class="btn btn-outline-primary btn-sm me-2" onclick="setBudget(50000)">50K</button>
                    <button class="btn btn-outline-primary btn-sm me-2" onclick="setBudget(75000)">75K</button>
                    <button class="btn btn-outline-primary btn-sm" onclick="setBudget(100000)">100K</button>
                </div>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="applyBudgetBtn">Terapkan</button>
            </div>
        </div>
    </div>
</div>

<!-- Camera Modal -->
<div class="modal fade" id="cameraModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header border-secondary">
                <h5 class="modal-title"><i class="fas fa-camera me-2"></i>Foto Makanan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <p>Fitur deteksi makanan dengan AI akan segera hadir!</p>
                <button class="btn btn-primary" onclick="simulateDetection()">
                    <i class="fas fa-magic me-2"></i>Simulasi Deteksi
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function viewRestaurant(id) {
    fetch(`/restaurant/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Restaurant: ' + data.restaurant.name + '\nRating: ' + data.restaurant.rating);
            }
        });
}

function setBudget(amount) {
    document.getElementById('budgetAmount').value = amount;
}

document.getElementById('applyBudgetBtn')?.addEventListener('click', function() {
    const budget = document.getElementById('budgetAmount').value;
    if (budget) {
        alert('Budget diatur: Rp ' + parseInt(budget).toLocaleString());
        bootstrap.Modal.getInstance(document.getElementById('budgetModal')).hide();
    }
});

document.getElementById('searchBtn')?.addEventListener('click', function() {
    const query = document.getElementById('searchInput').value;
    if (query) {
        fetch(`/search?query=${query}`)
            .then(response => response.json())
            .then(data => {
                alert(`Ditemukan ${data.count} restoran`);
            });
    }
});

function simulateDetection() {
    alert('Makanan terdeteksi: Nasi Goreng!\nMencari restoran...');
}
</script>
@endpush
