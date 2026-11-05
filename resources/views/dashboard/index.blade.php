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

        'pic_distribusi' => auth()->user()->pic_role_title ?? 'PIC ( SDK )',

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

                    @if ($isDistributorGudang ?? (auth()->user() && auth()->user()->isDistributor()))

                        <a href="{{ route('distribusi.index') }}" class="btn-dark-pill" style="font-size: 0.82rem; padding: 0.52rem 1.35rem;">

                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>

                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>

                            </svg>

                            Distribusi ATK ({{ $readyDistribusiCount }})

                        </a>

                    @else

                        <a href="{{ route('master-data.daftar-barang.index') }}#pengadaan-section" class="btn-dark-pill" style="font-size: 0.82rem; padding: 0.52rem 1.35rem;">

                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">

                                <polyline points="9 11 12 14 22 4"></polyline>

                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>

                            </svg>

                            Pemeriksaan Pengadaan ({{ $pendingPengadaanGudangCount }})

                        </a>

                        <a href="{{ route('barang-masuk') }}" class="btn-emerald-pill" style="font-size: 0.82rem; padding: 0.52rem 1.25rem;">

                            + Barang Masuk

                        </a>

                    @endif

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

        <!-- ======================================================== -->

        <!-- VIEW PIMPINAN (Kasubbag TU) & OPERASIONAL GUDANG         -->

        <!-- ======================================================== -->

        @if ($isKasubagTU)

        {{-- ======================================================== --}}

        {{-- VIEW KHUSUS KASUBBAG TU (Pimpinan / Approver BMN)       --}}

        {{-- ======================================================== --}}

        @php

            $activeStockCount = $availableProductsCount;

            $rawPercent = $totalProducts > 0 ? ($activeStockCount / $totalProducts) * 100 : 0;

            $displayReadiness = $rawPercent > 0 && $rawPercent < 1 ? number_format($rawPercent, 1) : round($rawPercent);

        @endphp



        <!-- ROW 1 KASUBBAG TU: 3 KARTU UTAMA (Antrean, Ketersediaan Stok, Valuasi BMN) -->

        <div class="row g-3 mb-4">

            <!-- Card 1: Antrean Verifikasi TU -->

            <div class="col-lg-4 col-md-6 col-12">

                <div class="card h-100 shadow-sm border-0 aeux-stat-card d-flex flex-column justify-content-between p-4" style="border-radius: 16px;">

                    <div>

                        <div class="d-flex align-items-center justify-content-between mb-2">

                            <span class="aeux-stat-label mb-0" style="font-size: 0.82rem; font-weight: 700;">Antrean Verifikasi TU</span>

                            <span class="badge {{ $pendingRequestsCount > 0 ? 'bg-warning text-dark' : 'bg-light text-muted border' }} rounded-pill px-2.5 py-1" style="font-size: 0.68rem; font-weight: 700;">

                                {{ $pendingRequestsCount > 0 ? $pendingRequestsCount . ' Perlu Tindakan' : 'Selesai' }}

                            </span>

                        </div>

                        <div class="aeux-stat-value mb-1" style="font-size: 2.8rem; color: #0c2b27; line-height: 1;">

                            {{ $pendingRequestsCount }} <span style="font-size: 1.05rem; font-weight: 600; color: #71877e;">Permohonan</span>

                        </div>

                        <small class="text-muted d-block mt-2" style="font-size: 0.74rem; line-height: 1.35;">

                            Permintaan ATK &amp; usulan pengadaan menunggu verifikasi dan persetujuan digital Anda.

                        </small>

                    </div>


                </div>

            </div>



            <!-- Card 2: Ketersediaan Stok Katalog BMN -->

            <div class="col-lg-4 col-md-6 col-12">

                <div class="card h-100 shadow-sm border-0 aeux-stat-card d-flex flex-column justify-content-between p-4" style="border-radius: 16px;">

                    <div>

                        <div class="d-flex align-items-center justify-content-between mb-2">

                            <span class="aeux-stat-label mb-0" style="font-size: 0.82rem; font-weight: 700;">Ketersediaan Stok</span>

                            <span class="badge bg-light text-muted border rounded-pill px-2.5 py-1" style="font-size: 0.68rem; font-weight: 700;">

                                {{ number_format($totalProducts) }} Jenis Katalog

                            </span>

                        </div>

                        <div class="aeux-stat-value mb-1" style="font-size: 2.8rem; color: #0c2b27; line-height: 1;">

                            {{ $displayReadiness }}%

                        </div>

                        <ul class="list-unstyled mb-0 mt-2" style="font-size: 0.8rem;">

                            <li class="d-flex justify-content-between align-items-center mb-1.5">

                                <a href="{{ route('katalog-atk', ['stock_status' => 'available']) }}" class="d-flex align-items-center gap-2 text-decoration-none text-muted" title="Klik untuk filter barang tersedia">

                                    <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #10b981; display: inline-block;"></span>

                                    <span>Stok Siap Salur (Tersedia)</span>

                                </a>

                                <a href="{{ route('katalog-atk', ['stock_status' => 'available']) }}" class="text-decoration-none">

                                    <strong class="text-success">{{ $activeStockCount }} Jenis &rarr;</strong>

                                </a>

                            </li>

                            <li class="d-flex justify-content-between align-items-center mb-1.5">

                                <a href="{{ route('katalog-atk', ['stock_status' => 'empty']) }}" class="d-flex align-items-center gap-2 text-decoration-none text-muted" title="Klik untuk filter barang stok kosong">

                                    <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #f87171; display: inline-block;"></span>

                                    <span>Stok Habis / Restock</span>

                                </a>

                                <a href="{{ route('katalog-atk', ['stock_status' => 'empty']) }}" class="text-decoration-none">

                                    <strong class="text-danger">{{ $outOfStockCount }} Jenis</strong>

                                </a>

                            </li>

                            <li class="d-flex justify-content-between align-items-center">

                                <span class="d-flex align-items-center gap-2 text-muted">

                                    <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #38bdf8; display: inline-block;"></span>

                                    Pengadaan Periode Ini

                                </span>

                                <strong class="text-primary">+{{ number_format($totalStockIn) }} Unit Masuk</strong>

                            </li>

                        </ul>

                    </div>


                </div>

            </div>



            <!-- Card 3: Valuasi Persediaan BMN -->

            <div class="col-lg-4 col-md-12 col-12">

                <div class="card h-100 shadow-sm border-0 aeux-stat-card d-flex flex-column justify-content-between p-4" style="border-radius: 16px;">

                    <div>

                        <div class="d-flex align-items-center justify-content-between mb-2">

                            <span class="aeux-stat-label mb-0" style="font-size: 0.82rem; font-weight: 700;">Valuasi Persediaan BMN</span>

                            <span class="badge bg-light text-muted border rounded-pill px-2.5 py-1" style="font-size: 0.68rem; font-weight: 700;">

                                Saldo {{ $selectedYear ?? date('Y') }}

                            </span>

                        </div>

                        <div class="aeux-stat-value mb-1" style="font-size: 2.1rem; color: #0c2b27; line-height: 1.1;">

                            Rp {{ number_format($totalInventoryValue ?? 0, 0, ',', '.') }}

                        </div>

                        <small class="text-muted d-block mt-1" style="font-size: 0.74rem;">

                            Nilai aset buku fisik persediaan ATK Kementerian PKP RI

                        </small>

                        <div class="d-flex justify-content-between align-items-end mt-3 pt-1">

                            <span class="aeux-stat-delta delta-coral">

                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>

                                Saldo Buku Tahun {{ $selectedYear ?? date('Y') }}

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

        </div>



        <!-- ROW 2 KASUBBAG TU: FEATURED BANNER ATEKA (Full Width) -->

        <div class="row g-3 mb-4">

            <div class="col-12">

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



        @elseif ($isPegawaiGudang)

        {{-- ======================================================== --}}

        {{-- VIEW KHUSUS PEGAWAI GUDANG (Operasional Fisik & Pemeriksaan) --}}

        {{-- ======================================================== --}}

        @php

            $activeStockCount = $availableProductsCount;

            $rawPercent = $totalProducts > 0 ? ($activeStockCount / $totalProducts) * 100 : 0;

            $displayReadiness = $rawPercent > 0 && $rawPercent < 1 ? number_format($rawPercent, 1) : round($rawPercent);

            $isDistributor = $isDistributorGudang ?? (auth()->user() && auth()->user()->isDistributor());

        @endphp



        <!-- ROW 1 GUDANG: STAT CARDS DISESUAIKAN BERDASARKAN PERAN (DISTRIBUSI VS PEMERIKSA BARANG) -->

        @if ($isDistributor)

            {{-- Tampilan Pegawai Gudang Distributor (Fokus: Penyerahan & Penyaluran ATK) --}}

            <div class="row g-3 mb-4">

                <!-- Card 1: Siap Diserahkan (Antrean Distribusi) -->

                <div class="col-lg-3 col-md-6 col-12">

                    <div class="card h-100 shadow-sm border-0 aeux-stat-card d-flex flex-column justify-content-between p-3.5" style="border-radius: 16px;">

                        <div>

                            <div class="d-flex align-items-center justify-content-between mb-2">

                                <span class="aeux-stat-label mb-0" style="font-size: 0.8rem; font-weight: 700;">Siap Diserahkan</span>

                                <span class="badge {{ $readyDistribusiCount > 0 ? 'bg-warning text-dark' : 'bg-light text-muted border' }} rounded-pill px-2.5 py-1" style="font-size: 0.68rem; font-weight: 700;">

                                    {{ $readyDistribusiCount > 0 ? $readyDistribusiCount . ' Perlu Penyerahan' : 'Selesai' }}

                                </span>

                            </div>

                            <div class="aeux-stat-value mb-1" style="font-size: 2.1rem; color: #0c2b27; line-height: 1.1;">

                                {{ number_format($readyDistribusiCount) }} <span style="font-size: 0.85rem; font-weight: 600; color: #71877e;">Antrean</span>

                            </div>

                            <small class="text-muted d-block" style="font-size: 0.74rem; line-height: 1.35;">

                                Permohonan ATK disetujui TU siap serah terima fisik

                            </small>

                        </div>

                        <div class="pt-3 mt-2 border-top" style="border-color: #edf3f0 !important;">

                            <a href="{{ route('distribusi.index') }}" class="btn btn-sm btn-light w-100 rounded-pill fw-bold d-flex align-items-center justify-content-center gap-1.5 py-1.5" style="font-size: 0.78rem; border-color: #dbe7e1; color: #0c2b27;">

                                Buka Antrean Distribusi &rarr;

                            </a>

                        </div>

                    </div>

                </div>



                <!-- Card 2: Penyaluran Subdit (Kolektif Unit Kerja) -->

                <div class="col-lg-3 col-md-6 col-12">

                    <div class="card h-100 shadow-sm border-0 aeux-stat-card d-flex flex-column justify-content-between p-3.5" style="border-radius: 16px;">

                        <div>

                            <div class="d-flex align-items-center justify-content-between mb-2">

                                <span class="aeux-stat-label mb-0" style="font-size: 0.8rem; font-weight: 700;">Distribusi Subdit</span>

                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1" style="font-size: 0.68rem; font-weight: 700;">

                                    Unit Kerja

                                </span>

                            </div>

                            <div class="aeux-stat-value mb-1" style="font-size: 2.1rem; color: #0c2b27; line-height: 1.1;">

                                {{ number_format($distribSubditTickets) }} <span style="font-size: 0.85rem; font-weight: 600; color: #71877e;">Permohonan</span>

                            </div>

                            <small class="text-muted d-block" style="font-size: 0.74rem; line-height: 1.35;">

                                Realisasi penyaluran unit kerja periode {{ $selectedPeriodLabel }}

                            </small>

                        </div>

                        <div class="pt-3 mt-2 border-top" style="border-color: #edf3f0 !important;">

                            <a href="{{ route('distribusi.index', ['type' => 'subdit']) }}" class="btn btn-sm btn-light w-100 rounded-pill fw-bold d-flex align-items-center justify-content-center gap-1.5 py-1.5" style="font-size: 0.78rem; border-color: #dbe7e1; color: #0c2b27;">

                                Antrean Subdit &rarr;

                            </a>

                        </div>

                    </div>

                </div>



                <!-- Card 3: Penyaluran Pegawai (Perorangan / Mandiri) -->

                <div class="col-lg-3 col-md-6 col-12">

                    <div class="card h-100 shadow-sm border-0 aeux-stat-card d-flex flex-column justify-content-between p-3.5" style="border-radius: 16px;">

                        <div>

                            <div class="d-flex align-items-center justify-content-between mb-2">

                                <span class="aeux-stat-label mb-0" style="font-size: 0.8rem; font-weight: 700;">Distribusi Pegawai</span>

                                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2.5 py-1" style="font-size: 0.68rem; font-weight: 700;">

                                    Perorangan

                                </span>

                            </div>

                            <div class="aeux-stat-value mb-1" style="font-size: 2.1rem; color: #0c2b27; line-height: 1.1;">

                                {{ number_format($distribIndividuTickets) }} <span style="font-size: 0.85rem; font-weight: 600; color: #71877e;">Permohonan</span>

                            </div>

                            <small class="text-muted d-block" style="font-size: 0.74rem; line-height: 1.35;">

                                Realisasi permohonan staf periode {{ $selectedPeriodLabel }}

                            </small>

                        </div>

                        <div class="pt-3 mt-2 border-top" style="border-color: #edf3f0 !important;">

                            <a href="{{ route('distribusi.index', ['type' => 'individu']) }}" class="btn btn-sm btn-light w-100 rounded-pill fw-bold d-flex align-items-center justify-content-center gap-1.5 py-1.5" style="font-size: 0.78rem; border-color: #dbe7e1; color: #0c2b27;">

                                Antrean Pegawai &rarr;

                            </a>

                        </div>

                    </div>

                </div>



                <!-- Card 4: Total Barang Keluar / Disalurkan -->

                <div class="col-lg-3 col-md-6 col-12">

                    <div class="card h-100 shadow-sm border-0 aeux-stat-card d-flex flex-column justify-content-between p-3.5" style="border-radius: 16px;">

                        <div>

                            <div class="d-flex align-items-center justify-content-between mb-2">

                                <span class="aeux-stat-label mb-0" style="font-size: 0.8rem; font-weight: 700;">Total Disalurkan</span>

                                <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-2.5 py-1" style="font-size: 0.68rem; font-weight: 700;">

                                    {{ $selectedYear ?? date('Y') }}

                                </span>

                            </div>

                            <div class="aeux-stat-value mb-1" style="font-size: 2.1rem; color: #0c2b27; line-height: 1.1;">

                                {{ number_format($distribTotalTickets) }} <span style="font-size: 0.85rem; font-weight: 600; color: #71877e;">Permohonan</span>

                            </div>

                            <small class="text-muted d-block" style="font-size: 0.74rem; line-height: 1.35;">

                                {{ number_format($distribSubditTickets) }} Subdit &bull; {{ number_format($distribIndividuTickets) }} Pegawai

                            </small>

                        </div>

                        <div class="pt-3 mt-2 border-top" style="border-color: #edf3f0 !important;">

                            <a href="{{ route('barang-keluar') }}" class="btn btn-sm btn-light w-100 rounded-pill fw-bold d-flex align-items-center justify-content-center gap-1.5 py-1.5" style="font-size: 0.78rem; border-color: #dbe7e1; color: #0c2b27;">

                                Riwayat Penyaluran &rarr;

                            </a>

                        </div>

                    </div>

                </div>

            </div>

        @else

            {{-- Tampilan Pegawai Gudang Pemeriksa Barang (Fokus: Pemeriksaan Pengadaan & Barang Masuk) --}}

            <div class="row g-3 mb-4">

                <!-- Card 1: Pengadaan Disetujui (Menunggu Pemeriksaan Fisik & Stok) -->

                <div class="col-lg-6 col-md-6 col-12">

                    <div class="card h-100 shadow-sm border-0 aeux-stat-card d-flex flex-column justify-content-between p-4" style="border-radius: 16px;">

                        <div>

                            <div class="d-flex align-items-center justify-content-between mb-2">

                                <span class="aeux-stat-label mb-0" style="font-size: 0.82rem; font-weight: 700;">Pengadaan Disetujui</span>

                                <span class="badge {{ $pendingPengadaanGudangCount > 0 ? 'bg-warning text-dark' : 'bg-light text-muted border' }} rounded-pill px-2.5 py-1" style="font-size: 0.68rem; font-weight: 700;">

                                    {{ $pendingPengadaanGudangCount > 0 ? $pendingPengadaanGudangCount . ' Perlu Pemeriksaan' : 'Nihil' }}

                                </span>

                            </div>

                            <div class="aeux-stat-value mb-1" style="font-size: 2.4rem; color: #0c2b27; line-height: 1.1;">

                                {{ number_format($pendingPengadaanGudangCount) }} <span style="font-size: 0.95rem; font-weight: 600; color: #71877e;">Usulan</span>

                            </div>

                            <small class="text-muted d-block" style="font-size: 0.78rem; line-height: 1.4;">

                                Usulan pengadaan barang disetujui Kasubbag TU yang menunggu barang tiba &amp; pemeriksaan fisik stok.

                            </small>

                        </div>

                        <div class="pt-3 mt-3 border-top" style="border-color: #edf3f0 !important;">

                            <a href="{{ route('master-data.daftar-barang.index') }}#pengadaan-section" class="btn btn-sm btn-dark-pill w-100 fw-bold d-flex align-items-center justify-content-center gap-1.5 py-1.5" style="font-size: 0.82rem;">

                                Periksa &amp; Input Stok &rarr;

                            </a>

                        </div>

                    </div>

                </div>



                <!-- Card 2: Total Barang Masuk Gudang Periode Ini -->

                <div class="col-lg-6 col-md-6 col-12">

                    <div class="card h-100 shadow-sm border-0 aeux-stat-card d-flex flex-column justify-content-between p-4" style="border-radius: 16px;">

                        <div>

                            <div class="d-flex align-items-center justify-content-between mb-2">

                                <span class="aeux-stat-label mb-0" style="font-size: 0.82rem; font-weight: 700;">Barang Masuk Gudang</span>

                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1" style="font-size: 0.68rem; font-weight: 700;">

                                    +{{ number_format($totalStockIn) }} Masuk

                                </span>

                            </div>

                            <div class="aeux-stat-value mb-1" style="font-size: 2.4rem; color: #0c2b27; line-height: 1.1;">

                                {{ number_format($totalStockIn) }} <span style="font-size: 0.95rem; font-weight: 600; color: #71877e;">Unit Masuk</span>

                            </div>

                            <small class="text-muted d-block" style="font-size: 0.78rem; line-height: 1.4;">

                                Total fisik barang masuk gudang yang telah selesai diperiksa dan dicatat pada periode {{ $selectedPeriodLabel }}.

                            </small>

                        </div>

                        <div class="pt-3 mt-3 border-top" style="border-color: #edf3f0 !important;">

                            <a href="{{ route('barang-masuk') }}" class="btn btn-sm btn-light w-100 rounded-pill fw-bold d-flex align-items-center justify-content-center gap-1.5 py-1.5" style="font-size: 0.82rem; border-color: #dbe7e1; color: #0c2b27;">

                                + Catat Barang Masuk

                            </a>

                        </div>

                    </div>

                </div>

            </div>

        @endif



        <!-- ROW 2 GUDANG: Kondisi Rak Persediaan & Daftar Tugas Operasional Terkini -->

        <div class="row g-3 mb-4">

            <!-- Kolom Kiri: Kondisi Persediaan & Rak Fisik -->

            <div class="col-lg-4 col-md-5 col-12">

                <div class="card h-100 shadow-sm border-0 p-4 d-flex flex-column justify-content-between" style="border-radius: 16px;">

                    <div>

                        <div class="d-flex justify-content-between align-items-center mb-1">

                            <span class="fw-bold text-dark" style="font-size: 0.95rem; letter-spacing: -0.01em;">Kondisi Stok Rak Gudang</span>

                            <span class="badge bg-light text-muted border" style="font-size: 0.7rem;">Katalog BMN</span>

                        </div>

                        <small class="text-muted d-block mb-3" style="font-size: 0.75rem;">Status ketersediaan fisik barang persediaan saat ini</small>



                        <div class="p-3 rounded-3 mb-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">

                            <div class="d-flex justify-content-between align-items-center mb-2">

                                <span class="small text-muted" style="font-size: 0.8rem;">Kesiapan Stok Siap Salur</span>

                                <span class="fw-bold text-success" style="font-size: 0.85rem;">{{ $displayReadiness }}%</span>

                            </div>

                            <div class="progress" style="height: 8px; border-radius: 6px; background-color: #e2ebe6;">

                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $displayReadiness }}%;" aria-valuenow="{{ $displayReadiness }}" aria-valuemin="0" aria-valuemax="100"></div>

                            </div>

                        </div>



                        <ul class="list-unstyled mb-0" style="font-size: 0.82rem;">

                            <li class="d-flex justify-content-between align-items-center mb-2.5 pb-2 border-bottom">

                                <span class="d-flex align-items-center gap-2 text-muted">

                                    <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #10b981; display: inline-block;"></span>

                                    Stok Siap Salur (Tersedia)

                                </span>

                                <strong class="text-dark">{{ number_format($activeStockCount) }} Jenis</strong>

                            </li>

                            <li class="d-flex justify-content-between align-items-center mb-2.5 pb-2 border-bottom">

                                <span class="d-flex align-items-center gap-2 text-muted">

                                    <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #ef4444; display: inline-block;"></span>

                                    Stok Habis / Kosong

                                </span>

                                <strong class="text-danger">{{ number_format($outOfStockCount) }} Jenis</strong>

                            </li>

                            <li class="d-flex justify-content-between align-items-center">

                                <span class="d-flex align-items-center gap-2 text-muted">

                                    <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #3b82f6; display: inline-block;"></span>

                                    Total Katalog Aktif

                                </span>

                                <strong class="text-primary">{{ number_format($totalProducts) }} Jenis ATK</strong>

                            </li>

                        </ul>

                    </div>





                </div>

            </div>



            <!-- Kolom Kanan: Antrean Tugas Operasional Gudang -->

            <div class="col-lg-8 col-md-7 col-12">

                <div class="card h-100 shadow-sm border-0 p-4 d-flex flex-column justify-content-between" style="border-radius: 16px;">

                    <div>

                        <div class="d-flex justify-content-between align-items-center mb-1">

                            <h5 class="fw-bold text-dark mb-0" style="font-size: 0.98rem; letter-spacing: -0.01em;">

                                @if($isDistributor)

                                    Antrean Distribusi Barang Siap Serah Terima

                                @else

                                    Antrean Pemeriksaan Pengadaan Menunggu Tindakan

                                @endif

                            </h5>

                            <span class="badge {{ ($isDistributor ? $readyDistribusiCount : $pendingPengadaanGudangCount) > 0 ? 'bg-warning text-dark' : 'bg-light text-muted border' }} rounded-pill px-2.5 py-1" style="font-size: 0.7rem; font-weight: 700;">

                                @if($isDistributor)

                                    {{ $readyDistribusiCount }} Siap Diserahkan

                                @else

                                    {{ $pendingPengadaanGudangCount }} Menunggu Input Stok

                                @endif

                            </span>

                        </div>

                        <p class="text-muted small mb-3" style="font-size: 0.76rem;">

                            @if($isDistributor)

                                Daftar permohonan ATK yang telah disetujui Kasubbag TU dan siap diserahkan fisiknya kepada pemohon / unit kerja.

                            @else

                                Usulan pengadaan barang ATK yang telah disetujui Kasubbag TU dan menunggu pemeriksaan fisik serta pencatatan stok masuk ke rak gudang.

                            @endif

                        </p>



                        @if($gudangTasksList->isNotEmpty())

                            <div class="ateka-queue" data-page-size="3">

                                <table class="table table-hover align-middle mb-0 ateka-queue-table">

                                    <thead class="table-light" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">

                                        <tr>

                                            <th class="ps-2 py-2">Kode Tiket</th>

                                            <th class="py-2">Pemohon / Unit</th>

                                            <th class="py-2">Item Barang</th>

                                            <th class="py-2">{{ $isDistributor ? 'Tipe Distribusi' : 'Status Pengadaan' }}</th>

                                            <th class="text-end pe-2 py-2">Aksi</th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        @foreach($gudangTasksList as $task)

                                            @php

                                                $isProc = $task->is_procurement;

                                                $hasUnfulfilled = $task->details->contains('is_fulfilled', false);

                                                $pemohonName = $task->user ? ($task->user->name_with_title ?: $task->user->name) : 'Pemohon';

                                                $firstDetail = $task->details->first();

                                                $detailCount = $task->details->count();

                                                $itemName = $firstDetail ? ($firstDetail->item_name ?: ($firstDetail->product->name ?? 'Barang ATK')) : 'Barang ATK';

                                                $itemSummary = $detailCount > 1 ? ($itemName . ' (+ ' . ($detailCount - 1) . ' item)') : $itemName;

                                            @endphp

                                            <tr>

                                                <td class="ps-2 py-2.5 fw-semibold text-dark ateka-q-code" data-label="Kode Tiket">

                                                    {{ $task->request_code }}

                                                    <small class="d-block text-muted" style="font-size: 0.68rem;">{{ $task->updated_at ? $task->updated_at->diffForHumans() : '-' }}</small>

                                                </td>

                                                <td class="py-2.5 ateka-q-who" data-label="Pemohon">

                                                    <span class="d-block fw-semibold text-dark">{{ $pemohonName }}</span>

                                                    <small class="text-muted" style="font-size: 0.72rem;">{{ $task->type === 'subdit' ? 'Kolektif Subdit' : 'Perorangan' }}</small>

                                                </td>

                                                <td class="py-2.5 ateka-q-item" data-label="Item" title="{{ $itemSummary }}">

                                                    {{ $itemSummary }}

                                                </td>

                                                <td class="py-2.5 ateka-q-status" data-label="Status">

                                                    @if($isDistributor)

                                                        @if($task->type === 'subdit')

                                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1" style="font-size: 0.68rem;">

                                                                Kolektif Subdit

                                                            </span>

                                                        @else

                                                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-1" style="font-size: 0.68rem;">

                                                                Perorangan

                                                            </span>

                                                        @endif

                                                    @else

                                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-1" style="font-size: 0.68rem;">

                                                            Pengadaan (Menunggu Stok)

                                                        </span>

                                                    @endif

                                                </td>

                                                <td class="text-end pe-2 py-2.5 ateka-q-act" data-label="Aksi">

                                                    @if($isDistributor)

                                                        <a href="{{ route('distribusi.index') }}" class="btn btn-outline-success rounded-pill fw-bold ateka-queue-btn">

                                                            Serahkan &rarr;

                                                        </a>

                                                    @else

                                                        <a href="{{ route('master-data.daftar-barang.index') }}#pengadaan-section" class="btn btn-outline-primary rounded-pill fw-bold ateka-queue-btn">

                                                            Input Stok &rarr;

                                                        </a>

                                                    @endif

                                                </td>

                                            </tr>

                                        @endforeach

                                    </tbody>

                                </table>

                            </div>

                        @else

                            <div class="p-4 text-center rounded-3 bg-light border">

                                <div class="mb-2 text-success">

                                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                                        <circle cx="12" cy="12" r="10"></circle>

                                        <polyline points="9 12 11 14 15 10"></polyline>

                                    </svg>

                                </div>

                                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.9rem;">

                                    {{ $isDistributor ? 'Seluruh Antrean Distribusi Selesai' : 'Seluruh Pemeriksaan Pengadaan Selesai' }}

                                </h6>

                                <small class="text-muted" style="font-size: 0.78rem;">

                                    {{ $isDistributor ? 'Tidak ada permohonan ATK yang menunggu serah terima fisik saat ini.' : 'Tidak ada usulan pengadaan yang menunggu pemeriksaan fisik atau pencatatan stok saat ini.' }}

                                </small>

                            </div>

                        @endif

                    </div>



                    <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-3" style="border-color: #edf3f0 !important;">

                        <span class="text-muted small" style="font-size: 0.76rem;">

                            {{ $isDistributor ? 'Menampilkan permohonan yang siap diserahkan' : 'Menampilkan antrean pengadaan butuh pemeriksaan fisik' }}

                        </span>

                        @if($isDistributor)

                            <a href="{{ route('distribusi.index') }}" class="btn btn-sm btn-link p-0 text-decoration-none fw-bold" style="font-size: 0.78rem; color: #0c2b27;">

                                Lihat Semua Antrean Distribusi &rarr;

                            </a>

                        @else

                            <a href="{{ route('master-data.daftar-barang.index') }}#pengadaan-section" class="btn btn-sm btn-link p-0 text-decoration-none fw-bold" style="font-size: 0.78rem; color: #0c2b27;">

                                Kelola Seluruh Pengadaan Masuk &rarr;

                            </a>

                        @endif

                    </div>

                </div>

            </div>

        </div>

        @endif



        @if ($isKasubagTU || $isPegawaiGudang)

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

                                <canvas id="stockChart" width="600" height="200" style="display: block; width: 100%; height: 200px; aspect-ratio: 3 / 1;"></canvas>

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

                                            <span class="badge bg-success-subtle text-success border" style="font-size: 0.7rem;">+{{ number_format($transaction->quantity) }} {{ $transaction->product->unit ?? 'Unit' }}</span>

                                        @else

                                            <span class="badge bg-danger-subtle text-danger border" style="font-size: 0.7rem;">-{{ number_format($transaction->quantity) }} {{ $transaction->product->unit ?? 'Unit' }}</span>

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







            {{-- DAFTAR BARANG DENGAN STOK TERSEDIA (SIAP SALUR) --}}

            @if (isset($availableProductsList) && $availableProductsList->isNotEmpty())

                <div class="row mb-4">

                    <div class="col-12">

                        <div class="card shadow-sm border-0 rounded-4 overflow-hidden" style="border: 1px solid #d1e7dd !important;">

                            <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: linear-gradient(180deg, #f0fdf4 0%, #ffffff 100%) !important;">

                                <div>

                                    <div class="d-flex align-items-center gap-2">

                                        <span class="badge bg-success text-white rounded-pill px-2.5 py-1" style="font-size: 0.72rem; font-weight: 700;"> {{ $availableProductsList instanceof \Illuminate\Pagination\LengthAwarePaginator ? $availableProductsList->total() : $availableProductsList->count() }} Jenis Barang

                                        </span>

                                        <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem; letter-spacing: -0.01em;">

                                            Daftar Barang Tersedia di Gudang (Stok Fisik Siap Salur)

                                        </h5>

                                    </div>

                                    <small class="text-muted d-block mt-1" style="font-size: 0.78rem;">

                                        Seluruh barang persediaan ATK yang saat ini memiliki stok fisik di gudang dan siap digunakan atau didistribusikan.

                                    </small>

                                </div>
                            </div>

                            <div class="card-body p-0">

                                <div class="table-responsive">

                                    <table class="table table-hover align-middle mb-0" style="font-size: 0.84rem;">

                                        <thead class="table-light" style="font-size: 0.73rem; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">

                                            <tr>

                                                <th class="ps-4 py-2.5" style="width: 50px;">No</th>

                                                <th class="py-2.5">Nama Barang &amp; Kode BMN</th>

                                                <th class="py-2.5">Kategori</th>

                                                <th class="text-center py-2.5">Sisa Stok Fisik</th>

                                                <th class="text-end py-2.5">Harga Satuan</th>

                                                <th class="text-end py-2.5">Total Valuasi</th>

                                                

                                                <th class="text-end pe-4 py-2.5">Aksi</th>

                                            </tr>

                                        </thead>

                                        <tbody>

                                            @foreach ($availableProductsList as $index => $prod)

                                                @php

                                                    $valItem = (float)($prod->stock * ($prod->purchase_price ?? 0));
                                                    $itemNo = $availableProductsList instanceof \Illuminate\Pagination\LengthAwarePaginator ? ($availableProductsList->firstItem() + $index) : ($index + 1);

                                                    $isMinSafe = $prod->stock >= ($prod->minimum_stock ?: 5);

                                                @endphp

                                                <tr>

                                                    <td class="ps-4 py-3 fw-bold text-muted">{{ $itemNo }}</td>

                                                    <td class="py-3">

                                                        <div class="d-flex align-items-center gap-2.5">

                                                            <div class="rounded-3 p-1.5 d-flex align-items-center justify-content-center border" style="width: 38px; height: 38px; background: #f8fafc;">

                                                                <img src="{{ asset($prod->image_url) }}" alt="{{ $prod->name }}" width="26" height="26" loading="lazy" decoding="async" style="width: 26px; height: 26px; object-fit: contain;">

                                                            </div>

                                                            <div>

                                                                <strong class="text-dark d-block" style="font-size: 0.88rem;">{{ $prod->name }}</strong>

                                                                <small class="text-muted" style="font-size: 0.74rem;">Kode: {{ $prod->bmn_code ?: ($prod->code ?: '-') }}</small>

                                                            </div>

                                                        </div>

                                                    </td>

                                                    <td class="py-3">

                                                        <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">

                                                            {{ $prod->category->name ?? 'ATK' }}

                                                        </span>

                                                    </td>

                                                    <td class="text-center py-3">

                                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill fw-bold" style="font-size: 0.85rem;">

                                                            {{ number_format($prod->stock) }} {{ $prod->unit ?: 'Unit' }}

                                                        </span>

                                                    </td>

                                                    <td class="text-end py-3 text-muted" style="font-size: 0.82rem;">

                                                        Rp {{ number_format($prod->purchase_price ?? 0, 0, ',', '.') }}

                                                    </td>

                                                    <td class="text-end py-3 fw-bold text-dark" style="font-size: 0.88rem;">

                                                        Rp {{ number_format($valItem, 0, ',', '.') }}

                                                    </td>



                                                    <td class="text-end pe-4 py-3">

                                                        <a href="{{ route('katalog-atk', ['search' => $prod->name]) }}" class="btn btn-xs btn-outline-dark rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">

                                                            Detail &rarr;

                                                        </a>

                                                    </td>

                                                </tr>

                                            @endforeach

                                        </tbody>

                                    </table>

                                </div>

                            </div>

                            <div class="card-footer bg-light px-4 py-2.5 border-top" style="font-size: 0.8rem;">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <span class="text-muted">
                                        @if($availableProductsList instanceof \Illuminate\Pagination\LengthAwarePaginator && $availableProductsList->total() > 0)
                                            Menampilkan <strong>{{ $availableProductsList->firstItem() }} - {{ $availableProductsList->lastItem() }}</strong> dari <strong>{{ $availableProductsList->total() }}</strong> jenis barang dengan stok fisik &bull; Total Fisik: <strong>{{ number_format($totalStock) }} Unit</strong>
                                        @else
                                            Menampilkan <strong>{{ $availableProductsList->count() }}</strong> jenis barang dengan stok fisik &bull; Total Fisik: <strong>{{ number_format($totalStock) }} Unit</strong>
                                        @endif
                                    </span>
                                    <span class="text-dark fw-bold">
                                        Total Valuasi Fisik Gudang: <span class="text-success" style="font-size: 0.95rem;">Rp {{ number_format($totalInventoryValue, 0, ',', '.') }}</span>
                                    </span>
                                </div>
                                @if ($availableProductsList instanceof \Illuminate\Pagination\LengthAwarePaginator && $availableProductsList->hasPages())
                                    <div class="d-flex justify-content-end align-items-center mt-2.5 pt-2 border-top">
                                        {{ $availableProductsList->links('pagination::bootstrap-4') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>

                </div>

            @endif



        @endif



        @if (!$isKasubagTU && !$isPegawaiGudang)

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



        <!-- ROW 2: PELACAKAN STATUS PENGAJUAN ATK -->
        @include('dashboard.tracker')



        <!-- ROW 3: BANNER FITUR -->
        <div class="row g-3 mb-4">
            <div class="col-12">
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
                            Layanan permohonan kebutuhan alat tulis kantor untuk menunjang tugas kedinasan secara praktis, transparan, dan terpantau langsung proses persetujuannya.
                        </p>
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

                        <div class="card-body p-2.5 p-md-3" data-page-size="5">

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

