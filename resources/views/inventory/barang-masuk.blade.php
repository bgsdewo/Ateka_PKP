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
                            <h1>Transaksi Barang Masuk</h1>
                            <p>
                                Kelola transaksi barang masuk, faktur pembelian, dan pantau pergerakan stok persediaan.
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

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="header-title">
                            <h4 class="card-title mb-1">Data Persediaan Barang Masuk</h4>
                            <p class="mb-0 text-secondary">
                                Tabel transaksi masuk barang lengkap dengan detail faktur.
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-success btn-sm btn-barang-masuk" data-bs-toggle="modal"
                                data-bs-target="#modalBarangMasuk">
                                Tambah Barang Masuk
                            </button>
                        </div>
                    </div>

                    <!-- TABEL DATA -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-striped table-hover word-wrap"
                                data-toggle="data-table">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>No. Faktur</th>
                                        <th>Tgl Faktur</th>
                                        <th>Barang</th>
                                        <th>Uraian / Supplier</th>
                                        <th>Kondisi</th>
                                        <th>Harga Satuan</th>
                                        <th>Masuk</th>
                                        <th>Stok Sisa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $transaction)
                                        @php
                                            $stockAfter = (int) $transaction->stock_after;

                                            // Ambil harga perolehan dari transaksi, jika 0 fallback ke harga beli produknya
                                            $hargaFinal =
                                                $transaction->harga_perolehan > 0
                                                    ? $transaction->harga_perolehan
                                                    : $transaction->product->purchase_price ?? 0;

                                            $hargaPerolehan = 'Rp ' . number_format($hargaFinal, 0, ',', '.');
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>

                                            <td><span class="badge bg-secondary">{{ $transaction->no_faktur ?? '-' }}</span>
                                            </td>
                                            <td>
                                                {{ $transaction->transaction_date ? \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') : '-' }}
                                            </td>

                                            <td>
                                                <strong>{{ $transaction->product->name ?? '-' }}</strong> <br>
                                                <small class="text-primary">{{ $transaction->product->code ?? '-' }}</small>
                                            </td>

                                            <td>{{ $transaction->uraian ?? '-' }}</td>
                                            <td>{{ $transaction->kondisi ?? '-' }}</td>
                                            <td class="text-end">{{ $hargaPerolehan }}</td>

                                            <!-- Jumlah Masuk (Murni Angka Saja) -->
                                            <td>
                                                <span class="text-success fw-bold">
                                                    {{ number_format($transaction->quantity) }}
                                                </span>
                                            </td>

                                            <!-- Stok Sisa (Murni Angka Saja) -->
                                            <td>
                                                <strong>
                                                    {{ number_format($stockAfter) }}
                                                </strong>
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

    {{-- MODAL TAMBAH BARANG MASUK --}}
    <div class="modal fade" id="modalBarangMasuk" tabindex="-1" aria-labelledby="modalBarangMasukLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('inventory.transaction.store') }}" method="POST" id="formBarangMasuk">
                    @csrf
                    <input type="hidden" name="type" value="masuk">

                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title text-white" id="modalBarangMasukLabel">Form Tambah Barang Masuk</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <!-- Bagian Kiri: Dokumen & Faktur -->
                            <div class="col-md-6 border-end">
                                <h6 class="mb-3 text-primary">Informasi Dokumen</h6>
                                <div class="mb-3">
                                    <label class="form-label">No. Faktur / Bukti <span class="text-danger">*</span></label>
                                    <input type="text" name="no_faktur" class="form-control"
                                        placeholder="Contoh: 121/F/MK/V/2026" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Faktur <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_faktur" class="form-control"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Uraian / Nama Supplier <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="uraian" class="form-control"
                                        placeholder="Contoh: CV. Milla Kirana" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kondisi Barang <span class="text-danger">*</span></label>
                                    <select name="kondisi" class="form-select" required>
                                        <option value="Baru" selected>Baru</option>
                                        <option value="Baik">Baik</option>
                                        <option value="Rusak">Rusak / Cacat</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Bagian Kanan: Detail Barang & Harga -->
                            <div class="col-md-6">
                                <h6 class="mb-3 text-primary">Detail Barang Masuk</h6>
                                <div class="mb-3">
                                    <label class="form-label">Barang <span class="text-danger">*</span></label>
                                    <select name="product_id" id="product_masuk" class="form-select" required>
                                        <option value="">Pilih Barang...</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" data-unit="{{ e($product->unit) }}"
                                                data-stock="{{ $product->stock }}">
                                                {{ $product->code }} - {{ $product->name }} | Stok Sisa:
                                                {{ $product->stock }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Jumlah Masuk <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" name="quantity" id="quantity_masuk"
                                                class="form-control" min="1" placeholder="Contoh: 120" required>
                                            <span class="input-group-text" id="unit_addon">-</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Harga Beli / Satuan <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" name="harga_perolehan" class="form-control"
                                                min="0" placeholder="Contoh: 250000" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Keterangan Tambahan</label>
                                    <textarea name="keterangan" class="form-control" rows="3"
                                        placeholder="Contoh: Barang dalam kemasan dus isi 12 pcs"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <svg class="icon-20 me-1" width="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Simpan Barang Masuk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#product_masuk').on('change', function() {
                    const selected = $(this).find(':selected');
                    const unit = selected.data('unit') || '-';
                    $('#unit_addon').text(unit);
                });

                $('#modalBarangMasuk').on('hidden.bs.modal', function() {
                    $('#formBarangMasuk')[0].reset();
                    $('#unit_addon').text('-');
                });
            });
        </script>
    @endpush
@endsection
