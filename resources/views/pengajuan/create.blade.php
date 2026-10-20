@extends('layouts.app')

@section('page-header')
    <div class="iq-navbar-header" style="height: 220px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="flex-wrap d-flex justify-content-between align-items-center text-white">
                        <div>
                            <h1 class="text-white fw-bold mb-1">Form Pengajuan Permintaan ATK</h1>
                            <p class="text-white mb-0" style="opacity: 0.92; max-width: 820px;">
                                Ajukan kebutuhan Alat Tulis Kantor secara resmi untuk kebutuhan operasional kerja.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .product-row {
            position: relative;
        }
        .category-combobox-wrapper {
            position: relative;
        }
        .category-dropdown-menu {
            border-color: #cbd5e1 !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05) !important;
        }
        .category-item-option {
            cursor: pointer;
            transition: background-color 0.12s ease, color 0.12s ease;
            color: #334155;
            user-select: none;
        }
        .category-item-option:hover {
            background-color: #f1f5f9;
            color: #0f172a;
        }
        .category-item-option.active {
            background-color: #eff6ff;
            color: #2563eb;
            font-weight: 600;
        }
        .category-dropdown-menu::-webkit-scrollbar {
            width: 5px;
        }
        .category-dropdown-menu::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0 fw-bold">Isi Formulir Permintaan ATK ke Subbag TU</h5>
                            <small class="text-muted">Permintaan diajukan ke Subbag Tata Usaha (Subbag TU) untuk direkapitulasi dan diverifikasi sebelum penyerahan fisik barang oleh PIC Gudang</small>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Mohon perbaiki data pengajuan:</strong>
                                <ul class="mb-0 mt-1 small">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('pengajuan.store') }}" method="POST" id="formPengajuan">
                            @csrf

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-uppercase text-secondary">
                                        Jenis Permintaan <span class="text-danger">*</span>
                                    </label>
                                    <select name="type" class="form-select" required>
                                        <option value="individu" selected>
                                            Individu (Staf / Pegawai)
                                        </option>
                                    </select>
                                    <small class="text-muted">Permohonan diajukan atas nama pegawai pemohon.</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-uppercase text-secondary">
                                        Pemohon / Unit Kerja
                                    </label>
                                    <input type="text" class="form-control bg-light" 
                                        value="{{ auth()->user()->name }} ({{ auth()->user()->position ?? 'Pegawai' }})" readonly>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold small text-uppercase text-secondary">
                                        Keperluan / Catatan Permintaan (Opsional)
                                    </label>
                                    <textarea name="notes" class="form-control" rows="2"
                                        placeholder="Kebutuhan operasional rapat koordinasi dan administrasi berkas bulan ini">{{ old('notes') }}</textarea>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="mb-0 fw-bold">Daftar Barang ATK yang Diminta</h5>
                                    <small class="text-muted">Tambahkan satu atau beberapa item ATK yang dibutuhkan</small>
                                </div>
                                <a href="{{ route('katalog-atk') }}" target="_blank" class="btn btn-sm btn-outline-info">
                                    Lihat Katalog & Stok
                                </a>
                            </div>





                            <div id="product-rows">
                                <div class="product-row mb-3 p-3 rounded-3 border bg-white shadow-xs">
                                    <div class="row g-2 align-items-start">
                                        {{-- 1. Saring Kategori ATK --}}
                                        <div class="col-xl-3 col-lg-3 col-md-3 col-12">
                                            <label class="form-label fw-bold small text-secondary mb-1 d-flex align-items-end" style="min-height: 2rem;">
                                                Saring Kategori (Opsional)
                                            </label>
                                            <div class="category-combobox-wrapper position-relative">
                                                <div class="input-group flex-nowrap" style="height: 38px;">
                                                    <input type="text" 
                                                           class="form-control category-search-input bg-white text-truncate" 
                                                           placeholder="Saring kategori..." 
                                                           autocomplete="off"
                                                           style="height: 38px; font-size: 0.875rem;">
                                                    <button type="button" class="btn btn-outline-secondary btn-clear-category d-none px-2 d-flex align-items-center justify-content-center" title="Hapus filter kategori" style="height: 38px;">
                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                                        </svg>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split btn-toggle-category-menu px-2 d-flex align-items-center justify-content-center" title="Buka daftar pilihan kategori" style="height: 38px;">
                                                        <span class="visually-hidden">Toggle</span>
                                                    </button>
                                                </div>

                                                {{-- Hidden input keeping selected category ID --}}
                                                <input type="hidden" class="category-filter-select" value="">

                                                {{-- Floating dropdown menu --}}
                                                <div class="category-dropdown-menu shadow-lg rounded-3 border bg-white position-absolute start-0 w-100 mt-1 d-none" 
                                                     style="z-index: 1060; max-height: 220px; min-width: 240px; overflow-y: auto;">
                                                    <div class="p-1 category-items-list">
                                                        <div class="category-item-option py-1.5 px-2 rounded-2 small d-flex justify-content-between align-items-center active" 
                                                             data-id="" data-name="-- Semua Kategori ATK --">
                                                            <span>-- Semua Kategori ATK ({{ isset($categories) ? $categories->count() : 0 }}) --</span>
                                                            <span class="badge bg-light text-secondary border" style="font-size: 0.65rem;">{{ $products->count() }} item</span>
                                                        </div>
                                                        @if (isset($categories))
                                                            @foreach ($categories as $cat)
                                                                @php
                                                                    $catCount = $products->where('category_id', $cat->id)->count();
                                                                @endphp
                                                                <div class="category-item-option py-1.5 px-2 rounded-2 small d-flex justify-content-between align-items-center" 
                                                                     data-id="{{ $cat->id }}" data-name="{{ $cat->name }}">
                                                                    <span>{{ $cat->name }}</span>
                                                                    <span class="badge bg-light text-secondary border" style="font-size: 0.65rem;">{{ $catCount }} item</span>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                        <div class="no-category-found p-2 text-center text-muted small d-none">
                                                            Kategori tidak ditemukan
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- 2. Pilih Barang ATK --}}
                                        <div class="col-xl-5 col-lg-4 col-md-4 col-12">
                                            <label class="form-label fw-bold small text-dark mb-1 d-flex align-items-end" style="min-height: 2rem;">
                                                Pilih Barang ATK <span class="text-danger ms-1">*</span>
                                            </label>
                                            <select name="product_id[]" class="form-select product-select" required style="height: 38px; font-size: 0.875rem;">
                                                <option value="">-- Pilih Barang dari Daftar ATK ({{ $products->count() }} Item) --</option>
                                                @foreach ($products as $product)
                                                    @php
                                                        $isSelected = (isset($selectedProductId) && $selectedProductId == $product->id);
                                                        $pUnit = $product->unit ?: 'Buah';
                                                        $unitListStr = implode(',', $product->unit_list);
                                                        $catName = $product->category->name ?? 'ATK';
                                                    @endphp
                                                    <option value="{{ $product->id }}" 
                                                        data-category-id="{{ $product->category_id }}"
                                                        data-category-name="{{ $catName }}"
                                                        data-stock="{{ $product->stock }}"
                                                        data-unit="{{ $pUnit }}"
                                                        data-available-units="{{ $unitListStr }}"
                                                        {{ $isSelected ? 'selected' : '' }}>
                                                        [{{ $product->code }} - {{ $catName }}] {{ $product->name }} — (Stok: {{ $product->stock }} {{ $pUnit }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted stock-info d-block mt-1">Pilih barang atau saring kategori di samping.</small>
                                        </div>

                                        {{-- 3. Jumlah Permintaan Stock --}}
                                        <div class="col-xl-2 col-lg-3 col-md-3 col-6">
                                            <label class="form-label fw-bold small text-dark mb-1 d-flex align-items-end" style="min-height: 2rem;">
                                                Jumlah Permintaan Stock <span class="text-danger ms-1">*</span>
                                            </label>
                                            <input type="number" name="quantity[]" class="form-control quantity-input text-center fw-bold" min="1"
                                                value="{{ $selectedQuantity ?? 1 }}" required style="height: 38px;">
                                            <input type="hidden" name="custom_unit[]" class="item-unit-input" value="">
                                        </div>

                                        {{-- 4. Tombol Hapus --}}
                                        <div class="col-xl-2 col-lg-2 col-md-2 col-6">
                                            <label class="form-label fw-bold small mb-1 d-none d-md-flex align-items-end" style="min-height: 2rem;">&nbsp;</label>
                                            <button type="button" class="btn btn-outline-danger remove-row w-100 d-inline-flex align-items-center justify-content-center" title="Hapus atau reset baris barang ini" style="height: 38px;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                </svg>
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <button type="button" id="add-row" class="btn btn-success btn-sm d-inline-flex align-items-center gap-1">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                    Tambah Barang Lain
                                </button>
                            </div>

                            <div class="d-flex justify-content-between align-items-center border-top pt-3">
                                <a href="{{ route('pengajuan.index') }}" class="btn btn-light border">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                    Kirim Pengajuan ke Kasubag TU
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const productRows = document.getElementById('product-rows');
                const addRowBtn = document.getElementById('add-row');

                // Simpan daftar semua opsi produk asli untuk keperluan pemfilteran cepat per baris
                const firstSelect = productRows.querySelector('.product-select');
                const masterOptions = firstSelect ? Array.from(firstSelect.querySelectorAll('option')).filter(o => o.value !== '').map(o => ({
                    value: o.value,
                    text: o.textContent.trim(),
                    searchText: o.textContent.toLowerCase(),
                    categoryId: o.getAttribute('data-category-id') || '',
                    categoryName: o.getAttribute('data-category-name') || '',
                    stock: o.getAttribute('data-stock') || '0',
                    unit: o.getAttribute('data-unit') || 'Buah',
                    availableUnits: o.getAttribute('data-available-units') || (o.getAttribute('data-unit') || 'Buah')
                })) : [];

                function filterRowOptions(row) {
                    const select = row.querySelector('.product-select');
                    const catFilter = row.querySelector('.category-filter-select');
                    const countBadge = row.querySelector('.filter-count-badge');

                    if (!select) return;
                    const selectedVal = select.value;
                    const catVal = catFilter ? catFilter.value : '';

                    const filtered = masterOptions.filter(item => {
                        return !catVal || item.categoryId === catVal;
                    });

                    // Rebuild options while preserving current selection if it matches or keeping it accessible
                    select.innerHTML = `<option value="">-- Pilih Barang ATK (${filtered.length} Barang Ditemukan) --</option>`;
                    let currentStillExists = false;

                    filtered.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.value;
                        opt.textContent = item.text;
                        opt.setAttribute('data-category-id', item.categoryId);
                        opt.setAttribute('data-category-name', item.categoryName);
                        opt.setAttribute('data-stock', item.stock);
                        opt.setAttribute('data-unit', item.unit);
                        opt.setAttribute('data-available-units', item.availableUnits);
                        if (item.value === selectedVal) {
                            opt.selected = true;
                            currentStillExists = true;
                        }
                        select.appendChild(opt);
                    });

                    if (!currentStillExists && selectedVal !== '') {
                        select.value = '';
                        select.dispatchEvent(new Event('change'));
                    }

                    if (countBadge) {
                        countBadge.textContent = filtered.length + ' barang';
                    }
                }

                function initRow(row) {
                    const select = row.querySelector('.product-select');
                    const qtyInput = row.querySelector('.quantity-input');
                    const unitInput = row.querySelector('.item-unit-input');
                    const stockInfo = row.querySelector('.stock-info');
                    const catHidden = row.querySelector('.category-filter-select');
                    const catInput = row.querySelector('.category-search-input');
                    const catClearBtn = row.querySelector('.btn-clear-category');
                    const catToggleBtn = row.querySelector('.btn-toggle-category-menu');
                    const catMenu = row.querySelector('.category-dropdown-menu');
                    const catOptions = row.querySelectorAll('.category-item-option');
                    const catNoFound = row.querySelector('.no-category-found');

                    function selectCategory(catId, catName) {
                        if (catHidden) catHidden.value = catId;
                        if (catInput) {
                            catInput.value = catId ? catName : '';
                        }
                        if (catClearBtn) {
                            if (catId) {
                                catClearBtn.classList.remove('d-none');
                            } else {
                                catClearBtn.classList.add('d-none');
                            }
                        }
                        // Update active class on dropdown options
                        catOptions.forEach(opt => {
                            if (opt.getAttribute('data-id') === catId) {
                                opt.classList.add('active');
                            } else {
                                opt.classList.remove('active');
                            }
                        });
                        // Hide menu & reset option visibility
                        if (catMenu) catMenu.classList.add('d-none');
                        catOptions.forEach(opt => opt.classList.remove('d-none'));
                        if (catNoFound) catNoFound.classList.add('d-none');

                        filterRowOptions(row);
                    }

                    // Expose selectCategory on row element for external/reset calls
                    row._selectCategory = selectCategory;

                    if (catInput) {
                        catInput.addEventListener('focus', function() {
                            document.querySelectorAll('.category-dropdown-menu').forEach(m => {
                                if (m !== catMenu) m.classList.add('d-none');
                            });
                            if (catMenu) catMenu.classList.remove('d-none');
                        });

                        catInput.addEventListener('input', function() {
                            const query = this.value.trim().toLowerCase();
                            if (catMenu) catMenu.classList.remove('d-none');

                            let visibleCount = 0;
                            catOptions.forEach(opt => {
                                const optName = (opt.getAttribute('data-name') || '').toLowerCase();
                                const optId = opt.getAttribute('data-id');
                                if (!optId && query === '') {
                                    opt.classList.remove('d-none');
                                    visibleCount++;
                                } else if (optId && optName.includes(query)) {
                                    opt.classList.remove('d-none');
                                    visibleCount++;
                                } else if (!optId) {
                                    opt.classList.add('d-none');
                                } else {
                                    opt.classList.add('d-none');
                                }
                            });

                            if (catNoFound) {
                                if (visibleCount === 0) {
                                    catNoFound.classList.remove('d-none');
                                } else {
                                    catNoFound.classList.add('d-none');
                                }
                            }

                            if (this.value) {
                                if (catClearBtn) catClearBtn.classList.remove('d-none');
                            } else {
                                selectCategory('', '');
                                if (catMenu) catMenu.classList.remove('d-none');
                            }
                        });

                        catInput.addEventListener('keydown', function(e) {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                const firstVisible = Array.from(catOptions).find(opt => !opt.classList.contains('d-none') && opt.getAttribute('data-id') !== '');
                                if (firstVisible) {
                                    selectCategory(firstVisible.getAttribute('data-id'), firstVisible.getAttribute('data-name'));
                                }
                            } else if (e.key === 'Escape') {
                                if (catMenu) catMenu.classList.add('d-none');
                            }
                        });
                    }

                    if (catToggleBtn) {
                        catToggleBtn.addEventListener('click', function(e) {
                            e.stopPropagation();
                            if (catMenu) {
                                const isClosed = catMenu.classList.contains('d-none');
                                document.querySelectorAll('.category-dropdown-menu').forEach(m => m.classList.add('d-none'));
                                if (isClosed) {
                                    catMenu.classList.remove('d-none');
                                    if (catInput) catInput.focus();
                                }
                            }
                        });
                    }

                    if (catClearBtn) {
                        catClearBtn.addEventListener('click', function(e) {
                            e.stopPropagation();
                            selectCategory('', '');
                            if (catInput) catInput.focus();
                        });
                    }

                    catOptions.forEach(opt => {
                        opt.addEventListener('click', function(e) {
                            e.stopPropagation();
                            const id = this.getAttribute('data-id') || '';
                            const name = this.getAttribute('data-name') || '';
                            selectCategory(id, name);
                        });
                    });

                    select.addEventListener('change', function() {
                        const opt = this.options[this.selectedIndex];
                        const stock = opt ? (parseInt(opt.getAttribute('data-stock')) || 0) : 0;
                        const unit = opt ? (opt.getAttribute('data-unit') || '') : '';

                        if (this.value && opt) {
                            if (unitInput) {
                                unitInput.value = unit;
                            }
                            qtyInput.removeAttribute('max');
                            if (stock > 0) {
                                stockInfo.innerHTML = '<span class="text-success fw-semibold">Sisa stok gudang: ' + stock + ' Stock.</span> Jika permintaan melebihi stok, kekurangan akan dibeli oleh Kasubag TU.';
                            } else {
                                stockInfo.innerHTML = '<span class="text-warning fw-semibold">Stok belum tersedia (0 Stock).</span> Permintaan ini akan otomatis diproses pengadaan oleh Kasubag TU.';
                            }
                        } else {
                            if (unitInput) {
                                unitInput.value = '';
                            }
                            qtyInput.removeAttribute('max');
                            stockInfo.textContent = 'Pilih barang atau saring kategori di samping.';
                        }
                    });

                    // Trigger if already selected
                    if (select.value) {
                        select.dispatchEvent(new Event('change'));
                    }
                }

                // Global click listener untuk menutup menu dropdown saat klik di luar
                document.addEventListener('click', function(e) {
                    if (!e.target.closest('.category-combobox-wrapper')) {
                        document.querySelectorAll('.category-dropdown-menu').forEach(m => m.classList.add('d-none'));
                    }
                });

                // Init existing rows
                productRows.querySelectorAll('.product-row').forEach(initRow);

                // Sinkronisasi kategori awal jika halaman dibuka dengan selectedProductId
                @if(isset($selectedProductId) && $selectedProductId)
                    const initialRow = productRows.querySelector('.product-row');
                    if (initialRow) {
                        const initialSelect = initialRow.querySelector('.product-select');
                        const initialCat = initialSelect?.selectedOptions[0]?.getAttribute('data-category-id');
                        const initialCatName = initialSelect?.selectedOptions[0]?.getAttribute('data-category-name');
                        if (initialCat && typeof initialRow._selectCategory === 'function') {
                            initialRow._selectCategory(initialCat, initialCatName || '');
                        }
                    }
                @endif

                addRowBtn.addEventListener('click', function() {
                    const firstRow = productRows.querySelector('.product-row');
                    const newRow = firstRow.cloneNode(true);

                    // Reset filter & values in new row
                    const catHidden = newRow.querySelector('.category-filter-select');
                    if (catHidden) catHidden.value = '';
                    const catInput = newRow.querySelector('.category-search-input');
                    if (catInput) catInput.value = '';
                    const catClearBtn = newRow.querySelector('.btn-clear-category');
                    if (catClearBtn) catClearBtn.classList.add('d-none');
                    const catMenu = newRow.querySelector('.category-dropdown-menu');
                    if (catMenu) catMenu.classList.add('d-none');

                    // Reset active states in cloned row
                    newRow.querySelectorAll('.category-item-option').forEach(opt => {
                        opt.classList.remove('d-none');
                        if (opt.getAttribute('data-id') === '') {
                            opt.classList.add('active');
                        } else {
                            opt.classList.remove('active');
                        }
                    });
                    const select = newRow.querySelector('select.product-select');
                    select.value = '';
                    const qty = newRow.querySelector('input[type="number"]');
                    qty.value = '1';
                    qty.removeAttribute('max');

                    const unitInput = newRow.querySelector('.item-unit-input');
                    if (unitInput) unitInput.value = '';

                    newRow.querySelector('.stock-info').textContent = 'Pilih barang atau saring kategori di samping.';

                    productRows.appendChild(newRow);
                    filterRowOptions(newRow);
                    initRow(newRow);
                });

                // Event delegation untuk tombol Hapus
                document.addEventListener('click', function(e) {
                    const removeBtn = e.target.closest('.remove-row');
                    if (!removeBtn) return;

                    e.preventDefault();
                    const row = removeBtn.closest('.product-row');
                    if (!row) return;

                    const allRows = productRows.querySelectorAll('.product-row');
                    if (allRows.length > 1) {
                        row.remove();
                    } else {
                        // Jika hanya 1 baris, kosongkan kembali
                        if (typeof row._selectCategory === 'function') {
                            row._selectCategory('', '');
                        }

                        const select = row.querySelector('.product-select');
                        const qty = row.querySelector('.quantity-input');
                        const unitInput = row.querySelector('.item-unit-input');

                        if (select) {
                            select.value = '';
                            select.dispatchEvent(new Event('change'));
                        }
                        if (qty) {
                            qty.value = '1';
                            qty.removeAttribute('max');
                        }
                        if (unitInput) {
                            unitInput.value = '';
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection
