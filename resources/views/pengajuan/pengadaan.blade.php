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
                                            <select class="form-select form-select-sm procure-cat-filter bg-white">
                                                <option value="">-- Semua Kategori ({{ isset($categories) ? $categories->count() : 0 }} Kategori) --</option>
                                                @if (isset($categories))
                                                    @foreach ($categories as $cat)
                                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-8 col-12">
                                            <label class="form-label small text-secondary fw-bold mb-1 d-flex justify-content-between align-items-center">
                                                <span>Nama Barang yang Diminta <span class="text-danger">*</span></span>
                                                <span class="badge bg-primary-subtle text-primary procure-count-badge" style="font-size: 0.72rem;">{{ $existingProducts->count() }} barang</span>
                                            </label>
                                            <div class="item-name-wrapper">
                                                <select name="item_name[]" class="form-select form-select-sm item-name-select bg-white" required>
                                                    <option value="">-- Pilih Barang yang Diminta ({{ $existingProducts->count() }} Barang) --</option>
                                                    @foreach ($existingProducts as $p)
                                                        <option value="{{ $p->name }}"
                                                            data-id="{{ $p->id }}"
                                                            data-name="{{ $p->name }}"
                                                            data-code="{{ $p->code }}"
                                                            data-category-id="{{ $p->category_id }}"
                                                            data-category-name="{{ $p->category->name ?? 'ATK' }}"
                                                            data-unit="{{ $p->unit ?: 'Buah' }}"
                                                            data-available-units="{{ implode(',', $p->unit_list) }}">
                                                            [{{ $p->code ?: '-' }} - {{ $p->category->name ?? 'ATK' }}] {{ $p->name }} (Satuan: {{ implode('/', $p->unit_list) }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <input type="hidden" name="product_id[]" class="item-product-id" value="">
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

                function populateItemSelect(row, selectedProductId = null) {
                    const catFilter = row.querySelector('.procure-cat-filter');
                    const nameSelect = row.querySelector('.item-name-select');
                    const badge = row.querySelector('.procure-count-badge');
                    if (!nameSelect) return;

                    const catVal = catFilter ? catFilter.value : '';
                    const filtered = masterProducts.filter(p => !catVal || String(p.category_id) === String(catVal));

                    let html = `<option value="">-- Pilih Barang yang Diminta (${filtered.length} Barang) --</option>`;

                    filtered.forEach(p => {
                        const uText = p.available_units.split(',').join('/');
                        const isSel = (selectedProductId !== null && String(p.id) === String(selectedProductId)) ? 'selected' : '';
                        const codePrefix = p.code ? `[${p.code} - ${p.category_name}]` : `[${p.category_name}]`;
                        html += `<option value="${p.name}" data-id="${p.id}" data-unit="${p.unit}" data-available-units="${p.available_units}" data-code="${p.code}" data-category-name="${p.category_name}" ${isSel}>${codePrefix} ${p.name} (Satuan: ${uText})</option>`;
                    });

                    nameSelect.innerHTML = html;
                    if (badge) {
                        badge.textContent = `${filtered.length} barang`;
                    }
                }

                function attachRowEvents(row) {
                    const catFilter = row.querySelector('.procure-cat-filter');
                    const nameSelect = row.querySelector('.item-name-select');
                    const idInput = row.querySelector('.item-product-id');
                    const unitSelect = row.querySelector('.item-unit-select');
                    const specInput = row.querySelector('.item-spec-input');
                    const removeBtn = row.querySelector('.remove-procure-row');

                    if (catFilter) {
                        catFilter.addEventListener('change', function() {
                            populateItemSelect(row);
                            if (nameSelect) nameSelect.value = '';
                            if (idInput) idInput.value = '';
                            setRowUnitSelect(unitSelect, '', 'Buah');
                        });
                    }

                    if (nameSelect) {
                        nameSelect.addEventListener('change', function() {
                            const selectedVal = this.value;
                            if (selectedVal) {
                                const opt = this.options[this.selectedIndex];
                                if (opt) {
                                    const pId = opt.getAttribute('data-id') || '';
                                    const pUnit = opt.getAttribute('data-unit') || 'Buah';
                                    const pAvail = opt.getAttribute('data-available-units') || pUnit;

                                    if (idInput) idInput.value = pId;
                                    setRowUnitSelect(unitSelect, pAvail, pUnit);
                                }
                            } else {
                                if (idInput) idInput.value = '';
                                setRowUnitSelect(unitSelect, '', 'Buah');
                            }
                        });
                    }

                    if (removeBtn) {
                        removeBtn.addEventListener('click', function() {
                            const rows = container.querySelectorAll('.procure-row');
                            if (rows.length > 1) {
                                row.remove();
                            } else {
                                if (catFilter) catFilter.value = '';
                                populateItemSelect(row);
                                row.querySelectorAll('input').forEach(input => {
                                    if (input.type === 'number') {
                                        input.value = '1';
                                    } else {
                                        input.value = '';
                                    }
                                });
                                if (nameSelect) nameSelect.value = '';
                                setRowUnitSelect(unitSelect, '', 'Buah');
                            }
                        });
                    }
                }

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
                            const catFilter = firstRow.querySelector('.procure-cat-filter');
                            if (catFilter) {
                                catFilter.value = targetProd.category_id;
                            }
                            populateItemSelect(firstRow, targetProd.id);
                            const nameSelect = firstRow.querySelector('.item-name-select');
                            if (nameSelect) {
                                nameSelect.value = targetProd.name;
                                nameSelect.dispatchEvent(new Event('change'));
                            }
                        }
                    }
                }

                // Add row handler
                addBtn.addEventListener('click', function() {
                    const firstRow = container.querySelector('.procure-row');
                    const newRow = firstRow.cloneNode(true);

                    const catFilter = newRow.querySelector('.procure-cat-filter');
                    if (catFilter) catFilter.value = '';

                    const nameSelect = newRow.querySelector('.item-name-select');
                    if (nameSelect) nameSelect.value = '';

                    newRow.querySelectorAll('input').forEach(input => {
                        if (input.type === 'number') {
                            input.value = '1';
                        } else {
                            input.value = '';
                        }
                    });

                    container.appendChild(newRow);
                    populateItemSelect(newRow);
                    const newUnitSelect = newRow.querySelector('.item-unit-select');
                    setRowUnitSelect(newUnitSelect, '', 'Buah');
                    attachRowEvents(newRow);
                    if (nameSelect) nameSelect.focus();
                });
            });
        </script>
    @endpush
@endsection
