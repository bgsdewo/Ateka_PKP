@extends('layouts.app')

@section('page-header')
    <div class="iq-navbar-header" style="height: 220px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="flex-wrap d-flex justify-content-between align-items-center text-white">
                        <div>
                            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2" style="background: rgba(255, 255, 255, 0.12); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.18); font-size: 0.72rem; letter-spacing: 0.05em; text-transform: uppercase; font-weight: 700;">
                                <span class="badge-dot badge-dot-warning"></span>
                                <span>Kementerian PKP RI &bull; Usulan Pengadaan Barang</span>
                            </div>
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
        /* Custom Dropdown Satuan Pengadaan */
        .procure-row {
            position: relative;
            overflow: visible !important;
        }
        .procure-row.dropdown-active {
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
            bottom: auto !important; /* Pastikan selalu terbuka ke bawah, tidak pernah ke atas */
            z-index: 1070;
            min-width: 140px;
            max-height: 195px; /* Kompak & tidak kepanjangan */
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
        /* Scrollbar minimalis & elegan */
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
                            <h5 class="card-title mb-0 fw-bold">Form Permintaan Pembelian Barang ke Subbag TU</h5>
                            <small class="text-muted">Gunakan form ini jika barang yang Anda butuhkan stoknya belum ada di gudang. Kasubag TU akan memproses pembelian ke supplier atas permintaan ini.</small>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        {{-- Petunjuk Alur Pengadaan --}}
                        <div class="alert alert-info border-0 shadow-sm mb-4 d-flex align-items-start gap-3 p-3">
                            <div class="badge bg-primary rounded-circle p-2 mt-1">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                </svg>
                            </div>
                            <div>
                                <h6 class="fw-bold text-primary mb-1">Alur Pembelian Berdasarkan Permintaan Pegawai/Subdit:</h6>
                                <p class="mb-0 small text-dark" style="line-height: 1.5;">
                                    1. Anda mengajukan daftar barang yang dibutuhkan beserta spesifikasinya.<br>
                                    2. Subbag TU merekapitulasi usulan dan Kasubag TU melakukan <strong>pembelian langsung ke supplier/toko</strong>.<br>
                                    3. Setelah barang dibeli dan tiba, barang akan diserahterimakan kepada Anda melalui PIC Distribusi.
                                </p>
                            </div>
                        </div>

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
                                        <option value="individu" {{ old('type') == 'individu' ? 'selected' : '' }}>
                                            Individu (Staf / Pegawai)
                                        </option>
                                        <option value="subdit" {{ (old('type') == 'subdit' || auth()->user()->role === 'subdit') ? 'selected' : '' }}>
                                            Subdirektorat (Kolektif Unit Kerja)
                                        </option>
                                    </select>
                                    <small class="text-muted">Pilih "Subdirektorat" jika mewakili kebutuhan kolektif satu unit kerja.</small>
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
                                        placeholder="Contoh: Kebutuhan perlengkapan rapat koordinasi nasional, map dokumen akreditasi, stok belum tersedia di gudang...">{{ old('notes') }}</textarea>
                                    <small class="text-muted">Jelaskan keperluan penggunaan barang ini agar Kasubag TU dapat memverifikasi pengadaan.</small>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="mb-0 fw-bold">Daftar Barang yang Diminta untuk Dibeli ke Supplier</h5>
                                    <small class="text-muted">Tuliskan nama barang, perkiraan spesifikasi / merk, dan jumlah yang dibutuhkan</small>
                                </div>
                            </div>

                            {{-- Datalist produk yang pernah ada sebagai rekomendasi autocomplete --}}
                            <datalist id="existingProductList">
                                @foreach ($existingProducts as $p)
                                    <option value="{{ $p->name }}" data-unit="{{ $p->unit }}" data-id="{{ $p->id }}">{{ $p->code }} - {{ $p->category->name ?? 'ATK' }}</option>
                                @endforeach
                            </datalist>

                            @php
                                $unitOptions = [
                                    'Pcs' => 'Pcs',
                                    'Rim' => 'Rim',
                                    'Dos' => 'Dos / Dus',
                                    'Pak' => 'Pak',
                                    'Buku' => 'Buku',
                                    'Buah' => 'Buah',
                                    'Lembar' => 'Lembar',
                                    'Set' => 'Set',
                                    'Roll' => 'Roll',
                                    'Box' => 'Box',
                                    'Kotak' => 'Kotak',
                                    'Batang' => 'Batang',
                                    'Tube' => 'Tube',
                                    'Bungkus' => 'Bungkus',
                                    'Botol' => 'Botol',
                                    'Lusin' => 'Lusin',
                                    'Unit' => 'Unit',
                                    'Meter' => 'Meter',
                                ];

                                if (isset($existingProducts)) {
                                    foreach ($existingProducts->pluck('unit')->filter()->unique() as $extUnit) {
                                        $extUnitTrim = trim($extUnit);
                                        if ($extUnitTrim !== '' && !isset($unitOptions[$extUnitTrim])) {
                                            $unitOptions[$extUnitTrim] = $extUnitTrim;
                                        }
                                    }
                                }
                            @endphp

                            <div id="procure-rows">
                                <div class="procure-row p-3 mb-3 rounded border bg-light">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-md-5 col-12">
                                            <label class="form-label fw-bold small">Nama Barang yang Diminta <span class="text-danger">*</span></label>
                                            <input type="text" name="item_name[]" class="form-control item-name-input" 
                                                list="existingProductList" 
                                                placeholder="Contoh: Map Gantung Folio, Spidol Whiteboard..." required autocomplete="off">
                                            <input type="hidden" name="product_id[]" class="item-product-id">
                                        </div>

                                        <div class="col-md-2 col-6">
                                            <label class="form-label fw-bold small">Jumlah <span class="text-danger">*</span></label>
                                            <input type="number" name="quantity[]" class="form-control text-center fw-bold" 
                                                min="1" value="1" required>
                                        </div>

                                        <div class="col-md-2 col-6">
                                            <label class="form-label fw-bold small">Satuan</label>
                                            <div class="custom-unit-dropdown position-relative">
                                                <button type="button" class="form-control custom-unit-trigger d-flex justify-content-between align-items-center bg-white text-dark text-start" aria-expanded="false">
                                                    <span class="custom-unit-label fw-semibold">Pcs</span>
                                                    <svg class="custom-unit-chevron text-secondary" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                        <polyline points="6 9 12 15 18 9"></polyline>
                                                    </svg>
                                                </button>

                                                <div class="custom-unit-menu shadow-lg bg-white">
                                                    @foreach ($unitOptions as $val => $label)
                                                        <div class="custom-unit-item d-flex align-items-center justify-content-between {{ $val === 'Pcs' ? 'active' : '' }}" data-value="{{ $val }}">
                                                            <span class="unit-text">{{ $label }}</span>
                                                            <svg class="check-icon text-primary" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                                <polyline points="20 6 9 17 4 12"></polyline>
                                                            </svg>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <input type="hidden" name="custom_unit[]" class="item-unit-input" value="Pcs">
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-12">
                                            <div class="d-flex align-items-end gap-2">
                                                <div class="flex-grow-1">
                                                    <label class="form-label fw-bold small">Spesifikasi / Merk (Opsional)</label>
                                                    <input type="text" name="specification[]" class="form-control" 
                                                        placeholder="Misal: Warna Biru / Joyko">
                                                </div>
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-procure-row" title="Hapus baris ini" style="height: 38px;">
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                    </svg>
                                                </button>
                                            </div>
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
                                    + Tambah Barang Pengadaan Lain
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

                // Map product options for auto-filling unit and hidden id
                const productOptions = {};
                document.querySelectorAll('#existingProductList option').forEach(opt => {
                    productOptions[opt.value.toLowerCase()] = {
                        unit: opt.getAttribute('data-unit') || 'Pcs',
                        id: opt.getAttribute('data-id') || ''
                    };
                });

                function closeAllDropdowns() {
                    document.querySelectorAll('.custom-unit-dropdown.is-open').forEach(dd => {
                        dd.classList.remove('is-open');
                        dd.querySelector('.custom-unit-trigger')?.setAttribute('aria-expanded', 'false');
                    });
                    document.querySelectorAll('.procure-row.dropdown-active').forEach(r => {
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

                    const target = (targetUnit || 'Pcs').trim().toLowerCase();
                    let matchedItem = null;

                    menu.querySelectorAll('.custom-unit-item').forEach(item => {
                        const val = (item.getAttribute('data-value') || '').toLowerCase();
                        const txt = (item.querySelector('.unit-text')?.textContent || '').toLowerCase();
                        if (val === target || txt === target || (target === 'dus' && val === 'dos')) {
                            matchedItem = item;
                        }
                    });

                    if (!matchedItem) {
                        matchedItem = document.createElement('div');
                        matchedItem.className = 'custom-unit-item d-flex align-items-center justify-content-between';
                        matchedItem.setAttribute('data-value', targetUnit);
                        matchedItem.innerHTML = `
                            <span class="unit-text">${targetUnit}</span>
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

                function attachRowEvents(row) {
                    const nameInput = row.querySelector('.item-name-input');
                    const idInput = row.querySelector('.item-product-id');
                    const removeBtn = row.querySelector('.remove-procure-row');

                    attachDropdownEvents(row);

                    if (nameInput) {
                        nameInput.addEventListener('input', function() {
                            const val = this.value.trim().toLowerCase();
                            if (productOptions[val]) {
                                if (productOptions[val].unit) {
                                    setRowUnit(row, productOptions[val].unit);
                                }
                                if (idInput) {
                                    idInput.value = productOptions[val].id;
                                }
                            } else {
                                if (idInput) idInput.value = '';
                            }
                        });
                    }

                    if (removeBtn) {
                        removeBtn.addEventListener('click', function() {
                            const rows = container.querySelectorAll('.procure-row');
                            if (rows.length > 1) {
                                row.remove();
                                closeAllDropdowns();
                            } else {
                                // Reset fields if only 1 row
                                row.querySelectorAll('input:not(.item-unit-input)').forEach(input => {
                                    if (input.type === 'number') {
                                        input.value = '1';
                                    } else {
                                        input.value = '';
                                    }
                                });
                                setRowUnit(row, 'Pcs');
                                closeAllDropdowns();
                            }
                        });
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

                // Attach to initial row
                container.querySelectorAll('.procure-row').forEach(attachRowEvents);

                // Add row handler
                addBtn.addEventListener('click', function() {
                    closeAllDropdowns();
                    const firstRow = container.querySelector('.procure-row');
                    const newRow = firstRow.cloneNode(true);

                    // Reset values in newRow
                    newRow.querySelectorAll('input:not(.item-unit-input)').forEach(input => {
                        if (input.type === 'number') {
                            input.value = '1';
                        } else {
                            input.value = '';
                        }
                    });

                    newRow.classList.remove('dropdown-active');
                    const newDropdown = newRow.querySelector('.custom-unit-dropdown');
                    if (newDropdown) {
                        newDropdown.classList.remove('is-open');
                        newDropdown.querySelector('.custom-unit-trigger')?.setAttribute('aria-expanded', 'false');
                    }

                    container.appendChild(newRow);
                    setRowUnit(newRow, 'Pcs');
                    attachRowEvents(newRow);
                    newRow.querySelector('.item-name-input').focus();
                });
            });
        </script>
    @endpush
@endsection
