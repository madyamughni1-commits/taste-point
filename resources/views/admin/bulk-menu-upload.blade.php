@extends('layouts.admin')

@section('title', 'Bulk Upload Menu - Admin TastePoint')

@section('content')
<div class="admin-header">
    <div>
        <h1><i class="fas fa-cloud-upload-alt"></i> Bulk Upload Menu dari Foto</h1>
        <p style="color: var(--text-muted); margin-top: 0.5rem;">Upload foto menu restoran dan AI akan otomatis mendeteksi nama menu & harga</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
    </a>
</div>

@if(session('success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
</div>
@endif

@if(session('info'))
<div class="alert alert-info">
    <i class="fas fa-info-circle"></i> {{ session('info') }}
</div>
@endif

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-camera"></i> Upload Foto Menu</h3>
        <p style="color: var(--text-muted); margin-top: 0.5rem; font-size: 0.9rem;">
            AI Claude akan mengekstrak nama menu, harga, dan kategori secara otomatis
        </p>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.bulk-menu-upload.process') }}" method="POST" enctype="multipart/form-data" id="bulkUploadForm">
            @csrf
            
            <!-- Restaurant Selection -->
            <div class="form-group">
                <label for="restaurant_id">
                    <i class="fas fa-store"></i> Pilih Restoran <span style="color: var(--danger);">*</span>
                </label>
                <select name="restaurant_id" id="restaurant_id" class="form-control" required>
                    <option value="">-- Pilih Restoran --</option>
                    @foreach($restaurants as $restaurant)
                        <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
                    @endforeach
                </select>
                @error('restaurant_id')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <!-- Menu Photo Upload -->
            <div class="form-group">
                <label for="menu_photo">
                    <i class="fas fa-image"></i> Upload Foto Menu <span style="color: var(--danger);">*</span>
                </label>
                <div class="upload-area" id="uploadArea">
                    <i class="fas fa-cloud-upload-alt" style="font-size: 3rem; color: var(--primary); margin-bottom: 1rem;"></i>
                    <p style="color: var(--light); margin-bottom: 0.5rem; font-weight: 600;">Klik atau drag & drop foto menu di sini</p>
                    <p style="color: var(--text-muted); font-size: 0.85rem;">Format: JPG, PNG (max 10MB)</p>
                    <input type="file" name="menu_photo" id="menu_photo" accept="image/*" required style="display: none;">
                </div>
                @error('menu_photo')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
                
                <!-- Image Preview -->
                <div id="imagePreview" style="display: none; margin-top: 1rem; text-align: center;">
                    <img id="previewImg" style="max-width: 100%; max-height: 400px; border-radius: 10px; border: 2px solid var(--card-border);">
                    <br>
                    <button type="button" onclick="removeImage()" class="btn btn-danger btn-sm" style="margin-top: 1rem;">
                        <i class="fas fa-trash"></i> Hapus Foto
                    </button>
                </div>
            </div>

            <!-- Tips -->
            <div class="tips-box">
                <h4><i class="fas fa-lightbulb"></i> Tips untuk hasil terbaik:</h4>
                <ul>
                    <li><strong>Pastikan foto menu jelas dan tidak blur</strong></li>
                    <li>Pencahayaan yang cukup agar teks terlihat jelas</li>
                    <li>Foto menu dalam posisi lurus (tidak miring)</li>
                    <li>Hindari bayangan atau pantulan yang menghalangi</li>
                    <li>Format menu yang direkomendasikan: <strong>Nama Menu - Harga</strong></li>
                    <li>Contoh: "Nasi Goreng Spesial - Rp 25.000" atau "Ayam Geprek 15rb"</li>
                    <li><strong>AI Claude Vision</strong> akan otomatis mendeteksi nama, harga, dan kategori</li>
                </ul>
            </div>

            <!-- Submit Button -->
            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary" id="submitBtn" style="flex: 1;">
                    <i class="fas fa-magic"></i> Ekstrak Menu dengan AI
                </button>
                <button type="reset" onclick="resetForm()" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Info Box -->
<div class="card" style="margin-top: 1.5rem;">
    <div class="card-body">
        <h4 style="color: var(--primary); margin-bottom: 1rem;">
            <i class="fas fa-info-circle"></i> Bagaimana Cara Kerjanya?
        </h4>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
            <div>
                <div style="background: rgba(255, 107, 0, 0.1); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                    <span style="color: var(--primary); font-size: 1.5rem; font-weight: bold;">1</span>
                </div>
                <h5 style="color: var(--light); margin-bottom: 0.5rem;">Upload Foto</h5>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Pilih restoran dan upload foto menu yang jelas</p>
            </div>
            <div>
                <div style="background: rgba(255, 107, 0, 0.1); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                    <span style="color: var(--primary); font-size: 1.5rem; font-weight: bold;">2</span>
                </div>
                <h5 style="color: var(--light); margin-bottom: 0.5rem;">AI Ekstrak Menu</h5>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Claude AI akan otomatis membaca dan mengekstrak menu</p>
            </div>
            <div>
                <div style="background: rgba(255, 107, 0, 0.1); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                    <span style="color: var(--primary); font-size: 1.5rem; font-weight: bold;">3</span>
                </div>
                <h5 style="color: var(--light); margin-bottom: 0.5rem;">Review & Simpan</h5>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Periksa hasil ekstraksi, edit jika perlu, lalu simpan</p>
            </div>
        </div>
    </div>
</div>

<style>
.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 2rem;
    gap: 2rem;
}

.admin-header h1 {
    color: var(--light);
    margin-bottom: 0;
}

.upload-area {
    border: 2px dashed var(--card-border);
    border-radius: 12px;
    padding: 3rem;
    text-align: center;
    cursor: pointer;
    transition: var(--transition);
    background: rgba(255, 255, 255, 0.02);
}

.upload-area:hover {
    border-color: var(--primary);
    background: rgba(255, 107, 0, 0.05);
    transform: translateY(-2px);
}

.upload-area.dragover {
    border-color: var(--primary);
    background: rgba(255, 107, 0, 0.1);
    border-style: solid;
}

.tips-box {
    background: rgba(59, 130, 246, 0.1);
    border: 1px solid rgba(59, 130, 246, 0.3);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.tips-box h4 {
    color: var(--info);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.tips-box ul {
    margin: 0;
    padding-left: 1.5rem;
    color: var(--light);
}

.tips-box li {
    margin-bottom: 0.5rem;
    line-height: 1.6;
}

@media (max-width: 768px) {
    .admin-header {
        flex-direction: column;
        align-items: stretch;
    }
    
    .upload-area {
        padding: 2rem 1rem;
    }
}
</style>

<script>
// Upload Area Click Handler
document.getElementById('uploadArea').addEventListener('click', function() {
    document.getElementById('menu_photo').click();
});

// Drag & Drop Handler
const uploadArea = document.getElementById('uploadArea');

uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.classList.add('dragover');
});

uploadArea.addEventListener('dragleave', () => {
    uploadArea.classList.remove('dragover');
});

uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('dragover');
    
    const file = e.dataTransfer.files[0];
    if (file && file.type.match('image.*')) {
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        document.getElementById('menu_photo').files = dataTransfer.files;
        previewImage(file);
    } else {
        alert('❌ File harus berupa gambar (JPG, PNG)');
    }
});

