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
                            <h1 class="text-white fw-bold mb-1">Form Pengajuan Permintaan ATK</h1>
                            <p class="text-white mb-0" style="opacity: 0.9;">
                                Ajukan kebutuhan Alat Tulis Kantor secara resmi untuk perorangan atau kolektif Subdirektorat.
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

                            <div id="product-rows">
                                <div class="row product-row align-items-center mb-3 p-3 rounded border bg-light">
                                    <div class="col-md-6 mb-2 mb-md-0">
                                        <label class="form-label fw-bold small">Pilih Barang ATK <span class="text-danger">*</span></label>
                                        <select name="product_id[]" class="form-select product-select" required>
                                            <option value="">-- Pilih Barang dari Gudang --</option>
                                            @foreach ($products as $product)
                                                @php
                                                    $isSelected = (isset($selectedProductId) && $selectedProductId == $product->id);
                                                @endphp
                                                <option value="{{ $product->id }}" 
                                                    data-stock="{{ $product->stock }}"
                                                    data-unit="Stok"
                                                    {{ $isSelected ? 'selected' : '' }}>
                                                    {{ $product->code }} - {{ $product->name }} (Stok: {{ $product->stock }} Stok{{ $product->stock <= 0 ? ' - Perlu Dibeli TU' : '' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-2 mb-md-0">
                                        <label class="form-label fw-bold small">Jumlah Permintaan <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" name="quantity[]" class="form-control quantity-input" min="1"
                                                value="1" required>
                                            <span class="input-group-text unit-addon">Stok</span>
                                        </div>
                                        <small class="text-muted stock-info">Pilih barang untuk melihat info stok.</small>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end justify-content-end">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-row w-100" title="Hapus atau reset baris barang ini">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            </svg>
                                            Hapus
                                        </button>
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

                function initRow(row) {
                    const select = row.querySelector('.product-select');
                    const qtyInput = row.querySelector('.quantity-input');
                    const unitAddon = row.querySelector('.unit-addon');
                    const stockInfo = row.querySelector('.stock-info');

                    select.addEventListener('change', function() {
                        const opt = this.options[this.selectedIndex];
                        const stock = parseInt(opt.getAttribute('data-stock')) || 0;

                        unitAddon.textContent = 'Stok';

                        if (this.value) {
                            qtyInput.removeAttribute('max');
                            if (stock > 0) {
                                stockInfo.innerHTML = '<span class="text-success fw-semibold">Sisa stok gudang: ' + stock + ' Stok.</span> Jika permintaan melebihi stok, kekurangan akan dibeli oleh Kasubag TU.';
                            } else {
                                stockInfo.innerHTML = '<span class="text-warning fw-semibold">Stok gudang: 0 (Kosong).</span> Permintaan ini akan diproses untuk dibeli oleh Kasubag TU.';
                            }
                        } else {
                            qtyInput.removeAttribute('max');
                            stockInfo.textContent = 'Pilih barang untuk melihat info stok.';
                        }
                    });

                    // Trigger if already selected
                    if (select.value) {
                        select.dispatchEvent(new Event('change'));
                    }
                }

                // Init existing rows
                productRows.querySelectorAll('.product-row').forEach(initRow);

                addRowBtn.addEventListener('click', function() {
                    const firstRow = productRows.querySelector('.product-row');
                    const newRow = firstRow.cloneNode(true);

                    // Reset values in new row
                    const select = newRow.querySelector('select');
                    select.value = '';
                    const qty = newRow.querySelector('input[type="number"]');
                    qty.value = '1';
                    qty.removeAttribute('max');
                    newRow.querySelector('.unit-addon').textContent = 'Stok';
                    newRow.querySelector('.stock-info').textContent = 'Pilih barang untuk melihat info stok.';

                    productRows.appendChild(newRow);
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
                        const unitAddon = row.querySelector('.unit-addon');
                        if (unitAddon) {
                            unitAddon.textContent = 'Stok';
                        }
                        const stockInfo = row.querySelector('.stock-info');
                        if (stockInfo) {
                            stockInfo.textContent = 'Pilih barang untuk melihat info stok.';
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection
