@extends('layouts.admin')

@section('title', 'Tambah Menu - TastePoint')

@section('content')
<!-- Top Bar -->
<div class="top-bar">
    <div>
        <h1 class="page-title">Tambah Menu</h1>
        <p style="color: var(--text-muted); margin-top: 0.5rem;">Tambahkan menu makanan ke restoran</p>
    </div>
    <div class="top-bar-actions">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>
</div>

<!-- Form Card -->
<div class="content-card" style="max-width: 800px; margin: 0 auto;">
    <form action="{{ route('admin.menus.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Pilih Restoran -->
        <div class="form-group">
            <label for="restaurant_id" class="form-label">
                Pilih Restoran <span style="color: var(--primary);">*</span>
            </label>
            <select name="restaurant_id" id="restaurant_id" class="form-control" required>
                <option value="">-- Pilih Restoran --</option>
                @foreach($restaurants as $restaurant)
                    <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
                @endforeach
            </select>
            @error('restaurant_id')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Nama Menu -->
        <div class="form-group">
            <label for="name" class="form-label">
                Nama Menu <span style="color: var(--primary);">*</span>
            </label>
            <input type="text" name="name" id="name" class="form-control" 
                   placeholder="Contoh: Nasi Goreng Special" required>
            @error('name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Harga -->
        <div class="form-group">
            <label for="price" class="form-label">
                Harga (Rp) <span style="color: var(--primary);">*</span>
            </label>
            <input type="number" name="price" id="price" class="form-control" 
                   placeholder="Contoh: 25000" min="0" required>
            @error('price')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Kategori -->
        <div class="form-group">
            <label for="category" class="form-label">
                Kategori
            </label>
            <select name="category" id="category" class="form-control" style="color: #000 !important; background: #fff;">
                <option value="" style="color: #000;">-- Pilih Kategori --</option>
                <option value="Makanan" style="color: #000;">Makanan</option>
                <option value="Minuman" style="color: #000;">Minuman</option>
                <option value="Snack" style="color: #000;">Snack</option>
                <option value="Dessert" style="color: #000;">Dessert</option>
                <option value="Appetizer" style="color: #000;">Appetizer</option>
                <option value="Main Course" style="color: #000;">Main Course</option>
            </select>
            @error('category')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Deskripsi -->
        <div class="form-group">
            <label for="description" class="form-label">
                Deskripsi
            </label>
            <textarea name="description" id="description" class="form-control" 
                      rows="4" placeholder="Deskripsi menu..."></textarea>
            @error('description')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Foto Menu -->
        <div class="form-group">
            <label for="image" class="form-label">
                Foto Menu
            </label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*">
            <small style="color: var(--text-muted);">Format: JPG, PNG, WEBP (Max: 5MB)</small>
            @error('image')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Status -->
        <div class="form-group">
            <label for="status" class="form-label">
                Status
            </label>
            <select name="status" id="status" class="form-control">
                <option value="available">Tersedia</option>
                <option value="unavailable">Tidak Tersedia</option>
            </select>
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Menu
            </button>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--light);
}

.form-control {
    width: 100%;
    padding: 0.8rem 1rem;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid var(--card-border);
    border-radius: 10px;
    color: var(--light);
    font-size: 1rem;
    transition: var(--transition);
}

.form-control:focus {
    outline: none;
    border-color: var(--primary);
    background: rgba(255, 255, 255, 0.15);
}

.form-control::placeholder {
    color: var(--text-muted);
}

select.form-control {
    color: #000 !important;
    background: #fff !important;
}

select.form-control option {
    color: #000 !important;
    background: #fff !important;
}

textarea.form-control {
    resize: vertical;
}

.text-danger {
    color: #ef4444;
    font-size: 0.875rem;
    margin-top: 0.25rem;
    display: block;
}
</style>
@endpush