// Image Preview
document.getElementById('menu_photo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        if (!file.type.match('image.*')) {
            alert('❌ File harus berupa gambar (JPG, PNG)');
            this.value = '';
            return;
        }
        if (file.size > 10 * 1024 * 1024) {
            alert('❌ Ukuran file maksimal 10MB');
            this.value = '';
            return;
        }
        previewImage(file);
    }
});

function previewImage(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('previewImg').src = e.target.result;
        document.getElementById('imagePreview').style.display = 'block';
        document.getElementById('uploadArea').style.display = 'none';
    };
    reader.readAsDataURL(file);
}

function removeImage() {
    document.getElementById('menu_photo').value = '';
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('uploadArea').style.display = 'block';
}

function resetForm() {
    removeImage();
    document.getElementById('restaurant_id').value = '';
}

// Form Submit Handler
document.getElementById('bulkUploadForm').addEventListener('submit', function(e) {
    const restaurantId = document.getElementById('restaurant_id').value;
    const menuPhoto = document.getElementById('menu_photo').files[0];
    
    if (!restaurantId) {
        e.preventDefault();
        alert('❌ Pilih restoran terlebih dahulu');
        document.getElementById('restaurant_id').focus();
        return false;
    }
    
    if (!menuPhoto) {
        e.preventDefault();
        alert('❌ Upload foto menu terlebih dahulu');
        document.getElementById('uploadArea').click();
        return false;
    }
    
    // Show loading state
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengekstrak menu dengan AI... Mohon tunggu (bisa memakan waktu 15-30 detik)';
    
    // Optional: Add visual feedback
    document.body.style.cursor = 'wait';
});

// Prevent accidental page close during upload
let formSubmitted = false;
document.getElementById('bulkUploadForm').addEventListener('submit', function() {
    formSubmitted = true;
});

window.addEventListener('beforeunload', function(e) {
    if (formSubmitted && document.getElementById('submitBtn').disabled) {
        e.preventDefault();
        e.returnValue = 'Upload sedang berlangsung. Yakin ingin meninggalkan halaman?';
        return e.returnValue;
    }
});
</script>
@endsection