@extends('layouts.app')

@section('page-header')
    <div class="iq-navbar-header" style="height: 220px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="flex-wrap d-flex justify-content-between align-items-center text-white">
                        <div>
                            <h1 class="text-white fw-bold mb-1">Transaksi Barang Keluar</h1>
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
                            <span class="badge bg-danger-subtle text-danger p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                                </svg>
                            </span>
                            <div>
                                <h6 class="card-title mb-0 fw-bold text-dark" style="font-size: 0.95rem;">Filter Periode Barang Keluar</h6>
                            </div>
                        </div>
                        @if ($month || $year || $startDate || $endDate)
                            <a href="{{ route('barang-keluar') }}" class="btn btn-outline-danger btn-sm px-3 d-inline-flex align-items-center gap-1.5 fw-semibold" style="height: 34px; border-radius: 8px; font-size: 0.8rem;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reset Filter
                            </a>
                        @endif
                    </div>
                    <div class="card-body p-3.5 bg-white" style="border-bottom-left-radius: 14px; border-bottom-right-radius: 14px;">
                        <form method="GET" action="{{ route('barang-keluar') }}" id="filterFormKeluar">
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
                                        <button type="submit" class="btn btn-danger btn-sm w-100 fw-bold d-inline-flex align-items-center justify-content-center gap-1.5 shadow-sm" style="height: 38px; border-radius: 8px; font-size: 0.84rem;">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                                            </svg>
                                            <span>Terapkan</span>
                                        </button>
                                        @if ($month || $year || $startDate || $endDate)
                                            <a href="{{ route('barang-keluar') }}" class="btn btn-light border btn-sm px-2.5 d-inline-flex align-items-center justify-content-center shadow-xs" title="Reset Filter" style="height: 38px; border-radius: 8px; font-size: 0.84rem;">
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
                            <h5 class="card-title mb-0 fw-bold text-dark">Data Persediaan Barang Keluar</h5>
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
                        </div>
                    </div>

                    <!-- TABEL DATA -->
                    <div class="card-body">
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
                                                $statusLabel = 'Stok Belum Tersedia';
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

                                            <td>
                                                <div class="d-flex flex-column gap-1">
                                                    <div>
                                                        <span class="badge bg-light text-dark border me-1" style="font-size: 0.72rem;">Penerima</span>
                                                        <strong>{{ $transaction->recipient_name }}</strong>
                                                    </div>
                                                    @if ($transaction->handed_over_by)
                                                        <div class="small text-muted" style="font-size: 0.75rem;">
                                                            <span class="text-success fw-semibold">Diserahkan oleh:</span> {{ $transaction->handed_over_by }}
                                                        </div>
                                                    @elseif ($transaction->user)
                                                        <div class="small text-muted" style="font-size: 0.72rem;">
                                                            <span class="text-secondary">Petugas Gudang:</span> {{ $transaction->user->name }}
                                                        </div>
                                                    @endif
                                                    @if (!empty($transaction->uraian) && $transaction->uraian !== $transaction->recipient_name)
                                                        <div class="small text-secondary" style="font-size: 0.72rem;">
                                                            {{ $transaction->uraian }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>{{ $transaction->kondisi ?? 'Baik' }}</td>

                                            <!-- Keluar -->
                                            <td>
                                                @if ($transaction->type === 'out' || $transaction->type === 'keluar')
                                                    <span class="text-danger fw-bold">
                                                        {{ number_format($transaction->quantity) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </td>

                                            <!-- Stok Sisa -->
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
@endsection
