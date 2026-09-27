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
                            <h1>Transaksi Barang Keluar</h1>
                            <p>
                                Kelola transaksi barang keluar, distribusi, dan pantau pergerakan stok persediaan.
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
                            <h4 class="card-title mb-1">Data Persediaan Barang Keluar</h4>
                            <p class="mb-0 text-secondary">
                                Tabel transaksi keluar barang lengkap dengan rincian penerima dan kondisi.
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-danger btn-sm btn-barang-keluar" data-bs-toggle="modal"
                                data-bs-target="#modalBarangKeluar">
                                Tambah Barang Keluar
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
                                        <th>Tanggal</th>
                                        <th>Barang</th>
                                        <th>Uraian / Penerima</th>
                                        <th>Kondisi</th>
                                        <th>Keluar</th>
                                        <th>Stok Sisa</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $transaction)
                                        @php
                                            // Ambil sisa stok dari kolom database atau fallback ke stok produk saat ini
                                            $stockAfter = isset($transaction->stock_after)
                                                ? (int) $transaction->stock_after
                                                : $transaction->product->stock ?? 0;

                                            if ($stockAfter > 0) {
                                                $statusLabel = 'Tersedia';
                                                $statusClass = 'bg-success';
                                            } else {
                                                $statusLabel = 'Tidak Tersedia';
                                                $statusClass = 'bg-danger';
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>

                                            <td>{{ $transaction->transaction_date ? \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') : '-' }}
                                            </td>

                                            <td>
                                                <strong>{{ $transaction->product->name ?? '-' }}</strong> <br>
                                                <small class="text-primary">{{ $transaction->product->code ?? '-' }}</small>
                                            </td>

                                            <td>{{ $transaction->description ?? ($transaction->uraian ?? '-') }}</td>
                                            <td>{{ $transaction->kondisi ?? 'Baik' }}</td>

                                            <!-- Keluar (Murni Angka Saja) -->
                                            <td>
                                                @if ($transaction->type === 'out' || $transaction->type === 'keluar')
                                                    <span class="text-danger fw-bold">
                                                        {{ number_format($transaction->quantity) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </td>

                                            <!-- Stok Sisa (Murni Angka Saja) -->
                                            <td>
                                                <strong>
                                                    {{ number_format($stockAfter) }}
                                                </strong>
                                            </td>
                                            <td>
                                                <span class="badge {{ $statusClass }}">
                                                    {{ $statusLabel }}
                                                </span>
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

    {{-- MODAL BARANG KELUAR --}}
    <div class="modal fade" id="modalBarangKeluar" tabindex="-1" aria-labelledby="modalBarangKeluarLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('inventory.transaction.store') }}" method="POST" id="formBarangKeluar">
                    @csrf
                    <input type="hidden" name="type" value="keluar">

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title text-white" id="modalBarangKeluarLabel">Form Tambah Barang Keluar</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <!-- Bagian Kiri: Informasi Distribusi / Penerima -->
                            <div class="col-md-6 border-end">
                                <h6 class="mb-3 text-danger">Informasi Penyerahan</h6>
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                                    <input type="date" name="transaction_date" class="form-control"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Uraian / Nama Penerima <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="uraian" class="form-control"
                                        placeholder="Contoh: Muhammad Ariq Irsyad" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kondisi Barang <span class="text-danger">*</span></label>
                                    <select name="kondisi" class="form-select" required>
                                        <option value="Baik" selected>Baik</option>
                                        <option value="Baru">Baru</option>
                                        <option value="Rusak">Rusak</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nomor Bukti / Faktur (Opsional)</label>
                                    <input type="text" name="no_faktur" class="form-control"
                                        placeholder="Contoh: BKK/2026/001">
                                </div>
                            </div>

                            <!-- Bagian Kanan: Detail Barang Keluar -->
                            <div class="col-md-6">
                                <h6 class="mb-3 text-danger">Detail Barang Keluar</h6>
                                <div class="mb-3">
                                    <label class="form-label">Barang <span class="text-danger">*</span></label>
                                    <select name="product_id" id="product_keluar" class="form-select" required>
                                        <option value="">Pilih Barang...</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" data-unit="{{ e($product->unit) }}"
                                                data-stock="{{ $product->stock }}"
                                                {{ $product->stock <= 0 ? 'disabled' : '' }}>
                                                {{ $product->code }} - {{ $product->name }} | Stok Tersedia:
                                                {{ $product->stock }}
                                                {{ $product->stock <= 0 ? '(Kosong)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Jumlah Keluar <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="quantity" id="quantity_keluar" class="form-control"
                                            min="1" placeholder="Contoh: 5" required>
                                        <span class="input-group-text" id="unit_addon">-</span>
                                    </div>
                                    <small class="text-muted" id="stok_keluar_info">Pilih barang untuk melihat batas stok
                                        keluar.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Keterangan Tambahan</label>
                                    <textarea name="description" class="form-control" rows="3"
                                        placeholder="Contoh: Keperluan kantor bagian administrasi"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">
                            Simpan Barang Keluar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#product_keluar').on('change', function() {
                    const selected = $(this).find(':selected');
                    const unit = selected.data('unit') || '-';
                    const stock = parseInt(selected.data('stock')) || 0;

                    $('#unit_addon').text(unit);

                    if ($(this).val()) {
                        $('#quantity_keluar').attr('max', stock);
                        $('#stok_keluar_info').text('Maksimal barang keluar: ' + stock + ' ' + unit);
                    } else {
                        $('#quantity_keluar').removeAttr('max');
                        $('#stok_keluar_info').text('Pilih barang untuk melihat batas stok keluar.');
                    }
                });

                $('#formBarangKeluar').on('submit', function(e) {
                    const selected = $('#product_keluar').find(':selected');
                    const stock = parseInt(selected.data('stock')) || 0;
                    const quantity = parseInt($('#quantity_keluar').val()) || 0;

                    if (quantity > stock) {
                        e.preventDefault();
                        alert('Jumlah barang keluar tidak boleh melebihi stok tersedia (' + stock + ').');
                        return false;
                    }
                });

                $('#modalBarangKeluar').on('hidden.bs.modal', function() {
                    $('#formBarangKeluar')[0].reset();
                    $('#unit_addon').text('-');
                    $('#quantity_keluar').removeAttr('max');
                    $('#stok_keluar_info').text('Pilih barang untuk melihat batas stok keluar.');
                });
            });
        </script>
    @endpush
@endsection
