@extends('layouts.app')

@section('page-header')
    <div class="iq-navbar-header" style="height: 220px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="flex-wrap d-flex justify-content-between align-items-center text-white">
                        <div>
                            <h1 class="text-white fw-bold mb-1">Permintaan Pengadaan Barang ATK (Beli Baru)</h1>
                            <p class="text-white mb-0" style="opacity: 0.92; max-width: 820px;">
                                Usulkan kebutuhan barang ATK yang belum ada di gudang agar dibeli langsung oleh Subbag TU dari penyedia/supplier resmi.
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
        .procure-row {
            transition: border-color 0.15s ease-in-out;
        }
        .procure-row:hover {
            border-color: #cbd5e1 !important;
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
                            <h5 class="card-title mb-0 fw-bold">Form Permintaan Pembelian Barang ke Subbag TU</h5>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Mohon perbaiki isian form:</strong>
                                <ul class="mb-0 mt-1 small">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('pengajuan.pengadaan.store') }}" method="POST" id="formProcurement">
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
                                        Keperluan / Alasan Pengadaan Barang <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="notes" class="form-control" rows="2" required
                                        placeholder="Kebutuhan perlengkapan rapat koordinasi nasional, map dokumen akreditasi, stok belum tersedia di gudang...">{{ old('notes') }}</textarea>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="mb-0 fw-bold">Daftar Barang yang Diminta untuk Dibeli ke Supplier</h5>
                                </div>
                            </div>

                            @php
                                $unitOptions = [
                                    'Buah' => 'Buah',
                                    'Dus' => 'Dus / Dos',
                                    'Rim' => 'Rim',
                                    'Pak' => 'Pak',
                                    'Kotak' => 'Kotak',
                                    'Doz' => 'Doz',
                                    'Set' => 'Set',
                                    'Roll' => 'Roll',
                                    'Slop' => 'Slop',
                                    'Pak/Slop' => 'Pak/Slop',
                                    'Buku' => 'Buku',
                                    'Tube' => 'Tube',
                                    'Box' => 'Box',
                                    'Botol' => 'Botol',
                                    'Lusin' => 'Lusin',
                                    'Lembar' => 'Lembar',
                                    'Batang' => 'Batang',
                                    'Bungkus' => 'Bungkus',
                                    'Pcs' => 'Pcs',
                                    'Unit' => 'Unit',
                                    'Meter' => 'Meter',
                                ];

                                if (isset($existingProducts)) {
                                    foreach ($existingProducts as $prodItem) {
                                        foreach ($prodItem->unit_list as $extUnit) {
                                            $extUnitTrim = trim($extUnit);
                                            if ($extUnitTrim !== '' && !isset($unitOptions[$extUnitTrim])) {
                                                $unitOptions[$extUnitTrim] = $extUnitTrim;
                                            }
                                        }
                                    }
                                }

                                $productsJsonData = $existingProducts->map(function($p) {
                                    return [
                                        'id' => $p->id,
                                        'name' => $p->name,
                                        'code' => $p->code ?? '',
                                        'category_id' => (string) $p->category_id,
                                        'category_name' => $p->category->name ?? 'ATK',
                                        'unit' => $p->unit ?: 'Buah',
                                        'available_units' => implode(',', $p->unit_list ?? [$p->unit ?: 'Buah']),
                                    ];
                                });
                            @endphp

                            <div id="procure-rows">
                                <div class="procure-row p-3 mb-3 rounded-3 border bg-light shadow-xs">
                                    {{-- Baris Filter Kategori & Nama Barang Dropdown --}}
                                    <div class="row g-2 mb-2 pb-2 border-bottom border-secondary-subtle">
                                        <div class="col-md-4 col-12">
                                            <label class="form-label small text-secondary fw-bold mb-1">
                                                Filter Kategori LIST ATK
                                            </label>
                                            <div class="category-combobox-wrapper position-relative">
                                                <div class="input-group flex-nowrap" style="height: 38px;">
                                                    <input type="text" 
                                                           class="form-control form-control-sm category-search-input bg-white text-truncate" 
                                                           placeholder="Saring kategori..." 
                                                           autocomplete="off"
                                                           style="height: 38px; font-size: 0.85rem;">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary btn-clear-category d-none px-2 d-flex align-items-center justify-content-center" title="Hapus filter kategori" style="height: 38px;">
                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                                        </svg>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split btn-toggle-category-menu px-2 d-flex align-items-center justify-content-center" title="Buka daftar pilihan kategori" style="height: 38px;">
                                                        <span class="visually-hidden">Toggle</span>
                                                    </button>
                                                </div>

                                                {{-- Hidden input keeping selected category ID --}}
                                                <input type="hidden" class="procure-cat-filter" value="">

                                                {{-- Floating dropdown menu --}}
                                                <div class="category-dropdown-menu shadow-lg rounded-3 border bg-white position-absolute start-0 w-100 mt-1 d-none" 
                                                     style="z-index: 1060; max-height: 220px; min-width: 240px; overflow-y: auto;">
                                                    <div class="p-1 category-items-list">
                                                        <div class="category-item-option py-1.5 px-2 rounded-2 small d-flex justify-content-between align-items-center active" 
                                                             data-id="" data-name="-- Semua Kategori ({{ isset($categories) ? $categories->count() : 0 }} Kategori) --">
                                                            <span>-- Semua Kategori ({{ isset($categories) ? $categories->count() : 0 }} Kategori) --</span>
                                                            <span class="badge bg-light text-secondary border" style="font-size: 0.65rem;">{{ $existingProducts->count() }} item</span>
                                                        </div>
                                                        @if (isset($categories))
                                                            @foreach ($categories as $cat)
                                                                @php
                                                                    $catCount = $existingProducts->where('category_id', $cat->id)->count();
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
                                        <div class="col-md-8 col-12">
                                            <label class="form-label small text-secondary fw-bold mb-1 d-flex justify-content-between align-items-center">
                                                <span>Nama Barang yang Diminta <span class="text-danger">*</span></span>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle procure-count-badge" style="font-size: 0.68rem;">{{ $existingProducts->count() }} barang</span>
                                            </label>
                                            <div class="product-select-dropdown-wrapper dropdown">
                                                {{-- Tombol Dropdown yang tampil persis seperti select --}}
                                                <button type="button" 
                                                        class="form-select form-select-sm product-select-trigger-btn dropdown-toggle w-100" 
                                                        data-bs-toggle="dropdown" 
                                                        data-bs-auto-close="outside"
                                                        aria-expanded="false">
                                                    <span class="product-btn-text text-truncate me-2">-- Pilih Barang yang Diminta ({{ $existingProducts->count() }} Barang) --</span>
                                                    <span class="product-select-arrow">
                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                                    </span>
                                                </button>

                                                {{-- Hidden inputs keeping selected item name & product ID for submission --}}
                                                <input type="hidden" name="item_name[]" class="item-name-input" required value="">
                                                <input type="hidden" name="product_id[]" class="item-product-id" value="">

                                                {{-- Dropdown menu dengan Quick Search di atas & List Barang Ber-scrollbar --}}
                                                <div class="dropdown-menu product-select-menu shadow-lg border rounded-3 mt-1">
                                                    <div class="p-2 border-bottom bg-light">
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text bg-white border-end-0 text-muted px-2">
                                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                                            </span>
                                                            <input type="text" 
                                                                   class="form-control border-start-0 product-quick-search" 
                                                                   placeholder="Cari nama / kode barang..." 
                                                                   autocomplete="off" 
                                                                   style="font-size: 0.82rem;">
                                                            <button type="button" class="btn btn-outline-secondary btn-clear-quick-search d-none px-2" title="Hapus pencarian">
                                                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="product-scroll-list p-1">
                                                        {{-- Rendered dynamically via JS --}}
                                                    </div>
                                                    <div class="no-product-found p-3 text-center text-muted small d-none">
                                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mb-1 text-secondary opacity-50">
                                                            <circle cx="11" cy="11" r="8"></circle>
                                                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                                        </svg>
                                                        <div>Barang tidak ditemukan</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-2 col-6">
                                            <label class="form-label fw-bold small mb-1">Jumlah <span class="text-danger">*</span></label>
                                            <input type="number" name="quantity[]" class="form-control form-control-sm text-center fw-bold" 
                                                min="1" value="1" required>
                                        </div>

                                        <div class="col-md-3 col-6">
                                            <label class="form-label fw-bold small mb-1">Satuan <span class="text-danger">*</span></label>
                                            <select name="custom_unit[]" class="form-select form-select-sm item-unit-select bg-white" required>
                                                @foreach ($unitOptions as $val => $label)
                                                    <option value="{{ $val }}" {{ $val === 'Buah' ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6 col-10">
                                            <label class="form-label fw-bold small mb-1">Spesifikasi / Merk</label>
                                            <input type="text" name="specification[]" class="form-control form-control-sm item-spec-input bg-white" 
                                                placeholder="Misal: Warna Biru / Joyko">
                                        </div>

                                        <div class="col-md-1 col-2">
                                            <label class="form-label d-none d-md-block small text-muted mb-1">&nbsp;</label>
                                            <button type="button" class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center remove-procure-row" title="Hapus baris ini" style="height: 31px;">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <button type="button" id="btn-add-procure-row" class="btn btn-success btn-sm d-inline-flex align-items-center gap-1.5 shadow-sm">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                    Tambah Barang Pengadaan Lain
                                </button>
                            </div>

                            <div class="d-flex justify-content-between align-items-center border-top pt-3">
                                <a href="{{ route('pengajuan.index') }}" class="btn btn-light border">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-primary px-4 shadow-sm d-inline-flex align-items-center gap-1.5 fw-bold">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                        <line x1="3" y1="6" x2="21" y2="6"></line>
                                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                                    </svg>
                                    Kirim Usulan Pengadaan ke Subbag TU
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
                const container = document.getElementById('procure-rows');
                const addBtn = document.getElementById('btn-add-procure-row');

                const masterProducts = @json($productsJsonData);
                const standardUnitsList = ['Buah', 'Dus', 'Rim', 'Pak', 'Kotak', 'Doz', 'Set', 'Roll', 'Slop', 'Pak/Slop', 'Buku', 'Tube', 'Box', 'Botol', 'Lusin', 'Lembar', 'Batang', 'Bungkus', 'Pcs', 'Unit', 'Meter'];

                function setRowUnitSelect(unitSelect, availableUnitsStr, defaultUnit) {
                    if (!unitSelect) return;
                    const units = (availableUnitsStr || '').split(',').map(u => u.trim()).filter(Boolean);
                    
                    unitSelect.innerHTML = '';
                    const added = new Set();

                    units.forEach(u => {
                        added.add(u.toLowerCase());
                        const opt = document.createElement('option');
                        opt.value = u;
                        opt.textContent = `${u} (Standar)`;
                        if (u.toLowerCase() === (defaultUnit || '').toLowerCase()) {
                            opt.selected = true;
                        }
                        unitSelect.appendChild(opt);
                    });

                    standardUnitsList.forEach(u => {
                        if (!added.has(u.toLowerCase())) {
                            const opt = document.createElement('option');
                            opt.value = u;
                            opt.textContent = u;
                            if (!units.length && u.toLowerCase() === (defaultUnit || 'buah').toLowerCase()) {
                                opt.selected = true;
                            }
                            unitSelect.appendChild(opt);
                        }
                    });
                }

                function attachRowEvents(row) {
                    // 1. Elemen Kategori Combobox
                    const catFilter = row.querySelector('.procure-cat-filter');
                    const catInput = row.querySelector('.category-search-input');
                    const catClearBtn = row.querySelector('.btn-clear-category');
                    const catToggleBtn = row.querySelector('.btn-toggle-category-menu');
                    const catMenu = row.querySelector('.category-dropdown-menu');
                    const catOptions = row.querySelectorAll('.category-item-option');
                    const catNoFound = row.querySelector('.no-category-found');

                    // 2. Elemen Produk Dropdown dengan Quick Search & Scrollbar
                    const nameInputHidden = row.querySelector('.item-name-input');
                    const prodIdHidden = row.querySelector('.item-product-id');
                    const prodTriggerBtn = row.querySelector('.product-select-trigger-btn');
                    const prodBtnText = row.querySelector('.product-btn-text');
                    const prodQuickSearch = row.querySelector('.product-quick-search');
                    const prodClearQuickSearch = row.querySelector('.btn-clear-quick-search');
                    const prodMenu = row.querySelector('.product-select-menu');
                    const prodList = row.querySelector('.product-scroll-list');
                    const prodNoFound = row.querySelector('.no-product-found');
                    const badge = row.querySelector('.procure-count-badge');

                    const unitSelect = row.querySelector('.item-unit-select');
                    const specInput = row.querySelector('.item-spec-input');
                    const removeBtn = row.querySelector('.remove-procure-row');

                    // Render daftar produk dalam dropdown
                    function renderProducts(query = '') {
                        if (!prodList) return;
                        const catVal = catFilter ? catFilter.value : '';
                        const q = (query || '').trim().toLowerCase();
                        const currentName = nameInputHidden ? nameInputHidden.value : '';

                        const matched = masterProducts.filter(p => {
                            const matchCat = !catVal || String(p.category_id) === String(catVal);
                            const searchText = (p.code + ' ' + p.category_name + ' ' + p.name).toLowerCase();
                            const matchQ = !q || searchText.includes(q);
                            return matchCat && matchQ;
                        });

                        if (badge) {
                            badge.textContent = `${matched.length} barang`;
                        }

                        prodList.innerHTML = '';
                        if (matched.length === 0) {
                            if (prodNoFound) prodNoFound.classList.remove('d-none');
                            return;
                        }

                        if (prodNoFound) prodNoFound.classList.add('d-none');

                        matched.forEach(p => {
                            const itemDiv = document.createElement('div');
                            const isActive = (p.name === currentName);
                            itemDiv.className = `product-select-option py-2 px-2.5 rounded-2 d-flex flex-column gap-1 ${isActive ? 'active' : ''}`;
                            itemDiv.dataset.id = p.id;
                            itemDiv.dataset.name = p.name;

                            const uText = p.available_units.split(',').join('/');
                            itemDiv.innerHTML = `
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <span class="product-item-title fw-semibold text-dark text-truncate" style="font-size: 0.85rem;" title="${p.name}">${p.name}</span>
                                    <span class="badge bg-light text-secondary border flex-shrink-0" style="font-size: 0.68rem;">Satuan: ${uText}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2 small text-muted" style="font-size: 0.74rem;">
                                    <span class="badge bg-light text-secondary border font-monospace">[${p.code || '-'}]</span>
                                    <span class="text-truncate">${p.category_name}</span>
                                </div>
                            `;

                            itemDiv.addEventListener('click', function(e) {
                                e.stopPropagation();
                                selectProduct(p);
                            });

                            prodList.appendChild(itemDiv);
                        });
                    }

                    // Fungsi Memilih Produk
                    function selectProduct(p) {
                        if (p) {
                            if (nameInputHidden) nameInputHidden.value = p.name;
                            if (prodIdHidden) prodIdHidden.value = p.id;
                            if (prodBtnText) {
                                prodBtnText.textContent = `[${p.code ? p.code + ' - ' : ''}${p.category_name}] ${p.name}`;
                                prodBtnText.title = p.name;
                            }
                            setRowUnitSelect(unitSelect, p.available_units, p.unit);
                        } else {
                            if (nameInputHidden) nameInputHidden.value = '';
                            if (prodIdHidden) prodIdHidden.value = '';
                            const catVal = catFilter ? catFilter.value : '';
                            const matchedCount = masterProducts.filter(item => !catVal || String(item.category_id) === String(catVal)).length;
                            if (prodBtnText) {
                                prodBtnText.textContent = `-- Pilih Barang yang Diminta (${matchedCount} Barang) --`;
                                prodBtnText.title = '';
                            }
                            setRowUnitSelect(unitSelect, '', 'Buah');
                        }

                        // Tutup Dropdown
                        if (prodTriggerBtn && window.bootstrap && bootstrap.Dropdown) {
                            const bsDrop = bootstrap.Dropdown.getInstance(prodTriggerBtn) || new bootstrap.Dropdown(prodTriggerBtn);
                            bsDrop.hide();
                        }

                        if (nameInputHidden) {
                            nameInputHidden.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    }

                    row._selectProduct = selectProduct;

                    // Event Listeners pada Quick Search & Trigger Dropdown
                    if (prodTriggerBtn) {
                        prodTriggerBtn.addEventListener('shown.bs.dropdown', function() {
                            renderProducts(prodQuickSearch ? prodQuickSearch.value : '');
                            if (prodQuickSearch) {
                                setTimeout(() => prodQuickSearch.focus(), 50);
                            }
                        });
                    }

                    if (prodQuickSearch) {
                        prodQuickSearch.addEventListener('input', function() {
                            const val = this.value;
                            if (prodClearQuickSearch) {
                                if (val) prodClearQuickSearch.classList.remove('d-none');
                                else prodClearQuickSearch.classList.add('d-none');
                            }
                            renderProducts(val);
                        });

                        prodQuickSearch.addEventListener('keydown', function(e) {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                const firstOption = prodList.querySelector('.product-select-option');
                                if (firstOption) {
                                    const pId = firstOption.dataset.id;
                                    const p = masterProducts.find(item => String(item.id) === String(pId));
                                    if (p) selectProduct(p);
                                }
                            } else if (e.key === 'Escape') {
                                if (prodTriggerBtn && window.bootstrap && bootstrap.Dropdown) {
                                    const bsDrop = bootstrap.Dropdown.getInstance(prodTriggerBtn) || new bootstrap.Dropdown(prodTriggerBtn);
                                    bsDrop.hide();
                                }
                            }
                        });
                    }

                    if (prodClearQuickSearch) {
                        prodClearQuickSearch.addEventListener('click', function(e) {
                            e.stopPropagation();
                            if (prodQuickSearch) {
                                prodQuickSearch.value = '';
                                prodQuickSearch.focus();
                            }
                            this.classList.add('d-none');
                            renderProducts('');
                        });
                    }

                    // Fungsi Memilih Kategori
                    function selectCategory(catId, catName) {
                        if (catFilter) catFilter.value = catId;
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
                        catOptions.forEach(opt => {
                            if (opt.getAttribute('data-id') === catId) {
                                opt.classList.add('active');
                            } else {
                                opt.classList.remove('active');
                            }
                        });
                        if (catMenu) catMenu.classList.add('d-none');
                        catOptions.forEach(opt => opt.classList.remove('d-none'));
                        if (catNoFound) catNoFound.classList.add('d-none');

                        // Re-render list barang sesuai kategori
                        renderProducts('');

                        // Reset pilihan barang jika kategori berubah
                        const currentProdId = prodIdHidden ? prodIdHidden.value : '';
                        if (currentProdId && catId) {
                            const currentP = masterProducts.find(p => String(p.id) === String(currentProdId));
                            if (currentP && String(currentP.category_id) !== String(catId)) {
                                selectProduct(null);
                            }
                        }
                    }

                    // Expose selectCategory on row element
                    row._selectCategory = selectCategory;

                    if (catInput) {
                        catInput.addEventListener('focus', function() {
                            document.querySelectorAll('.category-dropdown-menu').forEach(m => {
                                if (m !== catMenu) m.classList.add('d-none');
                            });
                            document.querySelectorAll('.product-dropdown-menu').forEach(m => m.classList.add('d-none'));
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
                                document.querySelectorAll('.product-dropdown-menu').forEach(m => m.classList.add('d-none'));
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

                    if (removeBtn) {
                        removeBtn.addEventListener('click', function() {
                            const rows = container.querySelectorAll('.procure-row');
                            if (rows.length > 1) {
                                row.remove();
                            } else {
                                selectCategory('', '');
                                selectProduct(null);
                                row.querySelectorAll('input').forEach(input => {
                                    if (input.type === 'number') {
                                        input.value = '1';
                                    } else if (!input.classList.contains('category-search-input') && !input.classList.contains('procure-cat-filter') && !input.classList.contains('product-quick-search') && !input.classList.contains('item-name-input') && !input.classList.contains('item-product-id')) {
                                        input.value = '';
                                    }
                                });
                            }
                        });
                    }

                    // Render awal produk pada row
                    renderProducts('');
                }

                // Global click listener untuk menutup menu combobox kategori saat klik di luar
                document.addEventListener('click', function(e) {
                    if (!e.target.closest('.category-combobox-wrapper')) {
                        document.querySelectorAll('.category-dropdown-menu').forEach(m => m.classList.add('d-none'));
                    }
                });

                // Attach to initial row
                container.querySelectorAll('.procure-row').forEach(attachRowEvents);

                // Auto-fill barang jika diarahkan dari Katalog ATK (Pesan Barang Restock)
                const urlParams = new URLSearchParams(window.location.search);
                const requestedProductId = urlParams.get('product_id');
                if (requestedProductId) {
                    const firstRow = container.querySelector('.procure-row');
                    if (firstRow) {
                        const targetProd = masterProducts.find(p => String(p.id) === String(requestedProductId));
                        if (targetProd) {
                            if (typeof firstRow._selectCategory === 'function') {
                                firstRow._selectCategory(targetProd.category_id, targetProd.category_name);
                            }
                            if (typeof firstRow._selectProduct === 'function') {
                                firstRow._selectProduct(targetProd);
                            }
                        }
                    }
                }

                // Add row handler
                addBtn.addEventListener('click', function() {
                    const firstRow = container.querySelector('.procure-row');
                    const newRow = firstRow.cloneNode(true);

                    // Reset combobox kategori in new row
                    const catHidden = newRow.querySelector('.procure-cat-filter');
                    if (catHidden) catHidden.value = '';
                    const catInput = newRow.querySelector('.category-search-input');
                    if (catInput) catInput.value = '';
                    const catClearBtn = newRow.querySelector('.btn-clear-category');
                    if (catClearBtn) catClearBtn.classList.add('d-none');
                    const catMenu = newRow.querySelector('.category-dropdown-menu');
                    if (catMenu) catMenu.classList.add('d-none');

                    newRow.querySelectorAll('.category-item-option').forEach(opt => {
                        opt.classList.remove('d-none');
                        if (opt.getAttribute('data-id') === '') {
                            opt.classList.add('active');
                        } else {
                            opt.classList.remove('active');
                        }
                    });

                    // Reset dropdown produk in new row
                    const nameInputHidden = newRow.querySelector('.item-name-input');
                    if (nameInputHidden) nameInputHidden.value = '';
                    const prodIdHidden = newRow.querySelector('.item-product-id');
                    if (prodIdHidden) prodIdHidden.value = '';
                    const prodBtnText = newRow.querySelector('.product-btn-text');
                    if (prodBtnText) {
                        prodBtnText.textContent = `-- Pilih Barang yang Diminta (${masterProducts.length} Barang) --`;
                        prodBtnText.title = '';
                    }
                    const prodQuickSearch = newRow.querySelector('.product-quick-search');
                    if (prodQuickSearch) prodQuickSearch.value = '';
                    const prodClearQuickSearch = newRow.querySelector('.btn-clear-quick-search');
                    if (prodClearQuickSearch) prodClearQuickSearch.classList.add('d-none');

                    newRow.querySelectorAll('input').forEach(input => {
                        if (input.type === 'number') {
                            input.value = '1';
                        } else if (!input.classList.contains('category-search-input') && !input.classList.contains('procure-cat-filter') && !input.classList.contains('product-quick-search') && !input.classList.contains('item-name-input') && !input.classList.contains('item-product-id')) {
                            input.value = '';
                        }
                    });

                    container.appendChild(newRow);
                    const newUnitSelect = newRow.querySelector('.item-unit-select');
                    setRowUnitSelect(newUnitSelect, '', 'Buah');
                    attachRowEvents(newRow);
                });
            });
        </script>
    @endpush
@endsection
