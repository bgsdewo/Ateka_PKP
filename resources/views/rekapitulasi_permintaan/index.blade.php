@extends('layouts.app')

@section('page-header')
    <div class="iq-navbar-header" style="height: 220px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="flex-wrap d-flex justify-content-between align-items-center text-white">
                        <div>
                            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2" style="background: rgba(255, 255, 255, 0.12); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.18); font-size: 0.72rem; letter-spacing: 0.05em; text-transform: uppercase; font-weight: 700;">
                                <span class="badge-dot badge-dot-primary"></span>
                                <span>Kementerian PKP RI &bull; Subbag Tata Usaha</span>
                            </div>
                            <h1 class="text-white fw-bold mb-1">Rekapitulasi Permintaan ATK</h1>
                            <p class="text-white mb-0" style="opacity: 0.92; max-width: 820px;">
                                Rekapitulasi seluruh usulan permohonan ATK dari Pegawai &amp; Subdirektorat serta pencetakan rekapitulasi kebutuhan kantor.
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

        {{-- 4 Kartu KPI Rekapitulasi --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card h-100 shadow-sm border-0 p-3" style="border-radius: 14px;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Total Usulan Masuk</span>
                        <div class="kpi-icon-squircle" style="background-color: var(--brand-primary-light); color: var(--brand-primary); width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <h2 class="fw-bold text-primary mb-0 fs-3">{{ $totalRequests }}</h2>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">Tiket</span>
                    </div>
                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">{{ $subditCount }} Subdit &bull; {{ $individuCount }} Individu</small>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card h-100 shadow-sm border-0 p-3" style="border-radius: 14px;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Total Barang Diminta</span>
                        <div class="kpi-icon-squircle" style="background-color: rgba(6, 182, 212, 0.12); color: #0891b2; width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line>
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                <line x1="12" y1="22.08" x2="12" y2="12"></line>
                            </svg>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <h2 class="fw-bold text-info mb-0 fs-3">{{ number_format($totalItemTypes) }}</h2>
                        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">Jenis</span>
                    </div>
                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Ragam jenis barang diajukan</small>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card h-100 shadow-sm border-0 p-3" style="border-radius: 14px;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Total Disetujui TU</span>
                        <div class="kpi-icon-squircle" style="background-color: var(--accent-emerald-light); color: var(--accent-emerald); width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <h2 class="fw-bold text-success mb-0 fs-3">{{ $approvedRequestsCount }}</h2>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">Tiket</span>
                    </div>
                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">{{ $subditApprovedCount }} Subdit &bull; {{ $individuApprovedCount }} Individu</small>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card h-100 shadow-sm border-0 p-3" style="border-radius: 14px;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Perlu Verifikasi</span>
                        <div class="kpi-icon-squircle" style="background-color: var(--accent-amber-light); color: var(--accent-amber); width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <h2 class="fw-bold text-warning mb-0 fs-3">{{ $pendingCount }}</h2>
                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">Pending</span>
                    </div>
                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">{{ $subditPendingCount }} Subdit &bull; {{ $individuPendingCount }} Individu</small>
                </div>
            </div>
        </div>

        {{-- Main Card --}}
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 14px;">
            <div class="card-header border-bottom bg-white" style="border-top-left-radius: 14px; border-top-right-radius: 14px; padding: 1.35rem 1.75rem !important;">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h4 class="card-title mb-1 fw-bold text-dark">Rekapitulasi Kebutuhan &amp; Permintaan ATK</h4>
                        <small class="text-secondary">Periode: <strong>{{ $periodLabel }}</strong> &bull; Rekapitulasi seluruh usulan permohonan ATK</small>
                    </div>
                </div>
            </div>

            {{-- Filter Bar Toolbar Luas & Proporsional (Unclipped) --}}
            <div class="px-4 py-3.5 border-bottom" style="background-color: #f8fafc; padding: 1.15rem 1.75rem !important;">
                <form action="{{ route('rekapitulasi-permintaan.index') }}" method="GET" class="d-flex align-items-center flex-wrap gap-2.5">

                    {{-- Filter Tipe --}}
                    <div style="min-width: 145px;">
                        <select name="type" class="form-select form-select-modern form-select-sm" onchange="this.form.submit()" style="font-size: 0.84rem; height: 38px; border-color: #cbd5e1;">
                            <option value="">Semua Tipe</option>
                            <option value="subdit" {{ ($typeFilter ?? '') === 'subdit' ? 'selected' : '' }}>🏢 Subdit</option>
                            <option value="individu" {{ ($typeFilter ?? '') === 'individu' ? 'selected' : '' }}>👤 Individu</option>
                        </select>
                    </div>

                    {{-- Filter Status --}}
                    <div style="min-width: 175px;">
                        <select name="status" class="form-select form-select-modern form-select-sm" onchange="this.form.submit()" style="font-size: 0.84rem; height: 38px; border-color: #cbd5e1;">
                            <option value="all" {{ empty($statusFilter) || $statusFilter === 'all' ? 'selected' : '' }}>Semua Status</option>
                            <option value="pending" {{ ($statusFilter ?? '') === 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                            <option value="approved" {{ ($statusFilter ?? '') === 'approved' ? 'selected' : '' }}>Disetujui TU</option>
                            <option value="distributed" {{ ($statusFilter ?? '') === 'distributed' ? 'selected' : '' }}>Selesai Diserahkan</option>
                            <option value="rejected" {{ ($statusFilter ?? '') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>

                    {{-- Filter Bulan --}}
                    <div style="min-width: 180px;">
                        <select name="month" class="form-select form-select-modern form-select-sm" onchange="this.form.submit()" style="font-size: 0.84rem; height: 38px; border-color: #cbd5e1;">
                            <option value="all" {{ ($isAllMonths ?? false) ? 'selected' : '' }}>Semua Bulan (Setahun)</option>
                            @php
                                $monthsList = [
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                ];
                            @endphp
                            @foreach ($monthsList as $num => $mName)
                                <option value="{{ $num }}" {{ !($isAllMonths ?? false) && ($selectedMonth ?? null) == $num ? 'selected' : '' }}>
                                    {{ $mName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Tahun --}}
                    <div style="min-width: 135px;">
                        <select name="year" class="form-select form-select-modern form-select-sm" onchange="this.form.submit()" style="font-size: 0.84rem; height: 38px; border-color: #cbd5e1;">
                            <option value="all" {{ empty($selectedYear) ? 'selected' : '' }}>Semua Tahun</option>
                            @for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                                <option value="{{ $y }}" {{ ($selectedYear ?? null) == $y ? 'selected' : '' }}>
                                    Tahun {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- Search Input Group --}}
                    <div class="flex-grow-1" style="min-width: 240px; max-width: 350px;">
                        <div class="input-group input-group-sm" style="height: 38px;">
                            <input type="text" name="search" class="form-control" placeholder="Cari kode / pemohon / barang..."
                                value="{{ $search ?? '' }}" style="font-size: 0.84rem; border-color: #cbd5e1;">
                            <button class="btn btn-primary d-inline-flex align-items-center px-3 shadow-sm fw-semibold" type="submit">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="me-1">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                                Cari
                            </button>
                        </div>
                    </div>

                    {{-- Standalone Reset Button --}}
                    @if (!empty($typeFilter) || !empty($search) || !empty($selectedMonth) || !empty($selectedYear) || ($isAllMonths ?? false) || (!empty($statusFilter) && $statusFilter !== 'all'))
                        <div>
                            <a href="{{ route('rekapitulasi-permintaan.index') }}" 
                               class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1.5 text-nowrap px-3 shadow-none" 
                               style="font-size: 0.84rem; height: 38px; border-color: #cbd5e1;" 
                               title="Reset Filter">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                                    <path d="M3 3v5h5"></path>
                                </svg>
                                Reset
                            </a>
                        </div>
                    @endif
                </form>
            </div>

            <div class="card-body p-4">
                <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="4%">No</th>
                                    <th>Kode & Tanggal</th>
                                    <th>Pemohon & Unit Kerja</th>
                                    <th>Tipe</th>
                                    <th>Rincian Barang (Diminta & Disetujui)</th>
                                    <th>Catatan Pemohon & Verifikasi</th>
                                    <th>Status</th>
                                    <th width="10%" class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($requests as $idx => $req)
                                    <tr>
                                        <td>{{ ($requests->currentPage() - 1) * $requests->perPage() + $idx + 1 }}</td>
                                        <td>
                                            <strong class="font-monospace text-primary">{{ $req->request_code }}</strong><br>
                                            <small class="text-muted">{{ $req->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB</small>
                                        </td>
                                        <td>
                                            <strong class="text-dark">{{ $req->user->name_with_title ?? ($req->user->name ?? '-') }}</strong><br>
                                            <small class="text-muted">{{ $req->user->position ?? ($req->user->subdit_name ?? '-') }}</small>
                                            @if(!empty($req->user->nip))
                                                <br><span class="badge bg-light text-secondary border mt-1" style="font-size: 0.72rem;">NIP: {{ $req->user->nip }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $req->type === 'subdit' ? 'bg-info text-white' : 'bg-secondary text-white' }}">
                                                {{ ucfirst($req->type) }}
                                            </span>
                                            @if ($req->is_procurement)
                                                <br><span class="badge bg-warning text-dark mt-1" style="font-size:0.7rem;">
                                                    Pengadaan (Beli TU)
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <ul class="ps-3 mb-0 small">
                                                @foreach ($req->details as $det)
                                                    <li class="mb-1">
                                                        <strong>{{ $det->item_name }}</strong>:
                                                        <span class="badge bg-light text-dark border">Diminta: {{ $det->quantity }} {{ $det->item_unit }}</span>
                                                        @if (in_array($req->status, ['approved', 'distributed']))
                                                            <span class="badge bg-success-subtle text-success border ms-1">
                                                                Disetujui: {{ $det->effective_quantity }} {{ $det->item_unit }}
                                                            </span>
                                                        @endif
                                                        @if ($det->specification)
                                                            <div class="text-muted fst-italic" style="font-size: 0.72rem;">
                                                                Spesifikasi: {{ $det->specification }}
                                                            </div>
                                                        @endif
                                                        @if ($det->verification_note)
                                                            <div class="text-muted fst-italic" style="font-size: 0.72rem;">
                                                                Catatan: {{ $det->verification_note }}
                                                            </div>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td>
                                            @if ($req->notes)
                                                <div class="small text-dark mb-1">
                                                    <strong>Pemohon:</strong> {{ $req->notes }}
                                                </div>
                                            @endif
                                            @if ($req->verification_notes)
                                                <div class="small text-primary">
                                                    <strong>TU:</strong> {{ $req->verification_notes }}
                                                </div>
                                            @endif
                                            @if (!$req->notes && !$req->verification_notes)
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($req->status === 'pending')
                                                <span class="badge bg-warning text-dark">Menunggu Verifikasi</span>
                                            @elseif ($req->status === 'approved')
                                                <span class="badge bg-success text-white">Disetujui TU</span>
                                                @if ($req->purchase_invoice)
                                                    <br><span class="badge bg-primary-subtle text-primary border mt-1" style="font-size: 0.72rem;" title="Pengadaan Faktur TU">
                                                        Dibeli: {{ $req->purchase_invoice }}
                                                    </span>
                                                @endif
                                            @elseif ($req->status === 'distributed')
                                                <span class="badge bg-primary text-white">Selesai Didistribusikan</span>
                                                @if ($req->purchase_invoice)
                                                    <br><span class="badge bg-info-subtle text-dark border mt-1" style="font-size: 0.72rem;">
                                                        Faktur: {{ $req->purchase_invoice }}
                                                    </span>
                                                @endif
                                            @else
                                                <span class="badge bg-danger text-white">Ditolak</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-1">
                                                <a href="{{ route('rekapitulasi-permintaan.cetak', ['request_id' => $req->id]) }}" 
                                                   target="_blank" 
                                                   class="btn btn-sm btn-primary fw-bold d-inline-flex align-items-center gap-1 shadow-sm"
                                                   title="Cetak Form Rekapan Permintaan {{ $req->request_code }}">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                                        <rect x="6" y="14" width="12" height="8"></rect>
                                                    </svg>
                                                    Cetak
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-info" 
                                                        data-bs-toggle="modal" data-bs-target="#detailModal{{ $req->id }}"
                                                        title="Lihat Rincian Usulan">
                                                    Detail
                                                </button>
                                            </div>

                                            {{-- Modal Detail --}}
                                            <div class="modal fade text-start" id="detailModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-primary text-white">
                                                            <h5 class="modal-title text-white">Detail Usulan {{ $req->request_code }}</h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <div class="row g-2 mb-3 bg-light p-3 rounded border">
                                                                <div class="col-sm-6">
                                                                    <small class="text-muted d-block">Pemohon / Unit Kerja:</small>
                                                                    <strong>{{ $req->user->name_with_title ?? ($req->user->name ?? '-') }} ({{ $req->user->position ?? '-' }})</strong>
                                                                    @if(!empty($req->user->nip))
                                                                        <br><span class="badge bg-light text-secondary border mt-1" style="font-size: 0.72rem;">NIP: {{ $req->user->nip }}</span>
                                                                    @endif
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <small class="text-muted d-block">Waktu Pengajuan:</small>
                                                                    <strong>{{ $req->created_at->format('d F Y, H:i') }} WIB</strong>
                                                                </div>
                                                                @if ($req->verified_at)
                                                                    <div class="col-sm-6">
                                                                        <small class="text-muted d-block">Diverifikasi Oleh:</small>
                                                                        <strong>{{ $req->verifier->name ?? 'Subbag TU' }} ({{ $req->verified_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB)</strong>
                                                                    </div>
                                                                @endif
                                                                @if ($req->purchase_invoice)
                                                                    <div class="col-sm-6">
                                                                        <small class="text-muted d-block">Pengadaan Faktur TU:</small>
                                                                        <strong class="text-primary">{{ $req->purchase_invoice }}</strong>
                                                                        @if ($req->purchase_date)
                                                                            <span class="text-muted">({{ $req->purchase_date->format('d/m/Y') }})</span>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                                @if ($req->verification_notes)
                                                                    <div class="col-12 mt-2">
                                                                        <small class="text-muted d-block">Catatan Verifikasi Subbag TU:</small>
                                                                        <div class="alert alert-info py-1 px-2 mb-0 small">{{ $req->verification_notes }}</div>
                                                                    </div>
                                                                @endif
                                                            </div>

                                                            <h6 class="fw-bold mb-2">Rincian Barang:</h6>
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered table-sm align-middle">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th>Barang</th>
                                                                            <th>Kategori</th>
                                                                            <th class="text-center">Qty Diminta</th>
                                                                            <th class="text-center">Qty Disetujui</th>
                                                                            <th>Catatan</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($req->details as $d)
                                                                            <tr>
                                                                                <td>
                                                                                    <strong>{{ $d->item_name }}</strong>
                                                                                    @if ($d->specification)
                                                                                        <small class="text-muted d-block fst-italic">"{{ $d->specification }}"</small>
                                                                                    @endif
                                                                                </td>
                                                                                <td>{{ $d->product->category->name ?? 'Pengadaan' }}</td>
                                                                                <td class="text-center fw-bold">{{ $d->quantity }} {{ $d->item_unit }}</td>
                                                                                <td class="text-center fw-bold text-success">
                                                                                    {{ in_array($req->status, ['approved', 'distributed']) ? ($d->effective_quantity . ' ' . $d->item_unit) : '-' }}
                                                                                </td>
                                                                                <td class="small">{{ $d->verification_note ?? '-' }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <a href="{{ route('rekapitulasi-permintaan.cetak', ['request_id' => $req->id]) }}" 
                                                               target="_blank" 
                                                               class="btn btn-primary fw-bold d-inline-flex align-items-center gap-1 shadow-sm">
                                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                                                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                                                    <rect x="6" y="14" width="12" height="8"></rect>
                                                                </svg>
                                                                Cetak Form Permintaan
                                                            </a>
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-5">
                                            Tidak ada data usulan permintaan pada filter periode ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($requests->total() > 0)
                    <div class="card-footer bg-white border-top py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2" style="border-bottom-left-radius: 14px; border-bottom-right-radius: 14px;">
                        <div class="text-muted small">
                            Menampilkan <strong>{{ $requests->firstItem() ?? 0 }}</strong> - <strong>{{ $requests->lastItem() ?? 0 }}</strong> dari <strong>{{ $requests->total() }}</strong> usulan permohonan (10 per halaman)
                        </div>
                        @if ($requests->hasPages())
                            <div>
                                {{ $requests->links() }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
    </div>
@endsection
