@extends('layouts.app')

@section('page-header')
    <div class="iq-navbar-header" style="height: 220px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="flex-wrap d-flex justify-content-between align-items-center text-white">
                        <div>
                            <h1 class="text-white fw-bold mb-1">Transaksi Barang Masuk</h1>
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

        {{-- Card Filter Periode Transaksi --}}
        <div class="row mb-4">
            <div class="col-sm-12">
                <div class="card shadow-sm border-0" style="border-radius: 14px;">
                    <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2 bg-white" style="border-top-left-radius: 14px; border-top-right-radius: 14px;">
                        <div class="d-flex align-items-center gap-2.5">
                            <span class="badge bg-primary-subtle text-primary p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                                </svg>
                            </span>
                            <div>
                                <h6 class="card-title mb-0 fw-bold text-dark" style="font-size: 0.95rem;">Filter Periode Barang Masuk</h6>
                            </div>
                        </div>
                        @if ($month || $year || $startDate || $endDate)
                            <a href="{{ route('barang-masuk') }}" class="btn btn-outline-danger btn-sm px-3 d-inline-flex align-items-center gap-1.5 fw-semibold" style="height: 34px; border-radius: 8px; font-size: 0.8rem;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reset Filter
                            </a>
                        @endif
                    </div>
                    <div class="card-body p-3.5 bg-white" style="border-bottom-left-radius: 14px; border-bottom-right-radius: 14px;">
                        <form method="GET" action="{{ route('barang-masuk') }}" id="filterForm">
                            <div class="row g-2.5 align-items-end">
                                {{-- Periode Bulan --}}
                                <div class="col-12 col-sm-6 col-md-3">
                                    <label class="form-label text-secondary small fw-bold text-uppercase mb-1.5" style="font-size: 0.72rem; letter-spacing: 0.04em;">
                                        Periode Bulan
                                    </label>
                                    <select name="month" class="form-select form-select-sm" style="height: 38px; border-radius: 8px; font-size: 0.84rem; border-color: #cbd5e1; background-color: #f8fafc;">
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
                                    <label class="form-label text-secondary small fw-bold text-uppercase mb-1.5" style="font-size: 0.72rem; letter-spacing: 0.04em;">
                                        Periode Tahun
                                    </label>
                                    <select name="year" class="form-select form-select-sm" style="height: 38px; border-radius: 8px; font-size: 0.84rem; border-color: #cbd5e1; background-color: #f8fafc;">
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
                                    <label class="form-label text-secondary small fw-bold text-uppercase mb-1.5" style="font-size: 0.72rem; letter-spacing: 0.04em;">
                                        Dari Tanggal
                                    </label>
                                    <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="form-control form-control-sm" style="height: 38px; border-radius: 8px; font-size: 0.84rem; border-color: #cbd5e1; background-color: #f8fafc;">
                                </div>

                                {{-- Sampai Tanggal --}}
                                <div class="col-12 col-sm-6 col-md-2">
                                    <label class="form-label text-secondary small fw-bold text-uppercase mb-1.5" style="font-size: 0.72rem; letter-spacing: 0.04em;">
                                        Sampai Tanggal
                                    </label>
                                    <input type="date" name="end_date" value="{{ $endDate ?? '' }}" class="form-control form-control-sm" style="height: 38px; border-radius: 8px; font-size: 0.84rem; border-color: #cbd5e1; background-color: #f8fafc;">
                                </div>

                                {{-- Tombol Aksi --}}
                                <div class="col-12 col-sm-12 col-md-2">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold d-inline-flex align-items-center justify-content-center gap-1.5 shadow-sm" style="height: 38px; border-radius: 8px; font-size: 0.84rem;">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                                            </svg>
                                            <span>Terapkan</span>
                                        </button>
                                        @if ($month || $year || $startDate || $endDate)
                                            <a href="{{ route('barang-masuk') }}" class="btn btn-light border btn-sm px-2.5 d-inline-flex align-items-center justify-content-center shadow-xs" title="Reset Filter" style="height: 38px; border-radius: 8px; font-size: 0.84rem;">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-danger">
                                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card shadow-sm border-0" style="border-radius: 14px;">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3 border-bottom py-3 bg-white" style="border-top-left-radius: 14px; border-top-right-radius: 14px;">
                        <div class="header-title">
                            <h5 class="card-title mb-0 fw-bold text-dark">Data Persediaan Barang Masuk</h5>
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
                        </div>
                    </div>

                    <!-- TABEL DATA -->
                    <div class="card-body">
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

                                            <!-- Jumlah Masuk -->
                                            <td>
                                                <span class="text-success fw-bold">
                                                    {{ number_format($transaction->quantity) }}
                                                </span>
                                                @if ($transaction->satuan_beli && $transaction->jumlah_beli && strtolower($transaction->satuan_beli) !== strtolower($transaction->product->unit ?? ''))
                                                    <br>
                                                    <small class="badge bg-light text-secondary border mt-1">
                                                        {{ $transaction->jumlah_beli }} {{ $transaction->satuan_beli }}
                                                        @if ($transaction->isi_per_kemasan > 1)
                                                            (@ {{ $transaction->isi_per_kemasan }} {{ $transaction->product->unit ?? 'Pcs' }})
                                                        @endif
                                                    </small>
                                                @endif
                                            </td>

                                            <!-- Stok Sisa -->
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
@endsection
