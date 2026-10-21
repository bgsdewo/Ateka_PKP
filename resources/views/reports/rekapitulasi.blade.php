@extends('layouts.app')

@section('page-header')
    <style>
        /* Modern Select Styling khusus filter rekapitulasi */
        .form-select-modern {
            border: 1px solid #cbd5e1 !important;
            border-radius: 10px !important;
            padding: 0.62rem 2.5rem 0.62rem 0.95rem !important;
            font-size: 0.875rem !important;
            background-color: #f8fafc !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23475569' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right 0.95rem center !important;
            background-size: 14px 10px !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            appearance: none !important;
            color: #1e293b !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
            width: 100% !important;
            cursor: pointer !important;
            line-height: 1.5 !important;
        }

        .form-select-modern:focus {
            background-color: #ffffff !important;
            border-color: #3a57e8 !important;
            box-shadow: 0 0 0 3px rgba(58, 87, 232, 0.15) !important;
            outline: none !important;
        }

        .form-select-modern:disabled {
            background-color: #f1f5f9 !important;
            color: #94a3b8 !important;
            cursor: not-allowed !important;
            opacity: 0.75 !important;
        }

        /* KPI Card Enhancements */
        .rekap-kpi-card {
            border: 1px solid rgba(226, 232, 240, 0.8) !important;
            border-radius: 14px !important;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
            background: #ffffff !important;
        }

        .rekap-kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05) !important;
        }

        .kpi-icon-pill {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
    <div class="iq-navbar-header" style="height: 220px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 text-white">
                        <div>
                            <h1 class="text-white fw-bold mb-1">Rekapitulasi Mutasi &amp; Arsip Dokumen</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        {{-- KPI Cards Arsiparis --}}
        <div class="row g-3 mb-4">
            {{-- Card 1: Total Item Terdaftar --}}
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card rekap-kpi-card h-100 shadow-sm border-0">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-secondary small fw-bold text-uppercase" style="letter-spacing: 0.04em;">Total Item Terdaftar</span>
                            <div class="kpi-icon-pill bg-primary-subtle text-primary">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex align-items-baseline gap-2 mb-1">
                                <h2 class="fw-bold text-dark mb-0">{{ number_format($totalAllProducts) }}</h2>
                                <span class="text-muted fw-semibold">Barang</span>
                            </div>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1 small">
                                Dalam {{ $categoriesRekap->count() }} Kategori ATK
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Total Saldo Fisik Stok --}}
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card rekap-kpi-card h-100 shadow-sm border-0">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-secondary small fw-bold text-uppercase" style="letter-spacing: 0.04em;">Total Saldo Fisik Stok</span>
                            <div class="kpi-icon-pill bg-success-subtle text-success">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex align-items-baseline gap-2 mb-1">
                                <h2 class="fw-bold text-success mb-0">{{ number_format($totalAllStock) }}</h2>
                                <span class="text-muted fw-semibold">Pcs</span>
                            </div>
                            <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1 small">
                                Kondisi persediaan aktif di gudang
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 3: Nilai Aset Saldo Persediaan --}}
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card rekap-kpi-card h-100 shadow-sm border-0">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-secondary small fw-bold text-uppercase" style="letter-spacing: 0.04em;">Nilai Aset Saldo Persediaan</span>
                            <div class="kpi-icon-pill bg-warning-subtle text-warning">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex align-items-baseline gap-2 mb-1">
                                <h2 class="fw-bold text-primary mb-0 font-monospace">Rp {{ number_format($totalAllValue, 0, ',', '.') }}</h2>
                            </div>
                            <span class="badge bg-warning-subtle text-dark rounded-pill px-2.5 py-1 small">
                                Valuasi nilai perolehan persediaan
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter & Menu Cetak Berita Acara --}}
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 14px;">
                    <div class="card-header border-bottom py-3 bg-white" style="border-top-left-radius: 14px; border-top-right-radius: 14px;">
                        <div class="d-flex align-items-center gap-2">
                            <div class="kpi-icon-pill bg-light text-primary" style="width: 36px; height: 36px;">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                                </svg>
                            </div>
                            <div>
                                <h5 class="card-title mb-0 fw-bold">Parameter Penyaringan &amp; Rekapitulasi</h5>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <form action="{{ route('reports.rekapitulasi') }}" method="GET">
                            <div class="row g-3">
                                {{-- 1. Bulan --}}
                                <div class="col-12 col-sm-6 col-lg-3">
                                    <label class="form-label text-secondary small fw-bold text-uppercase mb-1 d-flex align-items-center gap-1.5">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
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

                                {{-- 2. Tahun --}}
                                <div class="col-12 col-sm-6 col-lg-2">
                                    <label class="form-label text-secondary small fw-bold text-uppercase mb-1 d-flex align-items-center gap-1.5">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                        Tahun
                                    </label>
                                    <select name="year" class="form-select form-select-modern">
                                        @for ($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                                            <option value="{{ $y }}" {{ ($year ?? date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>

                                {{-- 3. Jenis Mutasi --}}
                                <div class="col-12 col-sm-6 col-lg-3">
                                    <label class="form-label text-secondary small fw-bold text-uppercase mb-1 d-flex align-items-center gap-1.5">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="17 1 21 5 17 9"></polyline>
                                            <path d="M3 11V9a4 4 0 0 1 4-4h14"></path>
                                            <polyline points="7 23 3 19 7 15"></polyline>
                                            <path d="M21 13v2a4 4 0 0 1-4 4H3"></path>
                                        </svg>
                                        Jenis Mutasi
                                    </label>
                                    <select name="type" id="filter-type" class="form-select form-select-modern">
                                        <option value="">Semua (Masuk &amp; Keluar)</option>
                                        <option value="masuk" {{ ($type ?? '') == 'masuk' ? 'selected' : '' }}>Hanya Barang Masuk</option>
                                        <option value="keluar" {{ ($type ?? '') == 'keluar' ? 'selected' : '' }}>Hanya Barang Keluar</option>
                                    </select>
                                </div>

                                {{-- 4. Penerima / Pemohon --}}
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <label class="form-label text-secondary small fw-bold text-uppercase mb-1 d-flex align-items-center gap-1.5">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                        </svg>
                                        Penerima / Pemohon
                                    </label>
                                    <select name="recipient_type" id="filter-recipient-type" class="form-select form-select-modern">
                                        <option value="">Semua (Pegawai &amp; Subdit)</option>
                                        <option value="pegawai" {{ in_array(($recipientType ?? ''), ['pegawai', 'individu']) ? 'selected' : '' }}>👤 Pegawai (Individu)</option>
                                        <option value="subdit" {{ ($recipientType ?? '') == 'subdit' ? 'selected' : '' }}>🏢 Subdit (Kolektif)</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Action Bar --}}
                            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-1.5 shadow-sm fw-semibold">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                        </svg>
                                        Terapkan Filter
                                    </button>
                                    <a href="{{ route('reports.rekapitulasi') }}" class="btn btn-light border px-3 d-inline-flex align-items-center gap-1.5" title="Kembalikan semua filter">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                                            <path d="M3 3v5h5"></path>
                                        </svg>
                                        Reset Filter
                                    </a>
                                </div>

                                <div>
                                    <a href="{{ route('reports.berita-acara', array_filter(['year' => $year, 'month' => $month, 'type' => $type, 'recipient_type' => $recipientType])) }}" target="_blank"
                                        class="btn btn-outline-danger rounded-pill px-3.5 py-2 fw-bold d-inline-flex align-items-center gap-2 shadow-sm">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                            <rect x="6" y="14" width="12" height="8"></rect>
                                        </svg>
                                        Cetak Berita Acara Rekapitulasi Resmi
                                    </a>
                                </div>
                            </div>
                        </form>

                        {{-- Indikator Filter Aktif --}}
                        @if (!empty($type) || !empty($recipientType) || !empty($month))
                            <div class="mt-3 p-2.5 px-3 rounded-3 bg-light border d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <span class="text-secondary small fw-bold d-flex align-items-center">
                                        <svg class="me-1" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                                        </svg>
                                        Sedang Menyaring:
                                    </span>
                                    @if (!empty($month))
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1">
                                            Bulan: {{ \Carbon\Carbon::createFromDate(2026, (int)$month, 1)->translatedFormat('F') }}
                                        </span>
                                    @endif
                                    @if (!empty($type))
                                        <span class="badge {{ $type == 'masuk' ? 'bg-success-subtle text-success border-success-subtle' : 'bg-danger-subtle text-danger border-danger-subtle' }} border rounded-pill px-2.5 py-1">
                                            Jenis: {{ $type == 'masuk' ? 'Hanya Barang Masuk' : 'Hanya Barang Keluar' }}
                                        </span>
                                    @endif
                                    @if (!empty($recipientType))
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2.5 py-1">
                                            Penerima: {{ $recipientType == 'subdit' ? '🏢 Subdit (Kolektif)' : '👤 Pegawai (Individu)' }}
                                        </span>
                                    @endif
                                </div>

                                <a href="{{ route('reports.rekapitulasi', ['year' => $year]) }}" class="text-danger small fw-semibold text-decoration-none d-inline-flex align-items-center">
                                    <svg class="me-1" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                    Hapus Semua Filter
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Rekapitulasi per Kategori --}}
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 14px;">
                    <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-3 bg-white" style="border-top-left-radius: 14px; border-top-right-radius: 14px;">
                        <div class="d-flex align-items-center gap-2">
                            <div class="kpi-icon-pill bg-light text-primary" style="width: 38px; height: 38px;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="8" y1="6" x2="21" y2="6"></line>
                                    <line x1="8" y1="12" x2="21" y2="12"></line>
                                    <line x1="8" y1="18" x2="21" y2="18"></line>
                                    <line x1="3" y1="6" x2="3.01" y2="6"></line>
                                    <line x1="3" y1="12" x2="3.01" y2="12"></line>
                                    <line x1="3" y1="18" x2="3.01" y2="18"></line>
                                </svg>
                            </div>
                            <div>
                                <h5 class="card-title mb-0 fw-bold">Rekapitulasi Mutasi per Kategori Barang ATK</h5>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr class="text-secondary text-uppercase small" style="background-color: #f8fafc; font-size: 0.75rem; letter-spacing: 0.04em;">
                                        <th width="4%" class="text-center py-3">No</th>
                                        <th class="py-3">Kategori ATK</th>
                                        <th class="text-center py-3">Jumlah Item</th>
                                        <th class="text-center py-3 text-success">Barang Masuk</th>
                                        <th class="text-center py-3 text-danger">Barang Keluar</th>
                                        <th class="text-center py-3">Sisa Stok Fisik</th>
                                        <th class="text-end py-3 pe-3">Nilai Saldo (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $sumMasuk = 0;
                                        $sumKeluar = 0;
                                        $sumStok = 0;
                                        $sumNilai = 0;
                                    @endphp
                                    @forelse ($categoriesRekap as $index => $cat)
                                        @php
                                            $sumMasuk += $cat->total_in;
                                            $sumKeluar += $cat->total_out;
                                            $sumStok += $cat->current_stock;
                                            $sumNilai += $cat->total_value;
                                        @endphp
                                        <tr>
                                            <td class="text-center text-muted fw-semibold">
                                                {{ method_exists($categoriesRekap, 'firstItem') && $categoriesRekap->firstItem() ? ($categoriesRekap->firstItem() + $loop->index) : ($index + 1) }}
                                            </td>
                                            <td>
                                                <strong class="text-dark">{{ $cat->name }}</strong>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill">
                                                    {{ $cat->product_count }} Jenis
                                                </span>
                                            </td>
                                            <td class="text-center fw-semibold text-success">
                                                +{{ number_format($cat->total_in) }} <span class="text-muted small">Pcs</span>
                                            </td>
                                            <td class="text-center fw-semibold text-danger">
                                                -{{ number_format($cat->total_out) }} <span class="text-muted small">Pcs</span>
                                            </td>
                                            <td class="text-center fw-bold text-dark">
                                                {{ number_format($cat->current_stock) }} <span class="text-muted small">Pcs</span>
                                            </td>
                                            <td class="text-end font-monospace fw-bold text-dark pe-3">
                                                Rp {{ number_format($cat->total_value, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5 text-muted">
                                                <div class="py-3">
                                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-muted mb-2">
                                                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                                        <line x1="8" y1="21" x2="16" y2="21"></line>
                                                        <line x1="12" y1="17" x2="12" y2="21"></line>
                                                    </svg>
                                                    <p class="mb-0 fw-medium">Belum ada data rekapitulasi kategori untuk filter ini.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="border-top border-2" style="background-color: #f8fafc; font-weight: 700;">
                                    <tr>
                                        <td colspan="3" class="text-center text-uppercase py-3 text-secondary" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                                            Total Rekapitulasi Keseluruhan
                                            @if(method_exists($categoriesRekap, 'total'))
                                                <span class="badge bg-white text-secondary border rounded-pill ms-1 fw-normal" style="font-size: 0.72rem;">(Semua {{ $categoriesRekap->total() }} Kategori)</span>
                                            @endif
                                        </td>
                                        <td class="text-center text-success py-3">+{{ number_format($grandTotalMasuk ?? $sumMasuk) }} <span class="small">Pcs</span></td>
                                        <td class="text-center text-danger py-3">-{{ number_format($grandTotalKeluar ?? $sumKeluar) }} <span class="small">Pcs</span></td>
                                        <td class="text-center text-dark py-3">{{ number_format($grandTotalStok ?? $sumStok) }} <span class="small">Pcs</span></td>
                                        <td class="text-end text-primary font-monospace py-3 pe-3" style="font-size: 1.05rem;">Rp {{ number_format($grandTotalNilai ?? $sumNilai, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    @if (method_exists($categoriesRekap, 'total') && $categoriesRekap->total() > 0)
                        <div class="card-footer bg-white border-top py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2" style="border-bottom-left-radius: 14px; border-bottom-right-radius: 14px;">
                            <div class="text-muted small">
                                Menampilkan <strong>{{ $categoriesRekap->firstItem() ?? 0 }}</strong> - <strong>{{ $categoriesRekap->lastItem() ?? 0 }}</strong> dari <strong>{{ $categoriesRekap->total() }}</strong> kategori ATK (10 kategori per halaman)
                            </div>
                            @if ($categoriesRekap->hasPages())
                                <div>
                                    {{ $categoriesRekap->links() }}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('filter-type');
            const recipientSelect = document.getElementById('filter-recipient-type');

            function syncRecipientState() {
                if (typeSelect && recipientSelect) {
                    if (typeSelect.value === 'masuk') {
                        recipientSelect.value = '';
                        recipientSelect.disabled = true;
                        recipientSelect.title = 'Filter penerima hanya berlaku untuk barang keluar';
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
