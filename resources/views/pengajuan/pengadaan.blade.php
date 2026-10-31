@extends('layouts.app')

@section('page-header')
    <div class="iq-navbar-header" style="height: 220px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="flex-wrap d-flex justify-content-between align-items-center text-white">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <h1 class="text-white fw-bold mb-0">Usulan Pengadaan Barang ATK</h1>
                                <span class="badge bg-warning text-dark fw-semibold" style="font-size: 0.78rem;">Barang Belum Tersedia di Katalog</span>
                            </div>
                            <p class="text-white mb-0" style="opacity: 0.92; max-width: 820px;">
                                Formulir usulan khusus untuk kebutuhan barang ATK yang belum tersedia di persediaan gudang agar diajukan ke Subbag TU untuk proses pembelian baru.
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
            transition: all 0.15s ease-in-out;
        }
        .procure-row:hover {
            border-color: #cbd5e1 !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        /* Unit Select Dropdown Styling */
        .unit-select-dropdown-wrapper {
            position: relative;
            width: 100%;
        }
        .unit-select-trigger-btn {
            text-align: left;
            display: flex !important;
            align-items: center;
            justify-content: space-between;
            white-space: nowrap;
            background-color: #ffffff;
            background-image: none !important;
            border: 1px solid #cbd5e1;
            cursor: pointer;
            user-select: none;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            height: 38px;
            padding: 0.375rem 0.75rem;
            font-size: 0.85rem;
            border-radius: 8px;
        }
        .unit-select-trigger-btn:hover {
            border-color: #94a3b8;
        }
        .unit-select-trigger-btn:focus,
        .unit-select-trigger-btn.show {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.15) !important;
            outline: none;
        }
        .unit-select-trigger-btn::after {
            display: none !important;
        }
        .unit-select-arrow {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            transition: transform 0.2s ease;
            flex-shrink: 0;
        }
        .unit-select-trigger-btn.show .unit-select-arrow {
            transform: rotate(180deg);
            color: #2563eb;
        }
        .unit-select-menu {
            border-color: #cbd5e1 !important;
            background: #ffffff !important;
            box-shadow: 0 12px 30px -5px rgba(15, 23, 42, 0.15), 0 8px 10px -6px rgba(15, 23, 42, 0.08) !important;
            z-index: 1060 !important;
            border-radius: 8px !important;
            min-width: 220px !important;
            width: 100% !important;
            padding: 0 !important;
            overflow: hidden !important;
        }
        .unit-scroll-list {
            max-height: 180px !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
        }
        .unit-scroll-list::-webkit-scrollbar {
            width: 6px;
        }
        .unit-scroll-list::-webkit-scrollbar-track {
            background: #f8fafc;
            border-radius: 4px;
        }
        .unit-scroll-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .unit-scroll-list::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .unit-select-option {
            cursor: pointer;
            transition: all 0.12s ease;
            border-radius: 6px;
            padding: 0.35rem 0.65rem;
            margin-bottom: 2px;
            border: 1px solid transparent;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.83rem;
            color: #1e293b;
        }
        .unit-select-option:hover {
            background-color: #f1f5f9;
            color: #0f172a;
        }
        .unit-select-option.active {
            background-color: #eff6ff !important;
            border-color: #bfdbfe !important;
            color: #1d4ed8 !important;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="row justify-content-center">
            <div class="col-lg-11 col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0 fw-bold">Form Usulan Pengadaan Barang (Belum Tersedia di Katalog)</h5>
                            <small class="text-muted">Ajukan kebutuhan ATK baru yang belum tercantum di persediaan gudang agar dibeli oleh Subbag TU.</small>
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
                                        @if(auth()->user()->isPicDistribusi())
                                            <option value="subdit" selected>
                                                Subdirektorat (Kolektif Unit Kerja)
                                            </option>
                                        @else
                                            <option value="individu" selected>
                                                Individu (Staf / Pegawai)
                                            </option>
                                        @endif
                                    </select>
                                    <small class="text-muted">
                                        @if(auth()->user()->isPicDistribusi())
                                            Permohonan diajukan secara kolektif untuk kebutuhan subdirektorat.
                                        @else
                                            Permohonan diajukan atas nama pegawai pemohon perorangan.
                                        @endif
                                    </small>
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
                                    <h5 class="mb-0 fw-bold">Daftar Barang yang Diusulkan</h5>
                                    <small class="text-muted">Tuliskan nama barang, jumlah, satuan, dan spesifikasi/merk yang diusulkan untuk diadakan.</small>
                                </div>
                            </div>

                            @php
                                $unitOptions = [
                                    'Buah' => 'Buah',
                                    'Rim' => 'Rim',
                                    'Dus' => 'Dus / Dos',
                                    'Pak' => 'Pak',
                                    'Kotak' => 'Kotak',
                                    'Box' => 'Box',
                                    'Pcs' => 'Pcs',
                                    'Unit' => 'Unit',
                                    'Set' => 'Set',
                                    'Roll' => 'Roll',
                                    'Slop' => 'Slop',
                                    'Buku' => 'Buku',
                                    'Tube' => 'Tube',
                                    'Botol' => 'Botol',
                                    'Lusin' => 'Lusin',
                                    'Lembar' => 'Lembar',
                                    'Batang' => 'Batang',
                                    'Bungkus' => 'Bungkus',
                                    'Meter' => 'Meter',
                                ];
                            @endphp

                            <div id="procure-rows">
                                <div class="procure-row p-3 mb-3 rounded-3 border bg-white shadow-xs">
                                    <div class="row g-2 align-items-end">
                                        {{-- 1. Nama Barang Input --}}
                                        <div class="col-md-5 col-12">
                                            <label class="form-label small text-secondary fw-bold mb-1">
                                                Nama Barang yang Diusulkan <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="item_name[]" class="form-control form-control-sm item-name-input bg-white" 
                                                placeholder="Contoh: Kertas NCR 3 Ply, Whiteboard 120x80, Mouse Wireless..." 
                                                required style="height: 38px;">
                                        </div>

                                        {{-- 2. Jumlah Input --}}
                                        <div class="col-md-2 col-6">
                                            <label class="form-label small text-secondary fw-bold mb-1">
                                                Jumlah <span class="text-danger">*</span>
                                            </label>
                                            <input type="number" name="quantity[]" class="form-control form-control-sm text-center fw-bold bg-white" 
                                                min="1" value="1" required style="height: 38px;">
                                        </div>

                                        {{-- 3. Satuan Dropdown --}}
                                        <div class="col-md-2 col-6">
                                            <label class="form-label small text-secondary fw-bold mb-1">
                                                Satuan <span class="text-danger">*</span>
                                            </label>
                                            <div class="unit-select-dropdown-wrapper dropdown">
                                                <button type="button" 
                                                        class="form-select form-select-sm unit-select-trigger-btn dropdown-toggle w-100" 
                                                        data-bs-toggle="dropdown" 
                                                        data-bs-auto-close="outside"
                                                        aria-expanded="false"
                                                        data-bs-popper-config='{"placement": "bottom-start"}'>
                                                    <span class="unit-btn-text text-truncate me-2">Buah</span>
                                                    <span class="unit-select-arrow">
                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                                    </span>
                                                </button>

                                                <input type="hidden" name="custom_unit[]" class="item-unit-input" value="Buah" required>

                                                <div class="dropdown-menu unit-select-menu shadow-lg border rounded-3 mt-1">
                                                    <div class="p-2 border-bottom bg-light">
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text bg-white border-end-0 text-muted px-2">
                                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                                            </span>
                                                            <input type="text" 
                                                                   class="form-control border-start-0 unit-quick-search" 
                                                                   placeholder="Cari / ketik satuan..." 
                                                                   autocomplete="off" 
                                                                   style="font-size: 0.82rem;">
                                                            <button type="button" class="btn btn-outline-secondary btn-clear-unit-search d-none px-2" title="Hapus pencarian">
                                                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="unit-scroll-list p-1">
                                                        {{-- Rendered dynamically via JS --}}
                                                    </div>
                                                    <div class="no-unit-found p-2 text-center text-muted small d-none">
                                                        Satuan tidak ditemukan
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- 4. Spesifikasi Input --}}
                                        <div class="col-md-2 col-10">
                                            <label class="form-label small text-secondary fw-bold mb-1">Spesifikasi / Merk</label>
                                            <input type="text" name="specification[]" class="form-control form-control-sm item-spec-input bg-white" 
                                                placeholder="Misal: Warna Biru / Joyko" style="height: 38px;">
                                        </div>

                                        {{-- 5. Tombol Hapus --}}
                                        <div class="col-md-1 col-2 text-end">
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-procure-row w-100 d-flex align-items-center justify-content-center" 
                                                title="Hapus baris barang ini" style="height: 38px;">
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
                                <button type="button" id="btn-add-procure-row" class="btn btn-success btn-sm d-inline-flex align-items-center gap-1">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                    Tambah Barang Pengadaan Lain
                                </button>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <a href="{{ route('pengajuan.index') }}" class="btn btn-outline-secondary">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-primary px-4 shadow-sm d-inline-flex align-items-center gap-2">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                        <polyline points="7 3 7 8 15 8"></polyline>
                                    </svg>
                                    Kirim Usulan Pengadaan ke Kasubbag TU
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('procure-rows');
            const addBtn = document.getElementById('btn-add-procure-row');
            const unitOptionsData = @json($unitOptions);

            function escapeHtml(str) {
                if (!str) return '';
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function attachUnitEvents(row) {
                const unitInputHidden = row.querySelector('.item-unit-input');
                const unitTriggerBtn = row.querySelector('.unit-select-trigger-btn');
                const unitBtnText = row.querySelector('.unit-btn-text');
                const unitQuickSearch = row.querySelector('.unit-quick-search');
                const unitClearSearch = row.querySelector('.btn-clear-unit-search');
                const unitList = row.querySelector('.unit-scroll-list');
                const unitNoFound = row.querySelector('.no-unit-found');
                const removeBtn = row.querySelector('.remove-procure-row');

                let currentUnitValue = unitInputHidden ? unitInputHidden.value : 'Buah';

                function renderUnits(searchQuery = '') {
                    if (!unitList) return;
                    const q = (searchQuery || '').trim().toLowerCase();
                    unitList.innerHTML = '';

                    const added = new Set();
                    const allUnits = Object.keys(unitOptionsData);

                    const matchedUnits = allUnits.filter(u => {
                        const label = unitOptionsData[u] || u;
                        return !q || u.toLowerCase().includes(q) || label.toLowerCase().includes(q);
                    });

                    matchedUnits.forEach(u => {
                        unitList.appendChild(createUnitOption(u, unitOptionsData[u] || u));
                        added.add(u.toLowerCase());
                    });

                    const showCustomAdd = q && !added.has(q);
                    if (matchedUnits.length === 0 && !showCustomAdd) {
                        if (unitNoFound) unitNoFound.classList.remove('d-none');
                        return;
                    }
                    if (unitNoFound) unitNoFound.classList.add('d-none');

                    if (showCustomAdd) {
                        const customDiv = document.createElement('div');
                        customDiv.className = 'unit-select-option custom-add-option py-1.5 px-2 rounded-2 mt-1 d-flex align-items-center justify-content-between';
                        customDiv.style.background = '#f0fdf4';
                        customDiv.style.border = '1px dashed #86efac';
                        customDiv.innerHTML = `
                            <span class="text-truncate" style="font-size: 0.82rem; color: #15803d;">
                                Gunakan satuan: <strong>${escapeHtml(searchQuery.trim())}</strong>
                            </span>
                            <span class="badge bg-success text-white" style="font-size: 0.65rem;">Baru</span>
                        `;
                        customDiv.addEventListener('click', function(e) {
                            e.stopPropagation();
                            selectUnit(searchQuery.trim());
                        });
                        unitList.appendChild(customDiv);
                    }
                }

                function createUnitOption(val, label) {
                    const div = document.createElement('div');
                    const isSelected = (val.toLowerCase() === (currentUnitValue || '').toLowerCase());
                    div.className = `unit-select-option py-1.5 px-2 rounded-2 ${isSelected ? 'active' : ''}`;
                    div.dataset.value = val;
                    div.innerHTML = `
                        <div class="d-flex align-items-center gap-1.5 text-truncate">
                            <span class="unit-option-title ${isSelected ? 'fw-bold text-primary' : ''}">${escapeHtml(label)}</span>
                        </div>
                        ${isSelected ? '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>' : ''}
                    `;
                    div.addEventListener('click', function(e) {
                        e.stopPropagation();
                        selectUnit(val);
                    });
                    return div;
                }

                function selectUnit(val) {
                    if (!val) return;
                    currentUnitValue = val;
                    if (unitInputHidden) {
                        unitInputHidden.value = val;
                        unitInputHidden.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                    if (unitBtnText) {
                        const displayLabel = unitOptionsData[val] || val;
                        unitBtnText.textContent = displayLabel;
                        unitBtnText.title = displayLabel;
                    }
                    if (unitTriggerBtn && window.bootstrap && bootstrap.Dropdown) {
                        const bsDrop = bootstrap.Dropdown.getInstance(unitTriggerBtn) || new bootstrap.Dropdown(unitTriggerBtn);
                        bsDrop.hide();
                    }
                }

                if (unitTriggerBtn) {
                    unitTriggerBtn.addEventListener('shown.bs.dropdown', function() {
                        renderUnits(unitQuickSearch ? unitQuickSearch.value : '');
                        if (unitQuickSearch) {
                            setTimeout(() => unitQuickSearch.focus(), 50);
                        }
                    });
                }

                if (unitQuickSearch) {
                    unitQuickSearch.addEventListener('input', function() {
                        const val = this.value;
                        if (unitClearSearch) {
                            if (val) unitClearSearch.classList.remove('d-none');
                            else unitClearSearch.classList.add('d-none');
                        }
                        renderUnits(val);
                    });

                    unitQuickSearch.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            const firstOption = unitList.querySelector('.unit-select-option');
                            if (firstOption) {
                                if (firstOption.classList.contains('custom-add-option')) {
                                    selectUnit(unitQuickSearch.value.trim());
                                } else if (firstOption.dataset.value) {
                                    selectUnit(firstOption.dataset.value);
                                }
                            }
                        }
                    });
                }

                if (unitClearSearch) {
                    unitClearSearch.addEventListener('click', function(e) {
                        e.stopPropagation();
                        if (unitQuickSearch) {
                            unitQuickSearch.value = '';
                            unitQuickSearch.focus();
                        }
                        this.classList.add('d-none');
                        renderUnits('');
                    });
                }

                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        const allRows = container.querySelectorAll('.procure-row');
                        if (allRows.length > 1) {
                            row.remove();
                        } else {
                            // Reset field jika tinggal 1 baris
                            const nameInput = row.querySelector('.item-name-input');
                            if (nameInput) nameInput.value = '';
                            const qtyInput = row.querySelector('input[name="quantity[]"]');
                            if (qtyInput) qtyInput.value = '1';
                            selectUnit('Buah');
                            const spec = row.querySelector('.item-spec-input');
                            if (spec) spec.value = '';
                        }
                    });
                }

                renderUnits('');
            }

            // Inisialisasi baris awal
            container.querySelectorAll('.procure-row').forEach(row => attachUnitEvents(row));

            // Handler tombol tambah baris baru
            if (addBtn) {
                addBtn.addEventListener('click', function() {
                    const firstRow = container.querySelector('.procure-row');
                    const newRow = firstRow.cloneNode(true);

                    // Reset values di row baru
                    const nameInput = newRow.querySelector('.item-name-input');
                    if (nameInput) nameInput.value = '';
                    const qtyInput = newRow.querySelector('input[name="quantity[]"]');
                    if (qtyInput) qtyInput.value = '1';
                    const spec = newRow.querySelector('.item-spec-input');
                    if (spec) spec.value = '';
                    const unitInput = newRow.querySelector('.item-unit-input');
                    if (unitInput) unitInput.value = 'Buah';
                    const unitBtnText = newRow.querySelector('.unit-btn-text');
                    if (unitBtnText) unitBtnText.textContent = 'Buah';
                    const searchInput = newRow.querySelector('.unit-quick-search');
                    if (searchInput) searchInput.value = '';
                    const clearBtn = newRow.querySelector('.btn-clear-unit-search');
                    if (clearBtn) clearBtn.classList.add('d-none');

                    container.appendChild(newRow);
                    attachUnitEvents(newRow);

                    if (nameInput) nameInput.focus();
                });
            }
        });
    </script>
@endpush
