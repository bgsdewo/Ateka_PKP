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
                    <div class="flex-wrap d-flex justify-content-between align-items-center">
                        <div>
                            <h1>Daftar Barang</h1>
                            <p>
                                Kelola master barang, stok awal, satuan, dan pencatatan persediaan barang.
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
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Card Filter Periode Transaksi / Pengadaan --}}
        <div class="row mb-4">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary-subtle text-primary p-2 rounded-circle">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                                </svg>
                            </span>
                            <div>
                                <h5 class="card-title mb-0 fw-bold">Filter Periode Daftar Barang</h5>
                                <small class="text-secondary">Saring riwayat faktur & persediaan barang berdasarkan bulan, tahun, atau rentang tanggal tertentu</small>
                            </div>
                        </div>
                        @if ($month || $year || $startDate || $endDate)
                            <a href="{{ route('master-data.daftar-barang.index') }}" class="btn btn-outline-danger btn-sm">
                                <svg class="icon-16 me-1" width="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reset Filter
                            </a>
                        @endif
                    </div>
                    <div class="card-body p-3">
                        <form method="GET" action="{{ route('master-data.daftar-barang.index') }}" id="filterForm">
                            <div class="row g-3 align-items-end">
                                {{-- Periode Bulan --}}
                                <div class="col-12 col-sm-6 col-md-3">
                                    <label class="form-label text-secondary small fw-bold text-uppercase mb-1">
                                        Periode Bulan
                                    </label>
                                    <select name="month" class="form-select form-select-sm">
                                        <option value="">Semua Bulan (Tahunan)</option>
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ sprintf('%02d', $m) }}" {{ ($month ?? '') == sprintf('%02d', $m) ? 'selected' : '' }}>
                                                {{ \Carbon\Carbon::createFromDate(2026, $m, 1)->translatedFormat('F') }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>

                                {{-- Periode Tahun (Dinamis jangka panjang) --}}
                                <div class="col-12 col-sm-6 col-md-3">
                                    <label class="form-label text-secondary small fw-bold text-uppercase mb-1">
                                        Periode Tahun
                                    </label>
                                    <select name="year" class="form-select form-select-sm">
                                        <option value="">Semua Tahun</option>
                                        @foreach ($availableYears as $y)
                                            <option value="{{ $y }}" {{ ($year ?? '') == $y ? 'selected' : '' }}>
                                                Tahun {{ $y }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Dari Tanggal --}}
                                <div class="col-12 col-sm-6 col-md-2">
                                    <label class="form-label text-secondary small fw-bold text-uppercase mb-1">
                                        Dari Tanggal
                                    </label>
                                    <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="form-control form-control-sm">
                                    <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">*Isi 1 tanggal untuk filter harian</small>
                                </div>

                                {{-- Sampai Tanggal --}}
                                <div class="col-12 col-sm-6 col-md-2">
                                    <label class="form-label text-secondary small fw-bold text-uppercase mb-1">
                                        Sampai Tanggal
                                    </label>
                                    <input type="date" name="end_date" value="{{ $endDate ?? '' }}" class="form-control form-control-sm">
                                    <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">*Opsional (rentang tanggal)</small>
                                </div>

                                {{-- Tombol Aksi --}}
                                <div class="col-12 col-sm-12 col-md-2 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                        <svg class="icon-16 me-1" width="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                        </svg>
                                        Terapkan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="header-title">
                            <h4 class="card-title mb-1">Master Data Barang</h4>
                            <p class="mb-0 text-secondary">
                                Kelola data barang seperti kode barang, kategori, satuan, stok, faktur, dan perolehan barang.
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            @if ($startDate && $endDate)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                                    Filter: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</strong>
                                </span>
                            @elseif ($startDate)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                                    Filter Tanggal: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</strong>
                                </span>
                            @elseif ($endDate)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                                    Filter s/d: <strong>{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</strong>
                                </span>
                            @elseif ($month || $year)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                                    Filter: <strong>{{ $month ? \Carbon\Carbon::createFromDate(2026, (int)$month, 1)->translatedFormat('F') : '' }} {{ $year ?? '' }}</strong>
                                </span>
                            @endif
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                                Total: <strong>{{ number_format($totalProducts) }} Barang</strong>
                            </span>
                            <button type="button" class="btn btn-primary btn-sm btn-create-barang">
                                Tambah Barang
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-striped table-hover word-wrap"
                                data-toggle="data-table">
                                <thead>
                                    <tr>
                                        <th width="4%">No</th>
                                        <th>No. Faktur</th>
                                        <th>Tgl Faktur</th>
                                        <th>Kode</th>
                                        <th>Nama Barang</th>
                                        <th>Kategori</th>
                                        <th>Uraian / Supplier</th>
                                        <th>Satuan</th>
                                        <th>Stok</th>
                                        <th>Harga Perolehan</th>
                                        <th>Total Nilai Saldo</th>
                                        <th>Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        @php
                                            $hargaBeli = $product->purchase_price ?? 0;
                                            $totalNilai = $product->stock * $hargaBeli;
                                            $noFaktur = $product->initial_no_faktur;
                                            $tglFaktur = $product->initial_transaction_date_formatted;
                                            $uraian = $product->initial_uraian;
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $noFaktur ?: '-' }}</span>
                                            </td>
                                            <td>
                                                {{ $tglFaktur }}
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $product->code }}</span>
                                            </td>
                                            <td>
                                                <strong>{{ $product->name }}</strong>
                                                @if ($product->description)
                                                    <br>
                                                    <small
                                                        class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $product->category->name ?? '-' }}</td>
                                            <td>{{ $uraian ?: '-' }}</td>
                                            <td>{{ $product->unit }}</td>
                                            <td>{{ number_format($product->stock) }}</td>
                                            <td>Rp {{ number_format($hargaBeli, 0, ',', '.') }}</td>
                                            <td><strong class="text-success">Rp
                                                    {{ number_format($totalNilai, 0, ',', '.') }}</strong></td>
                                            <td>
                                                @if ($product->stock <= 0)
                                                    <span class="badge bg-dark">Stok Habis</span>
                                                @else
                                                    <span class="badge bg-success">Tersedia</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1 flex-nowrap">
                                                    <button type="button" class="btn btn-info btn-sm btn-detail-barang"
                                                        data-id="{{ $product->id }}"
                                                        data-category-id="{{ $product->category_id }}"
                                                        data-category-name="{{ e($product->category->name ?? '-') }}"
                                                        data-code="{{ e($product->code) }}"
                                                        data-name="{{ e($product->name) }}"
                                                        data-unit="{{ e($product->unit) }}"
                                                        data-stock="{{ $product->stock }}"
                                                        data-minimum-stock="{{ $product->minimum_stock }}"
                                                        data-purchase-price="{{ $product->purchase_price }}"
                                                        data-no-faktur="{{ e($noFaktur ?? '-') }}"
                                                        data-transaction-date="{{ $product->initial_transaction_date ?? '' }}"
                                                        data-transaction-date-formatted="{{ $tglFaktur }}"
                                                        data-uraian="{{ e($uraian ?? '-') }}"
                                                        data-description="{{ e($product->description) }}"
                                                        data-transaction-count="{{ $product->stock_transactions_count }}"
                                                        data-created-at="{{ $product->created_at ? $product->created_at->format('d-m-Y H:i') : '-' }}"
                                                        data-updated-at="{{ $product->updated_at ? $product->updated_at->format('d-m-Y H:i') : '-' }}">
                                                        Detail
                                                    </button>
                                                    <button type="button" class="btn btn-warning btn-sm btn-edit-barang"
                                                        data-id="{{ $product->id }}"
                                                        data-category-id="{{ $product->category_id }}"
                                                        data-code="{{ e($product->code) }}"
                                                        data-name="{{ e($product->name) }}"
                                                        data-unit="{{ e($product->unit) }}"
                                                        data-stock="{{ $product->stock }}"
                                                        data-minimum-stock="{{ $product->minimum_stock }}"
                                                        data-purchase-price="{{ $product->purchase_price }}"
                                                        data-no-faktur="{{ e($noFaktur ?? '') }}"
                                                        data-transaction-date="{{ $product->initial_transaction_date ?? '' }}"
                                                        data-uraian="{{ e($uraian ?? '') }}"
                                                        data-description="{{ e($product->description) }}">
                                                        Edit
                                                    </button>
                                                    <form
                                                        action="{{ route('master-data.daftar-barang.destroy', $product->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Yakin ingin menghapus barang ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Create / Update / Detail --}}
    <div class="modal fade" id="modalBarang" tabindex="-1" aria-labelledby="modalBarangLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="formBarang" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="" disabled>

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalBarangLabel">Form Barang</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div id="detailBarangInfo" class="alert alert-info d-none mb-4">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Status Stok</small>
                                    <strong id="detailStatusStok">-</strong>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Riwayat Transaksi</small>
                                    <strong id="detailTransactionCount">0 Transaksi</strong>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Tanggal Dibuat</small>
                                    <strong id="detailCreatedAt">-</strong>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Terakhir Diupdate</small>
                                    <strong id="detailUpdatedAt">-</strong>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kategori Barang <span class="text-danger">*</span></label>
                                <select name="category_id" id="category_id" class="form-select" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kode Barang <span class="text-danger">*</span></label>
                                <input type="text" name="code" id="code" class="form-control"
                                    placeholder="Contoh: BRG-001" required>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="Contoh: Pulpen" required>
                            </div>

                            <!-- Tambahan No. Faktur, Tgl Faktur & Uraian agar sinkron dengan Barang Masuk -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">No. Faktur / Bukti Awal</label>
                                <input type="text" name="no_faktur" id="no_faktur" class="form-control"
                                    placeholder="Contoh: 121/FK">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tgl Faktur / Pengadaan</label>
                                <input type="date" name="transaction_date" id="transaction_date" class="form-control">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Uraian / Nama Supplier</label>
                                <input type="text" name="uraian" id="uraian" class="form-control"
                                    placeholder="Contoh: CV. Milia Pertama">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Satuan <span class="text-danger">*</span></label>
                                <select name="unit" id="unit" class="form-select" required>
                                    <option value="">Pilih Satuan</option>
                                    <option value="Pak">Pak</option>
                                    <option value="Lembar">Lembar</option>
                                    <option value="Dos">Dos / Dus</option>
                                    <option value="Batang">Batang</option>
                                    <option value="Set">Set</option>
                                    <option value="Rim">Rim</option>
                                    <option value="Buku">Buku</option>
                                    <option value="Buah">Buah</option>
                                    <option value="Roll">Roll</option>
                                    <option value="Tube">Tube</option>
                                    <option value="Bungkus">Bungkus</option>
                                    <option value="Pcs">Pcs</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Stok Awal <span class="text-danger">*</span></label>
                                <input type="number" name="stock" id="stock" class="form-control" min="0"
                                    value="0" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Harga Beli / Satuan <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="purchase_price" id="purchase_price" class="form-control"
                                        min="0" value="0" required>
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="description" id="description" class="form-control" rows="3"
                                    placeholder="Tulis deskripsi singkat barang"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitBarang">Simpan Barang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                const storeUrl = "{{ route('master-data.daftar-barang.store') }}";
                const updateUrlTemplate = "{{ route('master-data.daftar-barang.update', ':id') }}";

                const $modal = $('#modalBarang');
                const $form = $('#formBarang');
                const $method = $('#formMethod');
                const $title = $('#modalBarangLabel');
                const $btnSubmit = $('#btnSubmitBarang');
                const $categoryId = $('#category_id');
                const $code = $('#code');
                const $name = $('#name');
                const $noFaktur = $('#no_faktur');
                const $transactionDate = $('#transaction_date');
                const $uraian = $('#uraian');
                const $unit = $('#unit');
                const $stock = $('#stock');
                const $purchasePrice = $('#purchase_price');
                const $description = $('#description');
                const $detailInfo = $('#detailBarangInfo');
                const $detailStatusStok = $('#detailStatusStok');
                const $detailTransactionCount = $('#detailTransactionCount');
                const $detailCreatedAt = $('#detailCreatedAt');
                const $detailUpdatedAt = $('#detailUpdatedAt');

                function resetModalBarang() {
                    $form[0].reset();
                    $form.attr('action', storeUrl);
                    $method.prop('disabled', true).val('');
                    $categoryId.prop('disabled', false).val('');
                    $code.prop('readonly', false).val('');
                    $name.prop('readonly', false).val('');
                    $noFaktur.prop('readonly', false).val('');
                    $transactionDate.prop('readonly', false).val('');
                    $uraian.prop('readonly', false).val('');
                    $unit.prop('readonly', false).val('pcs');
                    $stock.prop('readonly', false).val(0);
                    $purchasePrice.prop('readonly', false).val(0);
                    $description.prop('readonly', false).val('');
                    $detailInfo.addClass('d-none');
                    $detailStatusStok.text('-');
                    $detailTransactionCount.text('0 Transaksi');
                    $detailCreatedAt.text('-');
                    $detailUpdatedAt.text('-');
                    $title.text('Form Barang');
                    $btnSubmit.removeClass('d-none').text('Simpan Barang');
                }

                // Event listener untuk tombol Tambah Barang
                $(document).on('click', '.btn-create-barang', function() {
                    resetModalBarang();
                    $title.text('Tambah Barang');
                    $btnSubmit.text('Simpan Barang');
                    $transactionDate.val(new Date().toISOString().split('T')[0]);
                    $modal.modal('show');
                });

                $(document).on('click', '.btn-edit-barang', function() {
                    resetModalBarang();
                    const id = $(this).data('id');
                    const updateUrl = updateUrlTemplate.replace(':id', id);

                    $form.attr('action', updateUrl);
                    $method.prop('disabled', false).val('PUT');
                    $categoryId.val($(this).data('category-id'));
                    $code.val($(this).data('code'));
                    $name.val($(this).data('name'));
                    $noFaktur.val($(this).data('no-faktur') || '');
                    $transactionDate.val($(this).data('transaction-date') || '');
                    $uraian.val($(this).data('uraian') || '');
                    $unit.val($(this).data('unit'));
                    $stock.val($(this).data('stock'));
                    $purchasePrice.val($(this).data('purchase-price'));
                    $description.val($(this).data('description'));

                    $title.text('Edit Barang');
                    $btnSubmit.text('Simpan Perubahan');
                    $modal.modal('show');
                });

                $(document).on('click', '.btn-detail-barang', function() {
                    resetModalBarang();
                    const categoryId = $(this).data('category-id');
                    const code = $(this).data('code');
                    const name = $(this).data('name');
                    const noFaktur = $(this).data('no-faktur');
                    const transactionDate = $(this).data('transaction-date');
                    const uraian = $(this).data('uraian');
                    const unit = $(this).data('unit');
                    const stock = parseInt($(this).data('stock'));
                    const purchasePrice = $(this).data('purchase-price');
                    const description = $(this).data('description');
                    const transactionCount = $(this).data('transaction-count');
                    const createdAt = $(this).data('created-at');
                    const updatedAt = $(this).data('updated-at');

                    $categoryId.val(categoryId);
                    $code.val(code);
                    $name.val(name);
                    $noFaktur.val(noFaktur ? noFaktur : '-');
                    $transactionDate.val(transactionDate || '');
                    $uraian.val(uraian ? uraian : '-');
                    $unit.val(unit);
                    $stock.val(stock);
                    $purchasePrice.val(purchasePrice);
                    $description.val(description ? description : '-');

                    $categoryId.prop('disabled', true);
                    $code.prop('readonly', true);
                    $name.prop('readonly', true);
                    $noFaktur.prop('readonly', true);
                    $transactionDate.prop('readonly', true);
                    $uraian.prop('readonly', true);
                    $unit.prop('readonly', true);
                    $stock.prop('readonly', true);
                    $purchasePrice.prop('readonly', true);
                    $description.prop('readonly', true);

                    if (stock <= 0) {
                        $detailStatusStok.html('<span class="badge bg-dark">Stok Habis</span>');
                    } else {
                        $detailStatusStok.html('<span class="badge bg-success">Tersedia</span>');
                    }

                    $detailTransactionCount.text(transactionCount + ' Transaksi');
                    $detailCreatedAt.text(createdAt);
                    $detailUpdatedAt.text(updatedAt);
                    $detailInfo.removeClass('d-none');
                    $title.text('Detail Barang');

                    $btnSubmit.addClass('d-none');
                    $modal.modal('show');
                });

                $modal.on('hidden.bs.modal', function() {
                    resetModalBarang();
                });
            });
        </script>
    @endpush
@endsection
