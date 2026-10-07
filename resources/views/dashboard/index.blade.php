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
    <div class="iq-navbar-header" style="height: 225px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="flex-wrap d-flex justify-content-between align-items-center text-white">
                        <div>
                            <span class="badge {{ $roleBadgeClass }} px-3 py-1.5 mb-2 fw-bold" style="font-size: 0.8rem;">
                                Peran: {{ $roleTitle }}
                            </span>
                            <h1 class="text-white fw-bold mb-1">Selamat Datang, {{ $user->name }}</h1>
                            <p class="text-white mb-0" style="opacity: 0.9;">
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
                            <div class="avatar avatar-50 rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center fs-4">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold text-dark">{{ $user->name }}</h5>
                                <small class="text-muted">{{ $user->position ?? 'Pegawai' }} &bull; <strong class="text-primary">{{ $roleTitle }}</strong></small>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            @if ($isKasubagTU)
                                <a href="{{ route('persetujuan.index', ['status' => 'pending']) }}" class="btn btn-warning text-dark fw-bold btn-sm shadow-sm d-inline-flex align-items-center gap-1.5">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="9 11 12 14 22 4"></polyline>
                                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                    </svg>
                                    Review Pengajuan ({{ $pendingRequestsCount }} Pending)
                                </a>
                                <a href="{{ route('rekapitulasi-permintaan.index') }}" class="btn btn-info text-white btn-sm fw-bold shadow-sm d-inline-flex align-items-center gap-1.5">
                                    Rekapitulasi Permintaan
                                </a>
                                <a href="{{ route('barang-masuk') }}" class="btn btn-success btn-sm text-white">
                                    + Pengadaan (Barang Masuk)
                                </a>
                                <a href="{{ route('reports.index') }}" class="btn btn-outline-primary btn-sm">
                                    Laporan & Pengesahan
                                </a>
                            @elseif ($isArsiparis)
                                <a href="{{ route('reports.rekapitulasi') }}" class="btn btn-info text-white btn-sm fw-bold shadow-sm d-inline-flex align-items-center gap-1.5">
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
                                <a href="{{ route('distribusi.index', ['status' => 'ready']) }}" class="btn btn-warning text-dark fw-bold btn-sm shadow-sm d-inline-flex align-items-center gap-1.5">
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
                                <a href="{{ route('pengajuan.create') }}" class="btn btn-warning text-dark fw-bold btn-sm shadow-sm">
                                    + Buat Pengajuan ATK Baru
                                </a>
                                <a href="{{ route('katalog-atk') }}" class="btn btn-outline-primary btn-sm">
                                    Katalog & Ketersediaan Stok
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
            {{-- Alert Banner Notifikasi Pengadaan Tersedia --}}
            @if(isset($recentProcurementNotifications) && $recentProcurementNotifications->isNotEmpty())
                @foreach($recentProcurementNotifications as $pNotif)
                    <div class="alert alert-success border-0 shadow-sm d-flex align-items-center justify-content-between p-3 mb-4 rounded-3 notif-banner" id="banner-notif-{{ $pNotif->id }}">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-success text-white p-2 rounded-circle d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 44px; height: 44px;">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                    <line x1="3" y1="6" x2="21" y2="6"></line>
                                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                                </svg>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold text-success d-flex align-items-center gap-2">
                                    {{ $pNotif->title }}
                                    <span class="badge bg-success text-white" style="font-size: 0.65rem;">Sudah Dibeli</span>
                                </h6>
                                <div class="small text-dark">{{ $pNotif->message }}</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-shrink-0 ms-3">
                            <a href="{{ route('katalog-atk') }}" class="btn btn-sm btn-success fw-bold px-3 shadow-sm">
                                Cek di Katalog ATK
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="markSingleNotificationRead({{ $pNotif->id }}, event, '')" title="Tandai Sudah Dibaca">
                                &times;
                            </button>
                        </div>
                    </div>
                @endforeach
            @endif

            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body p-3">
                            <span class="text-secondary small fw-bold text-uppercase d-block mb-1">Menunggu Review</span>
                            <h3 class="fw-bold text-warning mb-0">{{ $userPendingCount }}</h3>
                            <small class="text-muted">Kasubag TU</small>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body p-3">
                            @if(isset($userProcurementBoughtCount) && $userProcurementBoughtCount > 0)
                                <span class="text-secondary small fw-bold text-uppercase d-block mb-1">Pengadaan Siap</span>
                                <h3 class="fw-bold text-success mb-0">{{ $userProcurementBoughtCount }}</h3>
                                <small class="text-success fw-semibold">Sudah dibeli &amp; tersedia</small>
                            @else
                                <span class="text-secondary small fw-bold text-uppercase d-block mb-1">Disetujui TU (Proses Beli)</span>
                                <h3 class="fw-bold text-info mb-0">{{ $userApprovedCount }}</h3>
                                <small class="text-muted">Akan dibeli Subbag TU</small>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body p-3">
                            <span class="text-secondary small fw-bold text-uppercase d-block mb-1">Selesai Diterima</span>
                            <h3 class="fw-bold text-success mb-0">{{ $userDistributedCount }}</h3>
                            <small class="text-muted">Telah diserahterimakan</small>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body p-3">
                            <span class="text-secondary small fw-bold text-uppercase d-block mb-1">Ditolak</span>
                            <h3 class="fw-bold text-danger mb-0">{{ $userRejectedCount }}</h3>
                            <small class="text-muted">Belum disetujui</small>
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
                <div class="col-md-4 col-12">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body p-3.5">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="mb-1 text-secondary small fw-bold text-uppercase">Total Item Master</p>
                                    <h3 class="mb-0 fw-bold">{{ number_format($totalProducts) }} <small class="fs-6 text-muted">Jenis</small></h3>
                                    <small class="text-muted">Total master persediaan ATK</small>
                                </div>
                                <div class="bg-primary-subtle text-primary rounded p-3">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="14" width="7" height="7"></rect>
                                        <rect x="3" y="14" width="7" height="7"></rect>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-12">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body p-3.5">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="mb-1 text-secondary small fw-bold text-uppercase">Total Stok Fisik</p>
                                    <h3 class="mb-0 fw-bold text-dark">{{ number_format($totalStock) }} <small class="fs-6 text-muted">Pcs</small></h3>
                                    <small class="text-muted">Akumulasi kuantitas di gudang</small>
                                </div>
                                <div class="bg-info-subtle text-info rounded p-3">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-12">
                    <div class="card h-100 shadow-sm border-0 border-success">
                        <div class="card-body p-3.5">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="mb-1 text-secondary small fw-bold text-uppercase">Valuasi Persediaan</p>
                                    <h4 class="mb-0 fw-bold text-success">Rp {{ number_format($totalInventoryValue ?? 0, 0, ',', '.') }}</h4>
                                    <small class="text-muted">Total nilai perolehan persediaan</small>
                                </div>
                                <div class="bg-success-subtle text-success rounded p-3">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="1" x2="12" y2="23"></line>
                                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-12">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body p-3.5 d-flex justify-content-between align-items-center">
                            <div>
                                <p class="mb-1 text-secondary small fw-bold text-uppercase">Total Barang Masuk (Pengadaan)</p>
                                <h3 class="mb-0 fw-bold text-success">+{{ number_format($totalStockIn) }} <small class="fs-6 text-muted">Pcs</small></h3>
                                <small class="text-muted">Penerimaan pada {{ $selectedPeriodLabel }}</small>
                            </div>
                            <span class="badge bg-success-subtle text-success p-2.5 rounded-circle fs-5">&darr;</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-12">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body p-3.5 d-flex justify-content-between align-items-center">
                            <div>
                                <p class="mb-1 text-secondary small fw-bold text-uppercase">Total Barang Keluar (Distribusi)</p>
                                <h3 class="mb-0 fw-bold text-danger">-{{ number_format($totalStockOut) }} <small class="fs-6 text-muted">Pcs</small></h3>
                                <small class="text-muted">
                                    Subdit: <strong class="text-info">{{ number_format($totalStockOutSubdit ?? 0) }} Pcs</strong> &bull; Individu: <strong class="text-secondary">{{ number_format($totalStockOutIndividu ?? 0) }} Pcs</strong>
                                </small>
                            </div>
                            <span class="badge bg-danger-subtle text-danger p-2.5 rounded-circle fs-5">&uarr;</span>
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
    </script>
@endpush
