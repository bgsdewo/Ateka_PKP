@extends('layouts.app')

@php
    $kasubagUser = \App\Models\User::whereIn('role', ['kasubag_tu', 'admin'])->first();
    $kasubagName = $kasubagUser ? ($kasubagUser->name_with_title ?: $kasubagUser->name) : 'Utami M.';
    $kasubagNip = $kasubagUser->nip ?? '199812132025062010';
@endphp

@section('page-header')
    <style>
        .signature-section {
            display: none;
        }

        @media print {
            .signature-section {
                display: block !important;
                margin-top: 40px;
                page-break-inside: avoid;
            }
        }
    </style>
    <div class="iq-navbar-header" style="height: 220px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 text-white">
                        <div>
                            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2" style="background: rgba(255, 255, 255, 0.12); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.18); font-size: 0.72rem; letter-spacing: 0.05em; text-transform: uppercase; font-weight: 700;">
                                <span class="badge-dot badge-dot-primary"></span>
                                <span>Kementerian PKP RI &bull; Pelaporan &amp; Pengesahan Mutasi</span>
                            </div>
                            <h1 class="text-white fw-bold mb-1">Laporan Persediaan Barang</h1>
                            <p class="text-white mb-0" style="opacity: 0.92; max-width: 820px;">
                                Pantau pergerakan transaksi keluar-masuk barang, ringkasan mutasi, dan ekspor dokumen resmi berstandar instansi.
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
        /* Card modern styling */
        .report-card {
            border-radius: 16px;
            border: 1px solid rgba(226, 232, 240, 0.85);
            background: #ffffff;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
            overflow: hidden;
        }

        /* 3 Summary KPI Cards */
        .kpi-card {
            border-radius: 16px;
            border: 1px solid rgba(226, 232, 240, 0.85);
            background: #ffffff;
            box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.04);
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
        }

        .kpi-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px -4px rgba(15, 23, 42, 0.08);
        }

        .kpi-icon-wrapper {
            width: 48px;
            height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            flex-shrink: 0;
        }

        /* Subtle badge colors */
        .bg-primary-subtle {
            background-color: #eef2ff !important;
            color: #3a57e8 !important;
        }

        .bg-success-subtle {
            background-color: #ecfdf5 !important;
            color: #059669 !important;
        }

        .bg-danger-subtle {
            background-color: #fef2f2 !important;
            color: #dc2626 !important;
        }

        .bg-secondary-subtle {
            background-color: #f1f5f9 !important;
            color: #475569 !important;
        }

        .border-primary-subtle {
            border-color: #c7d2fe !important;
        }

        .border-success-subtle {
            border-color: #a7f3d0 !important;
        }

        .border-danger-subtle {
            border-color: #fecaca !important;
        }

        /* Form Controls */
        .form-control-modern {
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 0.65rem 0.9rem;
            font-size: 0.875rem;
            background-color: #f8fafc;
            color: #1e293b;
            transition: all 0.2s ease;
            width: 100%;
        }

        .form-select-modern {
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 0.65rem 2.4rem 0.65rem 0.9rem !important;
            font-size: 0.875rem;
            background-color: #f8fafc;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23475569' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right 0.85rem center !important;
            background-size: 14px 10px !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            appearance: none !important;
            color: #1e293b;
            transition: all 0.2s ease;
            width: 100%;
            cursor: pointer;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .form-control-modern:focus,
        .form-select-modern:focus {
            background-color: #ffffff;
            border-color: #3a57e8;
            box-shadow: 0 0 0 3px rgba(58, 87, 232, 0.15);
            outline: none;
        }

        /* Quick Filter Preset Chips */
        .preset-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.815rem;
            font-weight: 500;
            padding: 0.6rem 1rem;
            border-radius: 10px;
            cursor: pointer;
            border: 1px solid #cbd5e1;
            background-color: #ffffff;
            color: #334155;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            user-select: none;
            text-decoration: none;
            white-space: nowrap;
            line-height: 1.2;
        }

        .preset-chip:hover {
            background-color: #eef2ff;
            color: #3a57e8;
            border-color: #3a57e8;
            box-shadow: 0 2px 6px rgba(58, 87, 232, 0.12);
        }

        /* Modern Data Table */
        .custom-report-table {
            width: 100% !important;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
            table-layout: auto;
        }

        .custom-report-table thead th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border-top: 1px solid #e2e8f0;
            border-bottom: 2px solid #cbd5e1;
            padding: 12px 8px;
            vertical-align: middle;
            white-space: nowrap;
        }

        .custom-report-table tbody td {
            padding: 11px 8px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            font-size: 0.835rem;
        }

        .custom-report-table tbody tr:hover {
            background-color: #f8faff !important;
        }

        /* Kolom spesifik agar fit responsif */
        .col-nama-barang {
            max-width: 160px;
            word-break: break-word;
        }

        .col-catatan-teks {
            max-width: 140px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Hide default DataTables buttons container because we use custom header buttons */
        div.dt-buttons {
            display: none !important;
        }

        .dataTables_filter {
            text-align: right;
        }

        .dataTables_filter input {
            border-radius: 20px !important;
            padding: 6px 16px !important;
            border: 1px solid #cbd5e1 !important;
            background-color: #f8fafc !important;
            outline: none !important;
            font-size: 0.85rem !important;
            transition: all 0.2s ease !important;
            width: 220px !important;
            margin-left: 0.5rem !important;
        }

        .dataTables_filter input:focus {
            background-color: #ffffff !important;
            border-color: #3a57e8 !important;
            box-shadow: 0 0 0 3px rgba(58, 87, 232, 0.15) !important;
            width: 260px !important;
        }

        .dataTables_length label {
            font-size: 0.85rem;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .dataTables_length select {
            border-radius: 8px !important;
            padding: 4px 26px 4px 10px !important;
            border: 1px solid #cbd5e1 !important;
            background-color: #f8fafc !important;
            font-size: 0.85rem !important;
        }

        .user-avatar-initial {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background-color: #e2e8f0;
            color: #475569;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.72rem;
            font-weight: 700;
            flex-shrink: 0;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        {{-- 3 KARTU RINGKASAN: Total Transaksi, Total Masuk (Pcs), Total Keluar (Pcs) --}}
        <div class="row g-3 mb-4">
            {{-- Card 1: Total Transaksi --}}
            <div class="col-12 col-md-4">
                <div class="card kpi-card h-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-secondary small fw-bold text-uppercase"
                                style="letter-spacing: 0.05em;">Total Transaksi</span>
                            <div class="kpi-icon-wrapper bg-primary-subtle text-primary">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h2 class="fw-bold mb-1 text-dark">{{ number_format($totalTransactions) }}</h2>
                            <span class="badge bg-primary-subtle text-primary fw-medium rounded-pill px-2.5 py-1">
                                Seluruh mutasi tercatat
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Total Barang Masuk (Pcs) --}}
            <div class="col-12 col-md-4">
                <div class="card kpi-card h-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-secondary small fw-bold text-uppercase"
                                style="letter-spacing: 0.05em;">Total Barang Masuk</span>
                            <div class="kpi-icon-wrapper bg-success-subtle text-success">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <polyline points="19 12 12 19 5 12"></polyline>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h2 class="fw-bold mb-1 text-success">+{{ number_format($totalIn) }} <small
                                    class="fs-6 fw-normal text-muted">Pcs</small></h2>
                            <span class="badge bg-success-subtle text-success fw-medium rounded-pill px-2.5 py-1">
                                Total barang yang diterima
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 3: Total Barang Keluar (Pcs) --}}
            <div class="col-12 col-md-4">
                <div class="card kpi-card h-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-secondary small fw-bold text-uppercase"
                                style="letter-spacing: 0.05em;">Total Barang Keluar</span>
                            <div class="kpi-icon-wrapper bg-danger-subtle text-danger">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="19" x2="12" y2="5"></line>
                                    <polyline points="5 12 12 5 19 12"></polyline>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h2 class="fw-bold mb-1 text-danger">-{{ number_format($totalOut) }} <small
                                    class="fs-6 fw-normal text-muted">Pcs</small></h2>
                            <span class="badge bg-danger-subtle text-danger fw-medium rounded-pill px-2.5 py-1">
                                Total barang yang dikeluarkan
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Filter Laporan (Responsive & User-Friendly) --}}
        <div class="row">
            <div class="col-sm-12">
                <div class="card report-card mb-4">
                    <div
                        class="card-header border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="kpi-icon-wrapper bg-primary-subtle text-primary" style="width: 38px; height: 38px;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                                </svg>
                            </div>
                            <div>
                                <h5 class="card-title mb-0 fw-bold">Filter & Saring Laporan Persediaan</h5>
                                <small class="text-secondary">Saring data berdasarkan periode bulan, tahun, jenis mutasi, atau kategori pemohon</small>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <form id="filterReportForm" action="{{ route('reports.index') }}" method="GET">
                            <div class="row g-3">
                                {{-- Dropdown 1: Periode Bulan --}}
                                <div class="col-12 col-sm-6 col-md-3">
                                    <label class="form-label text-secondary small fw-bold text-uppercase mb-1">
                                        Periode Bulan
                                    </label>
                                    <select name="month" class="form-select form-select-modern">
                                        <option value="">Semua Bulan (Tahunan)</option>
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ sprintf('%02d', $m) }}" {{ ($month ?? '') == sprintf('%02d', $m) ? 'selected' : '' }}>
                                                {{ \Carbon\Carbon::createFromDate(2026, $m, 1)->translatedFormat('F') }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>

                                {{-- Dropdown 2: Periode Tahun --}}
                                <div class="col-12 col-sm-6 col-md-3">
                                    <label class="form-label text-secondary small fw-bold text-uppercase mb-1">
                                        Periode Tahun
                                    </label>
                                    <select name="year" class="form-select form-select-modern">
                                        <option value="">Semua Tahun</option>
                                        @for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                                            <option value="{{ $y }}" {{ ($year ?? '') == $y ? 'selected' : '' }}>
                                                {{ $y }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>

                                {{-- Dropdown 3: Jenis Mutasi --}}
                                <div class="col-12 col-sm-6 col-md-3">
                                    <label class="form-label text-secondary small fw-bold text-uppercase mb-1">
                                        Jenis Mutasi
                                    </label>
                                    <select name="type" id="filter-type" class="form-select form-select-modern">
                                        <option value="">Semua (Masuk & Keluar)</option>
                                        <option value="masuk" {{ ($type ?? '') == 'masuk' ? 'selected' : '' }}>
                                            Hanya Barang Masuk
                                        </option>
                                        <option value="keluar" {{ ($type ?? '') == 'keluar' ? 'selected' : '' }}>
                                            Hanya Barang Keluar
                                        </option>
                                    </select>
                                </div>

                                {{-- Dropdown 4: Kategori Pemohon / Penerima --}}
                                <div class="col-12 col-sm-6 col-md-3">
                                    <label class="form-label text-secondary small fw-bold text-uppercase mb-1">
                                        Kategori Pemohon
                                    </label>
                                    <select name="recipient_type" id="filter-recipient-type" class="form-select form-select-modern">
                                        <option value="">Semua (Pegawai & Subdit)</option>
                                        <option value="pegawai" {{ in_array(($recipientType ?? ''), ['pegawai', 'individu']) ? 'selected' : '' }}>
                                            👤 Pegawai (Individu)
                                        </option>
                                        <option value="subdit" {{ ($recipientType ?? '') == 'subdit' ? 'selected' : '' }}>
                                            🏢 Subdit (Kolektif)
                                        </option>
                                    </select>
                                </div>

                                {{-- Collapsible Rentang Tanggal Spesifik (Opsional) --}}
                                <div class="col-12 mt-2">
                                    <div class="collapse {{ (request('start_date') || request('end_date')) ? 'show' : '' }}" id="customDateCollapse">
                                        <div class="p-3 bg-light rounded-3 border mb-2">
                                            <div class="row g-3 align-items-end">
                                                {{-- Input Dari Tanggal --}}
                                                <div class="col-12 col-sm-6 col-md-3">
                                                    <label class="form-label text-secondary small fw-bold text-uppercase mb-1">
                                                        Dari Tanggal Spesifik
                                                    </label>
                                                    <input type="date" id="start_date" name="start_date"
                                                        class="form-control form-control-modern bg-white" value="{{ request('start_date') }}">
                                                </div>

                                                {{-- Input Sampai Tanggal --}}
                                                <div class="col-12 col-sm-6 col-md-3">
                                                    <label class="form-label text-secondary small fw-bold text-uppercase mb-1">
                                                        Sampai Tanggal Spesifik
                                                    </label>
                                                    <input type="date" id="end_date" name="end_date"
                                                        class="form-control form-control-modern bg-white" value="{{ request('end_date') }}">
                                                </div>

                                                {{-- Pilihan Rentang Cepat --}}
                                                <div class="col-12 col-md-6">
                                                    <label class="form-label text-secondary small fw-bold text-uppercase mb-1">
                                                        Pilihan Cepat
                                                    </label>
                                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                                        <button type="button" class="preset-chip date-preset-btn" data-preset="today">
                                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                                                                <circle cx="12" cy="12" r="10"></circle>
                                                                <polyline points="12 6 12 12 14 14"></polyline>
                                                            </svg>
                                                            Hari Ini
                                                        </button>
                                                        <button type="button" class="preset-chip date-preset-btn" data-preset="month">
                                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                                                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                                            </svg>
                                                            Bulan Ini
                                                        </button>
                                                        <button type="button" class="preset-chip date-preset-btn" data-preset="year">
                                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                                                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                                            </svg>
                                                            Tahun Ini
                                                        </button>
                                                        <button type="button" class="preset-chip date-preset-btn" data-preset="all">
                                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                                                                <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                                                                <path d="M3 3v5h5"></path>
                                                            </svg>
                                                            Semua Waktu
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Baris Aksi & Toggle Tanggal --}}
                                <div class="col-12 mt-2 pt-2 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <a class="btn btn-sm btn-link text-decoration-none text-secondary p-0 d-inline-flex align-items-center gap-1"
                                            data-bs-toggle="collapse" href="#customDateCollapse" role="button" aria-expanded="{{ (request('start_date') || request('end_date')) ? 'true' : 'false' }}">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                            </svg>
                                            <span>{{ (request('start_date') || request('end_date')) ? 'Tutup Filter Tanggal Spesifik' : '+ Rentang Tanggal Spesifik (Opsional)' }}</span>
                                        </a>
                                    </div>

                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <a href="{{ route('reports.index') }}"
                                            class="btn btn-light border px-3 d-inline-flex align-items-center gap-1.5"
                                            title="Kembalikan ke semua data">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2">
                                                <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                                                <path d="M3 3v5h5"></path>
                                            </svg>
                                            Reset Filter
                                        </a>
                                        <button type="submit"
                                            class="btn btn-primary px-4 d-inline-flex align-items-center gap-1.5 shadow-sm fw-semibold">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2.5">
                                                <circle cx="11" cy="11" r="8"></circle>
                                                <line x1="21" y1="21" x2="16.65" y2="16.65">
                                                </line>
                                            </svg>
                                            Terapkan Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Laporan Transaksi --}}
        <div class="row">
            <div class="col-sm-12">
                <div class="card report-card mb-4">
                    <div
                        class="card-header border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="kpi-icon-wrapper bg-light text-primary" style="width: 38px; height: 38px;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <line x1="8" y1="6" x2="21" y2="6"></line>
                                    <line x1="8" y1="12" x2="21" y2="12"></line>
                                    <line x1="8" y1="18" x2="21" y2="18"></line>
                                    <line x1="3" y1="6" x2="3.01" y2="6"></line>
                                    <line x1="3" y1="12" x2="3.01" y2="12"></line>
                                    <line x1="3" y1="18" x2="3.01" y2="18"></line>
                                </svg>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <h5 class="card-title mb-0 fw-bold">Data Laporan Transaksi</h5>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1 small">
                                        {{ number_format($totalTransactions) }} Data Ditemukan
                                    </span>
                                </div>
                                <small class="text-secondary">Daftar mutasi persediaan barang masuk dan keluar di
                                    gudang</small>
                            </div>
                        </div>

                        {{-- Tombol Ekspor Rapi di Header Kartu --}}
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <a href="{{ route('reports.rekapitulasi') }}"
                                class="btn btn-sm btn-outline-info rounded-pill px-3 shadow-sm d-inline-flex align-items-center gap-1.5 fw-semibold">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                </svg>
                                Rekapitulasi Arsip
                            </a>
                            <button type="button" id="btnExportExcel"
                                class="btn btn-sm btn-outline-success rounded-pill px-3 shadow-sm d-inline-flex align-items-center gap-1.5 fw-semibold">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="8" y1="13" x2="16" y2="13"></line>
                                    <line x1="8" y1="17" x2="16" y2="17"></line>
                                </svg>
                                Export Excel
                            </button>
                            <button type="button" id="btnExportPdf"
                                class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm d-inline-flex align-items-center gap-1.5 fw-semibold">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                </svg>
                                Export PDF
                            </button>
                            <button type="button" id="btnExportPrint"
                                class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm d-inline-flex align-items-center gap-1.5 fw-semibold">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2">
                                    </path>
                                    <rect x="6" y="14" width="12" height="8"></rect>
                                </svg>
                                Cetak Laporan
                            </button>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        {{-- Notifikasi Filter Aktif --}}
                        @if (request('type') || request('recipient_type') || request('month') || request('year') || request('start_date') || request('end_date'))
                            <div
                                class="p-2.5 px-3 rounded-3 bg-light border d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <span class="text-secondary small fw-bold d-flex align-items-center">
                                        <svg class="me-1" width="14" height="14" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2">
                                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                                        </svg>
                                        Sedang Menyaring:
                                    </span>

                                    @if (request('month'))
                                        <span
                                            class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1">
                                            Bulan: {{ \Carbon\Carbon::createFromDate(2026, (int)request('month'), 1)->translatedFormat('F') }}
                                        </span>
                                    @endif

                                    @if (request('year'))
                                        <span
                                            class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1">
                                            Tahun: {{ request('year') }}
                                        </span>
                                    @endif

                                    @if (request('type'))
                                        <span
                                            class="badge {{ request('type') == 'masuk' ? 'bg-success-subtle text-success border-success-subtle' : 'bg-danger-subtle text-danger border-danger-subtle' }} border rounded-pill px-2.5 py-1">
                                            Jenis: {{ request('type') == 'masuk' ? 'Hanya Barang Masuk' : 'Hanya Barang Keluar' }}
                                        </span>
                                    @endif

                                    @if (request('recipient_type'))
                                        <span
                                            class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2.5 py-1">
                                            Pemohon: {{ in_array(request('recipient_type'), ['pegawai', 'individu']) ? '👤 Pegawai (Individu)' : '🏢 Subdit (Kolektif)' }}
                                        </span>
                                    @endif

                                    @if (request('start_date') || request('end_date'))
                                        <span
                                            class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2.5 py-1">
                                            Rentang:
                                            {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d-m-Y') : 'Awal' }}
                                            s/d
                                            {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d-m-Y') : 'Akhir' }}
                                        </span>
                                    @endif
                                </div>

                                <a href="{{ route('reports.index') }}"
                                    class="text-danger small fw-semibold text-decoration-none d-inline-flex align-items-center">
                                    <svg class="me-1" width="13" height="13" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                    Hapus Semua Filter
                                </a>
                            </div>
                        @endif

                        <table id="laporanTable" class="table custom-report-table word-wrap">
                                <thead>
                                    <tr class="text-center">
                                        <th width="3%">No</th>
                                        <th width="7%">Tanggal Faktur</th>
                                        <th width="7%">Kode Barang</th>
                                        <th width="8%">Kategori</th>
                                        <th width="11%">Nama Barang</th>
                                        <th width="10%">Uraian</th>
                                        <th width="7%">Jenis</th>
                                        <th width="6%">Stok Awal</th>
                                        <th width="7%">Stok Dipinjam</th>
                                        <th width="6%">Stok Sisa</th>
                                        <th width="8%">Harga Satuan</th>
                                        <th width="9%">Nilai Saldo / Total</th>
                                        <th width="9%">Nama Penerima</th>
                                        <th width="9%">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $transaction)
                                        @php
                                            $harga = $transaction->harga_satuan ?? (($transaction->harga_perolehan > 0) ? (float) $transaction->harga_perolehan : (float) ($transaction->product->purchase_price ?? 0));
                                            $jumlah = (int) ($transaction->quantity ?? 0);
                                            $stockSisa = (int) ($transaction->stock_after ?? 0);
                                            // Stok Awal:
                                            // Jika transaksi keluar, stok awal sebelum pengurangan adalah stock_after + quantity
                                            // Jika transaksi masuk, stok awal saat pencatatan masuk
                                            if ($transaction->type === 'keluar') {
                                                $stockAwal = $stockSisa + $jumlah;
                                            } else {
                                                $stockAwal = ($stockSisa > $jumlah) ? ($stockSisa - $jumlah) : $jumlah;
                                            }
                                            // Stok Dipinjam = barang keluar pada transaksi ini (0 jika masuk)
                                            $stockDipinjam = $transaction->type === 'keluar' ? $jumlah : 0;
                                            // Nilai Saldo mengikuti persediaan sisa stok barang yang masih ada
                                            $nilaiSaldo = $stockSisa * $harga;
                                            $formattedHarga = 'Rp ' . number_format($harga, 0, ',', '.');
                                            $formattedTotal = 'Rp ' . number_format($nilaiSaldo, 0, ',', '.');
                                        @endphp
                                        <tr>
                                            {{-- 1. No --}}
                                            <td class="text-center text-muted fw-semibold">
                                                {{ $loop->iteration }}
                                            </td>
                                            {{-- 2. Tanggal Faktur --}}
                                            <td class="text-center text-nowrap fw-semibold text-dark">
                                                {{ $transaction->transaction_date ? \Carbon\Carbon::parse($transaction->transaction_date)->format('d-m-Y') : '-' }}
                                            </td>
                                            {{-- 3. Kode Barang --}}
                                            <td class="text-center text-nowrap">
                                                <span
                                                    class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace px-2 py-1">
                                                    {{ $transaction->product->code ?? '-' }}
                                                </span>
                                            </td>
                                            {{-- 4. Kategori --}}
                                            <td class="text-center text-nowrap">
                                                <span class="badge bg-light text-secondary border px-2 py-1 rounded-pill">
                                                    {{ $transaction->product->category->name ?? '-' }}
                                                </span>
                                            </td>
                                            {{-- 5. Nama Barang --}}
                                            <td class="col-nama-barang">
                                                <span
                                                    class="fw-bold text-dark">{{ $transaction->product->name ?? '-' }}</span>
                                            </td>
                                            {{-- 6. Uraian --}}
                                            <td class="col-uraian text-break">
                                                @if ($transaction->type === 'masuk')
                                                    <span class="text-dark small fw-medium">{{ $transaction->uraian ?? '-' }}</span>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                            {{-- 7. Jenis --}}
                                            <td class="text-center text-nowrap">
                                                @if ($transaction->type === 'masuk')
                                                    <span
                                                        class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 fw-semibold d-inline-flex align-items-center gap-1">
                                                        <svg width="11" height="11" viewBox="0 0 24 24"
                                                            fill="none" stroke="currentColor" stroke-width="2.5">
                                                            <polyline points="7 10 12 15 17 10"></polyline>
                                                            <line x1="12" y1="15" x2="12"
                                                                y2="3"></line>
                                                        </svg>
                                                        Masuk
                                                    </span>
                                                @else
                                                    <span
                                                        class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1 fw-semibold d-inline-flex align-items-center gap-1">
                                                        <svg width="11" height="11" viewBox="0 0 24 24"
                                                            fill="none" stroke="currentColor" stroke-width="2.5">
                                                            <polyline points="17 14 12 9 7 14"></polyline>
                                                            <line x1="12" y1="9" x2="12"
                                                                y2="21"></line>
                                                        </svg>
                                                        Keluar
                                                    </span>
                                                    <div class="mt-1">
                                                        <span class="badge {{ $transaction->is_subdit ? 'bg-info text-white' : 'bg-secondary text-white' }}" style="font-size: 0.65rem; padding: 2px 6px;">
                                                            {{ $transaction->is_subdit ? 'Subdit' : 'Individu' }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </td>
                                            {{-- 8. Jumlah Stok Awal --}}
                                            <td class="text-center text-nowrap">
                                                @if ($stockAwal > 0)
                                                    <span
                                                        class="fw-semibold {{ $transaction->type === 'masuk' ? 'text-success' : 'text-dark' }}">{{ number_format($stockAwal) }}</span>
                                                    <span class="text-muted small ms-1">Pcs</span>
                                                @elseif ($stockAwal === 0)
                                                    <span class="fw-semibold text-muted">0</span>
                                                    <span class="text-muted small ms-1">Pcs</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            {{-- 9. Jumlah Stok Dipinjam (Barang Keluar) --}}
                                            <td class="text-center text-nowrap">
                                                @if ($stockDipinjam > 0)
                                                    <span
                                                        class="fw-bold text-danger">{{ number_format($stockDipinjam) }}</span>
                                                    <span class="text-muted small ms-1">Pcs</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            {{-- 10. Jumlah Stok Sisa --}}
                                            <td class="text-center text-nowrap">
                                                <span
                                                    class="fw-semibold {{ $stockSisa < 5 ? 'text-danger' : 'text-success' }}">{{ number_format($stockSisa) }}</span>
                                                <span class="text-muted small ms-1">Pcs</span>
                                            </td>
                                            {{-- 11. Harga Satuan --}}
                                            <td class="text-end font-monospace text-secondary text-nowrap"
                                                data-order="{{ $harga }}">
                                                {{ $formattedHarga }}
                                            </td>
                                            {{-- 12. Nilai Saldo / Total --}}
                                            <td class="text-end font-monospace fw-bold text-dark text-nowrap"
                                                data-order="{{ $nilaiSaldo }}">
                                                {{ $formattedTotal }}
                                            </td>
                                            {{-- 13. Nama Penerima --}}
                                            <td class="text-center text-nowrap">
                                                @if ($transaction->type === 'keluar')
                                                    <span class="text-dark small fw-semibold">{{ $transaction->recipient_name }}</span>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                            {{-- 14. Catatan --}}
                                            <td class="col-catatan-teks" title="{{ $transaction->formatted_note ?? ($transaction->note ?? '') }}">
                                                <span class="text-secondary small">{{ $transaction->formatted_note ?? ($transaction->note ?? '-') }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold bg-light text-dark">
                                        <th colspan="7" class="text-center py-2 text-secondary">TOTAL REKAPITULASI</th>
                                        <th class="text-center py-2" id="footerTotalStokAwal">0 Pcs</th>
                                        <th class="text-center py-2" id="footerTotalStokDipinjam">0 Pcs</th>
                                        <th class="text-center py-2" id="footerTotalStokSisa">0 Pcs</th>
                                        <th class="text-end py-2 text-muted">-</th>
                                        <th class="text-end py-2 text-primary" id="footerTotalNilaiSaldo">Rp 0</th>
                                        <th class="text-center py-2 text-muted">-</th>
                                        <th class="text-center py-2 text-muted">-</th>
                                    </tr>
                                </tfoot>
                            </table>

                        {{-- Tanda Tangan Khusus untuk Print / Tampilan Bawah (Kasubag TU) --}}
                        <div class="signature-section mt-5 pt-3">
                            <div class="row justify-content-end text-center" style="width: 100%;">
                                <div class="col-6">
                                    <p class="mb-1">Jakarta, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                                    <p class="mb-5">Mengetahui & Mengesahkan,<br><strong>Kasubag TU (Approver / Pimpinan)</strong></p>
                                    <div style="height: 50px;"></div>
                                    <p class="mb-0">
                                        <strong><u>{{ $kasubagName }}</u></strong><br>
                                        <small class="text-muted">NIP. {{ $kasubagNip }}</small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.jQuery && $.fn.DataTable) {
                var startDateVal = '{{ request('start_date') }}';
                var endDateVal = '{{ request('end_date') }}';
                var monthVal = '{{ request('month') }}';
                var yearVal = '{{ request('year') }}';
                var typeVal = '{{ request('type') }}';

                var monthNames = {
                    '01': 'Januari', '02': 'Februari', '03': 'Maret', '04': 'April',
                    '05': 'Mei', '06': 'Juni', '07': 'Juli', '08': 'Agustus',
                    '09': 'September', '10': 'Oktober', '11': 'November', '12': 'Desember'
                };

                var periodeStr = 'Semua Periode';
                if (startDateVal && endDateVal) {
                    periodeStr = startDateVal + ' s/d ' + endDateVal;
                } else if (startDateVal) {
                    periodeStr = 'Mulai ' + startDateVal;
                } else if (endDateVal) {
                    periodeStr = 'Sampai ' + endDateVal;
                } else if (monthVal && yearVal) {
                    periodeStr = (monthNames[monthVal] || ('Bulan ' + monthVal)) + ' ' + yearVal;
                } else if (monthVal) {
                    periodeStr = 'Bulan ' + (monthNames[monthVal] || monthVal);
                } else if (yearVal) {
                    periodeStr = 'Tahun ' + yearVal;
                }

                if (typeVal === 'masuk') {
                    periodeStr += ' (Hanya Barang Masuk)';
                } else if (typeVal === 'keluar') {
                    periodeStr += ' (Hanya Barang Keluar)';
                }

                var tglHariIni = new Date().toLocaleDateString('id-ID', {
                    weekday: 'long',
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });

                var dataTableInstance = $('#laporanTable').DataTable({
                    pageLength: 25,
                    lengthMenu: [
                        [25, 50, 100, -1],
                        [25, 50, 100, 'Semua']
                    ],
                    dom: '<"row align-items-center g-2 mb-3"<"col-sm-6"l><"col-sm-6 text-sm-end"f>><"table-responsive my-1"rt><"row align-items-center g-2 mt-3"<"col-sm-6 text-muted small"i><"col-sm-6 text-sm-end"p>>',
                    footerCallback: function(row, data, start, end, display) {
                        var api = this.api();
                        var intVal = function(i) {
                            if (typeof i === 'number') return i;
                            var clean = String(i).replace(/<[^>]+>/g, '').replace(/[^\d]/g, '');
                            return clean ? parseInt(clean, 10) : 0;
                        };

                        var totalAwal = api.column(7, { search: 'applied' }).data().reduce(function(a, b) {
                            return a + intVal(b);
                        }, 0);
                        var totalDipinjam = api.column(8, { search: 'applied' }).data().reduce(function(a, b) {
                            return a + intVal(b);
                        }, 0);
                        var totalSisa = api.column(9, { search: 'applied' }).data().reduce(function(a, b) {
                            return a + intVal(b);
                        }, 0);
                        var totalNilai = api.column(11, { search: 'applied' }).data().reduce(function(a, b) {
                            return a + intVal(b);
                        }, 0);

                        $('#footerTotalStokAwal').html(totalAwal.toLocaleString('id-ID') + ' <span class="text-muted small">Pcs</span>');
                        $('#footerTotalStokDipinjam').html(totalDipinjam.toLocaleString('id-ID') + ' <span class="text-muted small">Pcs</span>');
                        $('#footerTotalStokSisa').html(totalSisa.toLocaleString('id-ID') + ' <span class="text-muted small">Pcs</span>');
                        $('#footerTotalNilaiSaldo').html('Rp ' + totalNilai.toLocaleString('id-ID'));
                    },
                    buttons: [{
                            extend: 'excelHtml5',
                            title: 'KEMENTERIAN PERUMAHAN DAN KAWASAN PERMUKIMAN',
                            messageTop: 'LAPORAN MUTASI & PERSEDIAAN BARANG (ATK) - Periode: ' + periodeStr + ' | Dicetak pada: ' + tglHariIni,
                            filename: 'Laporan_Persediaan_ATK_' + new Date().toISOString().slice(0, 10),
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13],
                                format: {
                                    header: function(data, columnIdx) {
                                        return $('<div>').html(data).text().trim();
                                    },
                                    body: function(data, row, column, node) {
                                        var cleanText = $('<div>').html(data).text().trim();
                                        if (column === 0) {
                                            var num = cleanText.replace(/[^\d]/g, '');
                                            return num ? parseInt(num, 10) : (row + 1);
                                        }
                                        if (column === 6) {
                                            if (cleanText.indexOf('Masuk') !== -1) return 'Masuk';
                                            if (cleanText.indexOf('Subdit') !== -1) return 'Keluar (Subdit)';
                                            if (cleanText.indexOf('Individu') !== -1) return 'Keluar (Individu)';
                                            return 'Keluar';
                                        }
                                        if (column === 7 || column === 8 || column === 9) {
                                            var num = cleanText.replace(/[^\d]/g, '');
                                            return num ? parseInt(num, 10) : 0;
                                        }
                                        if (column === 10 || column === 11) {
                                            var num = cleanText.replace(/[^\d]/g, '');
                                            return num ? parseInt(num, 10) : 0;
                                        }
                                        return cleanText;
                                    }
                                }
                            },
                            customize: function(xlsx) {
                                function xmlEscape(str) {
                                    if (!str) return '';
                                    return String(str)
                                        .replace(/&/g, '&amp;')
                                        .replace(/</g, '&lt;')
                                        .replace(/>/g, '&gt;')
                                        .replace(/"/g, '&quot;')
                                        .replace(/'/g, '&apos;');
                                }

                                var sheet = xlsx.xl.worksheets['sheet1.xml'];
                                var styles = xlsx.xl['styles.xml'];

                                // 1. Tambahkan Custom Styles ke styles.xml
                                var numFmts = styles.getElementsByTagName('numFmts')[0];
                                var numFmtIdRupiah = 170;
                                var nf = styles.createElement('numFmt');
                                nf.setAttribute('numFmtId', numFmtIdRupiah);
                                nf.setAttribute('formatCode', '"Rp " #,##0;("Rp " #,##0);"Rp " 0');
                                numFmts.appendChild(nf);
                                numFmts.setAttribute('count', parseInt(numFmts.getAttribute('count') || '6') + 1);

                                var fonts = styles.getElementsByTagName('fonts')[0];
                                var fontCount = parseInt(fonts.getAttribute('count') || '5');
                                function addFont(sz, name, bold, colorRgb) {
                                    var f = styles.createElement('font');
                                    if (bold) { f.appendChild(styles.createElement('b')); }
                                    var s = styles.createElement('sz'); s.setAttribute('val', sz); f.appendChild(s);
                                    if (colorRgb) {
                                        var c = styles.createElement('color'); c.setAttribute('rgb', colorRgb); f.appendChild(c);
                                    }
                                    var n = styles.createElement('name'); n.setAttribute('val', name); f.appendChild(n);
                                    fonts.appendChild(f);
                                    fontCount++;
                                }
                                var fontHeaderIdx = fontCount;
                                addFont('11', 'Calibri', true, 'FFFFFFFF');
                                var fontTitleIdx = fontCount;
                                addFont('13', 'Calibri', true, 'FF144B59');
                                var fontSubtitleIdx = fontCount;
                                addFont('10', 'Calibri', true, 'FF475569');
                                var fontTotalIdx = fontCount;
                                addFont('11', 'Calibri', true, 'FF0F172A');
                                var fontDataIdx = fontCount;
                                addFont('10', 'Calibri', false, 'FF1E293B');
                                var fontBoldDataIdx = fontCount;
                                addFont('10', 'Calibri', true, 'FF0F172A');
                                var fontSigRoleIdx = fontCount;
                                addFont('10', 'Calibri', true, 'FF334155');
                                var fontSigNameIdx = fontCount;
                                addFont('11', 'Calibri', true, 'FF0F172A');
                                fonts.setAttribute('count', fontCount);

                                var fills = styles.getElementsByTagName('fills')[0];
                                var fillCount = parseInt(fills.getAttribute('count') || '6');
                                function addFill(rgb) {
                                    var fill = styles.createElement('fill');
                                    var pf = styles.createElement('patternFill');
                                    pf.setAttribute('patternType', 'solid');
                                    var fg = styles.createElement('fgColor'); fg.setAttribute('rgb', rgb); pf.appendChild(fg);
                                    var bg = styles.createElement('bgColor'); bg.setAttribute('indexed', '64'); pf.appendChild(bg);
                                    fill.appendChild(pf);
                                    fills.appendChild(fill);
                                    fillCount++;
                                }
                                var fillHeaderIdx = fillCount; // Navy #144B59 (Kemen PKP)
                                addFill('FF144B59');
                                var fillTotalIdx = fillCount; // Slate Soft #E2E8F0
                                addFill('FFE2E8F0');
                                var fillZebraIdx = fillCount; // Zebra #F8FAFC
                                addFill('FFF8FAFC');
                                fills.setAttribute('count', fillCount);

                                var borders = styles.getElementsByTagName('borders')[0];
                                var borderCount = parseInt(borders.getAttribute('count') || '2');
                                // Accounting border (thin top, double bottom)
                                var bTotal = styles.createElement('border');
                                bTotal.appendChild(styles.createElement('left'));
                                bTotal.appendChild(styles.createElement('right'));
                                var bTop = styles.createElement('top'); bTop.setAttribute('style', 'thin');
                                var bTopColor = styles.createElement('color'); bTopColor.setAttribute('rgb', 'FF144B59'); bTop.appendChild(bTopColor);
                                bTotal.appendChild(bTop);
                                var bBot = styles.createElement('bottom'); bBot.setAttribute('style', 'double');
                                var bBotColor = styles.createElement('color'); bBotColor.setAttribute('rgb', 'FF144B59'); bBot.appendChild(bBotColor);
                                bTotal.appendChild(bBot);
                                bTotal.appendChild(styles.createElement('diagonal'));
                                borders.appendChild(bTotal);
                                var borderTotalIdx = borderCount;
                                borderCount++;
                                borders.setAttribute('count', borderCount);

                                var cellXfs = styles.getElementsByTagName('cellXfs')[0];
                                var xfCount = parseInt(cellXfs.getAttribute('count') || '68');
                                function addXf(numFmtId, fontId, fillId, borderId, hAlign, vAlign, wrap) {
                                    var xf = styles.createElement('xf');
                                    xf.setAttribute('numFmtId', numFmtId || '0');
                                    xf.setAttribute('fontId', fontId || '0');
                                    xf.setAttribute('fillId', fillId || '0');
                                    xf.setAttribute('borderId', borderId !== undefined ? borderId : '0');
                                    xf.setAttribute('xfId', '0');
                                    xf.setAttribute('applyFont', '1');
                                    if (fillId) xf.setAttribute('applyFill', '1');
                                    if (borderId) xf.setAttribute('applyBorder', '1');
                                    if (numFmtId) xf.setAttribute('applyNumberFormat', '1');
                                    
                                    var align = styles.createElement('alignment');
                                    align.setAttribute('horizontal', hAlign || 'left');
                                    align.setAttribute('vertical', vAlign || 'center');
                                    if (wrap) align.setAttribute('wrapText', '1');
                                    xf.appendChild(align);
                                    xf.setAttribute('applyAlignment', '1');
                                    
                                    cellXfs.appendChild(xf);
                                    var idx = xfCount;
                                    xfCount++;
                                    return idx;
                                }

                                var styleHeader = addXf('0', fontHeaderIdx, fillHeaderIdx, '1', 'center', 'center', true);
                                var styleTextLeft = addXf('0', fontDataIdx, '0', '1', 'left', 'center', false);
                                var styleTextCenter = addXf('0', fontDataIdx, '0', '1', 'center', 'center', false);
                                var styleNumberInt = addXf('3', fontDataIdx, '0', '1', 'right', 'center', false);
                                var styleCurrency = addXf(numFmtIdRupiah, fontDataIdx, '0', '1', 'right', 'center', false);
                                var styleCurrencyBold = addXf(numFmtIdRupiah, fontBoldDataIdx, '0', '1', 'right', 'center', false);
                                
                                var styleZebraLeft = addXf('0', fontDataIdx, fillZebraIdx, '1', 'left', 'center', false);
                                var styleZebraCenter = addXf('0', fontDataIdx, fillZebraIdx, '1', 'center', 'center', false);
                                var styleZebraNumberInt = addXf('3', fontDataIdx, fillZebraIdx, '1', 'right', 'center', false);
                                var styleZebraCurrency = addXf(numFmtIdRupiah, fontDataIdx, fillZebraIdx, '1', 'right', 'center', false);
                                var styleZebraCurrencyBold = addXf(numFmtIdRupiah, fontBoldDataIdx, fillZebraIdx, '1', 'right', 'center', false);

                                var styleTotalLabel = addXf('0', fontTotalIdx, fillTotalIdx, borderTotalIdx, 'center', 'center', false);
                                var styleTotalNumberInt = addXf('3', fontTotalIdx, fillTotalIdx, borderTotalIdx, 'right', 'center', false);
                                var styleTotalCurrency = addXf(numFmtIdRupiah, fontTotalIdx, fillTotalIdx, borderTotalIdx, 'right', 'center', false);

                                var styleTitle = addXf('0', fontTitleIdx, '0', '0', 'center', 'center', false);
                                var styleSubtitle = addXf('0', fontSubtitleIdx, '0', '0', 'center', 'center', false);
                                var styleSigTitle = addXf('0', fontDataIdx, '0', '0', 'center', 'center', false);
                                var styleSigRole = addXf('0', fontSigRoleIdx, '0', '0', 'center', 'center', false);
                                var styleSigName = addXf('0', fontSigNameIdx, '0', '0', 'center', 'center', false);
                                var styleSigNip = addXf('0', fontDataIdx, '0', '0', 'center', 'center', false);

                                cellXfs.setAttribute('count', xfCount);

                                // 2. Setup Grid Lines dan Lebar Kolom
                                // Urutan tag OpenXML strictly valid: sheetViews -> cols -> sheetData -> mergeCells
                                $('cols', sheet).remove();
                                $('sheetViews', sheet).remove();

                                // Sisipkan sheetViews sebelum sheetData
                                $('sheetData', sheet).before('<sheetViews><sheetView showGridLines="1" workbookViewId="0"/></sheetViews>');

                                // Sisipkan cols sebelum sheetData (otomatis berada tepat setelah sheetViews dan sebelum sheetData)
                                var colWidthsXml = '<cols>' +
                                    '<col min="1" max="1" width="6" customWidth="1"/>' +
                                    '<col min="2" max="2" width="14" customWidth="1"/>' +
                                    '<col min="3" max="3" width="14" customWidth="1"/>' +
                                    '<col min="4" max="4" width="18" customWidth="1"/>' +
                                    '<col min="5" max="5" width="28" customWidth="1"/>' +
                                    '<col min="6" max="6" width="22" customWidth="1"/>' +
                                    '<col min="7" max="7" width="14" customWidth="1"/>' +
                                    '<col min="8" max="8" width="12" customWidth="1"/>' +
                                    '<col min="9" max="9" width="13" customWidth="1"/>' +
                                    '<col min="10" max="10" width="12" customWidth="1"/>' +
                                    '<col min="11" max="11" width="16" customWidth="1"/>' +
                                    '<col min="12" max="12" width="20" customWidth="1"/>' +
                                    '<col min="13" max="13" width="22" customWidth="1"/>' +
                                    '<col min="14" max="14" width="32" customWidth="1"/>' +
                                    '</cols>';
                                $('sheetData', sheet).before(colWidthsXml);

                                // 3. Styling Header & Subtitle
                                $('row[r="1"]', sheet).attr('ht', '26').attr('customHeight', '1');
                                $('row[r="1"] c[r="A1"]', sheet).attr('s', styleTitle);

                                $('row[r="2"]', sheet).attr('ht', '20').attr('customHeight', '1');
                                $('row[r="2"] c[r="A2"]', sheet).attr('s', styleSubtitle);

                                // Table Header di Row 3
                                $('row[r="3"]', sheet).attr('ht', '28').attr('customHeight', '1');
                                $('row[r="3"] c', sheet).attr('s', styleHeader);

                                // 4. Data Rows Styling & Formulasi
                                var dataRows = $('row', sheet).filter(function() {
                                    return parseInt($(this).attr('r')) >= 4;
                                });

                                var totalRows = dataRows.length;
                                var startDataRow = 4;
                                var endDataRow = 3 + totalRows;

                                var runningTotalAwal = 0;
                                var runningTotalDipinjam = 0;
                                var runningTotalSisa = 0;
                                var runningTotalSaldo = 0;

                                dataRows.each(function(idx) {
                                    var rowNode = $(this);
                                    var rNum = parseInt(rowNode.attr('r'));
                                    var isZebra = (idx % 2 === 1);

                                    rowNode.attr('ht', '20').attr('customHeight', '1');

                                    rowNode.find('c[r="A' + rNum + '"]').attr('s', isZebra ? styleZebraCenter : styleTextCenter);
                                    rowNode.find('c[r="B' + rNum + '"]').attr('s', isZebra ? styleZebraCenter : styleTextCenter);
                                    rowNode.find('c[r="C' + rNum + '"]').attr('s', isZebra ? styleZebraCenter : styleTextCenter);
                                    rowNode.find('c[r="D' + rNum + '"]').attr('s', isZebra ? styleZebraLeft : styleTextLeft);
                                    rowNode.find('c[r="E' + rNum + '"]').attr('s', isZebra ? styleZebraLeft : styleTextLeft);
                                    rowNode.find('c[r="F' + rNum + '"]').attr('s', isZebra ? styleZebraLeft : styleTextLeft);
                                    rowNode.find('c[r="G' + rNum + '"]').attr('s', isZebra ? styleZebraCenter : styleTextCenter);

                                    var cH = rowNode.find('c[r="H' + rNum + '"]');
                                    cH.attr('s', isZebra ? styleZebraNumberInt : styleNumberInt);
                                    var valH = parseFloat(cH.find('v').text()) || 0;
                                    runningTotalAwal += valH;

                                    var cI = rowNode.find('c[r="I' + rNum + '"]');
                                    cI.attr('s', isZebra ? styleZebraNumberInt : styleNumberInt);
                                    var valI = parseFloat(cI.find('v').text()) || 0;
                                    runningTotalDipinjam += valI;

                                    var cJ = rowNode.find('c[r="J' + rNum + '"]');
                                    cJ.attr('s', isZebra ? styleZebraNumberInt : styleNumberInt);
                                    var valJ = parseFloat(cJ.find('v').text()) || 0;
                                    runningTotalSisa += valJ;

                                    var cK = rowNode.find('c[r="K' + rNum + '"]');
                                    cK.attr('s', isZebra ? styleZebraCurrency : styleCurrency);
                                    var valK = parseFloat(cK.find('v').text()) || 0;

                                    // PERUMUSAN EXCEL: Nilai Saldo = Stok Sisa * Harga Satuan (=J{rNum}*K{rNum})
                                    var cL = rowNode.find('c[r="L' + rNum + '"]');
                                    var expectedSaldo = Math.round(valJ * valK);
                                    runningTotalSaldo += expectedSaldo;

                                    cL.empty();
                                    cL.attr('t', 'n');
                                    cL.attr('s', isZebra ? styleZebraCurrencyBold : styleCurrencyBold);
                                    cL.append('<f>J' + rNum + '*K' + rNum + '</f>');
                                    cL.append('<v>' + expectedSaldo + '</v>');

                                    rowNode.find('c[r="M' + rNum + '"]').attr('s', isZebra ? styleZebraCenter : styleTextCenter);
                                    rowNode.find('c[r="N' + rNum + '"]').attr('s', isZebra ? styleZebraLeft : styleTextLeft);
                                });

                                // 5. Baris Total Rekapitulasi (Formula SUM Excel)
                                if (totalRows > 0) {
                                    var totalRowNum = endDataRow + 1;
                                    var totalRowXml = '<row r="' + totalRowNum + '" ht="24" customHeight="1">' +
                                        '<c r="A' + totalRowNum + '" s="' + styleTotalLabel + '" t="inlineStr"><is><t>TOTAL REKAPITULASI</t></is></c>' +
                                        '<c r="B' + totalRowNum + '" s="' + styleTotalLabel + '"/>' +
                                        '<c r="C' + totalRowNum + '" s="' + styleTotalLabel + '"/>' +
                                        '<c r="D' + totalRowNum + '" s="' + styleTotalLabel + '"/>' +
                                        '<c r="E' + totalRowNum + '" s="' + styleTotalLabel + '"/>' +
                                        '<c r="F' + totalRowNum + '" s="' + styleTotalLabel + '"/>' +
                                        '<c r="G' + totalRowNum + '" s="' + styleTotalLabel + '"/>' +
                                        '<c r="H' + totalRowNum + '" s="' + styleTotalNumberInt + '" t="n">' +
                                            '<f>SUM(H' + startDataRow + ':H' + endDataRow + ')</f><v>' + runningTotalAwal + '</v></c>' +
                                        '<c r="I' + totalRowNum + '" s="' + styleTotalNumberInt + '" t="n">' +
                                            '<f>SUM(I' + startDataRow + ':I' + endDataRow + ')</f><v>' + runningTotalDipinjam + '</v></c>' +
                                        '<c r="J' + totalRowNum + '" s="' + styleTotalNumberInt + '" t="n">' +
                                            '<f>SUM(J' + startDataRow + ':J' + endDataRow + ')</f><v>' + runningTotalSisa + '</v></c>' +
                                        '<c r="K' + totalRowNum + '" s="' + styleTotalLabel + '"/>' +
                                        '<c r="L' + totalRowNum + '" s="' + styleTotalCurrency + '" t="n">' +
                                            '<f>SUM(L' + startDataRow + ':L' + endDataRow + ')</f><v>' + runningTotalSaldo + '</v></c>' +
                                        '<c r="M' + totalRowNum + '" s="' + styleTotalLabel + '"/>' +
                                        '<c r="N' + totalRowNum + '" s="' + styleTotalLabel + '"/>' +
                                        '</row>';
                                    $('sheetData', sheet).append(totalRowXml);

                                    var mergeCells = $('mergeCells', sheet);
                                    if (mergeCells.length) {
                                        mergeCells.append('<mergeCell ref="A' + totalRowNum + ':G' + totalRowNum + '"/>');
                                        mergeCells.attr('count', parseInt(mergeCells.attr('count') || '0') + 1);
                                    } else {
                                        $('sheetData', sheet).after('<mergeCells count="1"><mergeCell ref="A' + totalRowNum + ':G' + totalRowNum + '"/></mergeCells>');
                                    }
                                }

                                // 6. Section Tanda Tangan Resmi (Kasubag TU)
                                var sigStartRow = (totalRows > 0 ? (endDataRow + 1) : 4) + 2;
                                var tglSkrXml = xmlEscape('Jakarta, ' + tglHariIni);
                                var kasubagNameXml = xmlEscape(@json($kasubagName));
                                var kasubagNipXml = xmlEscape('{{ $kasubagNip }}');

                                var signatureXml = '<row r="' + sigStartRow + '">' +
                                    '<c r="L' + sigStartRow + '" s="' + styleSigTitle + '" t="inlineStr"><is><t>' + tglSkrXml + '</t></is></c></row>' +
                                    '<row r="' + (sigStartRow + 1) + '">' +
                                    '<c r="L' + (sigStartRow + 1) + '" s="' + styleSigRole + '" t="inlineStr"><is><t>Mengetahui &amp; Mengesahkan,</t></is></c></row>' +
                                    '<row r="' + (sigStartRow + 2) + '">' +
                                    '<c r="L' + (sigStartRow + 2) + '" s="' + styleSigRole + '" t="inlineStr"><is><t>Kasubag TU (Approver / Pimpinan)</t></is></c></row>' +
                                    '<row r="' + (sigStartRow + 6) + '">' +
                                    '<c r="L' + (sigStartRow + 6) + '" s="' + styleSigName + '" t="inlineStr"><is><t>' + kasubagNameXml + '</t></is></c></row>' +
                                    '<row r="' + (sigStartRow + 7) + '">' +
                                    '<c r="L' + (sigStartRow + 7) + '" s="' + styleSigNip + '" t="inlineStr"><is><t>NIP. ' + kasubagNipXml + '</t></is></c></row>';

                                $('sheetData', sheet).append(signatureXml);
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            orientation: 'landscape',
                            pageSize: 'A4',
                            filename: 'Laporan_Persediaan_ATK_' + new Date().toISOString().slice(0, 10),
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13],
                                format: {
                                    header: function(data, columnIdx) {
                                        return $('<div>').html(data).text().trim();
                                    },
                                    body: function(data, row, column, node) {
                                        var cleanText = $('<div>').html(data).text().trim();
                                        if (column === 6) {
                                            if (cleanText.indexOf('Masuk') !== -1) return 'Masuk';
                                            if (cleanText.indexOf('Subdit') !== -1) return 'Keluar (Subdit)';
                                            if (cleanText.indexOf('Individu') !== -1) return 'Keluar (Individu)';
                                            return 'Keluar';
                                        }
                                        return cleanText;
                                    }
                                }
                            },
                            customize: function(doc) {
                                doc.pageMargins = [20, 20, 20, 28];

                                // Header Resmi Dokumen PDF Berstandar Kementerian PKP
                                doc.content.splice(0, 1, {
                                    stack: [
                                        {
                                            text: 'KEMENTERIAN PERUMAHAN DAN KAWASAN PERMUKIMAN REPUBLIK INDONESIA',
                                            fontSize: 10.5,
                                            bold: true,
                                            alignment: 'center',
                                            color: '#144B59',
                                            characterSpacing: 0.5,
                                            margin: [0, 0, 0, 2]
                                        },
                                        {
                                            text: 'SEKRETARIAT JENDERAL - PENGELOLAAN ARSIP & PERSEDIAAN BARANG (ATK)',
                                            fontSize: 8,
                                            alignment: 'center',
                                            color: '#64748B',
                                            margin: [0, 0, 0, 4]
                                        },
                                        {
                                            canvas: [{
                                                type: 'line',
                                                x1: 0, y1: 0,
                                                x2: 798, y2: 0,
                                                lineWidth: 1.5,
                                                lineColor: '#144B59'
                                            }]
                                        },
                                        {
                                            text: 'LAPORAN MUTASI & PERSEDIAAN BARANG (ATK)',
                                            fontSize: 13,
                                            bold: true,
                                            alignment: 'center',
                                            color: '#0F172A',
                                            margin: [0, 8, 0, 3]
                                        },
                                        {
                                            text: 'Periode: ' + periodeStr + '   |   Dicetak pada: ' + tglHariIni,
                                            fontSize: 8,
                                            alignment: 'center',
                                            color: '#475569',
                                            margin: [0, 0, 0, 10]
                                        }
                                    ]
                                });

                                var tableNode = doc.content.find(function(node) {
                                    return node.table;
                                });

                                if (tableNode) {
                                    tableNode.table.widths = [20, 50, 48, 55, 95, 65, 45, 42, 44, 42, 58, 70, 70, 80];

                                    tableNode.layout = {
                                        hLineWidth: function(i, node) {
                                            return (i === 0 || i === 1 || i === node.table.body.length) ? 1 : 0.5;
                                        },
                                        vLineWidth: function(i, node) {
                                            return 0.5;
                                        },
                                        hLineColor: function(i, node) {
                                            return (i === 0 || i === 1) ? '#144B59' : '#CBD5E1';
                                        },
                                        vLineColor: function(i, node) {
                                            return '#E2E8F0';
                                        },
                                        paddingLeft: function(i, node) { return 3; },
                                        paddingRight: function(i, node) { return 3; },
                                        paddingTop: function(i, node) { return 4; },
                                        paddingBottom: function(i, node) { return 4; }
                                    };

                                    var body = tableNode.table.body;
                                    var sumAwal = 0;
                                    var sumDipinjam = 0;
                                    var sumSisa = 0;
                                    var sumSaldo = 0;

                                    for (var i = 0; i < body.length; i++) {
                                        var row = body[i];
                                        if (i === 0) {
                                            for (var j = 0; j < row.length; j++) {
                                                row[j].fillColor = '#144B59';
                                                row[j].color = '#FFFFFF';
                                                row[j].fontSize = 7.5;
                                                row[j].bold = true;
                                                row[j].alignment = 'center';
                                            }
                                        } else {
                                            var isEven = (i % 2 === 0);
                                            var rowBg = isEven ? '#F8FAFC' : '#FFFFFF';

                                            for (var j = 0; j < row.length; j++) {
                                                row[j].fontSize = 7;
                                                row[j].fillColor = rowBg;
                                                row[j].color = '#1E293B';

                                                if (j === 0 || j === 1 || j === 2 || j === 6) {
                                                    row[j].alignment = 'center';
                                                } else if (j === 7 || j === 8 || j === 9) {
                                                    row[j].alignment = 'center';
                                                } else if (j === 10 || j === 11) {
                                                    row[j].alignment = 'right';
                                                } else {
                                                    row[j].alignment = 'left';
                                                }
                                            }

                                            var numAwal = parseInt(String(row[7].text || '').replace(/[^\d]/g, ''), 10) || 0;
                                            var numDipinjam = parseInt(String(row[8].text || '').replace(/[^\d]/g, ''), 10) || 0;
                                            var numSisa = parseInt(String(row[9].text || '').replace(/[^\d]/g, ''), 10) || 0;
                                            var numSaldo = parseInt(String(row[11].text || '').replace(/[^\d]/g, ''), 10) || 0;

                                            sumAwal += numAwal;
                                            sumDipinjam += numDipinjam;
                                            sumSisa += numSisa;
                                            sumSaldo += numSaldo;
                                        }
                                    }

                                    // Tambahkan Baris Total Rekapitulasi di akhir tabel PDF
                                    if (body.length > 1) {
                                        body.push([
                                            { text: 'TOTAL REKAPITULASI', colSpan: 7, alignment: 'center', bold: true, fontSize: 7.5, fillColor: '#E2E8F0', color: '#0F172A' },
                                            {}, {}, {}, {}, {}, {},
                                            { text: sumAwal.toLocaleString('id-ID'), alignment: 'center', bold: true, fontSize: 7.5, fillColor: '#E2E8F0', color: '#0F172A' },
                                            { text: sumDipinjam.toLocaleString('id-ID'), alignment: 'center', bold: true, fontSize: 7.5, fillColor: '#E2E8F0', color: '#0F172A' },
                                            { text: sumSisa.toLocaleString('id-ID'), alignment: 'center', bold: true, fontSize: 7.5, fillColor: '#E2E8F0', color: '#0F172A' },
                                            { text: '-', alignment: 'center', bold: true, fontSize: 7.5, fillColor: '#E2E8F0', color: '#0F172A' },
                                            { text: 'Rp ' + sumSaldo.toLocaleString('id-ID'), alignment: 'right', bold: true, fontSize: 7.5, fillColor: '#E2E8F0', color: '#0F172A' },
                                            { text: '-', alignment: 'center', bold: true, fontSize: 7.5, fillColor: '#E2E8F0', color: '#0F172A' },
                                            { text: '-', alignment: 'center', bold: true, fontSize: 7.5, fillColor: '#E2E8F0', color: '#0F172A' }
                                        ]);
                                    }
                                }

                                // Blok Tanda Tangan Tunggal: Kasubag TU di Kanan
                                doc.content.push({
                                    unbreakable: true,
                                    margin: [0, 20, 0, 0],
                                    columns: [
                                        {
                                            width: '*',
                                            text: ''
                                        },
                                        {
                                            width: 250,
                                            alignment: 'center',
                                            margin: [0, 0, 10, 0],
                                            stack: [{
                                                    text: 'Jakarta, ' + tglHariIni,
                                                    fontSize: 8.5,
                                                    color: '#334155',
                                                    margin: [0, 0, 0, 2]
                                                },
                                                {
                                                    text: 'Mengetahui & Mengesahkan,',
                                                    fontSize: 8.5,
                                                    color: '#334155',
                                                    margin: [0, 0, 0, 0]
                                                },
                                                {
                                                    text: 'Kasubag TU (Approver / Pimpinan)',
                                                    fontSize: 8.5,
                                                    bold: true,
                                                    color: '#0F172A',
                                                    margin: [0, 0, 0, 42]
                                                },
                                                {
                                                    text: @json($kasubagName),
                                                    fontSize: 8.5,
                                                    bold: true,
                                                    decoration: 'underline',
                                                    color: '#0F172A'
                                                },
                                                {
                                                    text: 'NIP. {{ $kasubagNip }}',
                                                    fontSize: 7.5,
                                                    color: '#475569'
                                                }
                                            ]
                                        }
                                    ]
                                });

                                // Footer Nomor Halaman
                                doc.footer = function(currentPage, pageCount) {
                                    return {
                                        margin: [20, 10, 20, 0],
                                        columns: [
                                            {
                                                text: 'Sistem Informasi Pengelolaan ATK (ATEKA) - Kementerian PKP RI',
                                                fontSize: 7.5,
                                                color: '#94A3B8'
                                            },
                                            {
                                                text: 'Halaman ' + currentPage + ' dari ' + pageCount,
                                                fontSize: 7.5,
                                                alignment: 'right',
                                                color: '#64748B'
                                            }
                                        ]
                                    };
                                };
                            }
                        },
                        {
                            extend: 'print',
                            title: '',
                            customize: function(win) {
                                $(win.document.body).css({
                                    'font-size': '9pt',
                                    'font-family': 'Arial, sans-serif',
                                    'color': '#111',
                                    'padding': '15px'
                                });

                                $(win.document.body).prepend(`
                                    <div style="text-align: center; margin-bottom: 20px; border-bottom: 2px solid #144B59; padding-bottom: 10px;">
                                        <h3 style="margin: 0; font-size: 13pt; text-transform: uppercase; font-weight: bold; color: #144B59; letter-spacing: 0.5px;">KEMENTERIAN PERUMAHAN DAN KAWASAN PERMUKIMAN REPUBLIK INDONESIA</h3>
                                        <p style="margin: 3px 0 0 0; font-size: 8.5pt; color: #64748B;">SEKRETARIAT JENDERAL - PENGELOLAAN ARSIP &amp; PERSEDIAAN BARANG (ATK)</p>
                                        <h4 style="margin: 6px 0 0 0; font-size: 11pt; font-weight: 700; color: #0F172A;">LAPORAN MUTASI &amp; PERSEDIAAN BARANG (ATK)</h4>
                                        <p style="margin: 3px 0 0 0; font-size: 8.5pt; color: #475569;">Periode: ${periodeStr} | Dicetak: ${tglHariIni}</p>
                                    </div>
                                `);

                                $(win.document.body).find('table')
                                    .removeClass('table-striped table-hover')
                                    .css({
                                        'width': '100%',
                                        'border-collapse': 'collapse',
                                        'margin-top': '10px',
                                        'font-size': '8pt'
                                    });

                                $(win.document.body).find('th').css({
                                    'background-color': '#144B59',
                                    'color': '#ffffff',
                                    'border': '1px solid #0e343e',
                                    'padding': '6px 6px',
                                    'text-align': 'center'
                                });

                                $(win.document.body).find('td').css({
                                    'border': '1px solid #cbd5e1',
                                    'padding': '5px 6px'
                                });

                                $(win.document.body).append(`
                                    <div style="margin-top: 40px; width: 100%; display: flex; justify-content: flex-end; page-break-inside: avoid;">
                                        <div style="text-align: center; width: 250px;">
                                            <p style="margin-bottom: 3px; font-size: 8.5pt;">Jakarta, ${tglHariIni}</p>
                                            <p style="margin-bottom: 50px; font-size: 8.5pt;">Mengetahui &amp; Mengesahkan,<br><strong>Kasubag TU (Approver / Pimpinan)</strong></p>
                                            <p style="margin-bottom: 0; font-size: 8.5pt;"><strong><u>${@json($kasubagName)}</u></strong><br>
                                            <span style="color: #475569; font-size: 8pt;">NIP. {{ $kasubagNip }}</span></p>
                                        </div>
                                    </div>
                                `);
                            }
                        }
                    ],
                    language: {
                        search: "",
                        searchPlaceholder: "Cari riwayat laporan...",
                        lengthMenu: "Tampilkan _MENU_ data",
                        info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ transaksi",
                        infoEmpty: "Menampilkan 0 data",
                        infoFiltered: "(disaring dari _MAX_ total data)",
                        zeroRecords: "Tidak ada transaksi yang cocok dengan pencarian",
                        emptyTable: "Belum ada data transaksi persediaan",
                        paginate: {
                            first: "&laquo;",
                            last: "&raquo;",
                            next: "&rsaquo;",
                            previous: "&lsaquo;"
                        }
                    }
                });

                // Sambungkan tombol ekspor di Header Kartu ke fungsi ekspor DataTables
                $('#btnExportExcel').on('click', function() {
                    dataTableInstance.button('.buttons-excel').trigger();
                });
                $('#btnExportPdf').on('click', function() {
                    dataTableInstance.button('.buttons-pdf').trigger();
                });
                $('#btnExportPrint').on('click', function() {
                    dataTableInstance.button('.buttons-print').trigger();
                });
            }

            // Interactive Quick Date Presets
            document.querySelectorAll('.date-preset-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const preset = this.getAttribute('data-preset');
                    const startInput = document.getElementById('start_date');
                    const endInput = document.getElementById('end_date');
                    const form = document.getElementById('filterReportForm');
                    const now = new Date();
                    const yyyy = now.getFullYear();
                    const mm = String(now.getMonth() + 1).padStart(2, '0');
                    const dd = String(now.getDate()).padStart(2, '0');
                    const todayStr = `${yyyy}-${mm}-${dd}`;

                    if (preset === 'today') {
                        startInput.value = todayStr;
                        endInput.value = todayStr;
                    } else if (preset === 'month') {
                        startInput.value = `${yyyy}-${mm}-01`;
                        endInput.value = todayStr;
                    } else if (preset === 'year') {
                        startInput.value = `${yyyy}-01-01`;
                        endInput.value = todayStr;
                    } else if (preset === 'all') {
                        startInput.value = '';
                        endInput.value = '';
                    }
                    form.submit();
                });
            });

            // Sinkronisasi status dropdown pemohon jika jenis mutasi 'masuk'
            const typeSelect = document.getElementById('filter-type');
            const recipientSelect = document.getElementById('filter-recipient-type');

            function syncRecipientState() {
                if (typeSelect && recipientSelect) {
                    if (typeSelect.value === 'masuk') {
                        recipientSelect.value = '';
                        recipientSelect.disabled = true;
                        recipientSelect.title = 'Filter pemohon hanya berlaku untuk barang keluar';
                    } else {
                        recipientSelect.disabled = false;
                        recipientSelect.title = '';
                    }
                }
            }

            if (typeSelect) {
                typeSelect.addEventListener('change', syncRecipientState);
                syncRecipientState();
            }
        });
    </script>
@endpush
