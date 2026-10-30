@extends('layouts.app')

@php
    $user = auth()->user();
    $role = $user->role ?? 'pegawai';

    $isKasubagTU = in_array($role, ['kasubag_tu', 'admin']);
    $isPicDistribusi = $role === 'pic_distribusi';
    $isPegawaiGudang = $role === 'pegawai_gudang';
    $isRequester = in_array($role, ['pegawai', 'pic_distribusi', 'member']) || empty($role);

    $roleBadgeClass = match ($role) {
        'kasubag_tu', 'admin' => 'bg-donezo-primary text-white',
        'pic_distribusi' => 'bg-warning text-dark',
        'pegawai_gudang' => 'bg-dark text-white',
        default => 'bg-secondary text-white',
    };

    $roleTitle = match ($role) {
        'kasubag_tu', 'admin' => 'Kasubbag TU (Approver / Pimpinan)',
        'pic_distribusi' => 'PIC Distribusi',
        'pegawai_gudang' => 'Pegawai Gudang',
        default => 'Pegawai Subdit / Pemohon',
    };
@endphp

@section('page-header')
    {{-- Minimal Page Header / Breadcrumb Space Sesuai Donezo --}}
    <div style="height: 12px;"></div>
@endsection

@section('content')
<div class="donezo-dashboard-wrapper">
    <div class="container-fluid px-0">

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm mb-3" style="border-radius: 18px;" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-3" style="border-radius: 18px;" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- MASTER DONEZO CANVAS --}}
        <div class="donezo-canvas">

            {{-- 1. TITLE ROW & ACTIONS (Sesuai Desain Donezo) --}}
            <div class="donezo-header-row">
                <div>
                    <h1 class="donezo-title">Dashboard</h1>
                    <p class="donezo-subtitle">
                        Sistem Informasi Inventori Persediaan &amp; Pengajuan ATK &bull; {{ $roleTitle }}
                    </p>
                </div>

                {{-- Action Buttons: Primary (+ Add Project style) & Secondary (Import / Reports style) --}}
                <div class="donezo-actions-group">
                    @if ($isKasubagTU)
                        <a href="{{ route('persetujuan.index', ['status' => 'pending']) }}" class="btn-donezo-primary" title="Verifikasi Pengajuan Masuk">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                                <polyline points="9 11 12 14 22 4"></polyline>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                            </svg>
                            <span>Review ({{ $pendingRequestsCount }} Pending)</span>
                        </a>
                        <a href="{{ route('barang-masuk') }}" class="btn-donezo-outline" title="Pengadaan Barang Masuk">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>+ Pengadaan</span>
                        </a>
                        <a href="{{ route('reports.index') }}" class="btn-donezo-outline" title="Laporan & Pengesahan Dokumen">
                            <span>Laporan &amp; BAST</span>
                        </a>
                    @elseif ($isPegawaiGudang)
                        @if (auth()->user()->isDistributor())
                            <a href="{{ route('distribusi.index') }}" class="btn-donezo-primary">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                </svg>
                                <span>Distribusi ({{ $readyDistribusiCount }} Siap)</span>
                            </a>
                        @endif
                        <a href="{{ route('barang-masuk') }}" class="btn-donezo-outline">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            <span>+ Barang Masuk</span>
                        </a>
                        <a href="{{ route('master-data.daftar-barang.index') }}" class="btn-donezo-outline">
                            <span>Cek Stok Barang</span>
                        </a>
                        <a href="{{ route('reports.index') }}" class="btn-donezo-outline">
                            <span>Laporan Mutasi</span>
                        </a>
                    @else
                        <a href="{{ route('pengajuan.create') }}" class="btn-donezo-primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>+ Buat Pengajuan ATK</span>
                        </a>
                        <a href="{{ route('katalog-atk') }}" class="btn-donezo-outline">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                            <span>Katalog ATK</span>
                        </a>
                        <a href="{{ route('pengajuan.index') }}" class="btn-donezo-outline">
                            <span>Riwayat Pengajuan</span>
                        </a>
                    @endif
                </div>
            </div>

            {{-- 2. FILTER PERIODE BULANAN BAR (Minimalist Donezo Style) --}}
            <div class="donezo-filter-bar">
                <div class="d-flex align-items-center gap-2.5">
                    <div style="width: 36px; height: 36px; border-radius: 12px; background: #e6f3ee; color: #1b4d3e; display: flex; align-items: center; justify-content: center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </div>
                    <div>
                        <span class="text-secondary small fw-bold text-uppercase d-block" style="font-size: 0.68rem; letter-spacing: 0.5px;">Periode Terpilih</span>
                        <strong style="color: #0f172a; font-size: 0.95rem;">{{ $selectedPeriodLabel }}</strong>
                    </div>
                </div>

                <form action="{{ route('dashboard') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap mb-0">
                    <span class="small text-muted fw-semibold d-none d-sm-inline">Pilih Filter:</span>
                    
                    {{-- Dropdown Bulan --}}
                    <select name="month" class="select-donezo" onchange="this.form.submit()">
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
                    <select name="year" class="select-donezo" onchange="this.form.submit()">
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

                    @if (($selectedMonth != date('n')) || ($selectedYear != date('Y')) || ($isAllMonths ?? false) || ($isAllYears ?? false))
                        <a href="{{ route('dashboard') }}" class="btn-donezo-outline" style="padding: 0.42rem 0.85rem; font-size: 0.82rem;" title="Reset ke Bulan Ini">
                            Bulan Ini
                        </a>
                    @endif
                </form>
            </div>

            {{-- 3. EMPAT KARTU STATISTIK UTAMA (Persis Baris Atas Desain Donezo) --}}
            <div class="row g-3 mb-4">
                @if ($isRequester && !$isKasubagTU)
                    {{-- Requester / Pegawai Metric Cards --}}
                    <!-- Card 1: Featured Dark Green Card (Total Permintaan Saya) -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="donezo-stat-card featured-card">
                            <div class="stat-header">
                                <span class="stat-label">Total Pengajuan</span>
                                <div class="stat-arrow-circle">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                                </div>
                            </div>
                            <div class="stat-number">
                                {{ $userRequests->count() }}
                            </div>
                            <div>
                                <span class="stat-bottom-pill">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="18 15 12 9 6 15"></polyline></svg>
                                    Permintaan ATK Saya
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Menunggu Review TU -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="donezo-stat-card light-card">
                            <div class="stat-header">
                                <span class="stat-label">Menunggu Review</span>
                                <div class="stat-arrow-circle">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                                </div>
                            </div>
                            <div class="stat-number text-warning">
                                {{ $userPendingCount }}
                            </div>
                            <div>
                                <span class="stat-bottom-pill">
                                    Menunggu Kasubbag TU
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Disetujui / Selesai -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="donezo-stat-card light-card">
                            <div class="stat-header">
                                <span class="stat-label">Disetujui / Selesai</span>
                                <div class="stat-arrow-circle">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                                </div>
                            </div>
                            <div class="stat-number text-success">
                                {{ $userApprovedCount + $userDistributedCount }}
                            </div>
                            <div>
                                <span class="stat-bottom-pill">
                                    {{ $userDistributedCount }} Sudah Diterima
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4: Ditolak -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="donezo-stat-card light-card">
                            <div class="stat-header">
                                <span class="stat-label">Belum Disetujui</span>
                                <div class="stat-arrow-circle">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                                </div>
                            </div>
                            <div class="stat-number {{ $userRejectedCount > 0 ? 'text-danger' : 'text-dark' }}">
                                {{ $userRejectedCount }}
                            </div>
                            <div>
                                <span class="stat-bottom-pill">
                                    {{ $userRejectedCount > 0 ? 'Perlu Disesuaikan' : 'Tidak Ada Penolakan' }}
                                </span>
                            </div>
                        </div>
                    </div>

                @else
                    {{-- Kasubbag TU & Pegawai Gudang KPI Metric Cards --}}
                    <!-- Card 1: Featured Dark Green Card (Total Item Master ATK) -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="donezo-stat-card featured-card">
                            <div class="stat-header">
                                <span class="stat-label">Total Item Master</span>
                                <div class="stat-arrow-circle">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                                </div>
                            </div>
                            <div class="stat-number">
                                {{ number_format($totalProducts) }}
                            </div>
                            <div>
                                <span class="stat-bottom-pill">
                                    Katalog ATK Aktif
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Total Stok Fisik Gudang -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="donezo-stat-card light-card">
                            <div class="stat-header">
                                <span class="stat-label">Total Stok Fisik</span>
                                <div class="stat-arrow-circle">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                                </div>
                            </div>
                            <div class="stat-number text-dark">
                                {{ number_format($totalStock) }}
                            </div>
                            <div>
                                <span class="stat-bottom-pill">
                                    Pcs Terdata di Gudang
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Barang Masuk (Pengadaan Periode Ini) -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="donezo-stat-card light-card">
                            <div class="stat-header">
                                <span class="stat-label">Barang Masuk</span>
                                <div class="stat-arrow-circle">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                                </div>
                            </div>
                            <div class="stat-number text-success">
                                +{{ number_format($totalStockIn) }}
                            </div>
                            <div>
                                <span class="stat-bottom-pill">
                                    Rp {{ number_format($totalStockInValue ?? 0, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4: Barang Keluar (Distribusi Periode Ini) -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="donezo-stat-card light-card">
                            <div class="stat-header">
                                <span class="stat-label">Barang Keluar</span>
                                <div class="stat-arrow-circle">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                                </div>
                            </div>
                            <div class="stat-number text-danger">
                                -{{ number_format($totalStockOut) }}
                            </div>
                            <div>
                                <span class="stat-bottom-pill">
                                    Subdit: {{ number_format($totalStockOutSubdit ?? 0) }} Pcs
                                </span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- 4. BENTO GRID BARIS KEDUA (Grafik Mutasi, Reminders/Aksi Cepat, Recent Projects/Barang) --}}
            <div class="row g-3 mb-4">
                {{-- Kolom Kiri: Analytics & Transaksi Chart (Mirip "Project Analytics" Donezo) --}}
                <div class="col-12 col-lg-7">
                    <div class="donezo-card">
                        <div class="donezo-card-header">
                            <div>
                                <h3 class="donezo-card-title">Grafik Mutasi Persediaan</h3>
                                <small class="text-muted">Perbandingan kuantitas barang masuk &amp; keluar ({{ $selectedPeriodLabel }})</small>
                            </div>
                            <span class="badge" style="background-color: #e6f3ee; color: #1b4d3e; border-radius: 12px; font-weight: 600; padding: 0.35rem 0.65rem;">
                                {{ $selectedPeriodLabel }}
                            </span>
                        </div>
                        <div class="py-2">
                            <canvas id="stockChart" height="135"></canvas>
                            @if ($chartLabels->count() == 0)
                                <div class="text-center text-muted py-4">
                                    Belum ada transaksi mutasi pada periode {{ $selectedPeriodLabel }}.
                                </div>
                            @endif
                        </div>
                        <div class="d-flex align-items-center justify-content-between pt-2 border-top border-light flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-3">
                                <span class="small d-inline-flex align-items-center gap-1.5" style="color: #0d8a57; font-weight: 600;">
                                    <span style="width: 10px; height: 10px; border-radius: 3px; background-color: #10b981;"></span> Masuk: +{{ number_format($totalStockIn) }} Pcs
                                </span>
                                <span class="small d-inline-flex align-items-center gap-1.5" style="color: #b91c1c; font-weight: 600;">
                                    <span style="width: 10px; height: 10px; border-radius: 3px; background-color: #ef4444;"></span> Keluar: -{{ number_format($totalStockOut) }} Pcs
                                </span>
                            </div>
                            @if($isKasubagTU || $isPegawaiGudang)
                                <span class="small text-muted">
                                    Valuasi Saldo: <strong class="text-success">Rp {{ number_format($totalInventoryValue ?? 0, 0, ',', '.') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Kolom Tengah/Kanan: Reminders & Agenda Pelayanan (Mirip "Reminders" Donezo) --}}
                <div class="col-12 col-md-6 col-lg-5">
                    <div class="donezo-reminder-card">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-uppercase fw-bold" style="font-size: 0.72rem; color: #0f766e; letter-spacing: 0.5px;">
                                    Pengingat &amp; Agenda Layanan
                                </span>
                                <span class="badge" style="background-color: #143d31; color: #ffffff; border-radius: 12px; font-size: 0.68rem;">
                                    Hari Ini
                                </span>
                            </div>

                            @if ($isKasubagTU)
                                <h4 class="fw-bold mb-1" style="color: #0f172a; font-size: 1.25rem;">
                                    Verifikasi Pengajuan ATK
                                </h4>
                                <p class="text-muted small mb-3">
                                    Terdapat <strong class="text-dark">{{ $pendingRequestsCount }} pengajuan ATK</strong> menunggu persetujuan Anda agar dapat diproses oleh Subbagian Tata Usaha.
                                </p>
                                <a href="{{ route('persetujuan.index', ['status' => 'pending']) }}" class="reminder-btn-green">
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                                    <span>Tinjau Pengajuan Masuk &rarr;</span>
                                </a>
                            @elseif ($isPegawaiGudang)
                                <h4 class="fw-bold mb-1" style="color: #0f172a; font-size: 1.25rem;">
                                    Serah Terima &amp; Penataan Stok
                                </h4>
                                <p class="text-muted small mb-3">
                                    Terdapat <strong class="text-dark">{{ $readyDistribusiCount }} paket ATK</strong> siap diserahterimakan kepada unit kerja / pegawai pemohon.
                                </p>
                                <a href="{{ route('distribusi.index') }}" class="reminder-btn-green">
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                                    <span>Proses Serah Terima Gudang &rarr;</span>
                                </a>
                            @else
                                <h4 class="fw-bold mb-1" style="color: #0f172a; font-size: 1.25rem;">
                                    Katalog &amp; Permintaan Mandiri
                                </h4>
                                <p class="text-muted small mb-3">
                                    Butuh alat tulis kantor untuk menunjang tugas kedinasan? Ajukan secara mudah &amp; lacak persetujuannya secara *real-time*.
                                </p>
                                <a href="{{ route('pengajuan.create') }}" class="reminder-btn-green">
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                    <span>Buat Permintaan ATK Baru &rarr;</span>
                                </a>
                            @endif
                        </div>

                        {{-- Mini Info Footer --}}
                        <div class="pt-3 mt-3 border-top d-flex justify-content-between align-items-center" style="border-color: rgba(187, 247, 208, 0.8) !important;">
                            <span style="font-size: 0.74rem; color: #166534; font-weight: 500;">Jam Layanan Operasional</span>
                            <span class="font-monospace" style="font-size: 0.74rem; color: #143d31; font-weight: 700;">08:00 - 16:00 WIB</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 5. PELACAKAN STATUS HORIZONTAL STEPPER KHUSUS PEMOHON / PEGAWAI --}}
            @if ($isRequester && !$isKasubagTU)
                @include('dashboard.stepper_partial')
            @endif

            {{-- 6. BENTO GRID BARIS KETIGA (Daftar Aktivitas/Transaksi, Progress Kinerja, & Waktu Operasional) --}}
            <div class="row g-3 mb-4">
                {{-- Card: Aktivitas Permintaan / Mutasi Terbaru (Mirip "Team Collaboration" Donezo) --}}
                <div class="col-12 col-lg-5">
                    <div class="donezo-card">
                        <div class="donezo-card-header">
                            <div>
                                <h3 class="donezo-card-title">Mutasi Terkini</h3>
                                <small class="text-muted">Aktivitas keluar-masuk barang terupdate</small>
                            </div>
                            <a href="{{ route('reports.index') }}" class="small fw-bold text-decoration-none" style="color: #1b4d3e; font-size: 0.8rem;">
                                Selengkapnya &rarr;
                            </a>
                        </div>
                        <div class="py-1">
                            @forelse ($latestTransactions->take(4) as $tx)
                                <div class="donezo-user-item">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="donezo-avatar-placeholder" style="background: {{ $tx->type === 'masuk' ? 'linear-gradient(135deg, #10b981 0%, #059669 100%)' : 'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)' }};">
                                            {{ strtoupper(substr($tx->product->name ?? 'A', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size: 0.85rem; line-height: 1.25;">
                                                {{ \Illuminate\Support\Str::limit($tx->product->name ?? '-', 22) }}
                                            </div>
                                            <small class="text-muted" style="font-size: 0.74rem;">
                                                {{ $tx->uraian ?? ($tx->user->name ?? 'Gudang TU') }} &bull; {{ $tx->transaction_date ? $tx->transaction_date->format('d/m/Y') : '-' }}
                                            </small>
                                        </div>
                                    </div>
                                    <div>
                                        @if ($tx->type === 'masuk')
                                            <span class="badge-donezo-status badge-donezo-completed">
                                                +{{ number_format($tx->quantity) }} Pcs
                                            </span>
                                        @else
                                            <span class="badge-donezo-status badge-donezo-progress">
                                                -{{ number_format($tx->quantity) }} Pcs
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4 small">
                                    Belum ada catatan mutasi terkini pada periode ini.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Card: Progres Pengajuan & Pemenuhan (Mirip "Project Progress" Gauge Donezo) --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="donezo-card">
                        <div class="donezo-card-header">
                            <div>
                                <h3 class="donezo-card-title">Efektivitas Layanan</h3>
                                <small class="text-muted">Tingkat persetujuan &amp; penyerahan ATK</small>
                            </div>
                        </div>

                        @php
                            $totalReqCalculated = $totalDistributedCount + $pendingRequestsCount + ($readyDistribusiCount ?? 0);
                            $percentageFulfilled = $totalReqCalculated > 0 ? round(($totalDistributedCount / $totalReqCalculated) * 100) : 100;
                        @endphp

                        <div class="d-flex flex-column align-items-center justify-content-center py-2">
                            {{-- Visual Semi-circle Gauge Donezo Style --}}
                            <div style="position: relative; width: 170px; height: 95px; overflow: hidden; margin-bottom: 0.75rem;">
                                <svg width="170" height="170" viewBox="0 0 170 170" style="transform: rotate(-90deg);">
                                    <circle cx="85" cy="85" r="70" fill="none" stroke="#e2e8f0" stroke-width="16" stroke-dasharray="220" stroke-dashoffset="0"/>
                                    <circle cx="85" cy="85" r="70" fill="none" stroke="#1b4d3e" stroke-width="16" stroke-dasharray="220" stroke-dashoffset="{{ 220 - (220 * ($percentageFulfilled / 100)) }}" stroke-linecap="round"/>
                                </svg>
                                <div style="position: absolute; bottom: 0; left: 0; right: 0; text-align: center;">
                                    <h2 class="fw-bold mb-0" style="color: #0f172a; font-size: 1.85rem; line-height: 1;">{{ $percentageFulfilled }}%</h2>
                                    <small class="text-muted" style="font-size: 0.7rem; font-weight: 600;">Selesai Diserahkan</small>
                                </div>
                            </div>

                            {{-- Legend Dots Sesuai Desain Donezo --}}
                            <div class="d-flex align-items-center justify-content-center gap-3 pt-2">
                                <span class="d-flex align-items-center gap-1.5 small text-muted" style="font-size: 0.76rem;">
                                    <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #1b4d3e;"></span> Selesai
                                </span>
                                <span class="d-flex align-items-center gap-1.5 small text-muted" style="font-size: 0.76rem;">
                                    <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #10b981;"></span> Siap Serah
                                </span>
                                <span class="d-flex align-items-center gap-1.5 small text-muted" style="font-size: 0.76rem;">
                                    <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #cbd5e1;"></span> Proses
                                </span>
                            </div>
                        </div>

                        <div class="pt-2 border-top border-light text-center">
                            <small class="text-muted" style="font-size: 0.74rem;">
                                Total Terdistribusi: <strong class="text-dark">{{ number_format($totalDistributedCount) }} Pengajuan</strong>
                            </small>
                        </div>
                    </div>
                </div>

                {{-- Card: Time Tracker / Jam Operasional Digital (Persis Widget Gelap Hijau Donezo) --}}
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="donezo-time-tracker-card">
                        <div>
                            <span class="text-uppercase fw-bold" style="font-size: 0.72rem; color: #a4c4b5; letter-spacing: 0.5px;">
                                Waktu Sistem Operasional
                            </span>
                            <div class="digital-clock-display" id="donezoLiveClock">
                                {{ date('H:i:s') }}
                            </div>
                            <span class="d-block" style="font-size: 0.76rem; color: #cde4db;">
                                {{ \Carbon\Carbon::now()->timezone('Asia/Jakarta')->translatedFormat('l, d F Y') }}
                            </span>
                        </div>

                        <div>
                            <div class="tracker-control-btns mb-2">
                                <a href="{{ route('katalog-atk') }}" class="tracker-btn-circle tracker-btn-pause" title="Buka E-Katalog">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                                </a>
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form-donezo').submit();" class="tracker-btn-circle tracker-btn-stop" title="Keluar / Logout">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                </a>
                                <form id="logout-form-donezo" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                            <small style="font-size: 0.7rem; color: #729686;">Server Aktif &bull; Waktu Indonesia Barat</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 7. TABEL RIWAYAT PERMINTAAN USER KHUSUS PEGAWAI / PIC --}}
            @if ($isRequester && !$isKasubagTU)
                @include('dashboard.user_requests_table_partial')
            @endif

            {{-- 8. PERINGATAN STOK MENIPIS (Khusus Kasubbag TU & Gudang) --}}
            @if ($lowStockProducts->count() > 0)
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="donezo-card" style="background-color: #fffbeb; border-color: #fde68a;">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge p-1.5 rounded-circle d-flex align-items-center justify-content-center" style="background-color: #fef3c7; color: #d97706;">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                                            <line x1="12" y1="9" x2="12" y2="13"></line>
                                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                                        </svg>
                                    </span>
                                    <div>
                                        <strong class="d-block" style="color: #92400e; font-size: 0.95rem;">Peringatan Stok Menipis (&lt; 10 Pcs)</strong>
                                        <small class="text-muted">Terdapat <strong>{{ $lowStockCount }}</strong> jenis barang dengan stok menipis di gudang</small>
                                    </div>
                                </div>
                                @if(auth()->user()->role === 'kasubag_tu' || auth()->user()->role === 'admin' || auth()->user()->role === 'pegawai_gudang')
                                    <a href="{{ route('master-data.daftar-barang.index') }}" class="btn btn-xs fw-semibold px-2.5 py-1 rounded-pill" style="background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a;">
                                        Kelola Master Barang &rarr;
                                    </a>
                                @endif
                            </div>
                            <div class="d-flex flex-wrap gap-2 pt-1">
                                @foreach ($lowStockProducts->take(6) as $lp)
                                    <span class="badge bg-white text-dark border py-1.5 px-2.5 d-inline-flex align-items-center gap-1 shadow-xs" style="font-weight: 500; font-size: 0.78rem; border-radius: 12px;">
                                        <span class="text-truncate" style="max-width: 250px;">{{ $lp->name }}:</span>
                                        <strong class="text-danger flex-shrink-0">{{ $lp->stock }} Pcs</strong>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div> {{-- End .donezo-canvas --}}

    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Chart Mutasi Transaksi Sesuai Tema Hijau Elegan Donezo
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
                            backgroundColor: 'rgba(27, 77, 62, 0.85)',
                            borderColor: '#1b4d3e',
                            borderRadius: 8,
                            barThickness: 18,
                        }, {
                            label: 'Barang Keluar',
                            data: dataOut,
                            backgroundColor: 'rgba(71, 140, 128, 0.5)',
                            borderColor: '#478c80',
                            borderRadius: 8,
                            barThickness: 18,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    boxWidth: 8,
                                    font: { family: 'Plus Jakarta Sans', size: 12, weight: 600 }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(226, 232, 240, 0.6)' }
                            }
                        }
                    }
                });
            }

            // 2. Live Digital Clock Widget (Donezo Right Bottom Card)
            function updateDonezoClock() {
                const clockEl = document.getElementById('donezoLiveClock');
                if (clockEl) {
                    const now = new Date();
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    const seconds = String(now.getSeconds()).padStart(2, '0');
                    clockEl.textContent = `${hours}:${minutes}:${seconds}`;
                }
            }
            setInterval(updateDonezoClock, 1000);
        });

        // Stepper functions
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
