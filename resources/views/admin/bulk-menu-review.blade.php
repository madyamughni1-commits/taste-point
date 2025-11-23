@extends('layouts.admin')

@section('title', 'Review Menu - Admin TastePoint')

@section('content')
<div class="admin-header">
    <h1><i class="fas fa-check-double"></i> Review & Konfirmasi Menu</h1>
    <p style="color: var(--text-muted); font-size: 1rem; margin-top: 0.5rem;">Review menu yang terdeteksi oleh AI sebelum menyimpan</p>
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

<div class="card">
    <div class="card-header">
        <div>
            <h3><i class="fas fa-store"></i> Restoran: {{ $restaurant->name }}</h3>
            <p style="color: var(--text-muted); margin-top: 0.5rem;">
                Total menu terdeteksi: <strong style="color: var(--primary);">{{ count($menuItems) }} menu</strong>
            </p>
        </div>
    </div>
    <div class="card-body">
        <!-- Raw Text (Collapsible) -->
        <div class="raw-text-section">
            <button type="button" class="btn btn-secondary btn-sm" onclick="toggleRawText()">
                <i class="fas fa-eye"></i> <span id="rawTextToggleLabel">Lihat Teks Asli dari OCR</span>
            </button>
            <div id="rawTextContent" style="display: none; margin-top: 1rem; padding: 1.5rem; background: rgba(0, 0, 0, 0.5); border-radius: 8px; color: var(--text-muted); font-family: 'Courier New', monospace; white-space: pre-wrap; font-size: 0.9rem; border: 1px solid var(--card-border); max-height: 300px; overflow-y: auto;">{{ $rawText }}</div>
        </div>

        <form action="{{ route('admin.bulk-menu-save') }}" method="POST" id="reviewForm" style="margin-top: 2rem;">
            @csrf
            
            <!-- Select All Checkbox -->
            <div style="margin-bottom: 1rem; padding: 1rem; background: rgba(255, 255, 255, 0.03); border-radius: 8px;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin: 0;">
                    <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" checked style="width: 18px; height: 18px; cursor: pointer;">
                    <span style="font-weight: 600; color: var(--light);">Pilih Semua Menu</span>
                    <span id="selectedCount" style="color: var(--primary); margin-left: auto;">({{ count($menuItems) }} terpilih)</span>
                </label>
            </div>

            <!-- Menu Items Table -->
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th width="50" style="text-align: center;">
                                <i class="fas fa-check-square"></i>
                            </th>
                            <th>Nama Menu</th>
                            <th>Harga</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($menuItems as $index => $item)
                        <tr>
                            <td style="text-align: center;">
                                <input type="checkbox" name="selected_items[]" value="{{ $index }}" class="menu-checkbox" checked style="width: 18px; height: 18px; cursor: pointer;">
                            </td>
                            <td>
                                <strong style="color: var(--light); font-size: 1.05rem;">{{ $item['name'] }}</strong>
                            </td>
                            <td>
                                <span class="price-badge">Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                            </td>
                            <td>
                                <span class="category-badge">{{ $item['category'] }}</span>
                            </td>
                            <td>
                                <span style="color: var(--text-muted); font-size: 0.9rem;">
                                    {{ $item['description'] ?: '-' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                <i class="fas fa-info-circle" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                                <p>Tidak ada menu terdeteksi</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons" style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end; flex-wrap: wrap;">
                <form action="{{ route('admin.bulk-menu-cancel') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </button>
                </form>
                
                <button type="submit" class="btn btn-primary" id="saveBtn">
                    <i class="fas fa-save"></i> Simpan Menu Terpilih
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.price-badge {
    display: inline-block;
    padding: 0.5rem 1rem;
    background: rgba(16, 185, 129, 0.15);
    border: 1px solid rgba(16, 185, 129, 0.4);
    border-radius: 6px;
    color: var(--success);
    font-weight: 700;
    font-size: 1rem;
}

.category-badge {
    display: inline-block;
    padding: 0.4rem 0.8rem;
    background: rgba(59, 130, 246, 0.15);
    border: 1px solid rgba(59, 130, 246, 0.4);
    border-radius: 6px;
    color: var(--info);
    font-size: 0.9rem;
    font-weight: 600;
}

.raw-text-section {
    margin-bottom: 1.5rem;
}

.admin-table tbody tr {
    transition: var(--transition);
}

.admin-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.05);
}
</style>

<script>
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.menu-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateSelectedCount();
}

function updateSelectedCount() {
    const checkedBoxes = document.querySelectorAll('.menu-checkbox:checked');
    const totalBoxes = document.querySelectorAll('.menu-checkbox');
    const selectAll = document.getElementById('selectAll');
    
    document.getElementById('selectedCount').textContent = `(${checkedBoxes.length} terpilih)`;
    
    // Update select all checkbox state
    if (checkedBoxes.length === 0) {
        selectAll.checked = false;
        selectAll.indeterminate = false;
    } else if (checkedBoxes.length === totalBoxes.length) {
        selectAll.checked = true;
        selectAll.indeterminate = false;
    } else {
        selectAll.checked = false;
        selectAll.indeterminate = true;
    }
}

// Add change listener to all checkboxes
document.querySelectorAll('.menu-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateSelectedCount);
});

function toggleRawText() {
    const content = document.getElementById('rawTextContent');
    const label = document.getElementById('rawTextToggleLabel');
    
    if (content.style.display === 'none') {
        content.style.display = 'block';
        label.innerHTML = '<i class="fas fa-eye-slash"></i> Sembunyikan Teks OCR';
    } else {
        content.style.display = 'none';
        label.innerHTML = '<i class="fas fa-eye"></i> Lihat Teks Asli dari OCR';
    }
}

// Form Submit Handler
document.getElementById('reviewForm').addEventListener('submit', function(e) {
    const checkedBoxes = document.querySelectorAll('.menu-checkbox:checked');
    
    if (checkedBoxes.length === 0) {
        e.preventDefault();
        alert('Pilih minimal 1 menu untuk disimpan!');
        return false;
    }
    
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
});

// Initialize
updateSelectedCount();
</script>
@endsection