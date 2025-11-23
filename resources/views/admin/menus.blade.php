@extends('layouts.admin')

@section('title', 'Kelola Menu - ' . $restaurant->name)

@section('content')
<!-- Top Bar -->
<div class="top-bar">
    <div>
        <h1 class="page-title">Kelola Menu - {{ $restaurant->name }}</h1>
        <p style="color: var(--text-muted); margin-top: 0.5rem;">Tambah dan kelola menu restoran</p>
    </div>
    <div class="top-bar-actions">
        <a href="{{ route('admin.restaurants.edit', $restaurant->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Restoran
        </a>
    </div>
</div>

<!-- Alert Messages -->
@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

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

<!-- Add New Menu Form -->
<div class="content-card">
    <div class="content-card-header">
        <h2 class="content-card-title">
            <i class="fas fa-plus"></i> Tambah Menu Baru
        </h2>
    </div>

    <form action="{{ route('admin.menus.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                        Nama Menu <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="Contoh: Nasi Goreng Spesial"
                           style="width: 100%; padding: 0.8rem 1rem; background: rgba(255, 255, 255, 0.1); border: 1px solid var(--card-border); border-radius: 10px; color: var(--light);">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                        Harga (Rp) <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="number" name="price" value="{{ old('price') }}" required min="0" step="1000"
                           placeholder="25000"
                           style="width: 100%; padding: 0.8rem 1rem; background: rgba(255, 255, 255, 0.1); border: 1px solid var(--card-border); border-radius: 10px; color: var(--light);">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                        Kategori
                    </label>
                    <select name="category"
                            style="width: 100%; padding: 0.8rem 1rem; background: rgba(255, 255, 255, 0.1); border: 1px solid var(--card-border); border-radius: 10px; color: var(--light);">
                        <option value="">Pilih Kategori</option>
                        <option value="Makanan Utama" {{ old('category') == 'Makanan Utama' ? 'selected' : '' }}>Makanan Utama</option>
                        <option value="Minuman" {{ old('category') == 'Minuman' ? 'selected' : '' }}>Minuman</option>
                        <option value="Dessert" {{ old('category') == 'Dessert' ? 'selected' : '' }}>Dessert</option>
                        <option value="Appetizer" {{ old('category') == 'Appetizer' ? 'selected' : '' }}>Appetizer</option>
                        <option value="Paket Hemat" {{ old('category') == 'Paket Hemat' ? 'selected' : '' }}>Paket Hemat</option>
                    </select>
                </div>
            </div>

            <div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                        Deskripsi
                    </label>
                    <textarea name="description" rows="3"
                              placeholder="Deskripsi menu..."
                              style="width: 100%; padding: 0.8rem 1rem; background: rgba(255, 255, 255, 0.1); border: 1px solid var(--card-border); border-radius: 10px; color: var(--light); resize: vertical;">{{ old('description') }}</textarea>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                        Foto Menu
                    </label>
                    <input type="file" name="image" accept="image/*" onchange="previewNewMenuImage(event)"
                           style="width: 100%; padding: 0.8rem 1rem; background: rgba(255, 255, 255, 0.1); border: 1px solid var(--card-border); border-radius: 10px; color: var(--light);">
                    <div id="newMenuPreview" style="margin-top: 1rem; display: none;">
                        <img id="newMenuPreviewImg" src="" alt="Preview" 
                             style="max-width: 200px; border-radius: 10px; border: 2px solid var(--card-border);">
                    </div>
                </div>
            </div>
        </div>

        <div style="text-align: right; padding-top: 1rem; border-top: 1px solid var(--card-border);">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Menu
            </button>
        </div>
    </form>
</div>

