@extends('layouts.admin')

@section('title', 'Edit Restoran - TastePoint')

@section('content')
<!-- Top Bar -->
<div class="top-bar">
    <div>
        <h1 class="page-title">Edit Restoran</h1>
        <p style="color: var(--text-muted); margin-top: 0.5rem;">Update informasi restoran</p>
    </div>
    <div class="top-bar-actions">
        <a href="{{ route('admin.restaurants') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<!-- Alert Messages -->
@if($errors->any())
    <div class="alert alert-danger">
        <strong><i class="fas fa-exclamation-circle"></i> Terdapat kesalahan:</strong>
        <ul style="margin: 0.5rem 0 0 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Form -->
<div class="content-card">
    <!-- Current Image -->
    @if($restaurant->image)
    <div style="margin-bottom: 2rem;">
        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Foto Saat Ini</label>
        <div style="position: relative; display: inline-block;">
            <img src="{{ asset('storage/' . $restaurant->image) }}" 
                 alt="{{ $restaurant->name }}" 
                 style="max-width: 300px; border-radius: 10px; border: 2px solid var(--card-border);">
            <form action="{{ route('admin.restaurants.deleteImage', $restaurant->id) }}" method="POST" 
                  style="margin-top: 0.5rem;" onsubmit="return confirm('Yakin ingin menghapus foto?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash"></i> Hapus Foto
                </button>
            </form>
        </div>
    </div>
    @endif

    <form action="{{ route('admin.restaurants.update', $restaurant->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <!-- Left Column -->
            <div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                        Nama Restoran <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $restaurant->name) }}" required
                           style="width: 100%; padding: 0.8rem 1rem; background: rgba(255, 255, 255, 0.1); border: 1px solid var(--card-border); border-radius: 10px; color: var(--light);">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                        Alamat Lengkap <span style="color: var(--danger);">*</span>
                    </label>
                    <textarea name="address" rows="3" required
                              style="width: 100%; padding: 0.8rem 1rem; background: rgba(255, 255, 255, 0.1); border: 1px solid var(--card-border); border-radius: 10px; color: var(--light); resize: vertical;">{{ old('address', $restaurant->address) }}</textarea>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                        Nomor Telepon <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="text" name="phone" value="{{ old('phone', $restaurant->phone) }}" required
                           style="width: 100%; padding: 0.8rem 1rem; background: rgba(255, 255, 255, 0.1); border: 1px solid var(--card-border); border-radius: 10px; color: var(--light);">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                        Harga Minimum (Rp) <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="number" name="min_price" value="{{ old('min_price', $restaurant->min_price) }}" required min="0" step="1000"
                           style="width: 100%; padding: 0.8rem 1rem; background: rgba(255, 255, 255, 0.1); border: 1px solid var(--card-border); border-radius: 10px; color: var(--light);">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                        Harga Maximum (Rp) <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="number" name="max_price" value="{{ old('max_price', $restaurant->max_price) }}" required min="0" step="1000"
                           style="width: 100%; padding: 0.8rem 1rem; background: rgba(255, 255, 255, 0.1); border: 1px solid var(--card-border); border-radius: 10px; color: var(--light);">
                </div>
            </div>

            <!-- Right Column -->
            <div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                        Jam Buka
                    </label>
                    <input type="text" name="opening_hours" value="{{ old('opening_hours', $restaurant->opening_hours) }}"
                           placeholder="08:00 - 22:00"
                           style="width: 100%; padding: 0.8rem 1rem; background: rgba(255, 255, 255, 0.1); border: 1px solid var(--card-border); border-radius: 10px; color: var(--light);">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                        Hari Buka
                    </label>
                    <input type="text" name="opening_days" value="{{ old('opening_days', $restaurant->opening_days) }}"
                           placeholder="Senin - Minggu"
                           style="width: 100%; padding: 0.8rem 1rem; background: rgba(255, 255, 255, 0.1); border: 1px solid var(--card-border); border-radius: 10px; color: var(--light);">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                        Rating (0-5)
                    </label>
                    <input type="number" name="rating" value="{{ old('rating', $restaurant->rating) }}" min="0" max="5" step="0.1"
                           style="width: 100%; padding: 0.8rem 1rem; background: rgba(255, 255, 255, 0.1); border: 1px solid var(--card-border); border-radius: 10px; color: var(--light);">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                        Link Google Maps
                    </label>
                    <input type="url" name="google_maps_link" value="{{ old('google_maps_link', $restaurant->google_maps_link) }}"
                           placeholder="https://maps.google.com/..."
                           style="width: 100%; padding: 0.8rem 1rem; background: rgba(255, 255, 255, 0.1); border: 1px solid var(--card-border); border-radius: 10px; color: var(--light);">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                        Latitude
                    </label>
                    <input type="number" name="latitude" value="{{ old('latitude', $restaurant->latitude) }}" step="0.0000001"
                           style="width: 100%; padding: 0.8rem 1rem; background: rgba(255, 255, 255, 0.1); border: 1px solid var(--card-border); border-radius: 10px; color: var(--light);">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                        Longitude
                    </label>
                    <input type="number" name="longitude" value="{{ old('longitude', $restaurant->longitude) }}" step="0.0000001"
                           style="width: 100%; padding: 0.8rem 1rem; background: rgba(255, 255, 255, 0.1); border: 1px solid var(--card-border); border-radius: 10px; color: var(--light);">
                </div>
            </div>
        </div>

        <!-- Full Width Fields -->
        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                Deskripsi Restoran
            </label>
            <textarea name="description" rows="4"
                      style="width: 100%; padding: 0.8rem 1rem; background: rgba(255, 255, 255, 0.1); border: 1px solid var(--card-border); border-radius: 10px; color: var(--light); resize: vertical;">{{ old('description', $restaurant->description) }}</textarea>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                Foto Restoran Baru (Opsional)
            </label>
            <input type="file" name="image" accept="image/*" onchange="previewImage(event)"
                   style="width: 100%; padding: 0.8rem 1rem; background: rgba(255, 255, 255, 0.1); border: 1px solid var(--card-border); border-radius: 10px; color: var(--light);">
            <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.5rem;">
                <i class="fas fa-info-circle"></i> Upload foto baru jika ingin mengganti. Kosongkan jika tidak ingin mengubah foto.
            </p>
            
            <!-- Image Preview -->
            <div id="imagePreview" style="margin-top: 1rem; display: none;">
                <img id="preview" src="" alt="Preview" 
                     style="max-width: 300px; border-radius: 10px; border: 2px solid var(--card-border);">
            </div>
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 1rem; justify-content: flex-end; padding-top: 1.5rem; border-top: 1px solid var(--card-border);">
            <a href="{{ route('admin.restaurants') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Restoran
            </button>
        </div>
    </form>
</div>

<!-- Link to Manage Menus -->
<div class="content-card" style="margin-top: 2rem;">
    <div style="text-align: center; padding: 2rem;">
        <h3 style="margin-bottom: 1rem;"><i class="fas fa-utensils"></i> Kelola Menu Restoran</h3>
        <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Tambah atau edit menu untuk restoran ini</p>
        <a href="{{ route('admin.menus', $restaurant->id) }}" class="btn btn-success">
            <i class="fas fa-utensils"></i> Kelola Menu
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewImage(event) {
    const preview = document.getElementById('preview');
    const previewContainer = document.getElementById('imagePreview');
    const file = event.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        previewContainer.style.display = 'none';
    }
}
</script>
@endpush