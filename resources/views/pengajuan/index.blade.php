@extends('layouts.app')

@section('page-header')
    <div class="iq-navbar-header" style="height: 220px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="flex-wrap d-flex justify-content-between align-items-center text-white">
                        <div>
                            <h1 class="text-white fw-bold mb-1">Pengajuan Kebutuhan ATK</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        {{-- Flash Notification Modern Floating Toast --}}
        @if (session('success'))
            <div id="modern-toast-notification" class="modern-floating-toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex align-items-start gap-3 p-3">
                    <div class="modern-toast-icon-badge">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <div class="flex-grow-1 pe-2">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2 py-0.5 fw-bold" style="font-size: 0.7rem; letter-spacing: 0.04em;">BERHASIL DIAJUKAN</span>
                            <span class="text-muted" style="font-size: 0.72rem;">Inventori ATK &bull; Kemen PKP</span>
                        </div>
                        @php
                            $cleanedSuccess = trim(preg_replace('/\s+/', ' ', preg_replace('/\s*\(\s*REQ-[A-Za-z0-9\-]+\s*\)\s*/i', ' ', session('success'))));
                            $toastTitle = str_contains(strtolower($cleanedSuccess), 'pengadaan') ? 'Permintaan Pengadaan Berhasil Diajukan!' : 'Pengajuan Berhasil Dikirim!';
                        @endphp
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.93rem;">{{ $toastTitle }}</h6>
                        <div class="text-secondary small toast-message" style="line-height: 1.45; font-size: 0.84rem;">
                            {{ $cleanedSuccess }}
                        </div>
                    </div>
                    <button type="button" class="btn-close-toast ms-auto" aria-label="Close" onclick="dismissModernToast()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <div class="modern-toast-progress-bar" id="toast-progress-bar"></div>
            </div>
        @endif

        @if (session('error'))
            <div id="modern-toast-notification" class="modern-floating-toast toast-error" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex align-items-start gap-3 p-3">
                    <div class="modern-toast-icon-badge badge-error">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </div>
                    <div class="flex-grow-1 pe-2">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5 fw-bold" style="font-size: 0.7rem; letter-spacing: 0.04em;">PERHATIAN</span>
                        </div>
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.92rem;">Pengajuan Tidak Dapat Diproses</h6>
                        <div class="text-secondary small toast-message" style="line-height: 1.45; font-size: 0.83rem;">
                            {{ session('error') }}
                        </div>
                    </div>
                    <button type="button" class="btn-close-toast ms-auto" aria-label="Close" onclick="dismissModernToast()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <div class="modern-toast-progress-bar" id="toast-progress-bar"></div>
            </div>
        @endif

        {{-- Ringkasan Status Pengajuan --}}
        @php
            $countPending = (int) ($statusCounts['pending'] ?? 0);
            $countApproved = (int) ($statusCounts['approved'] ?? 0);
            $countDistributed = (int) ($statusCounts['distributed'] ?? 0);
            $countRejected = (int) ($statusCounts['rejected'] ?? 0);
            $kpiLink = fn($st) => route('pengajuan.index', array_merge(request()->except(['status', 'page']), ['status' => $st]));
        @endphp
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="kpi-widget-card h-100 d-flex flex-column justify-content-between {{ ($statusFilter ?? '') === 'pending' ? 'kpi-active' : '' }}" role="link" tabindex="0" style="cursor: pointer;" title="Tampilkan pengajuan dengan status ini" onclick="window.location.href='{{ $kpiLink('pending') }}'" onkeydown="if(event.key==='Enter'){window.location.href='{{ $kpiLink('pending') }}'}">
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
                        <h2 class="fw-bold text-dark mb-0 fs-3">{{ $countPending }}</h2>
                        <small class="text-muted" style="font-size: 0.75rem;">Menunggu Kasubbag TU</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-widget-card h-100 d-flex flex-column justify-content-between {{ ($statusFilter ?? '') === 'approved' ? 'kpi-active' : '' }}" role="link" tabindex="0" style="cursor: pointer;" title="Tampilkan pengajuan dengan status ini" onclick="window.location.href='{{ $kpiLink('approved') }}'" onkeydown="if(event.key==='Enter'){window.location.href='{{ $kpiLink('approved') }}'}">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Disetujui TU</span>
                        <div class="kpi-icon-squircle" style="background-color: #f0f9ff; color: #0284c7;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h2 class="fw-bold text-primary mb-0 fs-3">{{ $countApproved }}</h2>
                        <small class="text-muted" style="font-size: 0.75rem;">Akan dibeli / disiapkan</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-widget-card h-100 d-flex flex-column justify-content-between {{ ($statusFilter ?? '') === 'distributed' ? 'kpi-active' : '' }}" role="link" tabindex="0" style="cursor: pointer;" title="Tampilkan pengajuan dengan status ini" onclick="window.location.href='{{ $kpiLink('distributed') }}'" onkeydown="if(event.key==='Enter'){window.location.href='{{ $kpiLink('distributed') }}'}">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Selesai Diterima</span>
                        <div class="kpi-icon-squircle" style="background-color: var(--accent-emerald-light); color: var(--accent-emerald);">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h2 class="fw-bold text-dark mb-0 fs-3">{{ $countDistributed }}</h2>
                        <small class="text-muted" style="font-size: 0.75rem;">Telah diserahterimakan</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-widget-card h-100 d-flex flex-column justify-content-between {{ ($statusFilter ?? '') === 'rejected' ? 'kpi-active' : '' }}" role="link" tabindex="0" style="cursor: pointer;" title="Tampilkan pengajuan dengan status ini" onclick="window.location.href='{{ $kpiLink('rejected') }}'" onkeydown="if(event.key==='Enter'){window.location.href='{{ $kpiLink('rejected') }}'}">
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
                        <h2 class="fw-bold text-danger mb-0 fs-3">{{ $countRejected }}</h2>
                        <small class="text-muted" style="font-size: 0.75rem;">Belum disetujui TU</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Riwayat Pengajuan --}}
        <div class="row">
            <div class="col-sm-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h4 class="card-title mb-0 fw-bold">Daftar Riwayat Permintaan Anda</h4>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('pengajuan.create') }}" class="btn btn-primary btn-sm fw-semibold shadow-sm d-inline-flex align-items-center gap-1.5" style="font-size: 0.8rem; padding: 0.35rem 0.75rem;" title="Buat Pengajuan Kebutuhan ATK">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                Buat Pengajuan Barang
                            </a>
                        </div>
                    </div>

                    {{-- Filter & Pencarian Riwayat --}}
                    @php
                        $monthNames = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                        $filterActive = $search !== '' || $statusFilter || $typeFilter || $selectedMonth || $selectedYear;
                    @endphp
                    <div class="px-3 px-md-4 py-3 border-bottom" style="background-color: #f8fafc;">
                        <form id="pengajuan-filter-form" action="{{ route('pengajuan.index') }}" method="GET" class="row g-2 align-items-center">
                            <div class="col-12 col-lg-4">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Cari kode tiket, nama barang, keperluan..." style="height: 38px; font-size: 0.84rem;" aria-label="Cari riwayat pengajuan">
                                    <button type="submit" class="btn btn-dark px-3" style="height: 38px;">Cari</button>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 col-lg-2">
                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="height: 38px; font-size: 0.84rem;" aria-label="Status">
                                    <option value="">Semua Status</option>
                                    <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Menunggu Review</option>
                                    <option value="approved" {{ $statusFilter === 'approved' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="distributed" {{ $statusFilter === 'distributed' ? 'selected' : '' }}>Selesai Diterima</option>
                                    <option value="rejected" {{ $statusFilter === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                            @if ($isPic)
                                <div class="col-6 col-md-3 col-lg-2">
                                    <select name="type" class="form-select form-select-sm" onchange="this.form.submit()" style="height: 38px; font-size: 0.84rem;" aria-label="Tipe">
                                        <option value="">Semua Tipe</option>
                                        <option value="subdit" {{ $typeFilter === 'subdit' ? 'selected' : '' }}>Subdit (Kolektif)</option>
                                        <option value="individu" {{ $typeFilter === 'individu' ? 'selected' : '' }}>Individu</option>
                                    </select>
                                </div>
                            @endif
                            <div class="col-6 col-md-3 col-lg-2">
                                <select name="month" class="form-select form-select-sm" onchange="this.form.submit()" style="height: 38px; font-size: 0.84rem;" aria-label="Bulan">
                                    <option value="all">Semua Bulan</option>
                                    @foreach ($monthNames as $num => $name)
                                        <option value="{{ $num }}" {{ $selectedMonth == $num ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-3 col-lg-2">
                                <select name="year" class="form-select form-select-sm" onchange="this.form.submit()" style="height: 38px; font-size: 0.84rem;" aria-label="Tahun">
                                    <option value="all">Semua Tahun</option>
                                    @for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            @if ($filterActive)
                                <div class="col-12 col-lg-auto">
                                    <a href="{{ route('pengajuan.index') }}" class="btn btn-outline-secondary btn-sm w-100" style="height: 38px; line-height: 1.7;">Reset Filter</a>
                                </div>
                            @endif
                        </form>
                        <div class="small text-muted mt-2" style="font-size: 0.76rem;">
                            Menampilkan {{ $requests->total() }} pengajuan{{ $filterActive ? ' sesuai filter' : '' }}.
                        </div>
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
                                    @forelse($requests as $req)
                                        <tr>
                                            <td style="white-space: nowrap;">
                                                <div class="font-monospace fw-bold text-primary" style="font-size: 0.78rem; letter-spacing: -0.01em;">{{ $req->request_code }}</div>
                                                @if ($req->is_procurement)
                                                    <span class="badge bg-warning-subtle text-dark border px-1.5 py-0.5 mt-0.5" style="font-size: 0.65rem;">
                                                        Pengadaan (Beli TU)
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $req->type === 'subdit' ? 'bg-info text-white' : 'bg-secondary' }}" style="font-size: 0.68rem; padding: 0.2rem 0.45rem;">
                                                    {{ ucfirst($req->type) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="text-dark fw-semibold" style="font-size: 0.74rem;">{{ $req->created_at->timezone('Asia/Jakarta')->format('d/m/Y') }}</div>
                                                <div class="text-muted" style="font-size: 0.68rem;">{{ $req->created_at->timezone('Asia/Jakarta')->format('H:i') }} WIB</div>
                                            </td>
                                            <td style="white-space: normal; word-break: break-word; overflow-wrap: anywhere;">
                                                <ul class="mb-0 ps-3" style="font-size: 0.75rem; word-break: break-word; white-space: normal;">
                                                    @foreach ($req->details as $detail)
                                                        <li class="mb-1" style="word-break: break-word;">
                                                            <div class="fw-bold text-dark" style="line-height: 1.3;">{{ $detail->item_name }}</div>
                                                            <div class="mt-0.5">
                                                                @if (in_array($req->status, ['approved', 'distributed']))
                                                                    <span class="badge bg-success-subtle text-success border px-1.5 py-0.5" style="font-size: 0.64rem;">
                                                                        Disetujui: {{ $detail->effective_quantity }} {{ $detail->item_unit }}
                                                                    </span>
                                                                @else
                                                                    <span class="text-secondary small" style="font-size: 0.68rem;">({{ $detail->quantity }} {{ $detail->item_unit }})</span>
                                                                @endif
                                                            </div>

                                                            @if ($detail->specification)
                                                                <div class="text-muted fst-italic mt-0.5" style="font-size: 0.68rem; line-height: 1.25;">Spesifikasi: {{ $detail->specification }}</div>
                                                            @endif

                                                            @if ($detail->verification_note)
                                                                <div class="mt-1 p-1 rounded bg-light-subtle text-primary border-start border-2 border-primary ps-1.5" style="font-size: 0.68rem; line-height: 1.3;">
                                                                    <span class="fw-bold">Catatan TU:</span> "{{ $detail->verification_note }}"
                                                                </div>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>

                                                @if ($req->verification_notes && (!isset($detail) || $req->verification_notes !== $detail->verification_note))
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
                                                            @php
                                                                $displayVerifNote = str_replace(
                                                                    'Seluruh barang telah ditambahkan ke Master Data dan kini tersedia di Katalog ATK.',
                                                                    'Barang telah dibeli/tersedia di gudang dan dialokasikan untuk Anda. Siap diserahterimakan.',
                                                                    $req->verification_notes
                                                                );
                                                            @endphp
                                                            <div class="mt-0.5" style="word-break: break-word;">{{ $displayVerifNote }}</div>
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
                                                        'pending' => 'Menunggu Review TU',
                                                        'approved' => $req->is_procurement 
                                                            ? ($req->is_purchased ? 'Siap Diserahkan' : 'Disetujui (Proses Beli)') 
                                                            : 'Disetujui (Siap Diserahkan)',
                                                        'distributed' => 'Selesai Diterima',
                                                        'rejected' => 'Ditolak',
                                                        default => ucfirst($req->status),
                                                    };
                                                    $statusBadgeClass = match ($req->status) {
                                                        'pending' => 'bg-warning text-dark',
                                                        'approved' => ($req->is_procurement && !$req->is_purchased) ? 'bg-info text-white' : 'bg-success text-white',
                                                        'distributed' => 'bg-primary text-white',
                                                        'rejected' => 'bg-danger text-white',
                                                        default => 'bg-secondary text-white',
                                                    };
                                                @endphp
                                                <span class="badge {{ $statusBadgeClass }}" style="font-size: 0.68rem; padding: 0.22rem 0.45rem; white-space: nowrap;" title="{{ $req->status_label }}">
                                                    {{ $shortStatus }}
                                                </span>
                                                @if ($req->is_procurement && $req->purchase_invoice)
                                                    <div class="mt-0.5">
                                                        <span class="badge bg-primary-subtle text-primary border text-truncate d-inline-block px-1.5 py-0.2" style="font-size: 0.64rem; max-width: 110px;" title="Faktur: {{ $req->purchase_invoice }}">
                                                            Faktur: {{ $req->purchase_invoice }}
                                                        </span>
                                                    </div>
                                                @endif
                                                @if ($req->status === 'approved' && (!$req->is_procurement || $req->is_purchased))
                                                    <div class="mt-0.5">
                                                        <span class="badge bg-success-subtle text-success border px-1.5 py-0.2" style="font-size: 0.64rem;">
                                                            Siap Serah Terima &check;
                                                        </span>
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
                                                    <!-- Tombol Detail Modal -->
                                                    <button type="button" class="btn btn-xs btn-outline-primary py-0.5 px-2 fw-semibold"
                                                        data-bs-toggle="modal" data-bs-target="#detailModal{{ $req->id }}"
                                                        style="font-size: 0.72rem; border-radius: 6px;"
                                                        title="Lihat rincian lengkap permintaan">
                                                        Detail
                                                    </button>

                                                    {{-- Tombol Cetak Dokumen Bukti Serah Terima ATK Resmi (Hanya untuk PIC Distribusi / Non-Pegawai) --}}
                                                    @if (in_array($req->status, ['approved', 'distributed']) && !auth()->user()->isPegawai())
                                                        <a href="{{ route('distribusi.bukti', $req->id) }}" target="_blank"
                                                            class="btn btn-xs btn-outline-secondary py-0.5 px-1.5 fw-semibold"
                                                            style="font-size: 0.72rem; border-radius: 6px;"
                                                            title="Cetak Dokumen Resmi Bukti Penyerahan ATK">
                                                            Cetak Bukti
                                                        </a>

                                                        <!-- Tombol Unggah / Status Berkas Dokumen -->
                                                        @if ($req->signed_proof)
                                                            <button type="button" class="btn btn-xs btn-success text-white py-0.5 px-1.5 fw-semibold"
                                                                data-bs-toggle="modal" data-bs-target="#uploadTtdModal{{ $req->id }}"
                                                                style="font-size: 0.72rem; border-radius: 6px;"
                                                                title="Berkas telah diunggah. Klik untuk melihat atau ganti berkas.">
                                                                Upload &check;
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-xs btn-warning text-dark py-0.5 px-1.5 fw-semibold"
                                                                data-bs-toggle="modal" data-bs-target="#uploadTtdModal{{ $req->id }}"
                                                                style="font-size: 0.72rem; border-radius: 6px;"
                                                                title="Unggah berkas dokumen serah terima">
                                                                Upload
                                                            </button>
                                                        @endif
                                                    @endif
                                                </div>

                                                {{-- Modal Detail Permintaan --}}
                                                <div class="modal fade text-start" id="detailModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-light">
                                                                <h5 class="modal-title">Rincian Permintaan ATK</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                                    <div>
                                                                        <strong class="font-monospace fs-6">{{ $req->request_code }}</strong><br>
                                                                        <small class="text-muted">Diajukan: {{ $req->created_at->timezone('Asia/Jakarta')->format('d F Y, H:i') }} WIB</small>
                                                                    </div>
                                                                    <span class="badge {{ $req->status_badge }}">{{ $req->status_label }}</span>
                                                                </div>

                                                                <h6 class="fw-bold mb-2 small text-uppercase text-secondary">Daftar Barang yang Diminta:</h6>
                                                                <ul class="list-group mb-3">
                                                                    @foreach ($req->details as $d)
                                                                        <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                                                            <div>
                                                                                <span class="fw-bold text-dark">{{ $d->item_name }}</span>
                                                                                @if ($d->product)
                                                                                    <small class="text-muted d-block font-monospace">{{ $d->product->code }}</small>
                                                                                @elseif ($d->specification)
                                                                                    <small class="text-muted d-block fst-italic">Spesifikasi: {{ $d->specification }}</small>
                                                                                @endif
                                                                                @if ($d->verification_note)
                                                                                    <small class="text-info d-block fst-italic">Catatan TU: {{ $d->verification_note }}</small>
                                                                                @endif
                                                                            </div>
                                                                            <div class="text-end">
                                                                                <span class="badge bg-secondary rounded-pill">Diminta: {{ $d->quantity }} {{ $d->item_unit }}</span>
                                                                                @if (in_array($req->status, ['approved', 'distributed']))
                                                                                    <span class="badge bg-success rounded-pill ms-1">Disetujui: {{ $d->effective_quantity }} {{ $d->item_unit }}</span>
                                                                                @endif
                                                                            </div>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>

                                                                <div class="card bg-light border-0 p-2.5 mb-2">
                                                                    <small class="text-muted d-block">Keperluan / Keterangan Pemohon:</small>
                                                                    <strong class="text-dark">{{ $req->notes ?? '-' }}</strong>
                                                                </div>

                                                                @if ($req->purchase_invoice)
                                                                    <div class="card bg-success-subtle border border-success-subtle p-2.5 mb-2">
                                                                        <small class="text-success fw-bold d-block">Barang Telah Dibeli / Tersedia di Gudang:</small>
                                                                        <span class="text-dark small">Nomor Faktur: <strong>{{ $req->purchase_invoice }}</strong> ({{ $req->purchase_date ? $req->purchase_date->format('d/m/Y') : '-' }})</span>
                                                                        <div class="mt-1 pt-1 border-top border-success-subtle">
                                                                            <span class="badge bg-success text-white py-1 px-2">
                                                                                Siap Diserahterimakan oleh Petugas Distribusi &check;
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                @if ($req->verification_notes)
                                                                    @php
                                                                        $modalVerifNote = str_replace(
                                                                            'Seluruh barang telah ditambahkan ke Master Data dan kini tersedia di Katalog ATK.',
                                                                            'Barang telah dibeli/tersedia di gudang dan dialokasikan untuk Anda. Siap diserahterimakan.',
                                                                            $req->verification_notes
                                                                        );
                                                                    @endphp
                                                                    <div class="card bg-info-subtle border-0 p-2.5 mb-2">
                                                                        <small class="text-primary fw-bold d-block">Catatan Verifikasi Subbag TU:</small>
                                                                        <span class="text-dark small">{{ $modalVerifNote }}</span>
                                                                    </div>
                                                                @endif

                                                                @if ($req->status === 'distributed')
                                                                    <div class="alert alert-success py-2 mb-0 small">
                                                                        <strong>Telah Diterima:</strong> {{ $req->receiver_name ?? '-' }}<br>
                                                                        <strong>Waktu Penyerahan:</strong> {{ $req->distributed_at ? $req->distributed_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') . ' WIB' : '-' }}<br>
                                                                        @if ($req->distribution_notes)
                                                                            <strong>Catatan Gudang:</strong> {{ $req->distribution_notes }}<br>
                                                                        @endif
                                                                        @if ($req->distribution_proof)
                                                                            <div class="mt-2 pt-2 border-top">
                                                                                <strong class="d-block mb-1">Bukti Foto Penyerahan Barang:</strong>
                                                                                <a href="{{ $req->distribution_proof_url }}" target="_blank" title="Klik untuk memperbesar gambar">
                                                                                    <img src="{{ $req->distribution_proof_url }}" alt="Bukti Serah Terima" class="img-thumbnail rounded border shadow-sm" style="max-height: 140px; max-width: 100%; object-fit: contain;">
                                                                                </a>
                                                                                <div class="text-muted mt-1" style="font-size: 0.72rem;">(Klik foto untuk membuka resolusi penuh)</div>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                @elseif ($req->status === 'rejected')
                                                                    <div class="alert alert-danger py-2 mb-0 small">
                                                                        <strong>Alasan Penolakan:</strong> {{ $req->rejection_reason ?? '-' }}
                                                                    </div>
                                                                @endif

                                                                @if ($req->signed_proof)
                                                                    <div class="card bg-success-subtle border border-success-subtle p-2.5 my-2">
                                                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                                                            <div>
                                                                                <strong class="text-success small d-block">&#10003; Berkas Fisik Telah Diunggah:</strong>
                                                                                <span class="text-muted" style="font-size: 0.72rem;">Diunggah {{ $req->signed_at ? $req->signed_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') : '-' }} WIB @if($req->signedBy) oleh {{ $req->signedBy->name }} @endif</span>
                                                                            </div>
                                                                            <div class="d-flex gap-1">
                                                                                <a href="{{ $req->signed_proof_url }}" target="_blank" class="btn btn-sm btn-success text-white">
                                                                                    Lihat Berkas
                                                                                </a>
                                                                                @if (!auth()->user()->isPegawai())
                                                                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#uploadTtdModal{{ $req->id }}">
                                                                                        Ganti Berkas
                                                                                    </button>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @elseif (in_array($req->status, ['approved', 'distributed']) && !auth()->user()->isPegawai())
                                                                    <div class="card bg-light border p-2.5 my-2">
                                                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                                                            <div>
                                                                                <small class="text-muted d-block">Status Berkas Dokumen:</small>
                                                                                <span class="badge bg-warning text-dark">Belum Diunggah</span>
                                                                            </div>
                                                                            <button type="button" class="btn btn-sm btn-outline-warning text-dark fw-bold" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#uploadTtdModal{{ $req->id }}">
                                                                                + Upload Berkas
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer d-flex justify-content-between flex-wrap gap-2">
                                                                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                                                    {{-- Tombol Cetak Bukti Resmi (Hanya untuk PIC Distribusi / Non-Pegawai) --}}
                                                                    @if (in_array($req->status, ['approved', 'distributed']) && !auth()->user()->isPegawai())
                                                                        <a href="{{ route('distribusi.bukti', $req->id) }}" target="_blank"
                                                                            class="btn btn-primary btn-sm text-white d-inline-flex align-items-center gap-1.5 fw-semibold shadow-sm"
                                                                            title="Cetak Dokumen Bukti Serah Terima ATK Resmi">
                                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                                                                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                                                                <rect x="6" y="14" width="12" height="8"></rect>
                                                                            </svg>
                                                                            Cetak Bukti
                                                                        </a>
                                                                    @endif

                                                                    {{-- Tombol Unggah Berkas Dokumen (Hanya untuk PIC Distribusi / Non-Pegawai) --}}
                                                                    @if (in_array($req->status, ['approved', 'distributed']) && !auth()->user()->isPegawai())
                                                                        <button type="button" class="btn btn-warning btn-sm text-dark fw-bold d-inline-flex align-items-center gap-1"
                                                                            data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#uploadTtdModal{{ $req->id }}">
                                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                                                <polyline points="17 8 12 3 7 8"></polyline>
                                                                                <line x1="12" y1="3" x2="12" y2="15"></line>
                                                                            </svg>
                                                                            {{ $req->signed_proof ? 'Ganti Berkas' : 'Upload Berkas' }}
                                                                        </button>
                                                                    @endif

                                                                    @if ($req->distribution_proof)
                                                                        <a href="{{ route('distribusi.bukti', ['id' => $req->id, 'view' => 'foto']) }}" target="_blank"
                                                                            class="btn btn-outline-info btn-sm d-inline-flex align-items-center gap-1">
                                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                                                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                                                                <polyline points="21 15 16 10 5 21"></polyline>
                                                                            </svg>
                                                                            Foto Dokumentasi
                                                                        </a>
                                                                    @endif
                                                                </div>
                                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Modal Upload Berkas Dokumen (Hanya untuk PIC Distribusi / Non-Pegawai) --}}
                                                @if (!auth()->user()->isPegawai())
                                                <div class="modal fade text-start" id="uploadTtdModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content border-0 shadow">
                                                            <div class="modal-header bg-light">
                                                                <h5 class="modal-title fw-bold text-dark">
                                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1 text-primary">
                                                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                                        <polyline points="14 2 14 8 20 8"></polyline>
                                                                        <line x1="12" y1="18" x2="12" y2="12"></line>
                                                                        <line x1="9" y1="15" x2="15" y2="15"></line>
                                                                    </svg>
                                                                    Unggah Berkas Dokumen Serah Terima
                                                                </h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form action="{{ route('pengajuan.uploadSignedProof', $req->id) }}" method="POST" enctype="multipart/form-data">
                                                                @csrf
                                                                <div class="modal-body p-4">
                                                                    <!-- Informasi Pengajuan -->
                                                                    <div class="card border mb-3 shadow-none" style="background-color: #f8fafc; border-color: #e2e8f0 !important;">
                                                                        <div class="card-body p-3">
                                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                                <span class="small text-muted fw-semibold">Nomor Pengajuan:</span>
                                                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace px-2 py-1 fs-6">{{ $req->request_code }}</span>
                                                                            </div>
                                                                            <p class="small text-secondary mb-0" style="font-size: 0.82rem; line-height: 1.45;">
                                                                                Silakan unggah berkas dokumen fisik serah terima (hasil scan / foto) yang telah ditandatangani.
                                                                            </p>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Status Berkas Terunggah (Jika Sudah Ada) -->
                                                                    @if ($req->signed_proof)
                                                                        <div class="card bg-success-subtle border-success-subtle mb-3 shadow-none">
                                                                            <div class="card-body p-3">
                                                                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                                                                    <div>
                                                                                        <strong class="text-success small d-block">&#10003; Berkas Saat Ini Telah Terunggah:</strong>
                                                                                        <small class="text-muted">Diunggah {{ $req->signed_at ? $req->signed_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') : '-' }} WIB</small>
                                                                                    </div>
                                                                                    <a href="{{ $req->signed_proof_url }}" target="_blank" class="btn btn-sm btn-success text-white">
                                                                                        Buka Berkas
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endif

                                                                    <!-- Area Input Upload Berkas dalam Kotak yang Rapi -->
                                                                    <div class="card border mb-0 shadow-none" style="background-color: #f8fafc; border-color: #e2e8f0 !important;">
                                                                        <div class="card-body p-3">
                                                                            <label class="form-label fw-bold small text-dark mb-1.5">
                                                                                Pilih Berkas Dokumen (PDF / Scan Gambar) <span class="text-danger">*</span>
                                                                            </label>
                                                                            <input type="file" name="signed_proof" class="form-control bg-white mb-2" accept=".pdf,.png,.jpg,.jpeg,.webp" required onchange="validateFileSize(this)">
                                                                            <div class="small text-muted d-flex align-items-center gap-1" style="font-size: 0.78rem;">
                                                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-secondary flex-shrink-0">
                                                                                    <circle cx="12" cy="12" r="10"></circle>
                                                                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                                                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                                                                </svg>
                                                                                <span>Format didukung: <strong class="text-dark">PDF, JPG, JPEG, PNG, WEBP</strong> (Maks. 5 MB).</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer bg-light py-2">
                                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-primary btn-sm fw-bold">
                                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                                                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                                            <polyline points="17 8 12 3 7 8"></polyline>
                                                                            <line x1="12" y1="3" x2="12" y2="15"></line>
                                                                        </svg>
                                                                        {{ $req->signed_proof ? 'Perbarui Berkas' : 'Simpan & Unggah Berkas' }}
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-5">
                                                {{ $filterActive ? 'Tidak ada pengajuan yang sesuai dengan filter Anda.' : 'Belum ada riwayat pengajuan permintaan barang. Silakan buat pengajuan baru.' }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($requests->hasPages())
                            <div class="d-flex justify-content-center justify-content-md-end mt-3">
                                {{ $requests->links('pagination::bootstrap-4') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
@endsection

@push('scripts')
    <script>
        function validateFileSize(input) {
            if (input.files && input.files[0]) {
                const maxBytes = 5 * 1024 * 1024; // 5 MB
                if (input.files[0].size > maxBytes) {
                    alert('Ukuran file terlalu besar! Maksimal ukuran file yang diperbolehkan adalah 5 MB.');
                    input.value = '';
                }
            }
        }

        function dismissModernToast() {
            const toast = document.getElementById('modern-toast-notification');
            if (toast) {
                toast.classList.add('toast-hiding');
                setTimeout(() => {
                    toast.remove();
                }, 350);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const toast = document.getElementById('modern-toast-notification');
            if (toast && toast.parentElement !== document.body) {
                document.body.appendChild(toast);
            }
            const progressBar = document.getElementById('toast-progress-bar');
            
            if (toast && progressBar) {
                const totalDuration = 6000; // 6 detik tampilan
                const intervalTime = 50;
                let elapsed = 0;
                let isPaused = false;

                // Jeda timer saat kursor mouse diarahkan ke toast
                toast.addEventListener('mouseenter', () => isPaused = true);
                toast.addEventListener('mouseleave', () => isPaused = false);

                const timer = setInterval(() => {
                    if (!isPaused) {
                        elapsed += intervalTime;
                        const remainingPercent = Math.max(0, 100 - (elapsed / totalDuration * 100));
                        progressBar.style.width = remainingPercent + '%';

                        if (elapsed >= totalDuration) {
                            clearInterval(timer);
                            dismissModernToast();
                        }
                    }
                }, intervalTime);
            }
        });
    </script>
@endpush
