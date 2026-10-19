@extends('layouts.app')

@php
    $user = auth()->user();
    $role = $user->role ?? 'pegawai';

    $isKasubagTU = in_array($role, ['kasubag_tu', 'admin']);
    $isPicDistribusi = $role === 'pic_distribusi';
    $isPegawaiGudang = $role === 'pegawai_gudang';
    $isRequester = in_array($role, ['pegawai', 'pic_distribusi', 'member']) || empty($role);

    $roleBadgeClass = match ($role) {
        'kasubag_tu', 'admin' => 'bg-primary text-white',
        'pic_distribusi' => 'bg-warning text-dark',
        'pegawai_gudang' => 'bg-dark text-white',
        default => 'bg-secondary text-white',
    };

    $roleTitle = match ($role) {
        'kasubag_tu', 'admin' => 'Kasubag TU (Approver / Pimpinan)',
        'pic_distribusi' => 'PIC Distribusi',
        'pegawai_gudang' => 'Pegawai Gudang',
        default => 'Pegawai',
    };
@endphp

@section('page-header')
    <div class="iq-navbar-header" style="height: 225px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="flex-wrap d-flex justify-content-between align-items-center text-white">
                        <div>
                            <h1 class="text-white fw-bold mb-1">Selamat Datang, {{ $user->name }}</h1>
                            @if ($isKasubagTU)
                                <p class="text-white mb-0" style="opacity: 0.92; max-width: 820px;">
                                    Wewenang tertinggi untuk menyetujui/menolak pengajuan ATK, mengelola pengadaan, distribusi barang, serta mengesahkan laporan mutasi persediaan.
                                </p>
                            @elseif ($isPegawaiGudang)
                                <p class="text-white mb-0" style="opacity: 0.92; max-width: 820px;">
                                    Pemeriksaan stok persediaan fisik, distribusi barang, pemantauan transaksi barang masuk & penerima barang keluar, serta pencetakan laporan resmi mutasi ATK.
                                </p>
                            @endif
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
                                <a href="{{ route('distribusi.index', ['status' => 'ready']) }}" class="btn btn-outline-warning text-dark fw-bold btn-sm shadow-sm d-inline-flex align-items-center gap-1.5">
                                    Distribusi ATK ({{ $readyDistribusiCount }} Siap Serah)
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
                            @elseif ($isPegawaiGudang)
                                <a href="{{ route('distribusi.index', ['status' => 'ready']) }}" class="btn btn-warning text-white fw-bold btn-sm shadow-sm d-inline-flex align-items-center gap-1.5">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                    </svg>
                                    Distribusi ATK ({{ $readyDistribusiCount }} Siap Serah)
                                </a>
                                <a href="{{ route('master-data.daftar-barang.index') }}" class="btn btn-primary text-white fw-bold btn-sm shadow-sm d-inline-flex align-items-center gap-1.5">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="14" width="7" height="7"></rect>
                                        <rect x="3" y="14" width="7" height="7"></rect>
                                    </svg>
                                    Cek Stok Barang
                                </a>
                                <a href="{{ route('barang-masuk') }}" class="btn btn-success text-white btn-sm shadow-sm">
                                    Cek Barang Masuk
                                </a>
                                <a href="{{ route('barang-keluar') }}" class="btn btn-outline-danger btn-sm">
                                    Cek Barang Keluar
                                </a>
                                <a href="{{ route('reports.index') }}" class="btn btn-outline-primary btn-sm">
                                    Cetak Laporan Mutasi
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
        <!-- KHUSUS PEGAWAI / PIC (REQUESTER VIEW) -->
        <!-- ======================================================== -->
        @if ($isRequester && !$isKasubagTU)
            {{-- Widget Pelacakan Status Pengajuan ATK (Horizontal Stepper) --}}
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

                                // Penentuan state tiap step: 'step-done', 'step-current', 'step-upcoming', 'step-rejected'
                                // Step 1: Pengajuan Dibuat
                                $s1Class = ($status === 'pending') ? 'step-done line-completed' : (($status === 'rejected') ? 'step-done line-rejected' : 'step-done line-completed');

                                // Apakah pengadaan sudah dibeli / ditambahkan di Master Data?
                                $isWaitingProcurement = ($tReq->is_procurement && !$tReq->is_purchased);

                                // Step 2: Verifikasi Kasubag TU
                                if ($status === 'pending') {
                                    $s2Class = 'step-current';
                                } elseif ($status === 'rejected') {
                                    $s2Class = 'step-rejected';
                                } elseif ($status === 'approved' && $isWaitingProcurement) {
                                    // Baru disetujui Kasubag TU, belum dibeli / belum ditambahkan di Master Data
                                    $s2Class = 'step-current';
                                } else {
                                    $s2Class = 'step-done line-completed';
                                }

                                // Step 3: Diproses Gudang / Pengadaan
                                if ($status === 'approved') {
                                    $s3Class = $isWaitingProcurement ? 'step-upcoming' : 'step-current';
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
                            @endphp

                            <div class="tracked-request-panel {{ $idx === 0 ? '' : 'd-none' }}" id="tracked-panel-{{ $tReq->id }}">
                                {{-- Info Bar Pengajuan --}}
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 pb-2 border-bottom border-light">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="font-monospace fw-bold text-primary">{{ $tReq->request_code }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="badge {{ $tReq->status_badge }}">{{ $tReq->status_label }}</span>
                                        


                                        <!-- Tombol Cetak Bukti Serah Terima Resmi -->
                                        @if(in_array($tReq->status, ['approved', 'distributed']))
                                            <a href="{{ route('distribusi.bukti', $tReq->id) }}" target="_blank"
                                                class="btn btn-xs btn-outline-success py-0.5 px-2 small fw-semibold d-inline-flex align-items-center gap-1"
                                                title="Cetak Dokumen Resmi Bukti Penyerahan ATK">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                Cetak Bukti
                                            </a>
                                        @endif

                                        @if($tReq->status === 'distributed' && !$tReq->signed_proof)
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
                                    </div>
                                </div>

                                {{-- 4-Step Horizontal Stepper --}}
                                <div class="atk-stepper-wrapper">
                                    {{-- STEP 1: Pengajuan Dibuat --}}
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
                                        <div class="atk-step-title">Pengajuan Dibuat</div>
                                        <div class="atk-step-subtitle">({{ $itemCount }} Jenis Barang)</div>
                                        <div class="atk-step-time">{{ $tReq->created_at->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB</div>
                                    </div>

                                    {{-- STEP 2: Verifikasi Kasubag TU --}}
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
                                                Ditolak Kasubag TU
                                            @elseif($status === 'pending')
                                                Menunggu Review TU
                                            @else
                                                Disetujui Kasubag TU
                                            @endif
                                        </div>
                                        <div class="atk-step-subtitle">
                                            @if($status === 'pending')
                                                (Sedang Ditinjau)
                                            @elseif($status === 'rejected')
                                                ({{ \Illuminate\Support\Str::limit($tReq->rejection_reason ?? 'Tidak disetujui', 32) }})
                                            @else
                                                (Telah Diverifikasi)
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

                                    {{-- STEP 3: Diproses Gudang / Pengadaan --}}
                                    <div class="atk-stepper-item {{ $s3Class }}">
                                        <div class="atk-step-circle">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="1" y="3" width="15" height="13"></rect>
                                                <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                                                <circle cx="5.5" cy="18.5" r="2.5"></circle>
                                                <circle cx="18.5" cy="18.5" r="2.5"></circle>
                                            </svg>
                                        </div>
                                        <div class="atk-step-title">
                                            {{ $tReq->is_procurement ? 'Pengadaan Barang' : 'Diproses Gudang' }}
                                        </div>
                                        <div class="atk-step-subtitle">
                                            @if($status === 'approved')
                                                @if($tReq->is_procurement)
                                                    ({{ $tReq->is_purchased ? 'Tersedia di Katalog' : 'Menunggu Pengadaan' }})
                                                @else
                                                    (Siap Diserahterimakan)
                                                @endif
                                            @elseif($status === 'distributed')
                                                (Telah Disiapkan Gudang)
                                            @else
                                                (Menunggu Antrean)
                                            @endif
                                        </div>
                                        <div class="atk-step-time">
                                            @if($status === 'distributed' || ($status === 'approved' && !$isWaitingProcurement))
                                                {{ ($tReq->updated_at ?? $tReq->verified_at)->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </div>

                                    {{-- STEP 4: Selesai Diterima --}}
                                    <div class="atk-stepper-item {{ $s4Class }}">
                                        <div class="atk-step-circle">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline>
                                                <path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path>
                                            </svg>
                                        </div>
                                        <div class="atk-step-title">Selesai Diterima</div>
                                        <div class="atk-step-subtitle">
                                            @if($status === 'distributed')
                                                @if(!$tReq->signed_proof)
                                                    <span class="text-warning fw-semibold">(Menunggu Berkas TTD)</span>
                                                @else
                                                    (Penerima: {{ $tReq->receiver_name ?? $user->name }})
                                                @endif
                                            @else
                                                (Bukti Serah Terima)
                                            @endif
                                        </div>
                                        <div class="atk-step-time">
                                            @if($status === 'distributed' && $tReq->distributed_at)
                                                {{ $tReq->distributed_at->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

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
                        <div class="card-body p-2.5 p-md-3">
                            <div class="table-responsive table-responsive-no-scroll">
                                <table class="table table-striped table-hover align-middle table-dashboard-compact mb-0 w-100" style="table-layout: fixed;">
                                    <colgroup>
                                        <col style="width: 18%;">
                                        <col style="width: 8%;">
                                        <col style="width: 12%;">
                                        <col style="width: 36%;">
                                        <col style="width: 14%;">
                                        <col style="width: 12%;">
                                    </colgroup>
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
                                                    <div class="font-monospace fw-bold text-primary" style="font-size: 0.76rem; letter-spacing: -0.02em;">{{ $req->request_code }}</div>
                                                    @if ($req->is_procurement)
                                                        <span class="badge bg-warning-subtle text-dark border px-1 py-0.5 mt-0.5" style="font-size: 0.62rem;">Pengadaan</span>
                                                    @endif
                                                </td>
                                                <td><span class="badge {{ $req->type === 'subdit' ? 'bg-info text-white' : 'bg-secondary' }}" style="font-size: 0.68rem; padding: 0.2rem 0.45rem;">{{ ucfirst($req->type) }}</span></td>
                                                <td>
                                                    <div class="text-dark fw-semibold" style="font-size: 0.74rem;">{{ $req->created_at->timezone('Asia/Jakarta')->format('d/m/Y') }}</div>
                                                    <div class="text-muted" style="font-size: 0.68rem;">{{ $req->created_at->timezone('Asia/Jakarta')->format('H:i') }} WIB</div>
                                                </td>
                                                <td>
                                                    <ul class="mb-0 ps-3" style="font-size: 0.75rem; word-break: break-word;">
                                                        @foreach ($req->details as $d)
                                                            <li class="mb-1">
                                                                <strong>{{ $d->item_name }}</strong>
                                                                @if (in_array($req->status, ['approved', 'distributed']))
                                                                    <span class="badge bg-success-subtle text-success border px-1 py-0.2" style="font-size: 0.65rem;">Disetujui: {{ $d->effective_quantity }} {{ $d->item_unit }}</span>
                                                                @else
                                                                    <span class="text-secondary" style="font-size: 0.7rem;">({{ $d->quantity }} {{ $d->item_unit }})</span>
                                                                @endif

                                                                @if ($d->specification)
                                                                    <div class="text-muted fst-italic" style="font-size: 0.7rem; line-height: 1.25;">Spesifikasi: {{ $d->specification }}</div>
                                                                @endif

                                                                @if ($d->verification_note)
                                                                    <div class="text-primary fw-semibold fst-italic" style="font-size: 0.7rem; line-height: 1.25;">
                                                                        Catatan TU: "{{ $d->verification_note }}"
                                                                    </div>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>

                                                    @if ($req->verification_notes && (!isset($d) || $req->verification_notes !== $d->verification_note))
                                                        <div class="mt-0.5 pt-0.5 border-top text-primary fst-italic" style="font-size: 0.7rem;">
                                                            <strong>Catatan TU:</strong> {{ $req->verification_notes }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $shortStatus = match ($req->status) {
                                                            'pending' => $req->is_procurement ? 'Pending Pengadaan' : 'Pending TU',
                                                            'approved' => $req->is_procurement ? ($req->is_purchased ? 'Tersedia' : 'Disetujui TU') : 'Disetujui TU',
                                                            'distributed' => 'Selesai',
                                                            'rejected' => 'Ditolak',
                                                            default => ucfirst($req->status),
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $req->status_badge }}" style="font-size: 0.68rem; padding: 0.22rem 0.45rem;" title="{{ $req->status_label }}">
                                                        {{ $shortStatus }}
                                                    </span>
                                                    @if ($req->is_procurement && $req->purchase_invoice)
                                                        <div class="mt-0.5">
                                                            <span class="badge bg-primary-subtle text-primary border text-truncate d-inline-block" style="font-size: 0.64rem; padding: 0.18rem 0.4rem; max-width: 110px;" title="Faktur: {{ $req->purchase_invoice }}">
                                                                Faktur: {{ $req->purchase_invoice }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                    @if ($req->is_procurement && $req->is_purchased)
                                                        <div class="mt-0.5">
                                                            <a href="{{ route('katalog-atk') }}" class="badge bg-success-subtle text-success border text-decoration-none" style="font-size: 0.64rem; padding: 0.18rem 0.4rem;">
                                                                Katalog &rarr;
                                                            </a>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <div class="d-flex justify-content-end gap-1 flex-wrap">
                                                        <a href="{{ route('pengajuan.index') }}" class="btn btn-xs btn-outline-primary py-0.5 px-2 fw-semibold" style="font-size: 0.72rem; border-radius: 6px;">
                                                            Detail
                                                        </a>
                                                        @if ($req->status === 'distributed')
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

        <!-- ======================================================== -->
        <!-- KPI METRICS KHUSUS GUDANG / PIMPINAN -->
        <!-- ======================================================== -->
        @if ($isKasubagTU || $isPegawaiGudang)
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
                            <small class="text-muted" style="font-size: 0.75rem;">Sisa nilai fisik di gudang (Masuk &minus; Keluar)</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-12">
                    <div class="kpi-widget-card h-100 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-secondary small fw-bold text-uppercase d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.04em;">Total Barang Masuk (Pengadaan)</span>
                            <div class="d-flex align-items-baseline gap-2 mb-1 flex-wrap">
                                <h2 class="mb-0 fw-bold text-success fs-3">+{{ number_format($totalStockIn) }} <small class="fs-6 text-muted fw-normal">Pcs</small></h2>
                                <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold" style="font-size: 0.78rem;">
                                    Rp {{ number_format($totalStockInValue ?? 0, 0, ',', '.') }}
                                </span>
                            </div>
                            <small class="text-muted" style="font-size: 0.75rem;">Total pengadaan pada {{ $selectedPeriodLabel }}</small>
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
                            <div class="d-flex align-items-baseline gap-2 mb-1 flex-wrap">
                                <h2 class="mb-0 fw-bold text-danger fs-3">-{{ number_format($totalStockOut) }} <small class="fs-6 text-muted fw-normal">Pcs</small></h2>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-bold" style="font-size: 0.78rem;">
                                    -Rp {{ number_format($totalStockOutValue ?? 0, 0, ',', '.') }}
                                </span>
                            </div>
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
