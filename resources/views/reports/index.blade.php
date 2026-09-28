@extends('layouts.app')

@section('page-header')
    <style>
        .iq-navbar-header {
            position: relative;
            background-image: linear-gradient(rgba(0, 70, 80, 0.35), rgba(0, 70, 80, 0.35)), url("{{ asset('hope-ui/assets/images/dashboard/bg-2.jpg') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .iq-navbar-header .iq-container {
            position: relative;
            z-index: 2;
        }

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
                            <h1 class="text-white fw-bold mb-1" style="text-shadow: 0 2px 4px rgba(0,0,0,0.35);">Laporan
                                Persediaan Barang</h1>
                            <p class="text-white mb-0" style="text-shadow: 0 1px 3px rgba(0,0,0,0.35); opacity: 0.9;">
                                Pantau pergerakan transaksi keluar-masuk barang, ringkasan mutasi, dan ekspor dokumen resmi.
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
        .form-control-modern,
        .form-select-modern {
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 0.65rem 0.9rem;
            font-size: 0.875rem;
            background-color: #f8fafc;
            color: #1e293b;
            transition: all 0.2s ease;
            width: 100%;
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
            font-size: 0.8rem;
            font-weight: 500;
            padding: 6px 14px;
            border-radius: 20px;
            cursor: pointer;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            color: #475569;
            transition: all 0.15s ease;
            user-select: none;
            text-decoration: none;
        }

        .preset-chip:hover {
            background-color: #eef2ff;
            color: #3a57e8;
            border-color: #c7d2fe;
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
        {{-- Card Filter Laporan (Responsive & User-Friendly) --}}
        <div class="row">
            <div class="col-sm-12">
                <div class="card report-card mb-4" data-aos="fade-up" data-aos-delay="200">
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
                                <h5 class="card-title mb-0 fw-bold">Cari & Saring Laporan</h5>
                                <small class="text-secondary">Pilih jenis transaksi atau rentang tanggal untuk menemukan
                                    data laporan</small>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <form id="filterReportForm" action="{{ route('reports.index') }}" method="GET">
                            <div class="row g-3">
                                {{-- Kolom 1: Jenis Transaksi --}}
                                <div class="col-12 col-md-3">
                                    <label class="form-label text-secondary small fw-bold text-uppercase mb-1">
                                        Jenis Transaksi
                                    </label>
                                    <select name="type" class="form-select form-select-modern">
                                        <option value="">Semua (Masuk & Keluar)</option>
                                        <option value="masuk" {{ request('type') == 'masuk' ? 'selected' : '' }}>
                                            Hanya Barang Masuk
                                        </option>
                                        <option value="keluar" {{ request('type') == 'keluar' ? 'selected' : '' }}>
                                            Hanya Barang Keluar
                                        </option>
                                    </select>
                                </div>

                                {{-- Kolom 2: Filter Pemohon / Penerima (Subdit vs Pegawai) --}}
                                <div class="col-12 col-md-3">
                                    <label class="form-label text-secondary small fw-bold text-uppercase mb-1">
                                        Kategori Pemohon
                                    </label>
                                    <select name="recipient_type" class="form-select form-select-modern">
                                        <option value="">Semua (Subdit & Individu)</option>
                                        <option value="subdit" {{ request('recipient_type') == 'subdit' ? 'selected' : '' }}>
                                            🏢 Subdit (Kolektif)
                                        </option>
                                        <option value="individu" {{ request('recipient_type') == 'individu' ? 'selected' : '' }}>
                                            👤 Pegawai (Individu)
                                        </option>
                                    </select>
                                </div>

                                {{-- Kolom 3: Dari Tanggal --}}
                                <div class="col-12 col-sm-6 col-md-3">
                                    <label class="form-label text-secondary small fw-bold text-uppercase mb-1">
                                        Dari Tanggal
                                    </label>
                                    <input type="date" id="start_date" name="start_date"
                                        class="form-control form-control-modern" value="{{ request('start_date') }}">
                                </div>

                                {{-- Kolom 4: Sampai Tanggal --}}
                                <div class="col-12 col-sm-6 col-md-3">
                                    <label class="form-label text-secondary small fw-bold text-uppercase mb-1">
                                        Sampai Tanggal
                                    </label>
                                    <input type="date" id="end_date" name="end_date"
                                        class="form-control form-control-modern" value="{{ request('end_date') }}">
                                </div>

                                {{-- Baris 2: Tombol Cepat di Kiri & Tombol Aksi di Kanan --}}
                                <div
                                    class="col-12 mt-3 pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-3">
                                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                        <span class="text-secondary small fw-bold me-1">Pilihan Cepat:</span>
                                        <button type="button" class="preset-chip date-preset-btn" data-preset="today">Hari
                                            Ini</button>
                                        <button type="button" class="preset-chip date-preset-btn" data-preset="month">Bulan
                                            Ini</button>
                                        <button type="button" class="preset-chip date-preset-btn" data-preset="year">Tahun
                                            Ini</button>
                                        <button type="button" class="preset-chip date-preset-btn" data-preset="all">Semua
                                            Waktu</button>
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
                                            class="btn btn-primary px-4 d-inline-flex align-items-center gap-1.5 shadow-sm">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2.5">
                                                <circle cx="11" cy="11" r="8"></circle>
                                                <line x1="21" y1="21" x2="16.65" y2="16.65">
                                                </line>
                                            </svg>
                                            Tampilkan Data
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3 KARTU RINGKASAN: Total Transaksi, Total Masuk (Pcs), Total Keluar (Pcs) --}}
        <div class="row g-3 mb-4">
            {{-- Card 1: Total Transaksi --}}
            <div class="col-12 col-md-4">
                <div class="card kpi-card h-100" data-aos="fade-up" data-aos-delay="250">
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
                <div class="card kpi-card h-100" data-aos="fade-up" data-aos-delay="300">
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
                <div class="card kpi-card h-100" data-aos="fade-up" data-aos-delay="350">
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

        {{-- Tabel Laporan Transaksi --}}
        <div class="row">
            <div class="col-sm-12">
                <div class="card report-card mb-4" data-aos="fade-up" data-aos-delay="400">
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
                        @if (request('type') || request('recipient_type') || request('start_date') || request('end_date'))
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

                                    @if (request('type'))
                                        <span
                                            class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1">
                                            Jenis: {{ request('type') == 'masuk' ? 'Barang Masuk' : 'Barang Keluar' }}
                                        </span>
                                    @endif

                                    @if (request('recipient_type'))
                                        <span
                                            class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2.5 py-1">
                                            Pemohon: {{ request('recipient_type') == 'subdit' ? 'Subdit (Kolektif)' : 'Pegawai (Individu)' }}
                                        </span>
                                    @endif

                                    @if (request('start_date') || request('end_date'))
                                        <span
                                            class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2.5 py-1">
                                            Periode:
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
                                    Tampilkan Semua Data
                                </a>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table id="laporanTable" class="table custom-report-table word-wrap">
                                <thead>
                                    <tr class="text-center">
                                        <th width="3%">No</th>
                                        <th width="7%">Tanggal</th>
                                        <th width="7%">Kode</th>
                                        <th width="8%">Kategori</th>
                                        <th width="13%">Nama</th>
                                        <th width="8%">Jenis</th>
                                        <th width="6%">Stok Awal</th>
                                        <th width="7%">Stok Dipinjam</th>
                                        <th width="6%">Stok Sisa</th>
                                        <th width="8%">Harga Satuan</th>
                                        <th width="9%">Nilai Saldo / Total</th>
                                        <th width="8%">Petugas</th>
                                        <th width="10%">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $transaction)
                                        @php
                                            $harga = $transaction->harga_perolehan ?? 0;
                                            $jumlah = $transaction->quantity ?? 0;
                                            $stockSisa = $transaction->stock_after ?? 0;
                                            // Stok Awal = barang masuk pada transaksi ini (0 jika keluar)
                                            $stockAwal = $transaction->type === 'masuk' ? $jumlah : 0;
                                            // Stok Dipinjam = barang keluar pada transaksi ini (0 jika masuk)
                                            $stockDipinjam = $transaction->type === 'keluar' ? $jumlah : 0;
                                            $nilaiSaldo = $jumlah * $harga;
                                            $formattedHarga = 'Rp ' . number_format($harga, 0, ',', '.');
                                            $formattedTotal = 'Rp ' . number_format($nilaiSaldo, 0, ',', '.');
                                        @endphp
                                        <tr>
                                            {{-- 1. No --}}
                                            <td class="text-center text-muted fw-semibold">
                                                {{ $loop->iteration }}
                                            </td>
                                            {{-- 2. Tanggal --}}
                                            <td class="text-center text-nowrap fw-semibold text-dark">
                                                {{ $transaction->transaction_date ? \Carbon\Carbon::parse($transaction->transaction_date)->format('d-m-Y') : '-' }}
                                            </td>
                                            {{-- 3. Kode --}}
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
                                            {{-- 5. Nama --}}
                                            <td class="col-nama-barang">
                                                <span
                                                    class="fw-bold text-dark">{{ $transaction->product->name ?? '-' }}</span>
                                            </td>
                                            {{-- 6. Jenis --}}
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
                                            {{-- 7. Jumlah Stok Awal (Barang Masuk) --}}
                                            <td class="text-center text-nowrap">
                                                @if ($stockAwal > 0)
                                                    <span
                                                        class="fw-semibold text-success">{{ number_format($stockAwal) }}</span>
                                                    <span class="text-muted small ms-1">Pcs</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            {{-- 8. Jumlah Stok Dipinjam (Barang Keluar) --}}
                                            <td class="text-center text-nowrap">
                                                @if ($stockDipinjam > 0)
                                                    <span
                                                        class="fw-bold text-danger">{{ number_format($stockDipinjam) }}</span>
                                                    <span class="text-muted small ms-1">Pcs</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            {{-- 9. Jumlah Stok Sisa --}}
                                            <td class="text-center text-nowrap">
                                                <span
                                                    class="fw-semibold {{ $stockSisa < 5 ? 'text-danger' : 'text-success' }}">{{ number_format($stockSisa) }}</span>
                                                <span class="text-muted small ms-1">Pcs</span>
                                            </td>
                                            {{-- 10. Harga Satuan --}}
                                            <td class="text-end font-monospace text-secondary text-nowrap"
                                                data-order="{{ $harga }}">
                                                {{ $formattedHarga }}
                                            </td>
                                            {{-- 11. Nilai Saldo / Total --}}
                                            <td class="text-end font-monospace fw-bold text-dark text-nowrap"
                                                data-order="{{ $nilaiSaldo }}">
                                                {{ $formattedTotal }}
                                            </td>
                                            {{-- 12. Petugas (Cukup Utami M tanpa bulatan U) --}}
                                            <td class="text-center text-nowrap">
                                                <span
                                                    class="text-dark small fw-medium">{{ $transaction->user->name ?? '-' }}</span>
                                            </td>
                                            {{-- 13. Catatan --}}
                                            <td class="col-catatan-teks" title="{{ $transaction->note ?? '' }}">
                                                @if ($transaction->note)
                                                    <span class="text-secondary small">{{ $transaction->note }}</span>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Tanda Tangan Khusus untuk Print / Tampilan Bawah (Arsiparis & Kasubag TU) --}}
                        <div class="signature-section mt-5 pt-3">
                            <div class="row text-center" style="width: 100%;">
                                <div class="col-6">
                                    <p class="mb-1">Disiapkan & Direkapitulasi oleh,</p>
                                    <p class="mb-5"><strong>Arsiparis (Pelaporan & Administrasi)</strong></p>
                                    <div style="height: 50px;"></div>
                                    <p class="mb-0">
                                        <strong><u>Devi Ismiyanti</u></strong><br>
                                        <small class="text-muted">NIP. 199508142022032005</small>
                                    </p>
                                </div>
                                <div class="col-6">
                                    <p class="mb-1">Jakarta, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                                    <p class="mb-5">Mengetahui & Mengesahkan,<br><strong>Kasubag TU (Approver / Pimpinan)</strong></p>
                                    <div style="height: 50px;"></div>
                                    <p class="mb-0">
                                        <strong><u>Utami M.</u></strong><br>
                                        <small class="text-muted">NIP. 199812132025062010</small>
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
                var typeVal = '{{ request('type') }}';

                var periodeStr = 'Semua Periode';
                if (startDateVal && endDateVal) {
                    periodeStr = startDateVal + ' s/d ' + endDateVal;
                } else if (startDateVal) {
                    periodeStr = 'Mulai ' + startDateVal;
                } else if (endDateVal) {
                    periodeStr = 'Sampai ' + endDateVal;
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
                    dom: '<"row align-items-center g-2 mb-3"<"col-sm-6"l><"col-sm-6 text-sm-end"f>>rt<"row align-items-center g-2 mt-3"<"col-sm-6 text-muted small"i><"col-sm-6 text-sm-end"p>>',
                    buttons: [{
                            extend: 'excelHtml5',
                            title: 'LAPORAN MUTASI PERSEDIAAN BARANG (ATK)',
                            filename: 'Laporan_Persediaan_ATK_' + new Date().toISOString().slice(0, 10),
                            customize: function(xlsx) {
                                var sheet = xlsx.xl.worksheets['sheet1.xml'];

                                // Lebar kolom rapi agar teks dan angka di Excel tidak terpotong (###)
                                var colWidthsXml = '<cols>' +
                                    '<col min="1" max="1" width="5" customWidth="1"/>' +
                                    '<col min="2" max="2" width="13" customWidth="1"/>' +
                                    '<col min="3" max="3" width="13" customWidth="1"/>' +
                                    '<col min="4" max="4" width="16" customWidth="1"/>' +
                                    '<col min="5" max="5" width="26" customWidth="1"/>' +
                                    '<col min="6" max="6" width="13" customWidth="1"/>' +
                                    '<col min="7" max="7" width="12" customWidth="1"/>' +
                                    '<col min="8" max="8" width="14" customWidth="1"/>' +
                                    '<col min="9" max="9" width="12" customWidth="1"/>' +
                                    '<col min="10" max="10" width="16" customWidth="1"/>' +
                                    '<col min="11" max="11" width="18" customWidth="1"/>' +
                                    '<col min="12" max="12" width="15" customWidth="1"/>' +
                                    '<col min="13" max="13" width="30" customWidth="1"/>' +
                                    '</cols>';

                                $('sheetData', sheet).before(colWidthsXml);

                                var tglSkr = 'Jakarta, ' + tglHariIni;
                                var rowCount = $('row', sheet).length + 3;

                                var signatureXml = '<row r="' + rowCount + '">' +
                                    '<c t="inlineStr" r="E' + rowCount + '" s="55"><is><t>Disiapkan oleh,</t></is></c>' +
                                    '<c t="inlineStr" r="M' + rowCount + '" s="55"><is><t>' + tglSkr + '</t></is></c></row>' +
                                    '<row r="' + (rowCount + 1) + '">' +
                                    '<c t="inlineStr" r="E' + (rowCount + 1) + '" s="55"><is><t>Arsiparis (Pelaporan &amp; Administrasi)</t></is></c>' +
                                    '<c t="inlineStr" r="M' + (rowCount + 1) + '" s="55"><is><t>Mengetahui &amp; Mengesahkan,</t></is></c></row>' +
                                    '<row r="' + (rowCount + 2) + '">' +
                                    '<c t="inlineStr" r="M' + (rowCount + 2) + '" s="55"><is><t>Kasubag TU (Approver / Pimpinan)</t></is></c></row>' +
                                    '<row r="' + (rowCount + 6) + '">' +
                                    '<c t="inlineStr" r="E' + (rowCount + 6) + '" s="55"><is><t>Devi Ismiyanti</t></is></c>' +
                                    '<c t="inlineStr" r="M' + (rowCount + 6) + '" s="55"><is><t>Utami M.</t></is></c></row>' +
                                    '<row r="' + (rowCount + 7) + '">' +
                                    '<c t="inlineStr" r="E' + (rowCount + 7) + '" s="55"><is><t>NIP. 199508142022032005</t></is></c>' +
                                    '<c t="inlineStr" r="M' + (rowCount + 7) + '" s="55"><is><t>NIP. 199812132025062010</t></is></c></row>';

                                $('sheetData', sheet).append(signatureXml);
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            orientation: 'landscape',
                            pageSize: 'A4',
                            filename: 'Laporan_Persediaan_ATK_' + new Date().toISOString().slice(0, 10),
                            customize: function(doc) {
                                doc.pageMargins = [20, 25, 20, 25];

                                // Header Resmi Dokumen PDF
                                doc.content.splice(0, 1, {
                                    text: 'KEMENTERIAN PERUMAHAN DAN KAWASAN PERMUKIMAN',
                                    fontSize: 10,
                                    alignment: 'center',
                                    color: '#334155',
                                    margin: [0, 0, 0, 2]
                                }, {
                                    text: 'LAPORAN MUTASI & PERSEDIAAN BARANG (ATK)',
                                    fontSize: 13,
                                    bold: true,
                                    alignment: 'center',
                                    color: '#0f172a',
                                    margin: [0, 0, 0, 4]
                                }, {
                                    text: 'Periode: ' + periodeStr + '  |  Dicetak pada: ' +
                                        tglHariIni,
                                    fontSize: 8.5,
                                    alignment: 'center',
                                    color: '#64748b',
                                    margin: [0, 0, 0, 14]
                                });

                                var tableNode = doc.content.find(function(node) {
                                    return node.table;
                                });

                                if (tableNode) {
                                    tableNode.table.widths = [
                                        '3%', // No
                                        '7%', // Tanggal
                                        '7%', // Kode
                                        '8%', // Kategori
                                        '13%', // Nama
                                        '7%', // Jenis
                                        '6%', // Stok Awal
                                        '7%', // Stok Dipinjam
                                        '6%', // Stok Sisa
                                        '8%', // Harga Satuan
                                        '9%', // Nilai Saldo/Total
                                        '9%', // Petugas
                                        '10%' // Catatan
                                    ];

                                    var body = tableNode.table.body;
                                    for (var i = 0; i < body.length; i++) {
                                        var row = body[i];
                                        for (var j = 0; j < row.length; j++) {
                                            if (i === 0) {
                                                row[j].fillColor = '#1e293b';
                                                row[j].color = '#ffffff';
                                                row[j].fontSize = 7.5;
                                                row[j].bold = true;
                                                row[j].alignment = 'center';
                                            } else {
                                                row[j].fontSize = 7;
                                                if (i % 2 === 0) {
                                                    row[j].fillColor = '#f8fafc';
                                                }
                                                // center: No(0), Tanggal(1), Kode(2), Kategori(3), Jenis(5), StokAwal(6), StokDipinjam(7), StokSisa(8)
                                                if (j === 0 || j === 1 || j === 2 || j === 3 ||
                                                    j === 5 || j === 6 || j === 7 || j === 8) {
                                                    row[j].alignment = 'center';
                                                    // right: HargaSatuan(9), NilaiTotal(10)
                                                } else if (j === 9 || j === 10) {
                                                    row[j].alignment = 'right';
                                                } else {
                                                    row[j].alignment = 'left';
                                                }
                                            }
                                        }
                                    }
                                }

                                // Blok Dual Tanda Tangan Rapi: Arsiparis di Kiri, Kasubag TU di Kanan
                                doc.content.push({
                                    columns: [{
                                            width: 220,
                                            alignment: 'center',
                                            margin: [10, 20, 0, 0],
                                            stack: [{
                                                    text: 'Disiapkan & Direkapitulasi oleh,',
                                                    fontSize: 8.5,
                                                    margin: [0, 0, 0, 3]
                                                },
                                                {
                                                    text: 'Arsiparis (Pelaporan & Administrasi)',
                                                    fontSize: 8.5,
                                                    bold: true,
                                                    margin: [0, 0, 0, 40]
                                                },
                                                {
                                                    text: 'Devi Ismiyanti',
                                                    fontSize: 8.5,
                                                    bold: true,
                                                    decoration: 'underline'
                                                },
                                                {
                                                    text: 'NIP. 199508142022032005',
                                                    fontSize: 7.5,
                                                    color: '#475569'
                                                }
                                            ]
                                        },
                                        {
                                            width: '*',
                                            text: ''
                                        },
                                        {
                                            width: 220,
                                            alignment: 'center',
                                            margin: [0, 20, 10, 0],
                                            stack: [{
                                                    text: 'Jakarta, ' + tglHariIni,
                                                    fontSize: 8.5,
                                                    margin: [0, 0, 0, 3]
                                                },
                                                {
                                                    text: 'Mengetahui & Mengesahkan,',
                                                    fontSize: 8.5
                                                },
                                                {
                                                    text: 'Kasubag TU (Approver / Pimpinan)',
                                                    fontSize: 8.5,
                                                    bold: true,
                                                    margin: [0, 0, 0, 40]
                                                },
                                                {
                                                    text: 'Utami M.',
                                                    fontSize: 8.5,
                                                    bold: true,
                                                    decoration: 'underline'
                                                },
                                                {
                                                    text: 'NIP. 199812132025062010',
                                                    fontSize: 7.5,
                                                    color: '#475569'
                                                }
                                            ]
                                        }
                                    ]
                                });
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
                                    <div style="text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px;">
                                        <h3 style="margin: 0; font-size: 13pt; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px;">KEMENTERIAN PERUMAHAN DAN KAWASAN PERMUKIMAN</h3>
                                        <h4 style="margin: 4px 0 0 0; font-size: 11pt; font-weight: 600;">LAPORAN MUTASI & PERSEDIAAN BARANG (ATK)</h4>
                                        <p style="margin: 4px 0 0 0; font-size: 9pt; color: #555;">Periode: ${periodeStr} | Dicetak: ${tglHariIni}</p>
                                    </div>
                                `);

                                $(win.document.body).find('table')
                                    .removeClass('table-striped table-hover')
                                    .css({
                                        'width': '100%',
                                        'border-collapse': 'collapse',
                                        'margin-top': '10px',
                                        'font-size': '8.5pt'
                                    });

                                $(win.document.body).find('th').css({
                                    'background-color': '#f1f5f9',
                                    'border': '1px solid #333',
                                    'padding': '6px 8px',
                                    'text-align': 'center'
                                });

                                $(win.document.body).find('td').css({
                                    'border': '1px solid #ccc',
                                    'padding': '5px 8px'
                                });

                                $(win.document.body).append(`
                                    <div style="margin-top: 35px; float: right; text-align: center; width: 230px; page-break-inside: avoid;">
                                        <p style="margin-bottom: 4px; font-size: 9pt;">Jakarta, ${tglHariIni}</p>
                                        <p style="margin-bottom: 50px; font-size: 9pt;">Mengetahui,<br><strong>Kasubag TU</strong></p>
                                        <p style="margin-bottom: 0; font-size: 9pt;"><strong><u>Utami M.</u></strong><br>
                                        <span style="color: #444; font-size: 8.5pt;">NIP. 199812132025062010</span></p>
                                    </div>
                                    <div style="clear: both;"></div>
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
        });
    </script>
@endpush
