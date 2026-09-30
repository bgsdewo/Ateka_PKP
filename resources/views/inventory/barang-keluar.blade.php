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

        {{-- Card Filter Periode Transaksi --}}
        <div class="row mb-4">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-danger-subtle text-danger p-2 rounded-circle">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                                </svg>
                            </span>
                            <div>
                                <h5 class="card-title mb-0 fw-bold">Filter Periode Barang Keluar</h5>
                                <small class="text-secondary">Saring riwayat pengeluaran barang berdasarkan bulan, tahun, atau rentang tanggal tertentu</small>
                            </div>
                        </div>
                        @if ($month || $year || $startDate || $endDate)
                            <a href="{{ route('barang-keluar') }}" class="btn btn-outline-danger btn-sm">
                                <svg class="icon-16 me-1" width="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reset Filter
                            </a>
                        @endif
                    </div>
                    <div class="card-body p-3">
                        <form method="GET" action="{{ route('barang-keluar') }}" id="filterFormKeluar">
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
                                    <button type="submit" class="btn btn-danger btn-sm w-100">
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
                            <h4 class="card-title mb-1">Data Persediaan Barang Keluar</h4>
                            <p class="mb-0 text-secondary">
                                Tabel transaksi keluar barang lengkap dengan rincian penerima, distribusi, dan kondisi.
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            @if ($startDate && $endDate)
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">
                                    Filter: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</strong>
                                </span>
                            @elseif ($startDate)
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">
                                    Filter Tanggal: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</strong>
                                </span>
                            @elseif ($endDate)
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">
                                    Filter s/d: <strong>{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</strong>
                                </span>
                            @elseif ($month || $year)
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">
                                    Filter: <strong>{{ $month ? \Carbon\Carbon::createFromDate(2026, (int)$month, 1)->translatedFormat('F') : '' }} {{ $year ?? '' }}</strong>
                                </span>
                            @endif
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">
                                Total: <strong>{{ number_format($totalTransactions) }} Transaksi</strong>
                                @if ($totalQuantity > 0)
                                    &bull; <strong>-{{ number_format($totalQuantity) }} Unit</strong>
                                @endif
                            </span>
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

                                            <!-- Keluar -->
                                            <td>
                                                @if ($transaction->type === 'out' || $transaction->type === 'keluar')
                                                    <span class="text-danger fw-bold">
                                                        -{{ number_format($transaction->quantity) }} {{ $transaction->product->unit ?? 'Pcs' }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </td>

                                            <!-- Stok Sisa -->
                                            <td>
                                                <strong>
                                                    {{ number_format($stockAfter) }} {{ $transaction->product->unit ?? '' }}
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
@endsection
