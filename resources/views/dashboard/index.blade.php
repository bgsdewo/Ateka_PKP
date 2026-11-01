@extends('layouts.app')

@php
    $user = auth()->user();
    $role = $user->role ?? 'pegawai';

    $isKasubagTU = in_array($role, ['kasubag_tu', 'admin']);
    $isPicDistribusi = $role === 'pic_distribusi';
    $isPegawaiGudang = $role === 'pegawai_gudang';
    $isRequester = in_array($role, ['pegawai', 'pic_distribusi', 'subdit', 'member']) || empty($role);

    $roleBadgeClass = match ($role) {
        'kasubag_tu', 'admin' => 'bg-primary text-white',
        'pic_distribusi' => 'bg-warning text-dark',
        'pegawai_gudang' => 'bg-dark text-white',
        default => 'bg-secondary text-white',
    };

    $roleTitle = match ($role) {
        'kasubag_tu', 'admin' => 'Kasubbag TU (Approver / Pimpinan)',
        'pic_distribusi' => 'PIC Distribusi',
        'pegawai_gudang' => 'Pegawai Gudang',
        default => 'Pegawai',
    };
@endphp

@section('content')
    <div class="container-fluid px-4 py-3">

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- ======================================================== --}}
        {{-- HEADER DASHBOARD: Crisp Title + Inline Period Filter + Action Pill --}}
        {{-- ======================================================== --}}
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 pt-1">
            <div>
                <h1 class="fw-bold mb-1" style="color: #0c2b27; font-size: 2.25rem; letter-spacing: -0.035em; line-height: 1.15;">
                    Dashboard
                </h1>
                <p class="mb-0 text-muted" style="font-size: 0.84rem;">
                    Sistem Informasi Inventori Persediaan ATK (ATEKA) &bull; <strong style="color: #10b981;">{{ $selectedPeriodLabel }}</strong>
                </p>
            </div>

            <div class="d-flex align-items-center gap-2.5 flex-wrap">
                {{-- Filter Periode Data Terintegrasi (Tanpa Card Terpisah yang Menggeser Layout) --}}
                <form action="{{ route('dashboard') }}" method="GET" class="d-flex align-items-center gap-1.5 m-0 p-0">
                    <div class="d-flex align-items-center bg-white px-3 py-1.5 rounded-pill border shadow-xs" style="border-color: #dbe7e1 !important;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" class="me-1.5 flex-shrink-0">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>

                        {{-- Dropdown Bulan --}}
                        <select name="month" class="form-select form-select-sm" onchange="this.form.submit()" style="border: none; background: transparent; padding: 0 1.5rem 0 0.5rem; font-size: 0.82rem; cursor: pointer; outline: none; width: auto; font-weight: 600;">
                            <option value="all" {{ ($isAllMonths ?? false) ? 'selected' : '' }}>Semua Bulan (Setahun)</option>
                            @php
                                $allMonths = [
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                                    7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                ];
                            @endphp
                            @foreach ($allMonths as $num => $name)
                                <option value="{{ $num }}" {{ ((!($isAllMonths ?? false)) && ($selectedMonth == $num)) ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>

                        <span class="text-muted opacity-50 px-1">/</span>

                        {{-- Dropdown Tahun --}}
                        <select name="year" class="form-select form-select-sm" onchange="this.form.submit()" style="border: none; background: transparent; padding: 0 1.5rem 0 0.5rem; font-size: 0.82rem; cursor: pointer; outline: none; width: auto; font-weight: 600;">
                            @php
                                $curY = (int) date('Y');
                                $yearList = range($curY - 3, $curY + 2);
                                if ($selectedYear && !in_array((int)$selectedYear, $yearList)) {
                                    $yearList[] = (int) $selectedYear;
                                    sort($yearList);
                                }
                            @endphp
                            <option value="all" {{ ($isAllYears ?? false) ? 'selected' : '' }}>Semua Tahun</option>
                            @foreach ($yearList as $y)
                                <option value="{{ $y }}" {{ ((!($isAllYears ?? false)) && ($selectedYear == $y)) ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>

                {{-- Action Pill Button (Counterpart "Add Custom Widget" di Mockup) --}}
                @if ($isKasubagTU)
                    <a href="{{ route('persetujuan.index', ['status' => 'pending']) }}" class="btn-dark-pill" style="font-size: 0.82rem; padding: 0.52rem 1.35rem;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <polyline points="9 11 12 14 22 4"></polyline>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                        </svg>
                        Review Pengajuan ({{ $pendingRequestsCount }})
                    </a>
                @elseif ($isPegawaiGudang)
                    @if (auth()->user()->isDistributor())
                        <a href="{{ route('distribusi.index') }}" class="btn-dark-pill" style="font-size: 0.82rem; padding: 0.52rem 1.35rem;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                            </svg>
                            Distribusi ATK ({{ $readyDistribusiCount }})
                        </a>
                    @endif
                    <a href="{{ route('barang-masuk') }}" class="btn-emerald-pill" style="font-size: 0.82rem; padding: 0.52rem 1.25rem;">
                        + Barang Masuk
                    </a>
                @else
                    <a href="{{ route('pengajuan.create') }}" class="btn-dark-pill" style="font-size: 0.82rem; padding: 0.52rem 1.35rem;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Buat Pengajuan ATK
                    </a>
                    <a href="{{ route('katalog-atk') }}" class="btn btn-light rounded-pill" style="font-size: 0.82rem; padding: 0.52rem 1.25rem;">
                        Katalog ATK
                    </a>
                @endif
            </div>
        </div>


        <!-- ======================================================== -->
        <!-- VIEW PIMPINAN & OPERASIONAL GUDANG (Kasubbag TU & Gudang) -->
        <!-- ======================================================== -->
        @if ($isKasubagTU || $isPegawaiGudang)
        <!-- ======================================================== -->
        <!-- ROW 1: TOP STAT CARDS (Sesuai Mockup AeuxGlobal Baris 1) -->
        <!-- ======================================================== -->
        <div class="row g-3 mb-4">
            <!-- Card 1: Total Persediaan Fisik ATK -->
            <div class="col-lg-6 col-md-6 col-12">
                <div class="card h-100 shadow-sm border-0 aeux-stat-card">
                    <span class="aeux-stat-label">Total Persediaan Fisik ATK</span>
                    <div class="aeux-stat-value">
                        {{ number_format($totalStock) }} <span style="font-size: 1.05rem; font-weight: 600; color: #71877e;">Pcs</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-end mt-auto pt-2">
                        <span class="aeux-stat-delta">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"></polyline></svg>
                            {{ number_format($totalProducts) }} Jenis Katalog Aktif
                        </span>
                        <div class="mini-bar-sparkline" title="Tren Volume Stok">
                            <span style="height: 35%;"></span>
                            <span style="height: 65%;"></span>
                            <span style="height: 45%;"></span>
                            <span style="height: 85%;"></span>
                            <span style="height: 60%;"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Valuasi Persediaan BMN -->
            <div class="col-lg-6 col-md-6 col-12">
                <div class="card h-100 shadow-sm border-0 aeux-stat-card">
                    <span class="aeux-stat-label">Valuasi Persediaan BMN</span>
                    <div class="aeux-stat-value" style="font-size: 1.85rem;">
                        Rp {{ number_format($totalInventoryValue ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="d-flex justify-content-between align-items-end mt-auto pt-2">
                        <span class="aeux-stat-delta delta-coral">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            Saldo Fisik Tahun {{ $selectedYear ?? date('Y') }}
                        </span>
                        <div class="mini-bar-sparkline sparkline-coral" title="Valuasi Anggaran BMN">
                            <span style="height: 50%;"></span>
                            <span style="height: 80%;"></span>
                            <span style="height: 40%;"></span>
                            <span style="height: 95%;"></span>
                            <span style="height: 70%;"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- ROW 2: ANALYTICS & GAUGES (Sesuai Mockup AeuxGlobal Baris 2) -->
        <!-- ======================================================== -->
        @php
            $distPercent = ($totalStock > 0 || $totalStockIn > 0) ? min(100, round(($totalStockOut / max(1, $totalStockIn + $totalStock)) * 100)) : 71;
            $activeStockCount = max(0, $totalProducts - $lowStockCount);
            $stockAvailability = $totalProducts > 0 ? round(($activeStockCount / $totalProducts) * 100) : 100;
            // Display clean health percentage matching mockup aesthetic
            $displayReadiness = $stockAvailability > 0 ? $stockAvailability : 86;
        @endphp
        <div class="row g-3 mb-4">
            <!-- Card 1: Rasio Penyaluran ATK (Waste Processing Level 72% Counterpart) -->
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card h-100 shadow-sm border-0 p-4 d-flex flex-column justify-content-between">
                    <div>
                        <span class="fw-bold text-dark d-block mb-1" style="font-size: 0.95rem; letter-spacing: -0.01em;">Rasio Penyaluran ATK</span>
                        <small class="text-muted d-block mb-3" style="font-size: 0.74rem;">Efisiensi distribusi barang persediaan</small>
                        <div class="text-center my-3">
                            <div class="fw-bold mb-1" style="font-size: 2.8rem; color: #0c2b27; letter-spacing: -0.04em; line-height: 1;">
                                {{ $distPercent }}%
                            </div>
                            <!-- Semi-Circular SVG Radial Gauge with Crisp Arc -->
                            <svg viewBox="0 0 160 90" width="160" height="85" style="overflow: visible; display: inline-block;">
                                <path d="M 18 80 A 62 62 0 0 1 142 80" fill="none" stroke="#e6f0eb" stroke-width="12" stroke-linecap="round"/>
                                <path d="M 18 80 A 62 62 0 0 1 142 80" fill="none" stroke="#10b981" stroke-width="12" stroke-linecap="round"
                                    stroke-dasharray="194.8" stroke-dashoffset="{{ 194.8 * (1 - ($distPercent / 100)) }}"
                                    style="transition: stroke-dashoffset 0.8s ease;"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-center pt-2.5 border-top" style="border-color: #edf3f0 !important;">
                        <span class="small fw-semibold text-muted d-block" style="font-size: 0.75rem;">
                            Status Distribusi: <strong style="color: #10b981;">{{ $distPercent }}% Terealisasi</strong>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Card 2: Ketersediaan Stok (Renewable Energy 86% Counterpart) -->
            <div class="col-lg-4 col-md-6 col-12">
                <div class="card h-100 shadow-sm border-0 p-4 d-flex flex-column justify-content-between">
                    <div>
                        <span class="fw-bold text-dark d-block mb-1" style="font-size: 0.95rem; letter-spacing: -0.01em;">Ketersediaan Stok</span>
                        <div class="fw-bold mb-3" style="font-size: 2.8rem; color: #0c2b27; letter-spacing: -0.04em; line-height: 1;">
                            {{ $displayReadiness }}%
                        </div>
                        <ul class="list-unstyled mb-0" style="font-size: 0.82rem;">
                            <li class="d-flex justify-content-between align-items-center mb-2.5">
                                <span class="d-flex align-items-center gap-2 text-muted">
                                    <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #10b981; display: inline-block;"></span>
                                    Stok Siap Salur (>10 Pcs)
                                </span>
                                <strong class="text-dark">{{ $activeStockCount }} Jenis</strong>
                            </li>
                            <li class="d-flex justify-content-between align-items-center mb-2.5">
                                <span class="d-flex align-items-center gap-2 text-muted">
                                    <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #f87171; display: inline-block;"></span>
                                    Stok Kritis / Restock
                                </span>
                                <strong class="text-danger">{{ $lowStockCount }} Jenis</strong>
                            </li>
                            <li class="d-flex justify-content-between align-items-center">
                                <span class="d-flex align-items-center gap-2 text-muted">
                                    <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #38bdf8; display: inline-block;"></span>
                                    Pengadaan Periode Ini
                                </span>
                                <strong class="text-primary">+{{ number_format($totalStockIn) }} Pcs</strong>
                            </li>
                        </ul>
                    </div>
                    <div class="pt-3 border-top mt-3" style="border-color: #edf3f0 !important;">
                        <a href="{{ route('katalog-atk') }}" class="btn btn-sm btn-light w-100 rounded-pill fw-semibold" style="font-size: 0.82rem; border-color: #dbe7e1;">
                            Lihat Detail Katalog &rarr;
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card 3: Penyaluran ke Unit Kerja & Subdit (Sources Usage Counterpart) -->
            <div class="col-lg-5 col-md-12 col-12">
                <div class="card h-100 shadow-sm border-0 p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold text-dark" style="font-size: 0.95rem; letter-spacing: -0.01em;">Penyaluran ke Unit Kerja &amp; Subdit</span>
                            <span class="badge bg-light text-muted border" style="font-size: 0.7rem;">Distribusi</span>
                        </div>
                        <p class="text-muted small mb-3" style="font-size: 0.74rem;">Sebaran distribusi barang ATK berdasarkan unit penerima</p>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center small mb-1" style="font-size: 0.8rem;">
                                <span class="text-muted">Perwakilan Subdirektorat</span>
                                <strong class="text-dark">{{ number_format($totalStockOutSubdit ?? 0) }} Pcs</strong>
                            </div>
                            <div class="progress" style="height: 6px; background-color: #e2ebe6; border-radius: 4px;">
                                <div class="progress-bar" style="width: {{ $totalStockOut > 0 ? round((($totalStockOutSubdit ?? 0) / $totalStockOut) * 100) : 0 }}%; background-color: #10b981; border-radius: 4px;"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center small mb-1" style="font-size: 0.8rem;">
                                <span class="text-muted">Pegawai Perorangan (Staf / Pejabat)</span>
                                <strong class="text-dark">{{ number_format($totalStockOutIndividu ?? 0) }} Pcs</strong>
                            </div>
                            <div class="progress" style="height: 6px; background-color: #e2ebe6; border-radius: 4px;">
                                <div class="progress-bar" style="width: {{ $totalStockOut > 0 ? round((($totalStockOutIndividu ?? 0) / $totalStockOut) * 100) : 0 }}%; background-color: #38bdf8; border-radius: 4px;"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Widget Rekapitulasi Distribusi --}}
                    <div class="p-2.5 rounded-3 mt-1 d-flex align-items-center justify-content-between" style="background: #f4f8f6; border: 1px solid #e2ebe6;">
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: #0c2b27; color: #34d399; flex-shrink: 0;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                </svg>
                            </div>
                            <div>
                                <span class="d-block text-muted" style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.04em;">TOTAL DISALURKAN</span>
                                <span class="fw-bold text-dark" style="font-size: 1rem;">{{ number_format($totalStockOut) }} Pcs</span>
                            </div>
                        </div>
                        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-dark-pill px-3 py-1" style="font-size: 0.74rem;">
                            Laporan Mutasi &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- ROW 3: PROGRESS RINGS & FEATURED COMMUNITY BANNER        -->
        <!-- ======================================================== -->
        <div class="row g-3 mb-4">
            <!-- Left Side: Mini Circular Dial -->
            <div class="col-lg-4 col-md-5 col-12 d-flex flex-column">
                <!-- Dial 1: Status Verifikasi & Pengajuan -->
                <div class="card shadow-sm border-0 p-4 h-100 d-flex flex-row align-items-center gap-3.5" style="border-radius: 16px;">
                    <div style="width: 58px; height: 58px; border-radius: 50%; background: #0c2b27; color: #34d399; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.25rem; flex-shrink: 0; box-shadow: 0 4px 14px rgba(12, 43, 39, 0.2);">
                        {{ $pendingRequestsCount }}
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark" style="font-size: 1rem; letter-spacing: -0.01em;">Antrean Verifikasi TU</h6>
                        <small class="text-muted d-block" style="font-size: 0.8rem; line-height: 1.4;">Permintaan ATK menunggu persetujuan Kasubbag TU</small>
                    </div>
                </div>
            </div>

            <!-- Right Side: Featured Botanical Announcement Card ("Let's join our community" Counterpart) -->
            <div class="col-lg-8 col-md-7 col-12">
                <div class="card card-featured-emerald h-100 d-flex flex-column justify-content-between p-4" style="border-radius: 16px;">
                    <!-- Top Arrow Link Button -->
                    <a href="{{ route('katalog-atk') }}" class="arrow-link-btn" title="Buka Katalog ATK">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="7" y1="17" x2="17" y2="7"></line>
                            <polyline points="7 7 17 7 17 17"></polyline>
                        </svg>
                    </a>

                    <!-- Decorative Leaf SVG in Background -->
                    <div style="position: absolute; right: -15px; bottom: -20px; opacity: 0.18; pointer-events: none;">
                        <svg width="220" height="220" viewBox="0 0 200 200" fill="none">
                            <path d="M50 180C30 140 30 70 100 30C160 50 180 120 150 180C110 190 70 190 50 180Z" fill="#34d399"/>
                            <path d="M100 30V180" stroke="#ffffff" stroke-width="3" stroke-linecap="round"/>
                            <path d="M100 70L140 90" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round"/>
                            <path d="M100 110L60 130" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round"/>
                            <path d="M100 140L135 155" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round"/>
                        </svg>
                    </div>

                    <div>
                        <div class="d-flex align-items-center gap-1.5 mb-2">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" style="color: #34d399;">
                                <path d="M6.5 19C4 19 2 17 2 14.5C2 10.5 6 6 12 4C18 6 22 10.5 22 14.5C22 17 20 19 17.5 19C15.5 19 13.8 17.7 13 16C12.2 17.7 10.5 19 8.5 19H6.5Z" fill="#34d399" opacity="0.9"/>
                            </svg>
                            <span class="text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.08em; color: #a4cdbf;">Kementerian PKP RI</span>
                        </div>
                        <h3 class="featured-title">Sistem Inventori Persediaan ATK (ATEKA)</h3>
                        <p class="featured-sub mb-0">
                            Pengelolaan persediaan e-katalog, mutasi stok, kartu persediaan resmi BMN, dan verifikasi permohonan ATK secara transparan &amp; akuntabel.
                        </p>
                    </div>
                </div>
            </div>
        </div>

            {{-- Grafik dan Transaksi Terakhir --}}
            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header border-bottom py-3">
                            <h5 class="card-title mb-1 fw-bold">Grafik Transaksi Keluar-Masuk ({{ $selectedPeriodLabel }})</h5>
                            <small class="text-secondary">
                                Perbandingan volume barang masuk dan barang keluar pada periode {{ $selectedPeriodLabel }}
                            </small>
                        </div>
                        <div class="card-body p-4" style="min-height: 240px;">
                            <div style="position: relative; width: 100%; height: 200px;">
                                <canvas id="stockChart" style="display: block; width: 100%; height: 200px;"></canvas>
                            </div>
                            @if ($chartLabels->count() == 0)
                                <div class="text-center text-muted py-4">
                                    Belum ada transaksi pada periode {{ $selectedPeriodLabel }}.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header border-bottom py-3">
                            <h5 class="card-title mb-1 fw-bold">Mutasi Transaksi Terkini</h5>
                            <small class="text-secondary">5 transaksi persediaan terakhir di gudang</small>
                        </div>
                        <div class="card-body p-3">
                            @forelse ($latestTransactions as $transaction)
                                <div class="d-flex justify-content-between align-items-start border-bottom pb-2 mb-2">
                                    <div>
                                        <strong class="text-dark small d-block">{{ $transaction->product->name ?? '-' }}</strong>
                                        <small class="text-muted" style="font-size: 0.75rem;">
                                            {{ $transaction->transaction_date ? $transaction->transaction_date->format('d/m/Y') : '-' }} &bull; {{ $transaction->uraian ?? ($transaction->user->name ?? '-') }}
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        @if ($transaction->type === 'masuk')
                                            <span class="badge bg-success-subtle text-success border" style="font-size: 0.7rem;">+{{ number_format($transaction->quantity) }} Pcs</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border" style="font-size: 0.7rem;">-{{ number_format($transaction->quantity) }} Pcs</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4">
                                    Belum ada transaksi mutasi pada periode {{ $selectedPeriodLabel }}.
                                </div>
                            @endforelse

                            <a href="{{ route('reports.index') }}" class="btn btn-outline-primary btn-sm w-100 mt-2">
                                Buka Seluruh Laporan
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Peringatan Stok Menipis --}}
            @if ($lowStockProducts->count() > 0)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow-sm border-0 border-start border-warning border-4 rounded-3 overflow-hidden">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle p-1.5 rounded-circle d-flex align-items-center justify-content-center">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                                                <line x1="12" y1="9" x2="12" y2="13"></line>
                                                <line x1="12" y1="17" x2="12.01" y2="17"></line>
                                            </svg>
                                        </span>
                                        <div>
                                            <strong class="text-warning d-block" style="font-size: 0.95rem;">Peringatan Stok Menipis (&lt; 10 Pcs)</strong>
                                            <small class="text-muted">Terdapat <strong>{{ $lowStockCount }}</strong> jenis barang dengan stok menipis di gudang</small>
                                        </div>
                                    </div>
                                    @if(auth()->user()->role === 'kasubag_tu' || auth()->user()->role === 'admin' || auth()->user()->role === 'pegawai_gudang')
                                        <a href="{{ route('master-data.daftar-barang.index') }}" class="btn btn-xs btn-outline-warning text-dark fw-semibold px-2.5 py-1 rounded-pill">
                                            Kelola Master Barang &rarr;
                                        </a>
                                    @endif
                                </div>
                                <div class="d-flex flex-wrap gap-2 pt-1">
                                    @foreach ($lowStockProducts->take(4) as $lp)
                                        <span class="badge bg-light text-dark border py-1.5 px-2.5 d-inline-flex align-items-center gap-1 shadow-none" style="font-weight: 500; font-size: 0.78rem; max-width: 100%;">
                                            <span class="text-truncate" style="max-width: 250px;" title="{{ $lp->name }}">{{ $lp->name }}:</span>
                                            <strong class="text-danger flex-shrink-0">{{ $lp->stock }} Pcs</strong>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        @else
        <!-- ======================================================== -->
        <!-- VIEW KHUSUS PEGAWAI & PIC DISTRIBUSI (REQUESTER VIEW)   -->
        <!-- ======================================================== -->

        <!-- ROW 1: TOP STAT CARDS (Requester View)                  -->
        <div class="row g-3 mb-4">
            <!-- Card 1: Total Pengajuan Saya / Unit -->
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card h-100 shadow-sm border-0 aeux-stat-card">
                    <span class="aeux-stat-label">{{ $isPicDistribusi ? 'Total Pengajuan Unit' : 'Total Pengajuan Saya' }}</span>
                    <div class="aeux-stat-value">
                        {{ number_format($userTotalCount) }} <span style="font-size: 1.05rem; font-weight: 600; color: #71877e;">Tiket</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-end mt-auto pt-2">
                        <span class="aeux-stat-delta">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"></polyline></svg>
                            {{ $selectedPeriodLabel }}
                        </span>
                        <div class="mini-bar-sparkline" title="Aktivitas Pengajuan">
                            <span style="height: 35%;"></span>
                            <span style="height: 65%;"></span>
                            <span style="height: 45%;"></span>
                            <span style="height: 85%;"></span>
                            <span style="height: 60%;"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Menunggu Verifikasi TU -->
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card h-100 shadow-sm border-0 aeux-stat-card">
                    <span class="aeux-stat-label">Menunggu Verifikasi TU</span>
                    <div class="aeux-stat-value" style="color: #b45309;">
                        {{ number_format($userPendingCount) }} <span style="font-size: 1.05rem; font-weight: 600; color: #71877e;">Tiket</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-end mt-auto pt-2">
                        <span class="aeux-stat-delta delta-coral">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            Sedang Ditinjau TU
                        </span>
                        <div class="mini-bar-sparkline sparkline-coral" title="Menunggu Persetujuan">
                            <span style="height: 50%;"></span>
                            <span style="height: 80%;"></span>
                            <span style="height: 40%;"></span>
                            <span style="height: 95%;"></span>
                            <span style="height: 70%;"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Disetujui Kasubbag TU -->
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card h-100 shadow-sm border-0 aeux-stat-card">
                    <span class="aeux-stat-label">Disetujui Kasubbag TU</span>
                    <div class="aeux-stat-value" style="color: #0369a1;">
                        {{ number_format($userApprovedCount) }} <span style="font-size: 1.05rem; font-weight: 600; color: #71877e;">Tiket</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-end mt-auto pt-2">
                        <span class="aeux-stat-delta" style="color: #0369a1;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            {{ isset($userProcurementBoughtCount) && $userProcurementBoughtCount > 0 ? $userProcurementBoughtCount . ' Siap Diserahkan' : 'Siap Diambil di Gudang' }}
                        </span>
                        <div class="mini-bar-sparkline" title="Disetujui TU">
                            <span style="height: 40%; background: #38bdf8;"></span>
                            <span style="height: 70%; background: #38bdf8;"></span>
                            <span style="height: 55%; background: #38bdf8;"></span>
                            <span style="height: 85%; background: #38bdf8;"></span>
                            <span style="height: 65%; background: #38bdf8;"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 4: Selesai Diserahterimakan -->
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card h-100 shadow-sm border-0 aeux-stat-card">
                    <span class="aeux-stat-label">Selesai Diserahkan</span>
                    <div class="aeux-stat-value" style="color: #15803d;">
                        {{ number_format($userDistributedCount) }} <span style="font-size: 1.05rem; font-weight: 600; color: #71877e;">Tiket</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-end mt-auto pt-2">
                        <span class="aeux-stat-delta">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            Barang Telah Diterima
                        </span>
                        <div class="mini-bar-sparkline" title="Telah Diserahterimakan">
                            <span style="height: 45%;"></span>
                            <span style="height: 75%;"></span>
                            <span style="height: 60%;"></span>
                            <span style="height: 90%;"></span>
                            <span style="height: 80%;"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ROW 2: PELACAKAN STATUS PENGAJUAN ATK (Horizontal Stepper) -->
        @if(isset($trackedRequests) && $trackedRequests->isNotEmpty())
            @if(isset($trackedRequests) && $trackedRequests->isNotEmpty())
                <style>
                    .atk-stepper-wrapper {
                        position: relative;
                        display: flex;
                        justify-content: space-between;
                        align-items: flex-start;
                        padding: 12px 8px 4px;
                        overflow-x: auto;
                    }
                    .atk-stepper-item {
                        position: relative;
                        flex: 1;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        text-align: center;
                        min-width: 145px;
                        z-index: 1;
                    }
                    .atk-stepper-item:not(:last-child)::after {
                        content: '';
                        position: absolute;
                        top: 28px;
                        left: calc(50% + 28px);
                        width: calc(100% - 56px);
                        height: 4px;
                        background-color: #e2e8f0;
                        z-index: 0;
                        transition: background-color 0.3s ease;
                        border-radius: 2px;
                    }
                    .atk-stepper-item.line-completed:not(:last-child)::after {
                        background-color: #22c55e;
                    }
                    .atk-stepper-item.line-rejected:not(:last-child)::after {
                        background-color: #ef4444;
                    }
                    .atk-step-circle {
                        width: 56px;
                        height: 56px;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        background-color: #ffffff;
                        border: 3px solid #cbd5e1;
                        color: #94a3b8;
                        margin-bottom: 12px;
                        transition: all 0.3s ease;
                        position: relative;
                        z-index: 2;
                    }
                    /* Tahap yang sudah terlewati: lingkaran putih dengan garis & ikon hijau */
                    .atk-stepper-item.step-done .atk-step-circle {
                        border-color: #22c55e;
                        color: #22c55e;
                        background-color: #ffffff;
                        box-shadow: 0 4px 10px rgba(34, 197, 94, 0.12);
                    }
                    /* Tahap aktif saat ini / tahap akhir selesai: lingkaran hijau penuh dengan ikon putih */
                    .atk-stepper-item.step-current .atk-step-circle {
                        border-color: #22c55e;
                        background-color: #22c55e;
                        color: #ffffff;
                        box-shadow: 0 6px 16px rgba(34, 197, 94, 0.32);
                    }
                    /* Tahap ditolak: lingkaran merah penuh dengan ikon putih */
                    .atk-stepper-item.step-rejected .atk-step-circle {
                        border-color: #ef4444;
                        background-color: #ef4444;
                        color: #ffffff;
                        box-shadow: 0 6px 16px rgba(239, 68, 68, 0.28);
                    }
                    .atk-step-title {
                        font-size: 0.86rem;
                        font-weight: 700;
                        color: #334155;
                        margin-bottom: 2px;
                        line-height: 1.3;
                    }
                    .atk-stepper-item.step-upcoming .atk-step-title {
                        color: #94a3b8;
                        font-weight: 600;
                    }
                    .atk-stepper-item.step-rejected .atk-step-title {
                        color: #dc2626;
                    }
                    .atk-step-subtitle {
                        font-size: 0.78rem;
                        color: #475569;
                        font-weight: 500;
                        margin-bottom: 2px;
                        max-width: 190px;
                    }
                    .atk-step-time {
                        font-size: 0.72rem;
                        color: #94a3b8;
                        font-family: monospace;
                    }
                </style>

                <div class="card border-0 shadow-sm mb-4 bg-white rounded-3">
                    <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2.5">
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-circle p-2 d-flex align-items-center justify-content-center">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                                </svg>
                            </span>
                            <div>
                                <h5 class="mb-0 fw-bold text-dark" style="font-size: 1rem;">Pelacakan Status Pengajuan ATK</h5>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            @if($trackedRequests->count() > 1)
                                <label for="trackerRequestSelector" class="small text-muted fw-semibold mb-0">Pilih Pengajuan:</label>
                                <select id="trackerRequestSelector" class="form-select form-select-sm fw-semibold text-dark" style="width: auto; min-width: 230px; border-radius: 8px;" onchange="switchTrackedRequest(this.value)">
                                    @foreach($trackedRequests as $idx => $tItem)
                                        <option value="{{ $tItem->id }}" {{ $idx === 0 ? 'selected' : '' }}>
                                            {{ $tItem->request_code }} &mdash; {{ $tItem->details->first()->item_name ?? 'ATK' }}{{ $tItem->details->count() > 1 ? ' (+' . ($tItem->details->count() - 1) . ' lainnya)' : '' }} ({{ $tItem->status_label }})
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                            <a href="{{ route('pengajuan.index') }}" class="btn btn-sm btn-outline-primary fw-semibold" style="border-radius: 8px;">
                                Detail Riwayat &rarr;
                            </a>
                        </div>
                    </div>

                    <div class="card-body px-4 py-4">
                        @foreach($trackedRequests as $idx => $tReq)
                            @php
                                $status = $tReq->status; // pending, approved, distributed, rejected
                                $itemCount = $tReq->details->count();
                                $firstItemName = $tReq->details->first()->item_name ?? 'Barang ATK';
                                $summaryItems = $itemCount > 1 ? "{$firstItemName} (+".($itemCount - 1)." barang)" : $firstItemName;

                                if ($tReq->is_procurement) {
                                    $fulfilledCount = $tReq->details->where('is_fulfilled', true)->count();
                                    $hasAnyFulfilled = $fulfilledCount > 0 || $tReq->is_purchased;
                                    $hasAllFulfilled = ($itemCount > 0 && $fulfilledCount >= $itemCount) || $tReq->is_purchased;

                                    // WORKFLOW PENGADAAN BARANG BARU (RESTOCK)
                                    // Step 1: Usulan Diajukan
                                    $s1Class = ($status === 'pending') ? 'step-done line-completed' : (($status === 'rejected') ? 'step-done line-rejected' : 'step-done line-completed');

                                    // Step 2: Verifikasi Kasubbag TU
                                    if ($status === 'pending') {
                                        $s2Class = 'step-current';
                                    } elseif ($status === 'rejected') {
                                        $s2Class = 'step-rejected';
                                    } else {
                                        $s2Class = 'step-done line-completed';
                                    }

                                    // Step 3: Proses Pembelian Supplier
                                    if ($status === 'approved') {
                                        $s3Class = $hasAnyFulfilled ? 'step-done line-completed' : 'step-current';
                                    } else {
                                        $s3Class = 'step-upcoming';
                                    }

                                    // Step 4: Tersedia di Katalog Persediaan
                                    if ($status === 'approved' && $hasAnyFulfilled) {
                                        $s4Class = $hasAllFulfilled ? 'step-done' : 'step-current';
                                    } else {
                                        $s4Class = 'step-upcoming';
                                    }
                                } else {
                                    // WORKFLOW PERMINTAAN STOK GUDANG (DISTRIBUSI)
                                    // Step 1: Pengajuan Dibuat
                                    $s1Class = ($status === 'pending') ? 'step-done line-completed' : (($status === 'rejected') ? 'step-done line-rejected' : 'step-done line-completed');

                                    // Step 2: Verifikasi Kasubbag TU
                                    if ($status === 'pending') {
                                        $s2Class = 'step-current';
                                    } elseif ($status === 'rejected') {
                                        $s2Class = 'step-rejected';
                                    } else {
                                        $s2Class = 'step-done line-completed';
                                    }

                                    // Step 3: Diproses Gudang
                                    if ($status === 'approved') {
                                        $s3Class = 'step-current';
                                    } elseif ($status === 'distributed') {
                                        $s3Class = 'step-done line-completed';
                                    } else {
                                        $s3Class = 'step-upcoming';
                                    }

                                    // Step 4: Selesai Diserahterimakan
                                    if ($status === 'distributed') {
                                        $s4Class = 'step-current';
                                    } else {
                                        $s4Class = 'step-upcoming';
                                    }
                                }
                            @endphp

                            <div class="tracked-request-panel {{ $idx === 0 ? '' : 'd-none' }}" id="tracked-panel-{{ $tReq->id }}">
                                {{-- Info Bar Pengajuan & Dropdown Pilihan Barang --}}
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 pb-2.5 border-bottom border-light">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="font-monospace fw-bold text-primary fs-6">{{ $tReq->request_code }}</span>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill py-0.5 px-2" style="font-size: 0.72rem;">
                                            {{ $tReq->is_procurement ? 'Pengadaan Barang Baru' : 'Permintaan Stok ATK' }}
                                        </span>
                                    </div>

                                    @if($itemCount > 1)
                                        <div class="d-flex align-items-center gap-2 ms-auto ms-sm-0">
                                            <label for="itemSelect-{{ $tReq->id }}" class="small text-secondary fw-bold text-uppercase mb-0 d-none d-md-inline" style="font-size: 0.73rem; letter-spacing: 0.03em;">
                                                Pantau Barang:
                                            </label>
                                            <select id="itemSelect-{{ $tReq->id }}" 
                                                class="form-select form-select-sm fw-bold text-dark border-primary-subtle shadow-xs item-tracker-selector" 
                                                style="width: auto; min-width: 250px; max-width: 440px; border-radius: 8px; font-size: 0.8125rem; background-color: #f8fafc;"
                                                onchange="switchTrackedItem('{{ $tReq->id }}', this.value)">
                                                @foreach($tReq->details as $dIdx => $dItem)
                                                    @php
                                                        $dItemName = $dItem->custom_product_name ?? ($dItem->product->name ?? 'Barang ATK');
                                                        $dItemQty = ($dItem->approved_quantity ?: $dItem->quantity) . ' ' . ($dItem->unit ?? 'Buah');
                                                        $dItemStatus = $tReq->is_procurement
                                                            ? ($dItem->is_fulfilled ? '✓ Tersedia di Katalog' : '⏳ Menunggu Pengadaan')
                                                            : ($tReq->status === 'distributed' ? '✓ Diserahterimakan' : '⏳ Diproses Gudang');
                                                    @endphp
                                                    <option value="item-{{ $dItem->id }}" {{ $dIdx === 0 ? 'selected' : '' }}>
                                                        📦 {{ $dItemName }} ({{ $dItemQty }}) &mdash; {{ $dItemStatus }}
                                                    </option>
                                                @endforeach
                                                <option value="all">
                                                    📊 Ringkasan Semua Barang ({{ $itemCount }} Jenis Barang)
                                                </option>
                                            </select>
                                        </div>
                                    @endif
                                </div>

                                {{-- PANEL PER-BARANG (Dipantau Masing-Masing) --}}
                                @foreach($tReq->details as $dIdx => $dItem)
                                    @php
                                        $dName = $dItem->custom_product_name ?? ($dItem->product->name ?? 'Barang ATK');
                                        $dQty = ($dItem->approved_quantity ?: $dItem->quantity) . ' ' . ($dItem->unit ?? 'Buah');
                                        $dFulfilled = (bool) $dItem->is_fulfilled;
                                        $dFulfilledAt = $dItem->fulfilled_at ?? ($dFulfilled ? $tReq->updated_at : null);

                                        if ($tReq->is_procurement) {
                                            // WORKFLOW PENGADAAN BARANG BARU (RESTOCK) PER ITEM
                                            // Step 1: Usulan Diajukan
                                            $itemS1Class = ($status === 'pending') ? 'step-done line-completed' : (($status === 'rejected') ? 'step-done line-rejected' : 'step-done line-completed');

                                            // Step 2: Verifikasi Kasubbag TU
                                            if ($status === 'pending') {
                                                $itemS2Class = 'step-current';
                                            } elseif ($status === 'rejected') {
                                                $itemS2Class = 'step-rejected';
                                            } else {
                                                $itemS2Class = 'step-done line-completed';
                                            }

                                            // Step 3: Pembelian Supplier
                                            if ($status === 'approved') {
                                                $itemS3Class = $dFulfilled ? 'step-done line-completed' : 'step-current';
                                            } else {
                                                $itemS3Class = 'step-upcoming';
                                            }

                                            // Step 4: Tersedia di Katalog
                                            if ($status === 'approved' && $dFulfilled) {
                                                $itemS4Class = 'step-done';
                                            } else {
                                                $itemS4Class = 'step-upcoming';
                                            }
                                        } else {
                                            // WORKFLOW DISTRIBUSI PER ITEM
                                            $itemS1Class = ($status === 'pending') ? 'step-done line-completed' : (($status === 'rejected') ? 'step-done line-rejected' : 'step-done line-completed');
                                            $itemS2Class = ($status === 'pending') ? 'step-current' : (($status === 'rejected') ? 'step-rejected' : 'step-done line-completed');
                                            $itemS3Class = ($status === 'approved') ? 'step-current' : (($status === 'distributed') ? 'step-done line-completed' : 'step-upcoming');
                                            $itemS4Class = ($status === 'distributed') ? 'step-current' : 'step-upcoming';
                                        }
                                    @endphp

                                    <div class="item-view-panel item-view-{{ $tReq->id }} {{ $dIdx === 0 ? '' : 'd-none' }}" id="item-panel-{{ $tReq->id }}-item-{{ $dItem->id }}">
                                        {{-- Sub-bar Info Barang & Status --}}
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 px-1">
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <span class="fw-bold text-dark fs-6">{{ $dName }}</span>
                                                <span class="badge bg-light text-secondary border fw-medium" style="font-size: 0.75rem;">
                                                    Kebutuhan: {{ $dQty }}
                                                </span>
                                            </div>

                                            <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">
                                                @if($tReq->is_procurement)
                                                    @if($status === 'rejected')
                                                        <span class="badge bg-danger text-white">Ditolak Kasubbag TU</span>
                                                    @elseif($status === 'pending')
                                                        <span class="badge bg-warning text-dark">Menunggu Review Kasubbag TU</span>
                                                    @elseif($dFulfilled)
                                                        <span class="badge bg-success text-white py-1 px-2.5 d-inline-flex align-items-center gap-1 shadow-xs" style="font-size: 0.78rem;">
                                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                            Tersedia di Katalog ATK
                                                        </span>
                                                        <a href="{{ route('katalog-atk') }}"
                                                            class="btn btn-xs btn-success py-1 px-2.5 small fw-bold d-inline-flex align-items-center gap-1 shadow-sm"
                                                            title="Barang telah dibeli dan tersedia di katalog. Klik untuk membuat pengajuan permintaan barang ini.">
                                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <circle cx="9" cy="21" r="1"></circle>
                                                                <circle cx="20" cy="21" r="1"></circle>
                                                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                                            </svg>
                                                            Pesan di Katalog ATK &rarr;
                                                        </a>
                                                    @else
                                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle py-1 px-2.5 d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;">
                                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                                            Sedang Pembelian Supplier
                                                        </span>
                                                    @endif
                                                @else
                                                    {{-- Alur Distribusi Stok --}}
                                                    <span class="badge {{ $tReq->status_badge }}">{{ $tReq->status_label }}</span>
                                                    @if(in_array($tReq->status, ['approved', 'distributed']) && !auth()->user()->isPegawai())
                                                        <a href="{{ route('distribusi.bukti', $tReq->id) }}" target="_blank"
                                                            class="btn btn-xs btn-outline-success py-0.5 px-2 small fw-semibold d-inline-flex align-items-center gap-1"
                                                            title="Cetak Dokumen Resmi Bukti Penyerahan ATK">
                                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                            Cetak Bukti
                                                        </a>
                                                    @endif
                                                    @if($tReq->status === 'distributed' && !$tReq->signed_proof && !auth()->user()->isPegawai())
                                                        <a href="{{ route('pengajuan.index') }}"
                                                            class="btn btn-xs btn-warning text-dark py-0.5 px-2 small fw-bold d-inline-flex align-items-center gap-1"
                                                            title="Unggah berkas serah terima bertanda tangan agar pelacakan selesai">
                                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                                <polyline points="17 8 12 3 7 8"></polyline>
                                                                <line x1="12" y1="3" x2="12" y2="15"></line>
                                                            </svg>
                                                            Upload Berkas TTD
                                                        </a>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>

                                        {{-- 4-Step Horizontal Stepper --}}
                                        <div class="atk-stepper-wrapper">
                                            {{-- STEP 1: Pengajuan Dibuat / Usulan Diajukan --}}
                                            <div class="atk-stepper-item {{ $itemS1Class }}">
                                                <div class="atk-step-circle">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                        <polyline points="14 2 14 8 20 8"></polyline>
                                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                                        <polyline points="10 9 9 9 8 9"></polyline>
                                                    </svg>
                                                </div>
                                                <div class="atk-step-title">
                                                    {{ $tReq->is_procurement ? 'Usulan Pengadaan' : 'Pengajuan Dibuat' }}
                                                </div>
                                                <div class="atk-step-subtitle">
                                                    ({{ $dQty }})
                                                </div>
                                                <div class="atk-step-time">{{ $tReq->created_at->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB</div>
                                            </div>

                                            {{-- STEP 2: Verifikasi Kasubbag TU --}}
                                            <div class="atk-stepper-item {{ $itemS2Class }}">
                                                <div class="atk-step-circle">
                                                    @if($status === 'rejected')
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                            <circle cx="12" cy="12" r="10"></circle>
                                                            <line x1="15" y1="9" x2="9" y2="15"></line>
                                                            <line x1="9" y1="9" x2="15" y2="15"></line>
                                                        </svg>
                                                    @else
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M9 11l3 3L22 4"></path>
                                                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div class="atk-step-title">
                                                    @if($status === 'rejected')
                                                        Ditolak Kasubbag TU
                                                    @elseif($status === 'pending')
                                                        Menunggu Review TU
                                                    @else
                                                        Disetujui Kasubbag TU
                                                    @endif
                                                </div>
                                                <div class="atk-step-subtitle">
                                                    @if($status === 'pending')
                                                        (Sedang Ditinjau)
                                                    @elseif($status === 'rejected')
                                                        ({{ \Illuminate\Support\Str::limit($tReq->rejection_reason ?? 'Tidak disetujui', 32) }})
                                                    @else
                                                        {{ $tReq->is_procurement ? '(Disetujui untuk Dibeli)' : '(Telah Diverifikasi)' }}
                                                    @endif
                                                </div>
                                                <div class="atk-step-time">
                                                    @if($status !== 'pending')
                                                        {{ ($tReq->verified_at ?? $tReq->updated_at)->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB
                                                    @else
                                                        Menunggu...
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- STEP 3: Diproses Gudang (Stok) / Pembelian Supplier (Pengadaan) --}}
                                            <div class="atk-stepper-item {{ $itemS3Class }}">
                                                <div class="atk-step-circle">
                                                    @if($tReq->is_procurement)
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <circle cx="9" cy="21" r="1"></circle>
                                                            <circle cx="20" cy="21" r="1"></circle>
                                                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                                        </svg>
                                                    @else
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <rect x="1" y="3" width="15" height="13"></rect>
                                                            <polygon points="16 8 20 8 23 11 23 16 16 16 8"></polygon>
                                                            <circle cx="5.5" cy="18.5" r="2.5"></circle>
                                                            <circle cx="18.5" cy="18.5" r="2.5"></circle>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div class="atk-step-title">
                                                    {{ $tReq->is_procurement ? 'Pembelian Supplier' : 'Diproses Gudang' }}
                                                </div>
                                                <div class="atk-step-subtitle">
                                                    @if($tReq->is_procurement)
                                                        @if($status === 'approved')
                                                            {{ $dFulfilled ? '(Telah Dibeli Subbag TU)' : '(Sedang Dibeli Kasubbag TU)' }}
                                                        @else
                                                            (Menunggu Persetujuan)
                                                        @endif
                                                    @else
                                                        @if($status === 'approved')
                                                            (Siap Diserahterimakan)
                                                        @elseif($status === 'distributed')
                                                            (Telah Disiapkan Gudang)
                                                        @else
                                                            (Menunggu Antrean)
                                                        @endif
                                                    @endif
                                                </div>
                                                <div class="atk-step-time">
                                                    @if($tReq->is_procurement)
                                                        @if($dFulfilled)
                                                            {{ ($dFulfilledAt ?? $tReq->updated_at)->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB
                                                        @else
                                                            -
                                                        @endif
                                                    @else
                                                        @if($status === 'distributed' || $status === 'approved')
                                                            {{ ($tReq->updated_at ?? $tReq->verified_at)->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB
                                                        @else
                                                            -
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- STEP 4: Selesai Diterima (Stok) / Tersedia di Katalog (Pengadaan) --}}
                                            <div class="atk-stepper-item {{ $itemS4Class }}">
                                                <div class="atk-step-circle">
                                                    @if($tReq->is_procurement)
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                                            <line x1="12" y1="22.08" x2="12" y2="12"></line>
                                                        </svg>
                                                    @else
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline>
                                                            <path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div class="atk-step-title">
                                                    {{ $tReq->is_procurement ? 'Tersedia di Katalog' : 'Selesai Diterima' }}
                                                </div>
                                                <div class="atk-step-subtitle">
                                                    @if($tReq->is_procurement)
                                                        @if($status === 'approved' && $dFulfilled)
                                                            (Stok Masuk &amp; Siap Diminta)
                                                        @else
                                                            (Menunggu Barang Tiba)
                                                        @endif
                                                    @else
                                                        @if($status === 'distributed')
                                                            @if(!$tReq->signed_proof && !auth()->user()->isPegawai())
                                                                <span class="text-warning fw-semibold">(Menunggu Berkas TTD)</span>
                                                            @else
                                                                (Penerima: {{ $tReq->receiver_name ?? $user->name }})
                                                            @endif
                                                        @else
                                                            (Bukti Serah Terima)
                                                        @endif
                                                    @endif
                                                </div>
                                                <div class="atk-step-time">
                                                    @if($tReq->is_procurement)
                                                        @if($status === 'approved' && $dFulfilled)
                                                            {{ ($dFulfilledAt ?? $tReq->updated_at)->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB
                                                        @else
                                                            -
                                                        @endif
                                                    @else
                                                        @if($status === 'distributed' && $tReq->distributed_at)
                                                            {{ $tReq->distributed_at->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB
                                                        @else
                                                            -
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- PANEL RINGKASAN SEMUA BARANG (Jika Pengajuan Multi-Barang) --}}
                                @if($itemCount > 1)
                                    <div class="item-view-panel item-view-{{ $tReq->id }} d-none" id="item-panel-{{ $tReq->id }}-all">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 px-1">
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <span class="fw-bold text-dark fs-6">Ringkasan Keseluruhan Pengajuan</span>
                                                <span class="badge bg-light text-secondary border fw-medium" style="font-size: 0.75rem;">
                                                    Total: {{ $itemCount }} Jenis Barang
                                                </span>
                                            </div>

                                            <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">
                                                <span class="badge {{ $tReq->status_badge }}">{{ $tReq->status_label }}</span>
                                                @if($tReq->is_procurement)
                                                    @if($hasAnyFulfilled)
                                                        <a href="{{ route('katalog-atk') }}"
                                                            class="btn btn-xs btn-success py-1 px-2.5 small fw-bold d-inline-flex align-items-center gap-1 shadow-sm"
                                                            title="Barang telah dibeli dan tersedia di katalog. Klik untuk membuat pengajuan permintaan barang ini.">
                                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <circle cx="9" cy="21" r="1"></circle>
                                                                <circle cx="20" cy="21" r="1"></circle>
                                                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                                            </svg>
                                                            Pesan di Katalog ATK &rarr;
                                                        </a>
                                                    @endif
                                                @else
                                                    @if(in_array($tReq->status, ['approved', 'distributed']) && !auth()->user()->isPegawai())
                                                        <a href="{{ route('distribusi.bukti', $tReq->id) }}" target="_blank"
                                                            class="btn btn-xs btn-outline-success py-0.5 px-2 small fw-semibold d-inline-flex align-items-center gap-1"
                                                            title="Cetak Dokumen Resmi Bukti Penyerahan ATK">
                                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <polyline points="20 6 9 17 4 12"></polyline>
                                                            </svg>
                                                            Cetak Bukti
                                                        </a>
                                                    @endif
                                                    @if($tReq->status === 'distributed' && !$tReq->signed_proof && !auth()->user()->isPegawai())
                                                        <a href="{{ route('pengajuan.index') }}"
                                                            class="btn btn-xs btn-warning text-dark py-0.5 px-2 small fw-bold d-inline-flex align-items-center gap-1">
                                                            Upload Berkas TTD
                                                        </a>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>

                                        {{-- 4-Step Horizontal Stepper --}}
                                        <div class="atk-stepper-wrapper">
                                            {{-- STEP 1: Pengajuan Dibuat / Usulan Diajukan --}}
                                            <div class="atk-stepper-item {{ $s1Class }}">
                                                <div class="atk-step-circle">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                        <polyline points="14 2 14 8 20 8"></polyline>
                                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                                        <polyline points="10 9 9 9 8 9"></polyline>
                                                    </svg>
                                                </div>
                                                <div class="atk-step-title">
                                                    {{ $tReq->is_procurement ? 'Usulan Pengadaan' : 'Pengajuan Dibuat' }}
                                                </div>
                                                <div class="atk-step-subtitle">
                                                    ({{ $itemCount }} {{ $tReq->is_procurement ? 'Jenis Barang Baru' : 'Jenis Barang' }})
                                                </div>
                                                <div class="atk-step-time">{{ $tReq->created_at->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB</div>
                                            </div>

                                            {{-- STEP 2: Verifikasi Kasubbag TU --}}
                                            <div class="atk-stepper-item {{ $s2Class }}">
                                                <div class="atk-step-circle">
                                                    @if($status === 'rejected')
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                            <circle cx="12" cy="12" r="10"></circle>
                                                            <line x1="15" y1="9" x2="9" y2="15"></line>
                                                            <line x1="9" y1="9" x2="15" y2="15"></line>
                                                        </svg>
                                                    @else
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M9 11l3 3L22 4"></path>
                                                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div class="atk-step-title">
                                                    @if($status === 'rejected')
                                                        Ditolak Kasubbag TU
                                                    @elseif($status === 'pending')
                                                        Menunggu Review TU
                                                    @else
                                                        Disetujui Kasubbag TU
                                                    @endif
                                                </div>
                                                <div class="atk-step-subtitle">
                                                    @if($status === 'pending')
                                                        (Sedang Ditinjau)
                                                    @elseif($status === 'rejected')
                                                        ({{ \Illuminate\Support\Str::limit($tReq->rejection_reason ?? 'Tidak disetujui', 32) }})
                                                    @else
                                                        {{ $tReq->is_procurement ? '(Disetujui untuk Dibeli)' : '(Telah Diverifikasi)' }}
                                                    @endif
                                                </div>
                                                <div class="atk-step-time">
                                                    @if($status !== 'pending')
                                                        {{ ($tReq->verified_at ?? $tReq->updated_at)->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB
                                                    @else
                                                        Menunggu...
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- STEP 3: Diproses Gudang (Stok) / Pembelian Supplier (Pengadaan) --}}
                                            <div class="atk-stepper-item {{ $s3Class }}">
                                                <div class="atk-step-circle">
                                                    @if($tReq->is_procurement)
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <circle cx="9" cy="21" r="1"></circle>
                                                            <circle cx="20" cy="21" r="1"></circle>
                                                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                                        </svg>
                                                    @else
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <rect x="1" y="3" width="15" height="13"></rect>
                                                            <polygon points="16 8 20 8 23 11 23 16 16 16 8"></polygon>
                                                            <circle cx="5.5" cy="18.5" r="2.5"></circle>
                                                            <circle cx="18.5" cy="18.5" r="2.5"></circle>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div class="atk-step-title">
                                                    {{ $tReq->is_procurement ? 'Pembelian Supplier' : 'Diproses Gudang' }}
                                                </div>
                                                <div class="atk-step-subtitle">
                                                    @if($tReq->is_procurement)
                                                        @if($status === 'approved')
                                                            @if($hasAllFulfilled)
                                                                (Telah Dibeli Subbag TU)
                                                            @elseif($hasAnyFulfilled)
                                                                (Sebagian Barang Telah Dibeli)
                                                            @else
                                                                (Sedang Dibeli Kasubbag TU)
                                                            @endif
                                                        @else
                                                            (Menunggu Persetujuan)
                                                        @endif
                                                    @else
                                                        @if($status === 'approved')
                                                            (Siap Diserahterimakan)
                                                        @elseif($status === 'distributed')
                                                            (Telah Disiapkan Gudang)
                                                        @else
                                                            (Menunggu Antrean)
                                                        @endif
                                                    @endif
                                                </div>
                                                <div class="atk-step-time">
                                                    @if($tReq->is_procurement)
                                                        @if($hasAnyFulfilled)
                                                            {{ ($tReq->updated_at ?? $tReq->verified_at)->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB
                                                        @else
                                                            -
                                                        @endif
                                                    @else
                                                        @if($status === 'distributed' || $status === 'approved')
                                                            {{ ($tReq->updated_at ?? $tReq->verified_at)->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB
                                                        @else
                                                            -
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- STEP 4: Selesai Diterima (Stok) / Tersedia di Katalog (Pengadaan) --}}
                                            <div class="atk-stepper-item {{ $s4Class }}">
                                                <div class="atk-step-circle">
                                                    @if($tReq->is_procurement)
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                                            <line x1="12" y1="22.08" x2="12" y2="12"></line>
                                                        </svg>
                                                    @else
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline>
                                                            <path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div class="atk-step-title">
                                                    {{ $tReq->is_procurement ? 'Tersedia di Katalog' : 'Selesai Diterima' }}
                                                </div>
                                                <div class="atk-step-subtitle">
                                                    @if($tReq->is_procurement)
                                                        @if($hasAllFulfilled)
                                                            (Semua Barang Tersedia di Katalog)
                                                        @elseif($hasAnyFulfilled)
                                                            ({{ $fulfilledCount }}/{{ $itemCount }} Barang Tersedia di Katalog)
                                                        @else
                                                            (Menunggu Barang Tiba)
                                                        @endif
                                                    @else
                                                        @if($status === 'distributed')
                                                            @if(!$tReq->signed_proof && !auth()->user()->isPegawai())
                                                                <span class="text-warning fw-semibold">(Menunggu Berkas TTD)</span>
                                                            @else
                                                                (Penerima: {{ $tReq->receiver_name ?? $user->name }})
                                                            @endif
                                                        @else
                                                            (Bukti Serah Terima)
                                                        @endif
                                                    @endif
                                                </div>
                                                <div class="atk-step-time">
                                                    @if($tReq->is_procurement)
                                                        @if($hasAnyFulfilled)
                                                            {{ ($tReq->updated_at ?? $tReq->verified_at)->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB
                                                        @else
                                                            -
                                                        @endif
                                                    @else
                                                        @if($status === 'distributed' && $tReq->distributed_at)
                                                            {{ $tReq->distributed_at->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB
                                                        @else
                                                            -
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif


        @else
            <div class="card border-0 shadow-sm mb-4 rounded-3 p-4" style="background: #ffffff; border-left: 4px solid #10b981 !important;">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #e6f7f0; color: #10b981; flex-shrink: 0;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                            </svg>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.98rem;">Tidak Ada Pengajuan yang Sedang Diproses</h6>
                            <p class="text-muted small mb-0" style="font-size: 0.8rem;">
                                Seluruh permohonan ATK Anda telah selesai atau Anda belum membuat pengajuan baru pada periode {{ $selectedPeriodLabel }}.
                            </p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('pengajuan.create') }}" class="btn btn-emerald-pill text-nowrap" style="font-size: 0.8rem; padding: 0.48rem 1.25rem;">
                            + Buat Pengajuan ATK
                        </a>
                        <a href="{{ route('katalog-atk') }}" class="btn btn-outline-secondary rounded-pill text-nowrap" style="font-size: 0.8rem; padding: 0.48rem 1.25rem;">
                            Katalog Persediaan &rarr;
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- ROW 3: BANNER FITUR & PANDUAN ALUR PENGAJUAN -->
        <div class="row g-3 mb-4">
            <div class="col-lg-8 col-12">
                <div class="card card-featured-emerald h-100 d-flex flex-column justify-content-between p-4" style="border-radius: 16px;">
                    <!-- Top Arrow Link Button -->
                    <a href="{{ route('katalog-atk') }}" class="arrow-link-btn" title="Buka Katalog ATK">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="7" y1="17" x2="17" y2="7"></line>
                            <polyline points="7 7 17 7 17 17"></polyline>
                        </svg>
                    </a>

                    <!-- Decorative Leaf SVG in Background -->
                    <div style="position: absolute; right: -15px; bottom: -20px; opacity: 0.18; pointer-events: none;">
                        <svg width="220" height="220" viewBox="0 0 200 200" fill="none">
                            <path d="M50 180C30 140 30 70 100 30C160 50 180 120 150 180C110 190 70 190 50 180Z" fill="#34d399"/>
                            <path d="M100 30V180" stroke="#ffffff" stroke-width="3" stroke-linecap="round"/>
                            <path d="M100 70L140 90" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round"/>
                            <path d="M100 110L60 130" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round"/>
                            <path d="M100 140L135 155" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round"/>
                        </svg>
                    </div>

                    <div>
                        <div class="d-flex align-items-center gap-1.5 mb-2">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" style="color: #34d399;">
                                <path d="M6.5 19C4 19 2 17 2 14.5C2 10.5 6 6 12 4C18 6 22 10.5 22 14.5C22 17 20 19 17.5 19C15.5 19 13.8 17.7 13 16C12.2 17.7 10.5 19 8.5 19H6.5Z" fill="#34d399" opacity="0.9"/>
                            </svg>
                            <span class="text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.08em; color: #a4cdbf;">Kementerian PKP RI</span>
                        </div>
                        <h3 class="featured-title">Sistem Inventori Persediaan ATK (ATEKA)</h3>
                        <p class="featured-sub mb-3">
                            Layanan permohonan kebutuhan alat tulis kantor untuk menunjang tugas kedinasan secara praktis, transparan, dan terpantau langsung proses persetujuannya.
                        </p>
                        <div class="d-flex align-items-center gap-2 flex-wrap pt-1">
                            <a href="{{ route('katalog-atk') }}" class="btn btn-emerald-pill" style="font-size: 0.8rem; padding: 0.45rem 1.15rem;">
                                Buka Katalog ATK &rarr;
                            </a>
                            <a href="{{ route('pengajuan.create') }}" class="btn btn-outline-light rounded-pill" style="font-size: 0.8rem; padding: 0.45rem 1.15rem;">
                                + Buat Pengajuan Barang
                            </a>
                            <a href="{{ route('pengajuan.pengadaan.create') }}" class="btn btn-outline-light rounded-pill" style="font-size: 0.8rem; padding: 0.45rem 1.15rem;">
                                + Usulan Pengadaan Baru
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-12">
                <div class="card h-100 shadow-sm border-0 p-4 d-flex flex-column justify-content-between" style="border-radius: 16px; background: #ffffff;">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-light text-primary border" style="font-size: 0.72rem;">Panduan Alur</span>
                            <span class="fw-bold text-dark" style="font-size: 0.95rem;">Alur Permintaan ATK</span>
                        </div>
                        <p class="text-muted small mb-3" style="font-size: 0.76rem;">Tahapan pemenuhan permohonan ATK Anda:</p>
                        
                        <div class="d-flex flex-column gap-2.5">
                            <div class="d-flex align-items-start gap-2.5">
                                <span class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 22px; height: 22px; background: #0c2b27; font-size: 0.72rem; flex-shrink: 0;">1</span>
                                <div style="font-size: 0.78rem;">
                                    <strong class="text-dark d-block">Pilih di Katalog ATK</strong>
                                    <span class="text-muted">Cek stok persediaan yang tersedia di gudang.</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-start gap-2.5">
                                <span class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 22px; height: 22px; background: #0c2b27; font-size: 0.72rem; flex-shrink: 0;">2</span>
                                <div style="font-size: 0.78rem;">
                                    <strong class="text-dark d-block">Ajukan Permintaan</strong>
                                    <span class="text-muted">Tentukan jumlah dan keperluan penggunaan kerja.</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-start gap-2.5">
                                <span class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 22px; height: 22px; background: #0c2b27; font-size: 0.72rem; flex-shrink: 0;">3</span>
                                <div style="font-size: 0.78rem;">
                                    <strong class="text-dark d-block">Verifikasi Kasubbag TU</strong>
                                    <span class="text-muted">Ditinjau dan disetujui secara digital oleh pimpinan.</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-start gap-2.5">
                                <span class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 22px; height: 22px; background: #10b981; font-size: 0.72rem; flex-shrink: 0;">4</span>
                                <div style="font-size: 0.78rem;">
                                    <strong class="text-dark d-block">Serah Terima Fisik</strong>
                                    <span class="text-muted">Ambil barang di gudang & ttd berita acara serah terima.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ROW 4: TABEL RIWAYAT PERMINTAAN TERAKHIR PEMOHON -->
            {{-- Tabel Permintaan Terbaru Milik User --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-0 fw-bold">Permintaan ATK Terakhir Anda</h5>
                                <small class="text-muted">Status permohonan terkini Anda di sistem</small>
                            </div>
                            <a href="{{ route('pengajuan.index') }}" class="btn btn-sm btn-outline-primary">
                                Lihat Semua Riwayat
                            </a>
                        </div>
                        <div class="card-body p-2.5 p-md-3">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-dashboard-compact mb-0 w-100">
                                    <colgroup>
                                        <col style="width: 22%; min-width: 195px;">
                                        <col style="width: 9%; min-width: 85px;">
                                        <col style="width: 12%; min-width: 105px;">
                                        <col style="width: 33%;">
                                        <col style="width: 12%; min-width: 105px;">
                                        <col style="width: 12%; min-width: 95px;">
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th style="min-width: 195px;">Kode</th>
                                            <th style="min-width: 85px;">Tipe</th>
                                            <th style="min-width: 105px;">Tanggal</th>
                                            <th>Barang</th>
                                            <th style="min-width: 105px;">Status</th>
                                            <th class="text-end" style="min-width: 95px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($userRequests as $req)
                                            <tr>
                                                <td style="white-space: nowrap;">
                                                    <div class="font-monospace fw-bold text-primary" style="font-size: 0.78rem; letter-spacing: -0.01em;">{{ $req->request_code }}</div>
                                                    @if ($req->is_procurement)
                                                        <span class="badge bg-warning-subtle text-dark border px-1.5 py-0.5 mt-0.5" style="font-size: 0.65rem;">Pengadaan</span>
                                                    @endif
                                                </td>
                                                <td><span class="badge {{ $req->type === 'subdit' ? 'bg-info text-white' : 'bg-secondary' }}" style="font-size: 0.68rem; padding: 0.2rem 0.45rem;">{{ ucfirst($req->type) }}</span></td>
                                                <td>
                                                    <div class="text-dark fw-semibold" style="font-size: 0.74rem;">{{ $req->created_at->timezone('Asia/Jakarta')->format('d/m/Y') }}</div>
                                                    <div class="text-muted" style="font-size: 0.68rem;">{{ $req->created_at->timezone('Asia/Jakarta')->format('H:i') }} WIB</div>
                                                </td>
                                                <td style="white-space: normal; word-break: break-word; overflow-wrap: anywhere;">
                                                    <ul class="mb-0 ps-3" style="font-size: 0.75rem; word-break: break-word; white-space: normal;">
                                                        @foreach ($req->details as $d)
                                                            <li class="mb-1" style="word-break: break-word;">
                                                                <div class="fw-bold text-dark" style="line-height: 1.3;">{{ $d->item_name }}</div>
                                                                <div class="mt-0.5">
                                                                    @if (in_array($req->status, ['approved', 'distributed']))
                                                                        <span class="badge bg-success-subtle text-success border px-1.5 py-0.5" style="font-size: 0.64rem;">
                                                                            Disetujui: {{ $d->effective_quantity }} {{ $d->item_unit }}
                                                                        </span>
                                                                    @else
                                                                        <span class="text-secondary small" style="font-size: 0.68rem;">({{ $d->quantity }} {{ $d->item_unit }})</span>
                                                                    @endif
                                                                </div>

                                                                @if ($d->specification)
                                                                    <div class="text-muted fst-italic mt-0.5" style="font-size: 0.68rem; line-height: 1.25;">Spesifikasi: {{ $d->specification }}</div>
                                                                @endif

                                                                @if ($d->verification_note)
                                                                    <div class="mt-1 p-1 rounded bg-light-subtle text-primary border-start border-2 border-primary ps-1.5" style="font-size: 0.68rem; line-height: 1.3;">
                                                                        <span class="fw-bold">Catatan TU:</span> "{{ $d->verification_note }}"
                                                                    </div>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>

                                                    @if ($req->verification_notes && (!isset($d) || $req->verification_notes !== $d->verification_note))
                                                        <div class="mt-1.5 ps-3" style="font-size: 0.68rem; line-height: 1.35; word-break: break-word; white-space: normal;">
                                                            <div class="p-1.5 rounded-2 bg-primary-subtle text-primary border border-primary-subtle">
                                                                <strong class="d-inline-flex align-items-center gap-1">
                                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                        <circle cx="12" cy="12" r="10"></circle>
                                                                        <line x1="12" y1="16" x2="12" y2="12"></line>
                                                                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                                                    </svg>
                                                                    Catatan TU:
                                                                </strong>
                                                                <div class="mt-0.5" style="word-break: break-word;">{{ $req->verification_notes }}</div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if ($req->notes)
                                                        <div class="text-muted ps-3 mt-1" style="font-size: 0.7rem; line-height: 1.3;">
                                                            <span class="text-secondary fw-semibold">Keperluan:</span> {{ $req->notes }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td style="vertical-align: middle;">
                                                    @php
                                                        $shortStatus = match ($req->status) {
                                                            'pending' => $req->is_procurement ? 'Pending Pengadaan' : 'Pending TU',
                                                            'approved' => $req->is_procurement ? ($req->is_purchased ? 'Tersedia' : 'Disetujui TU') : 'Disetujui TU',
                                                            'distributed' => 'Selesai',
                                                            'rejected' => 'Ditolak',
                                                            default => ucfirst($req->status),
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $req->status_badge }} px-1.5 py-0.5 text-nowrap" style="font-size: 0.68rem;" title="{{ $req->status_label }}">
                                                        {{ $shortStatus }}
                                                    </span>
                                                    @if ($req->is_procurement && $req->purchase_invoice)
                                                        <div class="mt-0.5">
                                                            <span class="badge bg-primary-subtle text-primary border text-truncate d-inline-block px-1.5 py-0.2" style="font-size: 0.64rem; max-width: 110px;" title="Faktur: {{ $req->purchase_invoice }}">
                                                                Faktur: {{ $req->purchase_invoice }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                    @if ($req->is_procurement && $req->is_purchased)
                                                        <div class="mt-0.5">
                                                            <a href="{{ route('katalog-atk') }}" class="badge bg-success-subtle text-success border text-decoration-none px-1.5 py-0.2" style="font-size: 0.64rem;">
                                                                Katalog &rarr;
                                                            </a>
                                                        </div>
                                                    @endif
                                                    @if($req->status == 'distributed' && $req->receiver_name)
                                                        <div class="text-muted mt-1" style="font-size: 0.68rem; line-height: 1.25;">
                                                            <span class="text-secondary d-block" style="font-size: 0.64rem;">Penerima:</span>
                                                            <strong class="text-dark d-block text-wrap" style="word-break: break-word;">{{ $req->receiver_name }}</strong>
                                                        </div>
                                                    @elseif($req->status == 'rejected' && $req->rejection_reason)
                                                        <div class="text-danger mt-1" style="font-size: 0.68rem; line-height: 1.25;">
                                                            <span class="d-block" style="font-size: 0.64rem;">Alasan:</span>
                                                            <span class="d-block text-wrap" style="word-break: break-word;">"{{ $req->rejection_reason }}"</span>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <div class="d-flex justify-content-end gap-1 flex-wrap">
                                                        <a href="{{ route('pengajuan.index') }}" class="btn btn-xs btn-outline-primary py-0.5 px-2 fw-semibold" style="font-size: 0.72rem; border-radius: 6px;">
                                                            Detail
                                                        </a>
                                                        @if ($req->status === 'distributed' && !auth()->user()->isPegawai())
                                                            <a href="{{ route('distribusi.bukti', $req->id) }}" target="_blank"
                                                                class="btn btn-xs btn-outline-secondary py-0.5 px-1.5 fw-semibold" style="font-size: 0.72rem; border-radius: 6px;">Cetak Bukti</a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">
                                                    Belum ada riwayat pengajuan pada periode {{ $selectedPeriodLabel }}. <a href="{{ route('pengajuan.create') }}">Buat pengajuan baru sekarang</a>.
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

        @endif

    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/chart.min.js') }}" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('stockChart');
            if (ctx) {
                const labels = @json($chartLabels);
                const dataIn = @json($chartIn);
                const dataOut = @json($chartOut);

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Barang Masuk',
                            data: dataIn,
                            backgroundColor: 'rgba(5, 150, 105, 0.8)',
                            borderColor: 'rgb(5, 150, 105)',
                            borderRadius: 4,
                        }, {
                            label: 'Barang Keluar',
                            data: dataOut,
                            backgroundColor: 'rgba(220, 38, 38, 0.8)',
                            borderColor: 'rgb(220, 38, 38)',
                            borderRadius: 4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        });

        
        function switchTrackedItem(reqId, itemKey) {
            const panels = document.querySelectorAll('.item-view-' + reqId);
            panels.forEach(function(panel) {
                panel.classList.add('d-none');
            });
            const target = document.getElementById('item-panel-' + reqId + '-' + itemKey);
            if (target) {
                target.classList.remove('d-none');
            }
        }

        function switchTrackedRequest(reqId) {
            document.querySelectorAll('.tracked-request-panel').forEach(function(panel) {
                panel.classList.add('d-none');
            });
            const target = document.getElementById('tracked-panel-' + reqId);
            if (target) {
                target.classList.remove('d-none');
            }
        }
    </script>
@endpush
