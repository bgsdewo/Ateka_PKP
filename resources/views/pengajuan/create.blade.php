@extends('layouts.app')

@section('page-header')
    <div class="iq-navbar-header" style="height: 220px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="flex-wrap d-flex justify-content-between align-items-center text-white">
                        <div>
                            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2" style="background: rgba(255, 255, 255, 0.12); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.18); font-size: 0.72rem; letter-spacing: 0.05em; text-transform: uppercase; font-weight: 700;">
                                <span class="badge-dot badge-dot-success"></span>
                                <span>Kementerian PKP RI &bull; Formulir Pengajuan ATK</span>
                            </div>
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
        /* Custom Dropdown Satuan Pengajuan */
        .product-row {
            position: relative;
            overflow: visible !important;
        }
        .product-row.dropdown-active {
            z-index: 1050;
        }
        .custom-unit-dropdown {
            position: relative;
        }
        .custom-unit-dropdown.is-open {
            z-index: 1060;
        }
        .custom-unit-trigger {
            height: 38px;
            cursor: pointer;
            user-select: none;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 0.375rem 0.75rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        .custom-unit-trigger:hover {
            border-color: #94a3b8;
        }
        .custom-unit-dropdown.is-open .custom-unit-trigger {
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.15);
        }
        .custom-unit-chevron {
            transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            flex-shrink: 0;
        }
        .custom-unit-dropdown.is-open .custom-unit-chevron {
            transform: rotate(180deg);
            color: #2563eb !important;
        }
        .custom-unit-menu {
            display: none;
            position: absolute;
            top: calc(100% + 5px) !important;
            left: 0;
            right: 0;
            bottom: auto !important;
            z-index: 1070;
            min-width: 140px;
            max-height: 195px;
            overflow-y: auto;
            overscroll-behavior: contain;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 6px;
            margin: 0;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
        }
        .custom-unit-dropdown.is-open .custom-unit-menu {
            display: block;
            animation: customDropdownSlideDown 0.18s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes customDropdownSlideDown {
            from {
                opacity: 0;
                transform: translateY(-6px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .custom-unit-item {
            padding: 7px 12px;
            border-radius: 8px;
            margin-bottom: 2px;
            cursor: pointer;
            font-size: 0.86rem;
            color: #334155;
            font-weight: 500;
            transition: background-color 0.12s ease, color 0.12s ease;
            user-select: none;
        }
        .custom-unit-item:last-child {
            margin-bottom: 0;
        }
        .custom-unit-item:hover {
            background-color: #f1f5f9;
            color: #0f172a;
        }
        .custom-unit-item.active {
            background-color: #eff6ff;
            color: #1d4ed8;
            font-weight: 600;
        }
        .custom-unit-item .check-icon {
            display: none;
            flex-shrink: 0;
        }
        .custom-unit-item.active .check-icon {
            display: inline-block;
        }
        .custom-unit-menu::-webkit-scrollbar {
            width: 5px;
        }
        .custom-unit-menu::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-unit-menu::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .custom-unit-menu::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
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
                                        placeholder="Contoh: Kebutuhan operasional rapat koordinasi dan administrasi berkas bulan ini">{{ old('notes') }}</textarea>
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

                                if (isset($products)) {
                                    foreach ($products as $prodItem) {
                                        foreach ($prodItem->unit_list as $extUnit) {
                                            $extUnitTrim = trim($extUnit);
                                            if ($extUnitTrim !== '' && !isset($unitOptions[$extUnitTrim])) {
                                                $unitOptions[$extUnitTrim] = $extUnitTrim;
                                            }
                                        }
                                    }
                                }
                            @endphp

                            <div id="product-rows">
                                <div class="product-row mb-3 p-3 rounded-3 border bg-light shadow-xs">
                                    {{-- Baris Bantu Filter Kategori & Cari Cepat Barang --}}
                                    <div class="row g-2 mb-2 pb-2 border-bottom border-secondary-subtle">
                                        <div class="col-md-5 col-12">
                                            <label class="form-label small text-secondary fw-semibold mb-1">
                                                1. Saring Kategori ATK (Opsional)
                                            </label>
                                            <select class="form-select form-select-sm category-filter-select bg-white">
                                                <option value="">-- Semua Kategori ATK ({{ isset($categories) ? $categories->count() : 0 }} Kategori) --</option>
                                                @if (isset($categories))
                                                    @foreach ($categories as $cat)
                                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-7 col-12">
                                            <label class="form-label small text-secondary fw-semibold mb-1 d-flex justify-content-between">
                                                <span>2. Ketik Cari Cepat Nama / Merk / Ukuran Barang</span>
                                                <span class="badge bg-primary-subtle text-primary filter-count-badge" style="font-size: 0.7rem;">{{ $products->count() }} barang</span>
                                            </label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-white text-muted border-end-0">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <circle cx="11" cy="11" r="8"></circle>
                                                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                                    </svg>
                                                </span>
                                                <input type="text" class="form-control border-start-0 product-quick-search bg-white"
                                                    placeholder="Ketik kata kunci: misal HVS A4, Pulpen Kenko, Binder 105, Epson 003..." autocomplete="off">
                                                <button type="button" class="btn btn-outline-secondary btn-clear-row-filter d-none" title="Reset pencarian baris ini">Reset</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-2 align-items-start">
                                        <div class="col-md-6 col-12">
                                            <label class="form-label fw-bold small">Pilih Barang ATK <span class="text-danger">*</span></label>
                                            <select name="product_id[]" class="form-select product-select" required>
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
                                                        [{{ $catName }}] {{ $product->name }} — ({{ count($product->unit_list) > 1 ? 'Satuan: ' . implode('/', $product->unit_list) : 'Satuan: ' . $pUnit }} | Stok: {{ $product->stock }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted stock-info d-block mt-1">Pilih barang atau gunakan filter kategori & kolom pencarian di atas.</small>
                                        </div>

                                        <div class="col-md-2 col-6">
                                            <label class="form-label fw-bold small">Jumlah Permintaan <span class="text-danger">*</span></label>
                                            <input type="number" name="quantity[]" class="form-control quantity-input text-center fw-bold" min="1"
                                                value="1" required>
                                        </div>

                                        <div class="col-md-2 col-6">
                                            <label class="form-label fw-bold small">Satuan</label>
                                            <div class="custom-unit-dropdown position-relative">
                                                <button type="button" class="form-control custom-unit-trigger d-flex justify-content-between align-items-center bg-white text-dark text-start" aria-expanded="false">
                                                    <span class="custom-unit-label fw-semibold">Buah</span>
                                                    <svg class="custom-unit-chevron text-secondary" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                        <polyline points="6 9 12 15 18 9"></polyline>
                                                    </svg>
                                                </button>

                                                <div class="custom-unit-menu shadow-lg bg-white">
                                                    @foreach ($unitOptions as $val => $label)
                                                        <div class="custom-unit-item d-flex align-items-center justify-content-between {{ $val === 'Buah' ? 'active' : '' }}" data-value="{{ $val }}">
                                                            <div class="d-flex align-items-center gap-1">
                                                                <span class="unit-text">{{ $label }}</span>
                                                                <span class="badge bg-primary-subtle text-primary unit-recommended-badge d-none" style="font-size: 0.62rem;">Standar</span>
                                                            </div>
                                                            <svg class="check-icon text-primary" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                                <polyline points="20 6 9 17 4 12"></polyline>
                                                            </svg>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <input type="hidden" name="custom_unit[]" class="item-unit-input" value="Buah">
                                            </div>
                                            <div class="recommended-units-pills mt-1 d-none"></div>
                                        </div>

                                        <div class="col-md-2 col-12 d-flex flex-column justify-content-end">
                                            <label class="form-label fw-bold small d-none d-md-block">&nbsp;</label>
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-row w-100 d-inline-flex align-items-center justify-content-center" title="Hapus atau reset baris barang ini" style="height: 38px;">
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

                function closeAllDropdowns() {
                    document.querySelectorAll('.custom-unit-dropdown.is-open').forEach(dd => {
                        dd.classList.remove('is-open');
                        dd.querySelector('.custom-unit-trigger')?.setAttribute('aria-expanded', 'false');
                    });
                    document.querySelectorAll('.product-row.dropdown-active').forEach(r => {
                        r.classList.remove('dropdown-active');
                    });
                }

                function setRowUnit(row, targetUnit) {
                    const dropdown = row.querySelector('.custom-unit-dropdown');
                    if (!dropdown) return;
                    const hiddenInput = dropdown.querySelector('.item-unit-input');
                    const labelElem = dropdown.querySelector('.custom-unit-label');
                    const menu = dropdown.querySelector('.custom-unit-menu');
                    if (!hiddenInput || !labelElem || !menu) return;

                    const target = (targetUnit || 'Buah').trim().toLowerCase();
                    let matchedItem = null;

                    menu.querySelectorAll('.custom-unit-item').forEach(item => {
                        const val = (item.getAttribute('data-value') || '').toLowerCase();
                        const txt = (item.querySelector('.unit-text')?.textContent || '').toLowerCase();
                        if (val === target || txt === target || (target === 'dus' && val === 'dos') || (target === 'dos' && val === 'dus')) {
                            matchedItem = item;
                        }
                    });

                    if (!matchedItem) {
                        matchedItem = document.createElement('div');
                        matchedItem.className = 'custom-unit-item d-flex align-items-center justify-content-between';
                        matchedItem.setAttribute('data-value', targetUnit);
                        matchedItem.innerHTML = `
                            <div class="d-flex align-items-center gap-1">
                                <span class="unit-text">${targetUnit}</span>
                                <span class="badge bg-primary-subtle text-primary unit-recommended-badge d-none" style="font-size: 0.62rem;">Standar</span>
                            </div>
                            <svg class="check-icon text-primary" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        `;
                        menu.appendChild(matchedItem);
                        bindItemClick(matchedItem, dropdown, row);
                    }

                    menu.querySelectorAll('.custom-unit-item').forEach(it => it.classList.remove('active'));
                    matchedItem.classList.add('active');

                    const finalVal = matchedItem.getAttribute('data-value');
                    const finalLabel = matchedItem.querySelector('.unit-text').textContent;

                    hiddenInput.value = finalVal;
                    labelElem.textContent = finalLabel;

                    // Update active state on quick pills
                    const pillsContainer = row.querySelector('.recommended-units-pills');
                    if (pillsContainer) {
                        pillsContainer.querySelectorAll('.btn-unit-pill').forEach(btn => {
                            if ((btn.getAttribute('data-unit') || '').toLowerCase() === finalVal.toLowerCase()) {
                                btn.classList.remove('btn-outline-secondary', 'bg-white');
                                btn.classList.add('btn-primary', 'text-white');
                            } else {
                                btn.classList.remove('btn-primary', 'text-white');
                                btn.classList.add('btn-outline-secondary', 'bg-white');
                            }
                        });
                    }
                }

                function updateRecommendedUnits(row, availableUnitsStr) {
                    const dropdown = row.querySelector('.custom-unit-dropdown');
                    const pillsContainer = row.querySelector('.recommended-units-pills');
                    if (!dropdown) return;
                    const menu = dropdown.querySelector('.custom-unit-menu');
                    const units = (availableUnitsStr || '').split(',').map(u => u.trim()).filter(Boolean);
                    const unitsLower = units.map(u => u.toLowerCase());

                    menu.querySelectorAll('.custom-unit-item').forEach(item => {
                        const val = (item.getAttribute('data-value') || '').toLowerCase();
                        const badge = item.querySelector('.unit-recommended-badge');
                        if (badge) {
                            if (unitsLower.includes(val) || (val === 'dus' && unitsLower.includes('dos'))) {
                                badge.classList.remove('d-none');
                            } else {
                                badge.classList.add('d-none');
                            }
                        }
                    });

                    if (pillsContainer) {
                        if (units.length > 1) {
                            pillsContainer.classList.remove('d-none');
                            pillsContainer.innerHTML = '<small class="text-muted d-block mb-1" style="font-size: 0.68rem;">Pilih cepat satuan standar:</small><div class="d-flex flex-wrap gap-1">' +
                                units.map(u => `<button type="button" class="btn btn-xs btn-outline-secondary bg-white py-0 px-2 btn-unit-pill" data-unit="${u}" style="font-size: 0.72rem; border-radius: 6px;">${u}</button>`).join('') +
                                '</div>';
                            pillsContainer.querySelectorAll('.btn-unit-pill').forEach(btn => {
                                btn.addEventListener('click', function() {
                                    setRowUnit(row, this.getAttribute('data-unit'));
                                });
                            });
                        } else {
                            pillsContainer.classList.add('d-none');
                            pillsContainer.innerHTML = '';
                        }
                    }
                }

                function bindItemClick(item, dropdown, row) {
                    item.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const val = this.getAttribute('data-value');
                        setRowUnit(row, val);
                        dropdown.classList.remove('is-open');
                        dropdown.querySelector('.custom-unit-trigger')?.setAttribute('aria-expanded', 'false');
                        row.classList.remove('dropdown-active');
                    });
                }

                function attachDropdownEvents(row) {
                    const dropdown = row.querySelector('.custom-unit-dropdown');
                    if (!dropdown) return;
                    const trigger = dropdown.querySelector('.custom-unit-trigger');
                    const menu = dropdown.querySelector('.custom-unit-menu');

                    if (trigger) {
                        trigger.addEventListener('click', function(e) {
                            e.stopPropagation();
                            const isOpen = dropdown.classList.contains('is-open');
                            closeAllDropdowns();
                            if (!isOpen) {
                                dropdown.classList.add('is-open');
                                trigger.setAttribute('aria-expanded', 'true');
                                row.classList.add('dropdown-active');
                            }
                        });
                    }

                    menu.querySelectorAll('.custom-unit-item').forEach(item => {
                        bindItemClick(item, dropdown, row);
                    });
                }

                function filterRowOptions(row) {
                    const select = row.querySelector('.product-select');
                    const catFilter = row.querySelector('.category-filter-select');
                    const searchInput = row.querySelector('.product-quick-search');
                    const countBadge = row.querySelector('.filter-count-badge');
                    const clearBtn = row.querySelector('.btn-clear-row-filter');

                    if (!select) return;
                    const selectedVal = select.value;
                    const catVal = catFilter ? catFilter.value : '';
                    const kw = searchInput ? searchInput.value.trim().toLowerCase() : '';

                    if (clearBtn) {
                        if (catVal !== '' || kw !== '') {
                            clearBtn.classList.remove('d-none');
                        } else {
                            clearBtn.classList.add('d-none');
                        }
                    }

                    const filtered = masterOptions.filter(item => {
                        const matchCat = !catVal || item.categoryId === catVal;
                        const matchKw = !kw || item.searchText.includes(kw);
                        return matchCat && matchKw;
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

                    // Jika hanya ada 1 hasil pencarian ketika mengetik kata kunci, bantu pilih otomatis
                    if (!currentStillExists && kw.length >= 3 && filtered.length === 1) {
                        select.value = filtered[0].value;
                        select.dispatchEvent(new Event('change'));
                    } else if (!currentStillExists && selectedVal !== '') {
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
                    const stockInfo = row.querySelector('.stock-info');
                    const catFilter = row.querySelector('.category-filter-select');
                    const searchInput = row.querySelector('.product-quick-search');
                    const clearBtn = row.querySelector('.btn-clear-row-filter');

                    attachDropdownEvents(row);

                    if (catFilter) {
                        catFilter.addEventListener('change', function() {
                            filterRowOptions(row);
                        });
                    }

                    if (searchInput) {
                        searchInput.addEventListener('input', function() {
                            filterRowOptions(row);
                        });
                    }

                    if (clearBtn) {
                        clearBtn.addEventListener('click', function() {
                            if (catFilter) catFilter.value = '';
                            if (searchInput) searchInput.value = '';
                            filterRowOptions(row);
                        });
                    }

                    select.addEventListener('change', function() {
                        const opt = this.options[this.selectedIndex];
                        const stock = opt ? (parseInt(opt.getAttribute('data-stock')) || 0) : 0;
                        const unit = opt ? (opt.getAttribute('data-unit') || 'Buah') : 'Buah';
                        const availUnits = opt ? (opt.getAttribute('data-available-units') || unit) : 'Buah';

                        if (this.value && opt) {
                            updateRecommendedUnits(row, availUnits);
                            setRowUnit(row, unit);
                            qtyInput.removeAttribute('max');
                            if (stock > 0) {
                                stockInfo.innerHTML = '<span class="text-success fw-semibold">Sisa stok gudang: ' + stock + ' ' + unit + '.</span> Jika permintaan melebihi stok, kekurangan akan dibeli oleh Kasubag TU.';
                            } else {
                                stockInfo.innerHTML = '<span class="text-warning fw-semibold">Stok belum tersedia (0).</span> Permintaan ini akan otomatis diproses pengadaan/pembelian oleh Kasubag TU.';
                            }
                        } else {
                            updateRecommendedUnits(row, '');
                            setRowUnit(row, 'Buah');
                            qtyInput.removeAttribute('max');
                            stockInfo.textContent = 'Pilih barang atau gunakan filter kategori & kolom pencarian di atas.';
                        }
                    });

                    // Trigger if already selected
                    if (select.value) {
                        select.dispatchEvent(new Event('change'));
                    }
                }

                // Global click and escape key handlers for closing dropdowns
                document.addEventListener('click', function(e) {
                    if (!e.target.closest('.custom-unit-dropdown')) {
                        closeAllDropdowns();
                    }
                });

                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        closeAllDropdowns();
                    }
                });

                // Init existing rows
                productRows.querySelectorAll('.product-row').forEach(initRow);

                addRowBtn.addEventListener('click', function() {
                    closeAllDropdowns();
                    const firstRow = productRows.querySelector('.product-row');
                    const newRow = firstRow.cloneNode(true);

                    // Reset filter & values in new row
                    const catFilter = newRow.querySelector('.category-filter-select');
                    if (catFilter) catFilter.value = '';
                    const searchInput = newRow.querySelector('.product-quick-search');
                    if (searchInput) searchInput.value = '';
                    const clearBtn = newRow.querySelector('.btn-clear-row-filter');
                    if (clearBtn) clearBtn.classList.add('d-none');

                    const select = newRow.querySelector('select.product-select');
                    select.value = '';
                    const qty = newRow.querySelector('input[type="number"]');
                    qty.value = '1';
                    qty.removeAttribute('max');
                    newRow.querySelector('.stock-info').textContent = 'Pilih barang atau gunakan filter kategori & kolom pencarian di atas.';

                    newRow.classList.remove('dropdown-active');
                    const newDropdown = newRow.querySelector('.custom-unit-dropdown');
                    if (newDropdown) {
                        newDropdown.classList.remove('is-open');
                        newDropdown.querySelector('.custom-unit-trigger')?.setAttribute('aria-expanded', 'false');
                    }

                    productRows.appendChild(newRow);
                    filterRowOptions(newRow);
                    updateRecommendedUnits(newRow, '');
                    setRowUnit(newRow, 'Buah');
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
                        closeAllDropdowns();
                    } else {
                        // Jika hanya 1 baris, kosongkan kembali
                        const catFilter = row.querySelector('.category-filter-select');
                        if (catFilter) catFilter.value = '';
                        const searchInput = row.querySelector('.product-quick-search');
                        if (searchInput) searchInput.value = '';
                        filterRowOptions(row);

                        const select = row.querySelector('.product-select');
                        const qty = row.querySelector('.quantity-input');
                        if (select) {
                            select.value = '';
                            select.dispatchEvent(new Event('change'));
                        }
                        if (qty) {
                            qty.value = '1';
                            qty.removeAttribute('max');
                        }
                        updateRecommendedUnits(row, '');
                        setRowUnit(row, 'Buah');
                        closeAllDropdowns();
                    }
                });
            });
        </script>
    @endpush
@endsection