<!-- Existing Menus List -->
<div class="content-card" style="margin-top: 2rem;">
    <div class="content-card-header">
        <h2 class="content-card-title">
            <i class="fas fa-list"></i> Daftar Menu ({{ $menus->count() }})
        </h2>
    </div>

    @if($menus->count() > 0)
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
            @foreach($menus as $menu)
            <div class="menu-card" style="background: rgba(255, 255, 255, 0.05); border: 1px solid var(--card-border); border-radius: 15px; overflow: hidden;">
                <!-- Menu Image -->
                @if($menu->image)
                <img src="{{ asset('storage/' . $menu->image) }}" 
                     alt="{{ $menu->name }}" 
                     style="width: 100%; height: 200px; object-fit: cover;">
                @else
                <div style="width: 100%; height: 200px; background: rgba(255, 255, 255, 0.05); display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-image" style="font-size: 3rem; color: var(--text-muted);"></i>
                </div>
                @endif

                <!-- Menu Info -->
                <div style="padding: 1.5rem;">
                    @if($menu->category)
                    <span style="background: rgba(255, 107, 0, 0.2); color: var(--primary); padding: 0.3rem 0.8rem; border-radius: 6px; font-size: 0.85rem; display: inline-block; margin-bottom: 0.5rem;">
                        {{ $menu->category }}
                    </span>
                    @endif

                    <h3 style="color: var(--light); margin-bottom: 0.5rem; font-size: 1.2rem;">{{ $menu->name }}</h3>
                    
                    @if($menu->description)
                    <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1rem; line-height: 1.4;">
                        {{ Str::limit($menu->description, 80) }}
                    </p>
                    @endif

                    <div style="color: var(--primary); font-weight: 600; font-size: 1.2rem; margin-bottom: 1rem;">
                        Rp {{ number_format($menu->price, 0, ',', '.') }}
                    </div>

                    <!-- Edit Form (Hidden by default) -->
                    <div id="editForm{{ $menu->id }}" style="display: none; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--card-border);">
                        <form action="{{ route('admin.menus.update', $menu->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div style="margin-bottom: 1rem;">
                                <input type="text" name="name" value="{{ $menu->name }}" required
                                       style="width: 100%; padding: 0.6rem; background: rgba(255, 255, 255, 0.1); border: 1px solid var(--card-border); border-radius: 8px; color: var(--light);">
                            </div>

                            <div style="margin-bottom: 1rem;">
                                <input type="number" name="price" value="{{ $menu->price }}" required min="0" step="1000"
                                       style="width: 100%; padding: 0.6rem; background: rgba(255, 255, 255, 0.1); border: 1px solid var(--card-border); border-radius: 8px; color: var(--light);">
                            </div>

                            <div style="margin-bottom: 1rem;">
                                <select name="category"
                                        style="width: 100%; padding: 0.6rem; background: rgba(255, 255, 255, 0.1); border: 1px solid var(--card-border); border-radius: 8px; color: var(--light);">
                                    <option value="">Pilih Kategori</option>
                                    <option value="Makanan Utama" {{ $menu->category == 'Makanan Utama' ? 'selected' : '' }}>Makanan Utama</option>
                                    <option value="Minuman" {{ $menu->category == 'Minuman' ? 'selected' : '' }}>Minuman</option>
                                    <option value="Dessert" {{ $menu->category == 'Dessert' ? 'selected' : '' }}>Dessert</option>
                                    <option value="Appetizer" {{ $menu->category == 'Appetizer' ? 'selected' : '' }}>Appetizer</option>
                                    <option value="Paket Hemat" {{ $menu->category == 'Paket Hemat' ? 'selected' : '' }}>Paket Hemat</option>
                                </select>
                            </div>

                            <div style="margin-bottom: 1rem;">
                                <textarea name="description" rows="2"
                                          style="width: 100%; padding: 0.6rem; background: rgba(255, 255, 255, 0.1); border: 1px solid var(--card-border); border-radius: 8px; color: var(--light); resize: vertical;">{{ $menu->description }}</textarea>
                            </div>

                            <div style="margin-bottom: 1rem;">
                                <label style="color: var(--text-muted); font-size: 0.85rem; display: block; margin-bottom: 0.3rem;">Ganti Foto (opsional)</label>
                                <input type="file" name="image" accept="image/*"
                                       style="width: 100%; padding: 0.6rem; background: rgba(255, 255, 255, 0.1); border: 1px solid var(--card-border); border-radius: 8px; color: var(--light); font-size: 0.85rem;">
                            </div>

                            <div style="display: flex; gap: 0.5rem;">
                                <button type="submit" class="btn btn-success btn-sm" style="flex: 1;">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="toggleEdit({{ $menu->id }})" style="flex: 1;">
                                    <i class="fas fa-times"></i> Batal
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Action Buttons -->
                    <div id="actionButtons{{ $menu->id }}" style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                        <button onclick="toggleEdit({{ $menu->id }})" class="btn btn-secondary btn-sm" style="flex: 1;">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <form action="{{ route('admin.menus.delete', $menu->id) }}" method="POST" 
                              onsubmit="return confirm('Yakin ingin menghapus menu ini?')" style="flex: 1;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" style="width: 100%;">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div style="text-align: center; padding: 3rem; color: var(--text-muted);">
            <i class="fas fa-utensils" style="font-size: 3rem; margin-bottom: 1rem;"></i>
            <p>Belum ada menu. Tambahkan menu pertama di atas!</p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function previewNewMenuImage(event) {
    const preview = document.getElementById('newMenuPreviewImg');
    const previewContainer = document.getElementById('newMenuPreview');
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

function toggleEdit(menuId) {
    const editForm = document.getElementById('editForm' + menuId);
    const actionButtons = document.getElementById('actionButtons' + menuId);
    
    if (editForm.style.display === 'none') {
        editForm.style.display = 'block';
        actionButtons.style.display = 'none';
    } else {
        editForm.style.display = 'none';
        actionButtons.style.display = 'flex';
    }
}
</script>
@endpush

@push('styles')
<style>
.menu-card {
    transition: var(--transition);
}

.menu-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(255, 107, 0, 0.2);
    border-color: var(--primary);
}
select[name="category"],
select[name="category"] option {
    color: #000 !important;
    background: #fff !important;
}
</style>
@endpush