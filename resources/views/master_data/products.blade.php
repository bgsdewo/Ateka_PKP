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
                                Kelola master barang, stok awal, satuan, dan batas minimum persediaan.
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
        <div class="row">
            <div class="col-sm-12">

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

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="header-title">
                            <h4 class="card-title mb-1">Master Data Barang</h4>
                            <p class="mb-0 text-secondary">
                                Kelola data barang seperti kode barang, kategori, satuan, stok, dan stok minimum.
                            </p>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm btn-create-barang">
                            Tambah Barang
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover word-wrap">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Kode</th>
                                        <th>Nama Barang</th>
                                        <th>Kategori</th>
                                        <th>Satuan</th>
                                        <th>Stok</th>
                                        <th>Harga Perolehan</th>
                                        <th>Total Nilai Saldo</th>
                                        <th>Status</th>
                                        <th width="25%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($products as $product)
                                        @php
                                            $hargaBeli = $product->purchase_price ?? 0;
                                            $totalNilai = $product->stock * $hargaBeli;
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <span class="badge bg-primary">{{ $product->code }}</span>
                                            </td>
                                            <td>
                                                <strong>{{ $product->name }}</strong>
                                                @if ($product->description)
                                                    <br>
                                                    <small
                                                        class="text-muted">{{ Str::limit($product->description, 60) }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $product->category->name ?? '-' }}</td>
                                            <td>{{ $product->unit }}</td>
                                            <td>{{ number_format($product->stock) }}</td>
                                            <td>Rp {{ number_format($hargaBeli, 0, ',', '.') }}</td>
                                            <td><strong class="text-success">Rp
                                                    {{ number_format($totalNilai, 0, ',', '.') }}</strong></td>
                                            <td>
                                                @if ($product->stock <= 0)
                                                    <span class="badge bg-dark">Stok Habis</span>
                                                @elseif ($product->stock <= $product->minimum_stock)
                                                    <span class="badge bg-danger">Stok Minimum</span>
                                                @else
                                                    <span class="badge bg-success">Aman</span>
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
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center text-muted py-4">
                                                Belum ada data barang.
                                            </td>
                                        </tr>
                                    @endforelse
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

                            <!-- Tambahan No. Faktur & Uraian agar sinkron dengan Barang Masuk -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. Faktur / Bukti Awal</label>
                                <input type="text" name="no_faktur" id="no_faktur" class="form-control"
                                    placeholder="Contoh: 121/F/MK/V/2026">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Uraian / Nama Supplier</label>
                                <input type="text" name="uraian" id="uraian" class="form-control"
                                    placeholder="Contoh: CV. Milla Kirana">
                            </div>

                            <div class="col-md-3 mb-3">
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

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Stok Awal <span class="text-danger">*</span></label>
                                <input type="number" name="stock" id="stock" class="form-control" min="0"
                                    value="0" required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Stok Minimum <span class="text-danger">*</span></label>
                                <input type="number" name="minimum_stock" id="minimum_stock" class="form-control"
                                    min="0" value="10" required>
                            </div>

                            <div class="col-md-3 mb-3">
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
                const $uraian = $('#uraian');
                const $unit = $('#unit');
                const $stock = $('#stock');
                const $minimumStock = $('#minimum_stock');
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
                    $uraian.prop('readonly', false).val('');
                    $unit.prop('readonly', false).val('pcs');
                    $stock.prop('readonly', false).val(0);
                    $minimumStock.prop('readonly', false).val(10);
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
                    $unit.val($(this).data('unit'));
                    $stock.val($(this).data('stock'));
                    $minimumStock.val($(this).data('minimum-stock'));
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
                    const unit = $(this).data('unit');
                    const stock = parseInt($(this).data('stock'));
                    const minimumStock = parseInt($(this).data('minimum-stock'));
                    const purchasePrice = $(this).data('purchase-price');
                    const description = $(this).data('description');
                    const transactionCount = $(this).data('transaction-count');
                    const createdAt = $(this).data('created-at');
                    const updatedAt = $(this).data('updated-at');

                    $categoryId.val(categoryId);
                    $code.val(code);
                    $name.val(name);
                    $unit.val(unit);
                    $stock.val(stock);
                    $minimumStock.val(minimumStock);
                    $purchasePrice.val(purchasePrice);
                    $description.val(description ? description : '-');

                    $categoryId.prop('disabled', true);
                    $code.prop('readonly', true);
                    $name.prop('readonly', true);
                    $noFaktur.prop('readonly', true);
                    $uraian.prop('readonly', true);
                    $unit.prop('readonly', true);
                    $stock.prop('readonly', true);
                    $minimumStock.prop('readonly', true);
                    $purchasePrice.prop('readonly', true);
                    $description.prop('readonly', true);

                    if (stock <= 0) {
                        $detailStatusStok.html('<span class="badge bg-dark">Stok Habis</span>');
                    } else if (stock <= minimumStock) {
                        $detailStatusStok.html('<span class="badge bg-danger">Stok Minimum</span>');
                    } else {
                        $detailStatusStok.html('<span class="badge bg-success">Aman</span>');
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
