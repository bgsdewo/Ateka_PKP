@extends('layouts.app')

@section('page-header')
    <div class="iq-navbar-header" style="height: 220px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="flex-wrap d-flex justify-content-between align-items-center text-white">
                        <div>
                            <h1 class="text-white fw-bold mb-1">Koordinasi &amp; Distribusi Barang ATK</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    @php
        $user = auth()->user();
        $isKasubagTU = $user && $user->isKasubagTU();
        $canDistribute = $isKasubagTU || ($user && $user->isPegawaiGudang() && $user->isDistributor());
    @endphp
    <div class="container-fluid content-inner mt-n5 py-0">
        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-2" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    @if (session('print_id') && $isKasubagTU)
                        <a href="{{ route('distribusi.bukti', session('print_id')) }}" target="_blank"
                            class="btn btn-sm btn-dark text-white d-inline-flex align-items-center gap-1 shadow-sm fw-bold">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                <rect x="6" y="14" width="12" height="8"></rect>
                            </svg>
                            Cetak Bukti Serah Terima
                        </a>
                    @endif
                    <button type="button" class="btn-close position-static p-2" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- 4 Kartu KPI PIC Distribusi --}}
        <div class="row g-3 mb-4" id="distribution-metrics">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="kpi-widget-card h-100 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Siap Didistribusikan</span>
                        <div class="kpi-icon-squircle" style="background-color: var(--accent-amber-light); color: var(--accent-amber);">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h2 class="fw-bold text-warning mb-0 fs-3">{{ number_format($totalReady) }}</h2>
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Telah disetujui Kasubag TU</small>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="kpi-widget-card h-100 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Permintaan Subdit</span>
                        <div class="kpi-icon-squircle" style="background-color: #f0f9ff; color: #0284c7;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h2 class="fw-bold text-info mb-0 fs-3">{{ number_format($totalSubditReady) }}</h2>
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Paket kolektif unit kerja</small>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="kpi-widget-card h-100 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Permintaan Pegawai</span>
                        <div class="kpi-icon-squircle" style="background-color: var(--brand-primary-light); color: var(--brand-primary);">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h2 class="fw-bold text-primary mb-0 fs-3">{{ number_format($totalIndividuReady) }}</h2>
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Kebutuhan staf perorangan</small>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="kpi-widget-card h-100 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Selesai Diserahkan</span>
                        <div class="kpi-icon-squircle" style="background-color: var(--accent-emerald-light); color: var(--accent-emerald);">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h2 class="fw-bold text-success mb-0 fs-3">{{ number_format($totalDistributed) }}</h2>
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Sudah diserahterimakan</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pengelompokan Kolektif Subdirektorat (Accordion Rapi) --}}
        <div id="subdit-groups-container">
            @if ($subditGroups->count() > 0)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-info text-white p-2">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                        </svg>
                                    </span>
                                    <div>
                                        <h5 class="mb-0 fw-bold">Pengelompokan Kolektif Permintaan per Subdirektorat</h5>
                                        <small class="text-muted">PIC Gudang dapat menyiapkan dan membundel paket ATK sekaligus per Subdirektorat sebelum diserahkan</small>
                                    </div>
                                </div>
                                <span class="badge bg-primary rounded-pill px-3 py-1">{{ $subditGroups->count() }} Subdit Aktif</span>
                            </div>
                            <div class="card-body p-3">
                                <div class="accordion" id="accordionSubdit">
                                    @foreach ($subditGroups as $subditName => $items)
                                        <div class="accordion-item mb-2 border rounded">
                                            <h2 class="accordion-header" id="headingSubdit{{ $loop->index }}">
                                                <button class="accordion-button collapsed py-2.5" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapseSubdit{{ $loop->index }}" aria-expanded="false"
                                                    aria-controls="collapseSubdit{{ $loop->index }}">
                                                    <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                                        <div>
                                                            <strong class="text-dark">{{ $subditName }}</strong>
                                                            <span class="text-muted small ms-2">({{ $items->count() }} Surat Pengajuan)</span>
                                                        </div>
                                                        <span class="badge bg-warning text-dark">Siap Disiapkan</span>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapseSubdit{{ $loop->index }}" class="accordion-collapse collapse"
                                                aria-labelledby="headingSubdit{{ $loop->index }}" data-bs-parent="#accordionSubdit">
                                                <div class="accordion-body p-3 bg-light">
                                                    <div class="table-responsive bg-white rounded border">
                                                        <table class="table table-sm table-hover mb-0">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Kode Pengajuan</th>
                                                                    <th>Pemohon</th>
                                                                    <th>Rincian Barang yang Diminta</th>
                                                                    <th>Catatan</th>
                                                                    <th class="text-end">Aksi Cepat</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($items as $subItem)
                                                                    <tr>
                                                                        <td class="fw-bold font-monospace">{{ $subItem->request_code }}</td>
                                                                        <td>{{ $subItem->user->name ?? '-' }}</td>
                                                                        <td>
                                                                            <ul class="mb-0 ps-3">
                                                                                @foreach ($subItem->details as $d)
                                                                                    <li class="mb-1">
                                                                                        <strong>{{ $d->item_name }}</strong>
                                                                                        @if ($d->approved_quantity !== null && $d->approved_quantity != $d->quantity)
                                                                                            <span class="badge bg-success-subtle text-success border px-1.5 py-0.5">Disetujui: {{ $d->effective_quantity }} Pcs</span>
                                                                                            <small class="text-muted">(Diminta: {{ $d->quantity }} Pcs)</small>
                                                                                        @else
                                                                                            <strong>({{ $d->effective_quantity }} Pcs)</strong>
                                                                                        @endif

                                                                                        @if ($d->verification_note)
                                                                                            <small class="text-primary d-block fst-italic">Catatan TU: "{{ $d->verification_note }}"</small>
                                                                                        @endif
                                                                                    </li>
                                                                                @endforeach
                                                                            </ul>
                                                                            @if ($subItem->verification_notes)
                                                                                <small class="text-primary d-block fst-italic mt-1">Catatan TU: {{ $subItem->verification_notes }}</small>
                                                                            @endif
                                                                        </td>
                                                                        <td><small class="text-muted">{{ $subItem->notes ?? '-' }}</small></td>
                                                                        <td class="text-end text-nowrap">
                                                                            @if ($isKasubagTU)
                                                                                <a href="{{ route('distribusi.bukti', $subItem->id) }}" target="_blank"
                                                                                    class="btn btn-xs btn-outline-secondary py-1 px-2">
                                                                                    Cetak Tanda Terima
                                                                                </a>
                                                                            @endif
                                                                            @if ($canDistribute)
                                                                                <button type="button" class="btn btn-xs btn-success py-1 px-2"
                                                                                    data-bs-toggle="modal" data-bs-target="#handoverModal{{ $subItem->id }}">
                                                                                    Serahkan
                                                                                </button>
                                                                            @else
                                                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-0.5" style="font-size: 0.68rem;">
                                                                                    Siap Diserahkan
                                                                                </span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Tabel Utama Distribusi Barang --}}
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <form id="distribusi-filter-form" action="{{ route('distribusi.index') }}" method="GET">
                        {{-- 1. Card Header: Judul di Kiri, Kotak Pencarian di Kanan --}}
                        <div class="card-header border-bottom py-2.5 px-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h5 class="card-title mb-0 fw-bold" style="font-size: 1.05rem;">Daftar Penyerahan Barang ke Pemohon</h5>
                            </div>

                            {{-- Input Pencarian di Sisi Kanan Header --}}
                            <div style="min-width: 220px; max-width: 320px;" class="flex-grow-1 flex-sm-grow-0">
                                <div class="input-group input-group-sm" style="height: 32px;">
                                    <input type="text" id="distribusi-search" name="search" class="form-control form-control-sm" placeholder="Cari tiket / pemohon / barang..."
                                        value="{{ $search ?? '' }}" autocomplete="off" style="height: 32px; font-size: 0.8rem; border-color: #cbd5e1;">
                                    <button class="btn btn-outline-secondary" type="button" id="btn-clear-search" style="{{ empty($search) ? 'display: none;' : '' }}; height: 32px; border-color: #cbd5e1; padding: 0 0.5rem;" title="Hapus pencarian">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </button>
                                    <button class="btn btn-primary d-inline-flex align-items-center px-2.5 shadow-sm" id="btn-distribusi-search" type="submit" style="height: 32px;" title="Cari">
                                        <span id="search-spinner" class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                                        <svg id="search-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                        </svg>
                                        <span class="ms-1 fw-semibold d-none d-sm-inline" style="font-size: 0.8rem;">Cari</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- 2. Filter Bar: Dropdown Teratur dan Rapi dalam 1 Baris --}}
                        <div class="px-3 py-2 bg-light border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <div class="d-flex align-items-center gap-1 text-secondary me-1" style="font-size: 0.78rem; font-weight: 600;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                                    </svg>
                                    <span>Filter:</span>
                                </div>

                                {{-- Filter Status --}}
                                <div style="min-width: 125px;">
                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="height: 32px; font-size: 0.8rem; border-color: #cbd5e1; padding-top: 0.2rem; padding-bottom: 0.2rem;">
                                        <option value="all" {{ ($filterStatus ?? 'all') === 'all' ? 'selected' : '' }}>Semua Status</option>
                                        <option value="ready" {{ ($filterStatus ?? '') === 'ready' ? 'selected' : '' }}>Siap Distribusi</option>
                                        <option value="distributed" {{ ($filterStatus ?? '') === 'distributed' ? 'selected' : '' }}>Selesai Diserahkan</option>
                                    </select>
                                </div>

                                {{-- Filter Tipe --}}
                                <div style="min-width: 115px;">
                                    <select name="type" class="form-select form-select-sm" onchange="this.form.submit()" style="height: 32px; font-size: 0.8rem; border-color: #cbd5e1; padding-top: 0.2rem; padding-bottom: 0.2rem;">
                                        <option value="">Semua Tipe</option>
                                        <option value="subdit" {{ ($filterType ?? '') === 'subdit' ? 'selected' : '' }}>Subdit (Kolektif)</option>
                                        <option value="individu" {{ ($filterType ?? '') === 'individu' ? 'selected' : '' }}>Individu (Pegawai)</option>
                                    </select>
                                </div>

                                {{-- Filter Periode Bulan --}}
                                <div style="min-width: 130px;">
                                    <select name="month" class="form-select form-select-sm" onchange="this.form.submit()" style="height: 32px; font-size: 0.8rem; border-color: #cbd5e1; padding-top: 0.2rem; padding-bottom: 0.2rem;">
                                        <option value="all" {{ ($isAllMonths ?? false) ? 'selected' : '' }}>Semua Bulan</option>
                                        @php
                                            $allMonths = [
                                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                                                 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                            ];
                                        @endphp
                                        @foreach ($allMonths as $num => $name)
                                            <option value="{{ $num }}" {{ ((!($isAllMonths ?? false)) && (($selectedMonth ?? null) == $num)) ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Filter Periode Tahun --}}
                                <div style="min-width: 105px;">
                                    <select name="year" class="form-select form-select-sm" onchange="this.form.submit()" style="height: 32px; font-size: 0.8rem; border-color: #cbd5e1; padding-top: 0.2rem; padding-bottom: 0.2rem;">
                                        <option value="all" {{ empty($selectedYear) ? 'selected' : '' }}>Semua Tahun</option>
                                        @for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                                            <option value="{{ $y }}" {{ (($selectedYear ?? null) == $y) ? 'selected' : '' }}>
                                                Tahun {{ $y }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>

                                {{-- Reset Button --}}
                                <div id="distribusi-reset-container">
                                    @if (!empty($filterType) || (($filterStatus ?? 'all') !== 'all') || !empty($search) || !empty($selectedMonth) || !empty($selectedYear) || ($isAllMonths ?? false))
                                        <a href="{{ route('distribusi.index') }}" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1 px-2.5 shadow-none" style="height: 32px; font-size: 0.8rem; border-color: #fca5a5; white-space: nowrap;" title="Reset Filter">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="1 4 1 10 7 10"></polyline>
                                                <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
                                            </svg>
                                            <span>Reset Filter</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="card-body p-3" id="table-container">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th width="4%">No</th>
                                        <th style="white-space: nowrap;">Kode Pengajuan</th>
                                        <th>Tipe & Pemohon</th>
                                        <th>Daftar Barang & Qty</th>
                                        <th style="white-space: nowrap;">Status Distribusi</th>
                                        <th style="white-space: nowrap; min-width: 170px;">Waktu / Penerima</th>
                                        <th style="white-space: nowrap; min-width: 100px;" class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="distribusi-table-body">
                                    @forelse ($requests as $index => $req)
                                        <tr class="distribusi-row" data-search-text="{{ strtolower($req->request_code . ' ' . ($req->user->name ?? '') . ' ' . ($req->user->position ?? '') . ' ' . ($req->receiver_name ?? '') . ' ' . ($req->distribution_notes ?? '') . ' ' . $req->type . ' ' . $req->status . ' ' . $req->details->pluck('product.name')->join(' ') . ' ' . $req->details->pluck('product.code')->join(' ')) }}">
                                            <td>{{ ($requests->currentPage() - 1) * $requests->perPage() + $index + 1 }}</td>
                                            <td>
                                                <strong class="font-monospace text-primary">{{ $req->request_code }}</strong><br>
                                                <small class="text-muted">{{ $req->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB</small>
                                            </td>
                                            <td>
                                                <strong>{{ $req->user->name ?? '-' }}</strong><br>
                                                <small class="text-muted d-block">{{ $req->user->position ?? '-' }}</small>
                                                @if ($req->type === 'subdit')
                                                    <span class="badge bg-info-subtle text-info border px-2 py-0.5 rounded-pill" style="font-size: 0.72rem;">
                                                        Kolektif Subdit
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-0.5 rounded-pill" style="font-size: 0.72rem;">
                                                        Perorangan
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <ul class="mb-0 ps-3 small">
                                                    @foreach ($req->details as $detail)
                                                        <li class="mb-1">
                                                            <strong>{{ $detail->item_name }}</strong>
                                                            @if ($detail->approved_quantity !== null && $detail->approved_quantity != $detail->quantity)
                                                                <span class="badge bg-success-subtle text-success border px-1.5 py-0.5">Disetujui: {{ $detail->effective_quantity }} Pcs</span>
                                                                <small class="text-muted">(Diminta: {{ $detail->quantity }} Pcs)</small>
                                                            @else
                                                                (<strong>{{ $detail->effective_quantity }} Pcs</strong>)
                                                            @endif

                                                            @if ($detail->verification_note)
                                                                <small class="text-primary d-block fst-italic">Catatan TU: "{{ $detail->verification_note }}"</small>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                                @if ($req->verification_notes)
                                                    <small class="text-primary d-block fst-italic mt-1">Catatan TU: {{ $req->verification_notes }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($req->status === 'approved')
                                                    <span class="badge bg-warning text-dark">
                                                        Siap Diambil / Diserahkan
                                                    </span>
                                                @elseif ($req->status === 'distributed')
                                                    <span class="badge bg-success">
                                                        Telah Diserahterimakan
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($req->status) }}</span>
                                                @endif
                                            </td>
                                             <td>
                                                @if ($req->status === 'distributed')
                                                    <strong class="text-dark small">{{ $req->receiver_name ?? ($req->user->name ?? '-') }}</strong><br>
                                                    <small class="text-muted">{{ $req->distributed_at ? $req->distributed_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') . ' WIB' : '-' }}</small>
                                                    @if ($req->distribution_notes)
                                                        <br><small class="text-secondary fst-italic">"{{ $req->distribution_notes }}"</small>
                                                    @endif
                                                    <div class="mt-1 d-flex align-items-center gap-1 flex-wrap">
                                                        @if ($req->distribution_proof)
                                                            <button type="button" class="btn btn-xs btn-outline-info rounded-pill px-2 py-0.5 d-inline-flex align-items-center gap-1 shadow-none"
                                                                style="font-size: 0.72rem; white-space: nowrap; line-height: 1.3;"
                                                                data-bs-toggle="modal" data-bs-target="#photoModal{{ $req->id }}"
                                                                title="Lihat foto bukti serah terima">
                                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                                                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                                                    <polyline points="21 15 16 10 5 21"></polyline>
                                                                </svg>
                                                                <span>Lihat Bukti Foto</span>
                                                            </button>
                                                        @elseif ($canDistribute)
                                                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-0.5 d-inline-flex align-items-center gap-1 shadow-none"
                                                                style="font-size: 0.72rem; white-space: nowrap; line-height: 1.3;"
                                                                data-bs-toggle="modal" data-bs-target="#uploadModal{{ $req->id }}"
                                                                title="Upload foto bukti dokumentasi serah terima">
                                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                                    <polyline points="17 8 12 3 7 8"></polyline>
                                                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                                                </svg>
                                                                <span>+ Upload Bukti Foto</span>
                                                            </button>
                                                        @endif
                                                        @if ($req->signed_proof)
                                                            <a href="{{ $req->signed_proof_url }}" target="_blank" download class="btn btn-xs btn-success text-white rounded-pill px-2 py-0.5 d-inline-flex align-items-center gap-1 shadow-none"
                                                                style="font-size: 0.72rem; white-space: nowrap; line-height: 1.3;"
                                                                title="Download Berkas Serah Terima yang diunggah PIC / Pemohon">
                                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                                    <polyline points="7 10 12 15 17 10"></polyline>
                                                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                                                </svg>
                                                                <span>Download Berkas</span>
                                                            </a>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted small">Menunggu pengambilan</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end gap-1 flex-wrap">
                                                    @if ($isKasubagTU)
                                                        <!-- Tombol Cetak Tanda Terima / Surat Jalan (Hanya Kasubag TU) -->
                                                        <a href="{{ route('distribusi.bukti', $req->id) }}" target="_blank"
                                                            class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1"
                                                            title="Cetak Bukti Penyerahan Barang">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                                                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                                                <rect x="6" y="14" width="12" height="8"></rect>
                                                            </svg>
                                                            Cetak Bukti
                                                        </a>
                                                    @endif

                                                    @if ($req->status === 'approved')
                                                        @if ($canDistribute)
                                                            <!-- Tombol Serahkan Barang (Buka Modal Handover) -->
                                                            <button type="button" class="btn btn-sm btn-success d-inline-flex align-items-center gap-1"
                                                                data-bs-toggle="modal" data-bs-target="#handoverModal{{ $req->id }}">
                                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                                </svg>
                                                                Serahkan
                                                            </button>
                                                        @else
                                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 small">
                                                                Siap Diserahkan
                                                            </span>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-light text-secondary border px-2 py-1 small">
                                                            Selesai
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="table-initial-empty-row">
                                            <td colspan="7" class="text-center py-5 text-muted">
                                                Tidak ada data distribusi yang sesuai kriteria filter.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($requests->hasPages())
                            <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2 pt-3 border-top">
                                <div class="text-muted small">
                                    Menampilkan <strong>{{ $requests->firstItem() }}</strong> - <strong>{{ $requests->lastItem() }}</strong> dari <strong>{{ $requests->total() }}</strong> data penyerahan
                                </div>
                                <div>
                                    {{ $requests->links() }}
                                </div>
                            </div>
                        @endif

                        {{-- KUMPULAN MODAL (DI LUAR TABEL AGAR POSISI TIDAK TERPENGARUH KELOMPOK TABEL & TERTATA RAPI DI TENGAH LAYAR) --}}
                        <div id="distribusi-modals-wrapper">
                            @foreach ($requests as $req)
                                {{-- 1. Modal Konfirmasi Penyerahan Barang --}}
                                @if ($req->status === 'approved' && $canDistribute)
                                    <div class="modal fade" id="handoverModal{{ $req->id }}" tabindex="-1" aria-labelledby="handoverModalLabel{{ $req->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" style="max-width: 460px;">
                                            <form action="{{ route('distribusi.handover', $req->id) }}" method="POST" enctype="multipart/form-data" class="w-100">
                                                @csrf
                                                <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden text-start">
                                                    <div class="modal-header bg-success text-white px-3 py-2.5 border-0">
                                                        <h5 class="modal-title text-white d-flex align-items-center gap-2 fw-bold" id="handoverModalLabel{{ $req->id }}" style="font-size: 0.95rem;">
                                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                                            </svg>
                                                            Konfirmasi Penyerahan ATK
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-3">
                                                        {{-- Alert Info Pengajuan --}}
                                                        <div class="alert alert-light border rounded-2 mb-2.5 p-2.5">
                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                <strong class="font-monospace text-primary" style="font-size: 0.85rem;">{{ $req->request_code }}</strong>
                                                                <span class="badge {{ $req->type === 'subdit' || ($req->user && $req->user->isPicDistribusi()) ? 'bg-info-subtle text-info border border-info' : 'bg-secondary-subtle text-secondary border border-secondary' }} rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">
                                                                    {{ $req->type === 'subdit' || ($req->user && $req->user->isPicDistribusi()) ? 'Subdit' : 'Individu' }}
                                                                </span>
                                                            </div>
                                                            <div class="text-muted" style="font-size: 0.78rem;">
                                                                Pemohon: <strong class="text-dark">{{ $req->user->name_with_title ?: ($req->user->name ?? '-') }}</strong> 
                                                                ({{ $req->user->position ?? ($req->type === 'subdit' ? 'Perwakilan Subdirektorat' : 'Pegawai') }})
                                                            </div>
                                                        </div>

                                                        {{-- Petugas Gudang yang Menyerahkan --}}
                                                        @php
                                                            $assignedDist = $defaultDistributor ?? auth()->user();
                                                        @endphp
                                                        <div class="mb-2.5">
                                                            <label class="form-label fw-bold text-dark text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.03em;">
                                                                Petugas Gudang yang Menyerahkan
                                                            </label>
                                                            <div class="p-2 border rounded-2 bg-light d-flex align-items-center justify-content-between">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px; min-width: 32px;">
                                                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                                            <circle cx="12" cy="7" r="4"></circle>
                                                                        </svg>
                                                                    </div>
                                                                    <div>
                                                                        <strong class="text-dark d-block" style="font-size: 0.84rem;">
                                                                            {{ $assignedDist->name_with_title ?: $assignedDist->name }}
                                                                        </strong>
                                                                        <small class="text-muted d-block" style="font-size: 0.72rem;">{{ $assignedDist->position ?: 'Petugas Distribusi Gudang' }}</small>
                                                                    </div>
                                                                </div>
                                                                <span class="badge bg-success-subtle text-success border border-success px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">
                                                                    Ditunjuk
                                                                </span>
                                                            </div>
                                                            <input type="hidden" name="distributed_by" value="{{ $assignedDist->id ?? auth()->id() }}">
                                                        </div>

                                                        {{-- Nama Penerima Fisik --}}
                                                        <div class="mb-2.5">
                                                            <label class="form-label fw-bold text-dark text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.03em;">
                                                                Nama Penerima Barang Fisik <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="text" name="receiver_name" class="form-control form-control-sm"
                                                                value="{{ $req->receiver_name ?? ($req->user->name ?? '') }}" required
                                                                placeholder="Nama yang mengambil barang fisik" style="font-size: 0.8125rem; min-height: 32px;">
                                                            <small class="text-muted d-block mt-0.5" style="font-size: 0.72rem;">
                                                                Bisa nama pemohon langsung atau perwakilan yang mengambil barang.
                                                            </small>
                                                        </div>

                                                        {{-- Foto Bukti Penyerahan --}}
                                                        <div class="mb-2.5">
                                                            <label class="form-label fw-bold text-dark text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.03em;">
                                                                Foto Bukti Penyerahan (Dokumentasi)
                                                            </label>
                                                            <input type="file" name="distribution_proof" class="form-control form-control-sm" accept="image/jpeg,image/png,image/jpg,image/webp"
                                                                onchange="previewProofImage(this, 'proofPreview{{ $req->id }}', 'proofPreviewWrap{{ $req->id }}')" style="font-size: 0.8125rem;">
                                                            <small class="text-muted d-block mt-0.5" style="font-size: 0.72rem;">
                                                                Unggah foto serah terima barang (JPG, JPEG, PNG, WEBP, maks 5MB).
                                                            </small>
                                                            <div id="proofPreviewWrap{{ $req->id }}" class="mt-2 text-center p-2 border rounded bg-light d-none">
                                                                <img id="proofPreview{{ $req->id }}" src="" alt="Pratinjau Foto Bukti" class="img-fluid rounded border shadow-sm" style="max-height: 140px; object-fit: contain;">
                                                            </div>
                                                        </div>

                                                        {{-- Catatan Penyerahan --}}
                                                        <div class="mb-0">
                                                            <label class="form-label fw-bold text-dark text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.03em;">
                                                                Catatan Penyerahan (Opsional)
                                                            </label>
                                                            <textarea name="distribution_notes" class="form-control form-control-sm" rows="2"
                                                                placeholder="Barang diserahkan lengkap dalam kondisi baik." style="font-size: 0.8125rem;">{{ $req->distribution_notes }}</textarea>
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer bg-light px-3 py-2 border-top d-flex justify-content-end gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal" style="font-size: 0.8rem; height: 32px;">Batal</button>
                                                        <button type="submit" class="btn btn-sm btn-success fw-bold px-3 shadow-sm" style="font-size: 0.8rem; height: 32px;">
                                                            Simpan &amp; Tandai Diserahkan
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif

                                {{-- 2. Modal Pratinjau Foto Bukti Serah Terima --}}
                                @if ($req->distribution_proof)
                                    <div class="modal fade" id="photoModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden text-start">
                                                <div class="modal-header bg-primary text-white px-3 py-2.5 border-0">
                                                    <h5 class="modal-title text-white d-flex align-items-center gap-2 fw-bold" style="font-size: 0.95rem;">
                                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                                            <polyline points="21 15 16 10 5 21"></polyline>
                                                        </svg>
                                                        Foto Bukti Serah Terima ({{ $req->request_code }})
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-3 text-center">
                                                    <div class="p-2 border rounded bg-light mb-2.5">
                                                        <img src="{{ $req->distribution_proof_url }}" alt="Bukti Penyerahan {{ $req->request_code }}"
                                                            class="img-fluid rounded border shadow-sm w-100" style="max-height: 380px; object-fit: contain;">
                                                    </div>
                                                    <div class="alert alert-light border text-start small mb-0 p-2.5">
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <strong>Penerima:</strong> {{ $req->receiver_name ?? ($req->user->name ?? '-') }}<br>
                                                                <strong>Diserahkan oleh:</strong> {{ $req->distributor->name ?? 'Petugas Gudang' }}
                                                            </div>
                                                            <div class="col-sm-6 text-sm-end">
                                                                <strong>Waktu:</strong> {{ $req->distributed_at ? $req->distributed_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') . ' WIB' : '-' }}<br>
                                                                @if ($req->distribution_notes)
                                                                    <span class="fst-italic text-secondary">"{{ $req->distribution_notes }}"</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light px-3 py-2 border-top d-flex justify-content-between">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#uploadModal{{ $req->id }}" style="font-size: 0.8rem; height: 32px;">
                                                        Ganti / Perbarui Foto
                                                    </button>
                                                    <div class="d-flex gap-2">
                                                        <a href="{{ $req->distribution_proof_url }}" target="_blank" class="btn btn-sm btn-outline-primary" style="font-size: 0.8rem; height: 32px; display: inline-flex; align-items: center;">
                                                            Buka Ukuran Penuh
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal" style="font-size: 0.8rem; height: 32px;">Tutup</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- 3. Modal Upload / Perbarui Foto Bukti --}}
                                @if ($canDistribute)
                                    <div class="modal fade" id="uploadModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
                                            <form action="{{ route('distribusi.uploadProof', $req->id) }}" method="POST" enctype="multipart/form-data" class="w-100">
                                                @csrf
                                                <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden text-start">
                                                    <div class="modal-header bg-info text-white px-3 py-2.5 border-0">
                                                        <h5 class="modal-title text-white d-flex align-items-center gap-2 fw-bold" style="font-size: 0.95rem;">
                                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                                <polyline points="17 8 12 3 7 8"></polyline>
                                                                <line x1="12" y1="3" x2="12" y2="15"></line>
                                                            </svg>
                                                            Upload Foto Bukti Penyerahan
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body p-3">
                                                        <div class="alert alert-light border rounded-2 mb-2.5 p-2.5">
                                                            <strong class="font-monospace text-primary" style="font-size: 0.85rem;">{{ $req->request_code }}</strong><br>
                                                            <small class="text-muted" style="font-size: 0.78rem;">Penerima: <strong>{{ $req->receiver_name ?? ($req->user->name ?? '-') }}</strong></small>
                                                        </div>
                                                        <div class="mb-0">
                                                            <label class="form-label fw-bold text-dark text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.03em;">
                                                                Pilih File Foto Bukti <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="file" name="distribution_proof" class="form-control form-control-sm" accept="image/jpeg,image/png,image/jpg,image/webp" required
                                                                onchange="previewProofImage(this, 'proofReuploadPreview{{ $req->id }}', 'proofReuploadPreviewWrap{{ $req->id }}')" style="font-size: 0.8125rem;">
                                                            <small class="text-muted d-block mt-0.5" style="font-size: 0.72rem;">
                                                                Format: JPG, JPEG, PNG, WEBP (maksimal 5 MB).
                                                            </small>
                                                            <div id="proofReuploadPreviewWrap{{ $req->id }}" class="mt-2 text-center p-2 border rounded bg-light d-none">
                                                                <img id="proofReuploadPreview{{ $req->id }}" src="" alt="Pratinjau Foto" class="img-fluid rounded border shadow-sm" style="max-height: 140px; object-fit: contain;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light px-3 py-2 border-top d-flex justify-content-end gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal" style="font-size: 0.8rem; height: 32px;">Batal</button>
                                                        <button type="submit" class="btn btn-sm btn-info text-white fw-bold px-3 shadow-sm" style="font-size: 0.8rem; height: 32px;">Simpan Foto Bukti</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function previewProofImage(input, previewImgId, previewWrapId) {
    const wrap = document.getElementById(previewWrapId);
    const img = document.getElementById(previewImgId);
    if (!wrap || !img) return;

    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran file terlalu besar! Maksimal 5 MB.');
            input.value = '';
            wrap.classList.add('d-none');
            img.src = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            wrap.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    } else {
        wrap.classList.add('d-none');
        img.src = '';
    }
}
window.previewProofImage = previewProofImage;

document.addEventListener('DOMContentLoaded', function() {
    // Pastikan posisi popup modal selalu tepat di tengah layar viewport saat dibuka
    document.addEventListener('show.bs.modal', function (event) {
        const modal = event.target;
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    });

    const searchInput = document.getElementById('distribusi-search');
    const clearBtn = document.getElementById('btn-clear-search');
    const filterForm = document.getElementById('distribusi-filter-form');
    const searchSpinner = document.getElementById('search-spinner');
    const searchIcon = document.getElementById('search-icon');
    let debounceTimer = null;
    let abortCtrl = null;

    function escapeHtml(str) {
        if (!str) return '';
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    // 1. Instant local row filtering (0ms)
    function filterRowsLocally(keyword) {
        const rows = document.querySelectorAll('#distribusi-table-body tr.distribusi-row');
        let emptyRow = document.getElementById('no-local-match-row');
        const initialEmptyRow = document.getElementById('table-initial-empty-row');
        
        if (initialEmptyRow) {
            return;
        }

        if (!keyword) {
            rows.forEach(r => r.style.display = '');
            if (emptyRow) emptyRow.remove();
            return;
        }

        let visibleCount = 0;
        rows.forEach(r => {
            const rowText = (r.getAttribute('data-search-text') || r.innerText).toLowerCase();
            if (rowText.includes(keyword)) {
                r.style.display = '';
                visibleCount++;
            } else {
                r.style.display = 'none';
            }
        });

        if (visibleCount === 0) {
            if (!emptyRow) {
                const tbody = document.getElementById('distribusi-table-body');
                if (tbody) {
                    emptyRow = document.createElement('tr');
                    emptyRow.id = 'no-local-match-row';
                    emptyRow.innerHTML = `
                        <td colspan="7" class="text-center text-muted py-4">
                            <div class="d-inline-flex align-items-center gap-2">
                                <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                                <span>Mencari "<strong>${escapeHtml(keyword)}</strong>"...</span>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(emptyRow);
                }
            }
        } else {
            if (emptyRow) emptyRow.remove();
        }
    }

    // 2. Debounced background AJAX sync to server
    function performServerSearch(targetUrl = null) {
        let url = targetUrl;
        if (!url && filterForm) {
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData);
            url = filterForm.action + '?' + params.toString();
        }

        if (!url) return;

        if (abortCtrl) {
            abortCtrl.abort();
        }
        abortCtrl = new AbortController();

        if (searchSpinner) searchSpinner.classList.remove('d-none');
        if (searchIcon) searchIcon.classList.add('d-none');

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            signal: abortCtrl.signal
        })
        .then(response => {
            if (!response.ok) throw new Error('Response error: ' + response.status);
            return response.text();
        })
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            const newTable = doc.getElementById('table-container');
            const currentTable = document.getElementById('table-container');
            if (newTable && currentTable) {
                currentTable.innerHTML = newTable.innerHTML;
            }

            const newTotalBadge = doc.getElementById('distribution-total-badge');
            const currentTotalBadge = document.getElementById('distribution-total-badge');
            if (newTotalBadge && currentTotalBadge) {
                currentTotalBadge.innerHTML = newTotalBadge.innerHTML;
            }

            const newMetrics = doc.getElementById('distribution-metrics');
            const currentMetrics = document.getElementById('distribution-metrics');
            if (newMetrics && currentMetrics) {
                currentMetrics.innerHTML = newMetrics.innerHTML;
            }

            const newReset = doc.getElementById('distribusi-reset-container');
            const currentReset = document.getElementById('distribusi-reset-container');
            if (newReset && currentReset) {
                currentReset.innerHTML = newReset.innerHTML;
            }

            const newSubditGroups = doc.getElementById('subdit-groups-container');
            const currentSubditGroups = document.getElementById('subdit-groups-container');
            if (newSubditGroups && currentSubditGroups) {
                currentSubditGroups.innerHTML = newSubditGroups.innerHTML;
            }

            // Sync URL in address bar without reload
            window.history.replaceState(null, '', url);

            // Re-apply local filter if user kept typing while AJAX was in-flight
            if (searchInput) {
                const currentVal = searchInput.value.trim().toLowerCase();
                let searchedVal = '';
                try {
                    const urlParams = new URL(url, window.location.origin).searchParams;
                    searchedVal = (urlParams.get('search') || '').trim().toLowerCase();
                } catch (e) {}

                if (currentVal !== searchedVal) {
                    filterRowsLocally(currentVal);
                }
            }
        })
        .catch(err => {
            if (err.name !== 'AbortError') {
                console.error('Error saat sinkronisasi pencarian distribusi:', err);
            }
        })
        .finally(() => {
            if (searchSpinner) searchSpinner.classList.add('d-none');
            if (searchIcon) searchIcon.classList.remove('d-none');
        });
    }

    if (searchInput) {
        // As-you-type input handler
        searchInput.addEventListener('input', function(e) {
            const val = e.target.value;
            const kw = val.trim().toLowerCase();

            // Toggle clear button
            if (clearBtn) {
                clearBtn.style.display = val.length > 0 ? '' : 'none';
            }

            // Instant local filter (0ms delay)
            filterRowsLocally(kw);

            // Debounced server sync (350ms)
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                performServerSearch();
            }, 350);
        });

        // Clear button click
        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                this.style.display = 'none';
                searchInput.focus();
                filterRowsLocally('');
                clearTimeout(debounceTimer);
                performServerSearch();
            });
        }
    }

    // Intercept form submit (Enter key or Cari button click)
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            clearTimeout(debounceTimer);
            performServerSearch();
        });

        // Dynamic change for dropdown selects (Status, Tipe, Bulan, Tahun)
        filterForm.querySelectorAll('select').forEach(select => {
            select.onchange = function(e) {
                e.preventDefault();
                clearTimeout(debounceTimer);
                performServerSearch();
            };
        });
    }

    // Intercept pagination clicks inside table container
    const tableContainer = document.getElementById('table-container');
    if (tableContainer) {
        tableContainer.addEventListener('click', function(e) {
            const pageLink = e.target.closest('.pagination a');
            if (pageLink && pageLink.href) {
                e.preventDefault();
                performServerSearch(pageLink.href);
            }
        });
    }
});
</script>
@endpush
