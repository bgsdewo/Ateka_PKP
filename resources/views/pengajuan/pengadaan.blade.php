@extends('layouts.app')

@section('page-header')
    <style>
        .iq-navbar-header {
            position: relative;
            background-image: linear-gradient(rgba(0, 70, 80, 0.45), rgba(0, 70, 80, 0.45)), url("{{ asset('hope-ui/assets/images/dashboard/bg-2.jpg') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .iq-navbar-header .iq-container {
            position: relative;
            z-index: 2;
        }
    </style>
    <div class="iq-navbar-header" style="height: 215px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="flex-wrap d-flex justify-content-between align-items-center text-white">
                        <div>
                            <h1 class="text-white fw-bold mb-1">Permintaan Pengadaan Barang ATK (Beli Baru)</h1>
                            <p class="text-white mb-0" style="opacity: 0.9;">
                                Usulkan kebutuhan barang ATK yang belum ada di gudang agar dibeli langsung oleh Subbag TU dari supplier/toko.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

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
                                            <input type="text" name="custom_unit[]" class="form-control item-unit-input text-center" 
                                                placeholder="Pcs/Rim/Dos" value="Pcs">
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

                function attachRowEvents(row) {
                    const nameInput = row.querySelector('.item-name-input');
                    const unitInput = row.querySelector('.item-unit-input');
                    const idInput = row.querySelector('.item-product-id');
                    const removeBtn = row.querySelector('.remove-procure-row');

                    if (nameInput) {
                        nameInput.addEventListener('input', function() {
                            const val = this.value.trim().toLowerCase();
                            if (productOptions[val]) {
                                if (unitInput && !unitInput.value) {
                                    unitInput.value = productOptions[val].unit;
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
                            } else {
                                // Reset fields if only 1 row
                                row.querySelectorAll('input').forEach(input => {
                                    if (input.type === 'number') {
                                        input.value = '1';
                                    } else if (input.classList.contains('item-unit-input')) {
                                        input.value = 'Pcs';
                                    } else {
                                        input.value = '';
                                    }
                                });
                            }
                        });
                    }
                }

                // Attach to initial row
                container.querySelectorAll('.procure-row').forEach(attachRowEvents);

                // Add row handler
                addBtn.addEventListener('click', function() {
                    const firstRow = container.querySelector('.procure-row');
                    const newRow = firstRow.cloneNode(true);

                    // Reset values
                    newRow.querySelectorAll('input').forEach(input => {
                        if (input.type === 'number') {
                            input.value = '1';
                        } else if (input.classList.contains('item-unit-input')) {
                            input.value = 'Pcs';
                        } else {
                            input.value = '';
                        }
                    });

                    container.appendChild(newRow);
                    attachRowEvents(newRow);
                    newRow.querySelector('.item-name-input').focus();
                });
            });
        </script>
    @endpush
@endsection
