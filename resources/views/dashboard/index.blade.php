@extends('layouts.app')

@php
    $user = auth()->user();
    $role = $user->role ?? 'pegawai';

    $isKasubagTU = in_array($role, ['kasubag_tu', 'admin']);
    $isArsiparis = $role === 'arsiparis';
    $isPicDistribusi = $role === 'pic_distribusi';
    $isRequester = in_array($role, ['pegawai', 'subdit', 'member']) || empty($role);

    $roleBadgeClass = match ($role) {
        'kasubag_tu', 'admin' => 'bg-primary text-white',
        'arsiparis' => 'bg-info text-white',
        'pic_distribusi' => 'bg-warning text-dark',
        'subdit' => 'bg-success text-white',
        default => 'bg-secondary text-white',
    };

    $roleTitle = match ($role) {
        'kasubag_tu', 'admin' => 'Kasubag TU (Approver / Pimpinan)',
        'arsiparis' => 'Arsiparis (Pelaporan & Administrasi)',
        'pic_distribusi' => 'PIC Distribusi (Pengelola / Gudang)',
        'subdit' => 'Subdirektorat (Pemohon Kolektif)',
        default => 'Pegawai (Pemohon Individu)',
    };
@endphp

@section('page-header')
    <div class="iq-navbar-header" style="height: 225px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="flex-wrap d-flex justify-content-between align-items-center text-white">
                        <div>
                            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2" style="background: rgba(255, 255, 255, 0.12); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.18); font-size: 0.72rem; letter-spacing: 0.05em; text-transform: uppercase; font-weight: 700;">
                                <span class="badge-dot badge-dot-success"></span>
                                <span>Kementerian PKP RI &bull; Dashboard Inventori &mdash; {{ $roleTitle }}</span>
                            </div>
                            <h1 class="text-white fw-bold mb-1">Selamat Datang, {{ $user->name }}</h1>
                            <p class="text-white mb-0" style="opacity: 0.92; max-width: 820px;">
                                @if ($isKasubagTU)
                                    Wewenang tertinggi untuk menyetujui/menolak pengajuan ATK, mengelola pengadaan, serta mengesahkan laporan mutasi persediaan.
                                @elseif ($isArsiparis)
                                    Pengelolaan arsip, rekapitulasi mutasi barang masuk/keluar, serta menyiapkan & mencetak dokumen laporan resmi kantor.
                                @elseif ($isPicDistribusi)
                                    Koordinasi, penarikan, dan pengelompokan data permintaan dari berbagai Subdirektorat untuk diserahkan/didistribusikan.
                                @else
                                    Pengajuan permohonan ATK kantor secara harian/bulanan serta pemantauan status permohonan secara real-time.
                                @endif
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

        <!-- ======================================================== -->
        <!-- BANNER AKSI CEPAT ROLE-BASED -->
        <!-- ======================================================== -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0 bg-white">
                    <div class="card-body p-3.5 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle fw-bold d-flex align-items-center justify-content-center text-white shadow-sm" style="width: 52px; height: 52px; font-size: 1.35rem; background: var(--brand-gradient); box-shadow: 0 4px 14px -2px rgba(15, 76, 129, 0.35) !important;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold text-dark">{{ $user->name }}</h5>
                                <small class="text-muted">{{ $user->position ?? 'Pegawai' }} <span class="text-muted mx-1">&vert;</span> <strong class="text-primary">{{ $roleTitle }}</strong></small>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            @if ($isKasubagTU)
                                <a href="{{ route('persetujuan.index', ['status' => 'pending']) }}" class="btn btn-warning text-white fw-bold btn-sm shadow-sm d-inline-flex align-items-center gap-1.5">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="9 11 12 14 22 4"></polyline>
                                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                    </svg>
                                    Review Pengajuan ({{ $pendingRequestsCount }} Pending)
                                </a>
                                <a href="{{ route('rekapitulasi-permintaan.index') }}" class="btn btn-outline-primary btn-sm fw-bold shadow-sm d-inline-flex align-items-center gap-1.5">
                                    Rekapitulasi Permintaan
                                </a>
                                <a href="{{ route('barang-masuk') }}" class="btn btn-success btn-sm text-white shadow-sm">
                                    + Pengadaan (Barang Masuk)
                                </a>
                                <a href="{{ route('reports.index') }}" class="btn btn-light border btn-sm">
                                    Laporan &amp; Pengesahan
                                </a>
                            @elseif ($isArsiparis)
                                <a href="{{ route('reports.rekapitulasi') }}" class="btn btn-primary text-white btn-sm fw-bold shadow-sm d-inline-flex align-items-center gap-1.5">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                    </svg>
                                    Rekapitulasi Arsip Dokumen
                                </a>
                                <a href="{{ route('reports.berita-acara') }}" target="_blank" class="btn btn-outline-danger btn-sm">
                                    Cetak Berita Acara Resmi
                                </a>
                                <a href="{{ route('reports.index') }}" class="btn btn-outline-primary btn-sm">
                                    Laporan Mutasi Persediaan
                                </a>
                            @elseif ($isPicDistribusi)
                                <a href="{{ route('distribusi.index', ['status' => 'ready']) }}" class="btn btn-warning text-white fw-bold btn-sm shadow-sm d-inline-flex align-items-center gap-1.5">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                    </svg>
                                    Distribusi ATK ({{ $readyDistribusiCount }} Siap Serah)
                                </a>
                                <a href="{{ route('barang-keluar') }}" class="btn btn-outline-danger btn-sm">
                                    Catat Pengeluaran Gudang
                                </a>
                                <a href="{{ route('master-data.daftar-barang.index') }}" class="btn btn-outline-secondary btn-sm">
                                    Cek Stok Fisik
                                </a>
                            @else
                                <a href="{{ route('pengajuan.create') }}" class="btn btn-warning text-white fw-bold btn-sm shadow-sm">
                                    + Buat Pengajuan ATK Baru
                                </a>
                                <a href="{{ route('katalog-atk') }}" class="btn btn-outline-primary btn-sm">
                                    Katalog &amp; Ketersediaan Stok
                                </a>
                                <a href="{{ route('pengajuan.index') }}" class="btn btn-outline-secondary btn-sm">
                                    Riwayat Pengajuan Saya
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- FILTER PERIODE BULANAN DASHBOARD -->
        <!-- ======================================================== -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0 bg-white">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="bg-primary-subtle text-primary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </div>
                            <div>
                                <span class="text-secondary small fw-bold text-uppercase d-block" style="font-size: 0.72rem; letter-spacing: 0.5px;">Filter Periode Data</span>
                                <h6 class="mb-0 fw-bold text-dark">{{ $selectedPeriodLabel }}</h6>
                            </div>
                        </div>

                        <form action="{{ route('dashboard') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap mb-0">
                            <label class="small text-muted mb-0 fw-semibold">Pilih Periode:</label>
                            
                            {{-- Dropdown Bulan --}}
                            <select name="month" class="form-select form-select-sm" onchange="this.form.submit()" style="width: 165px; border-radius: 8px;">
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

                            {{-- Dropdown Tahun --}}
                            <select name="year" class="form-select form-select-sm" onchange="this.form.submit()" style="width: 130px; border-radius: 8px;">
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
                                        Tahun {{ $y }}
                                    </option>
                                @endforeach
                            </select>

                            <button type="submit" class="btn btn-sm btn-primary shadow-sm px-3 fw-semibold">
                                Terapkan
                            </button>

                            @if (($selectedMonth != date('n')) || ($selectedYear != date('Y')) || ($isAllMonths ?? false) || ($isAllYears ?? false))
                                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-light border px-2.5" title="Kembalikan ke bulan saat ini">
                                    Bulan Ini
                                </a>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- KHUSUS PEGAWAI / SUBDIT (REQUESTER VIEW) -->
        <!-- ======================================================== -->
        @if ($isRequester && !$isKasubagTU)
            {{-- Smart Consolidated Notification Hub --}}
            @if(isset($recentProcurementNotifications) && $recentProcurementNotifications->isNotEmpty())
                @php
                    $totalUnreadProcurements = $recentProcurementNotifications->count();
                    $productNames = $recentProcurementNotifications->map(function($n) {
                        return $n->data['product_name'] ?? ($n->product->name ?? null);
                    })->filter()->unique()->values();

                    if ($productNames->isEmpty()) {
                        $productNames = $recentProcurementNotifications->map(function($n) {
                            if (preg_match('/Barang\s+"([^"]+)"/i', $n->message, $m)) {
                                return $m[1];
                            }
                            return $n->title;
                        })->filter()->unique()->values();
                    }

                    $allNotifIds = $recentProcurementNotifications->pluck('id')->join(',');
                    $firstTwoProducts = $productNames->take(2)->join(', ');
                    $remainingProductCount = $productNames->count() - 2;
                @endphp

                <div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden notif-consolidated-card" id="consolidated-notif-hub" style="background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); border-left: 5px solid #10b981 !important;">
                    <div class="card-body p-3 p-md-3.5">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-success text-white rounded-circle p-2 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 44px; height: 44px;">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                        <line x1="3" y1="6" x2="21" y2="6"></line>
                                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-0.5 flex-wrap">
                                        <h6 class="mb-0 fw-bold text-success fs-6">
                                            @if($totalUnreadProcurements === 1)
                                                Barang Usulan Anda Telah Tersedia!
                                            @else
                                                {{ $totalUnreadProcurements }} Barang Usulan Anda Telah Tersedia di Persediaan!
                                            @endif
                                        </h6>
                                        <span class="badge bg-success text-white rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                            Siap Diajukan di Katalog
                                        </span>
                                    </div>
                                    <p class="small text-muted mb-0" style="font-size: 0.84rem; line-height: 1.4;">
                                        @if($productNames->count() <= 1)
                                            Subbag TU telah memproses pembelian untuk <strong>"{{ $productNames->first() ?? 'Barang Usulan' }}"</strong>.
                                        @elseif($productNames->count() == 2)
                                            Subbag TU telah memproses pembelian untuk: <strong>{{ $productNames->join(' dan ') }}</strong>.
                                        @else
                                            Subbag TU telah memproses pembelian untuk: <strong>{{ $firstTwoProducts }}</strong>, dan <strong>{{ $remainingProductCount }} barang lainnya</strong>.
                                        @endif
                                        Kini stok fisik telah tersedia dan dapat dipesan melalui katalog ATK.
                                    </p>
                                </div>
                            </div>

                            {{-- Aksi Cepat --}}
                            <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">
                                <a href="{{ route('katalog-atk') }}" class="btn btn-sm btn-success fw-bold px-3 py-1.5 rounded-pill shadow-sm d-inline-flex align-items-center gap-1.5">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="9" cy="21" r="1"></circle>
                                        <circle cx="20" cy="21" r="1"></circle>
                                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                    </svg>
                                    Cek di Katalog ATK
                                </a>

                                @if($totalUnreadProcurements > 1)
                                    <button type="button" class="btn btn-sm btn-outline-success fw-semibold px-2.5 py-1.5 rounded-pill" data-bs-toggle="collapse" data-bs-target="#notifDetailCollapse" aria-expanded="false" aria-controls="notifDetailCollapse">
                                        <span class="d-inline-flex align-items-center gap-1" style="font-size: 0.8rem;">
                                            Rincian ({{ $totalUnreadProcurements }})
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="6 9 12 15 18 9"></polyline>
                                            </svg>
                                        </span>
                                    </button>
                                @endif

                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 32px; height: 32px;" onclick="dismissConsolidatedHub('{{ $allNotifIds }}')" title="Tandai Selesai & Tutup">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Panel Rincian Terbuka (Collapsible Drawer) --}}
                        @if($totalUnreadProcurements > 1)
                            <div class="collapse mt-3" id="notifDetailCollapse">
                                <div class="border-top pt-2.5">
                                    <div class="small fw-bold text-secondary mb-2" style="font-size: 0.76rem; letter-spacing: 0.03em;">RINCIAN BARANG PENGADAAN TERSEDIA:</div>
                                    <div class="row g-2">
                                        @foreach($recentProcurementNotifications as $itemNotif)
                                            <div class="col-12 col-md-6 col-lg-4">
                                                <div class="bg-white border rounded-3 p-2 d-flex align-items-center justify-content-between shadow-xs">
                                                    <div class="d-flex align-items-center gap-2 min-w-0">
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-circle p-1 flex-shrink-0">
                                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <polyline points="20 6 9 17 4 12"></polyline>
                                                            </svg>
                                                        </span>
                                                        <div class="text-truncate">
                                                            <div class="small fw-semibold text-dark text-truncate" style="font-size: 0.82rem;">
                                                                {{ $itemNotif->data['product_name'] ?? $itemNotif->title }}
                                                            </div>
                                                            <small class="text-muted" style="font-size: 0.7rem;">{{ $itemNotif->created_at->diffForHumans() }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="kpi-widget-card h-100 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Menunggu Review</span>
                            <div class="kpi-icon-squircle" style="background-color: var(--accent-amber-light); color: var(--accent-amber);">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h2 class="fw-bold text-dark mb-0 fs-3">{{ $userPendingCount }}</h2>
                            <small class="text-muted" style="font-size: 0.75rem;">Menunggu Kasubag TU</small>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="kpi-widget-card h-100 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            @if(isset($userProcurementBoughtCount) && $userProcurementBoughtCount > 0)
                                <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Pengadaan Siap</span>
                                <div class="kpi-icon-squircle" style="background-color: var(--accent-emerald-light); color: var(--accent-emerald);">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                        <line x1="3" y1="6" x2="21" y2="6"></line>
                                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                                    </svg>
                                </div>
                            @else
                                <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Disetujui TU</span>
                                <div class="kpi-icon-squircle" style="background-color: #f0f9ff; color: #0284c7;">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div>
                            @if(isset($userProcurementBoughtCount) && $userProcurementBoughtCount > 0)
                                <h2 class="fw-bold text-success mb-0 fs-3">{{ $userProcurementBoughtCount }}</h2>
                                <small class="text-success fw-semibold" style="font-size: 0.75rem;">Sudah dibeli &amp; tersedia</small>
                            @else
                                <h2 class="fw-bold text-primary mb-0 fs-3">{{ $userApprovedCount }}</h2>
                                <small class="text-muted" style="font-size: 0.75rem;">Akan dibeli Subbag TU</small>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="kpi-widget-card h-100 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Selesai Diterima</span>
                            <div class="kpi-icon-squircle" style="background-color: var(--accent-emerald-light); color: var(--accent-emerald);">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h2 class="fw-bold text-dark mb-0 fs-3">{{ $userDistributedCount }}</h2>
                            <small class="text-muted" style="font-size: 0.75rem;">Telah diserahterimakan</small>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="kpi-widget-card h-100 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Ditolak</span>
                            <div class="kpi-icon-squircle" style="background-color: var(--accent-rose-light); color: var(--accent-rose);">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h2 class="fw-bold text-danger mb-0 fs-3">{{ $userRejectedCount }}</h2>
                            <small class="text-muted" style="font-size: 0.75rem;">Belum disetujui TU</small>
                        </div>
                    </div>
                </div>
            </div>

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
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>Kode</th>
                                            <th>Tipe</th>
                                            <th>Tanggal</th>
                                            <th>Barang</th>
                                            <th>Status</th>
                                            <th class="text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($userRequests as $req)
                                            <tr>
                                                <td>
                                                    <strong class="font-monospace text-primary">{{ $req->request_code }}</strong>
                                                    @if ($req->is_procurement)
                                                        <br><span class="badge bg-warning-subtle text-dark border px-1.5 py-0.5 mt-1" style="font-size: 0.7rem;">Pengadaan</span>
                                                    @endif
                                                </td>
                                                <td><span class="badge {{ $req->type === 'subdit' ? 'bg-info text-white' : 'bg-secondary' }}">{{ ucfirst($req->type) }}</span></td>
                                                <td><small class="text-muted">{{ $req->created_at->format('d/m/Y H:i') }}</small></td>
                                                <td>
                                                    <ul class="mb-0 ps-3 small">
                                                        @foreach ($req->details as $d)
                                                            <li class="mb-1">
                                                                <strong>{{ $d->item_name }}</strong>
                                                                @if (in_array($req->status, ['approved', 'distributed']))
                                                                    @if ($d->approved_quantity !== null && $d->approved_quantity != $d->quantity)
                                                                        <span class="badge bg-success-subtle text-success border px-1.5 py-0.5">Disetujui: {{ $d->effective_quantity }} {{ $d->item_unit }}</span>
                                                                        <small class="text-muted">(Diminta: {{ $d->quantity }} {{ $d->item_unit }})</small>
                                                                    @else
                                                                        <span class="badge bg-success-subtle text-success border px-1.5 py-0.5">Disetujui: {{ $d->effective_quantity }} {{ $d->item_unit }}</span>
                                                                    @endif
                                                                @else
                                                                    <span class="text-secondary">({{ $d->quantity }} {{ $d->item_unit }})</span>
                                                                @endif

                                                                @if ($d->specification)
                                                                    <small class="text-muted d-block fst-italic">Spesifikasi: {{ $d->specification }}</small>
                                                                @endif

                                                                @if ($d->verification_note)
                                                                    <small class="text-primary d-block fw-semibold fst-italic mt-0.5">
                                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                                                        Catatan Kasubag TU: "{{ $d->verification_note }}"
                                                                    </small>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>

                                                    @if ($req->verification_notes)
                                                        <div class="mt-1 pt-1 border-top small text-primary fst-italic" style="font-size: 0.75rem;">
                                                            <strong>Catatan TU:</strong> {{ $req->verification_notes }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge {{ $req->status_badge }}">{{ $req->status_label }}</span>
                                                    @if ($req->is_procurement && $req->purchase_invoice)
                                                        <br><span class="badge bg-primary-subtle text-primary border mt-1" style="font-size: 0.7rem;">
                                                            Faktur: {{ $req->purchase_invoice }}
                                                        </span>
                                                    @endif
                                                    @if ($req->is_procurement && $req->is_purchased)
                                                        <br><a href="{{ route('katalog-atk') }}" class="badge bg-success-subtle text-success border mt-1 text-decoration-none" style="font-size: 0.7rem;">
                                                            Tersedia di Katalog &rarr;
                                                        </a>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <div class="d-flex justify-content-end gap-1 flex-wrap">
                                                        <a href="{{ route('pengajuan.index') }}" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.75rem;">
                                                            Detail
                                                        </a>
                                                        @if ($req->status === 'distributed')
                                                            <a href="{{ route('distribusi.bukti', $req->id) }}" target="_blank"
                                                                class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 0.75rem;">Bukti</a>
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

        <!-- ======================================================== -->
        <!-- KPI METRICS KHUSUS GUDANG / PIMPINAN / ARSIPARIS -->
        <!-- ======================================================== -->
        @if ($isKasubagTU || $isArsiparis || $isPicDistribusi)
            <div class="row g-3 mb-4">
            <div class="row g-3 mb-4">
                <div class="col-md-4 col-12">
                    <div class="kpi-widget-card h-100 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Total Item Master</span>
                            <div class="kpi-icon-squircle" style="background-color: var(--brand-primary-light); color: var(--brand-primary);">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="14" width="7" height="7"></rect>
                                    <rect x="3" y="14" width="7" height="7"></rect>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h2 class="mb-0 fw-bold text-dark fs-3">{{ number_format($totalProducts) }} <small class="fs-6 text-muted fw-normal">Jenis ATK</small></h2>
                            <small class="text-muted" style="font-size: 0.75rem;">Total katalog master terdaftar</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-12">
                    <div class="kpi-widget-card h-100 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Total Stok Fisik</span>
                            <div class="kpi-icon-squircle" style="background-color: #f0f9ff; color: #0284c7;">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h2 class="mb-0 fw-bold text-dark fs-3">{{ number_format($totalStock) }} <small class="fs-6 text-muted fw-normal">Pcs</small></h2>
                            <small class="text-muted" style="font-size: 0.75rem;">Akumulasi kuantitas di gudang</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-12">
                    <div class="kpi-widget-card h-100 d-flex flex-column justify-content-between" style="border-left: 3px solid var(--accent-emerald) !important;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Valuasi Persediaan</span>
                            <div class="kpi-icon-squircle" style="background-color: var(--accent-emerald-light); color: var(--accent-emerald);">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="1" x2="12" y2="23"></line>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="mb-0 fw-bold text-success fs-4">Rp {{ number_format($totalInventoryValue ?? 0, 0, ',', '.') }}</h3>
                            <small class="text-muted" style="font-size: 0.75rem;">Total nilai perolehan persediaan</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-12">
                    <div class="kpi-widget-card h-100 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-secondary small fw-bold text-uppercase d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.04em;">Total Barang Masuk (Pengadaan)</span>
                            <h2 class="mb-0 fw-bold text-success fs-3">+{{ number_format($totalStockIn) }} <small class="fs-6 text-muted fw-normal">Pcs</small></h2>
                            <small class="text-muted" style="font-size: 0.75rem;">Penerimaan pada {{ $selectedPeriodLabel }}</small>
                        </div>
                        <div class="kpi-icon-squircle" style="background-color: var(--accent-emerald-light); color: var(--accent-emerald); width: 44px; height: 44px;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <polyline points="19 12 12 19 5 12"></polyline>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-12">
                    <div class="kpi-widget-card h-100 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-secondary small fw-bold text-uppercase d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.04em;">Total Barang Keluar (Distribusi)</span>
                            <h2 class="mb-0 fw-bold text-danger fs-3">-{{ number_format($totalStockOut) }} <small class="fs-6 text-muted fw-normal">Pcs</small></h2>
                            <small class="text-muted" style="font-size: 0.75rem;">
                                Subdit: <strong class="text-info">{{ number_format($totalStockOutSubdit ?? 0) }} Pcs</strong> &bull; Individu: <strong class="text-secondary">{{ number_format($totalStockOutIndividu ?? 0) }} Pcs</strong>
                            </small>
                        </div>
                        <div class="kpi-icon-squircle" style="background-color: var(--accent-rose-light); color: var(--accent-rose); width: 44px; height: 44px;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="19" x2="12" y2="5"></line>
                                <polyline points="5 12 12 5 19 12"></polyline>
                            </svg>
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
                        <div class="card-body p-4">
                            <canvas id="stockChart" height="120"></canvas>
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
                        <div class="card shadow-sm border-0 border-start border-warning border-4">
                            <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <strong class="text-warning d-block">Peringatan Stok Menipis (< 10 Pcs)</strong>
                                    <small class="text-muted">Terdapat {{ $lowStockCount }} barang dengan stok yang menipis di gudang</small>
                                </div>
                                <div class="d-flex gap-2">
                                    @foreach ($lowStockProducts->take(4) as $lp)
                                        <span class="badge bg-light text-dark border">
                                            {{ $lp->name }}: <strong class="text-danger">{{ $lp->stock }} Pcs</strong>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        function dismissConsolidatedHub(ids) {
            const hub = document.getElementById('consolidated-notif-hub');
            if (hub) {
                hub.style.transition = 'all 0.35s ease';
                hub.style.opacity = '0';
                hub.style.transform = 'translateY(-10px)';
                setTimeout(() => hub.remove(), 350);
            }

            fetch("{{ route('notifications.markBatchAsRead') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ ids: ids })
            }).then(res => res.json())
            .then(data => {
                const navBadge = document.getElementById('nav-notif-badge');
                if (navBadge) {
                    if (data.unread_count > 0) {
                        navBadge.textContent = data.unread_count;
                        navBadge.classList.remove('d-none');
                    } else {
                        navBadge.classList.add('d-none');
                    }
                }
                const navSubtitle = document.getElementById('nav-notif-subtitle');
                if (navSubtitle) {
                    navSubtitle.textContent = data.unread_count + ' belum dibaca';
                }
            }).catch(err => console.error(err));
        }
    </script>
@endpush
