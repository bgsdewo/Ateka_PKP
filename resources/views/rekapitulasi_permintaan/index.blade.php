@extends('layouts.app')

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
    <div class="iq-navbar-header" style="height: 215px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="flex-wrap d-flex justify-content-between align-items-center text-white">
                        <div>
                            <h1 class="text-white fw-bold mb-1">Rekapitulasi Permintaan ATK (Subbag TU)</h1>
                            <p class="text-white mb-0" style="opacity: 0.9;">
                                Rekapitulasi seluruh usulan permohonan ATK dari Pegawai & Subdirektorat serta pencetakan rekapitulasi kebutuhan kantor.
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
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-secondary small fw-bold text-uppercase">Total Usulan Masuk</span>
                            <span class="badge bg-primary text-white rounded-pill">Tiket</span>
                        </div>
                        <h2 class="fw-bold text-primary mb-0">{{ $totalRequests }}</h2>
                        <small class="text-muted">{{ $subditCount }} Subdit &bull; {{ $individuCount }} Individu</small>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-secondary small fw-bold text-uppercase">Total Barang Diminta</span>
                            <span class="badge bg-info text-white rounded-pill">Jenis</span>
                        </div>
                        <h2 class="fw-bold text-info mb-0">{{ number_format($totalItemTypes) }}</h2>
                        <small class="text-muted">Ragam jenis barang diajukan</small>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-secondary small fw-bold text-uppercase">Total Disetujui TU</span>
                            <span class="badge bg-success text-white rounded-pill">Tiket</span>
                        </div>
                        <h2 class="fw-bold text-success mb-0">{{ $approvedRequestsCount }}</h2>
                        <small class="text-muted">{{ $subditApprovedCount }} Subdit &bull; {{ $individuApprovedCount }} Individu</small>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-secondary small fw-bold text-uppercase">Perlu Verifikasi</span>
                            <span class="badge bg-warning text-dark rounded-pill">Pending</span>
                        </div>
                        <h2 class="fw-bold text-warning mb-0">{{ $pendingCount }}</h2>
                        <small class="text-muted">{{ $subditPendingCount }} Subdit &bull; {{ $individuPendingCount }} Individu</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Card --}}
        <div class="card shadow-sm border-0">
            <div class="card-header border-bottom py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <h4 class="card-title mb-0 fw-bold">Rekapitulasi Kebutuhan & Permintaan ATK</h4>
                        <small class="text-muted">Periode: <strong>{{ $periodLabel }}</strong></small>
                    </div>
                </div>

                {{-- Filter Bar --}}
                <div class="p-3 bg-light rounded border">
                    <form action="{{ route('rekapitulasi-permintaan.index') }}" method="GET" class="row g-2 align-items-center">

                        {{-- Filter Tipe --}}
                        <div class="col-12 col-sm-6 col-md-2">
                            <label class="form-label small fw-bold mb-1">Tipe Pemohon</label>
                            <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">Semua Tipe</option>
                                <option value="subdit" {{ ($typeFilter ?? '') === 'subdit' ? 'selected' : '' }}>Subdit (Kolektif)</option>
                                <option value="individu" {{ ($typeFilter ?? '') === 'individu' ? 'selected' : '' }}>Individu (Pegawai)</option>
                            </select>
                        </div>

                        {{-- Filter Status --}}
                        <div class="col-12 col-sm-6 col-md-2">
                            <label class="form-label small fw-bold mb-1">Status Verifikasi</label>
                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="all" {{ empty($statusFilter) || $statusFilter === 'all' ? 'selected' : '' }}>Semua Status</option>
                                <option value="pending" {{ ($statusFilter ?? '') === 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                                <option value="approved" {{ ($statusFilter ?? '') === 'approved' ? 'selected' : '' }}>Disetujui TU</option>
                                <option value="distributed" {{ ($statusFilter ?? '') === 'distributed' ? 'selected' : '' }}>Selesai Diserahkan</option>
                                <option value="rejected" {{ ($statusFilter ?? '') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>

                        {{-- Filter Bulan --}}
                        <div class="col-12 col-sm-6 col-md-2">
                            <label class="form-label small fw-bold mb-1">Bulan</label>
                            <select name="month" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="all" {{ ($isAllMonths ?? false) ? 'selected' : '' }}>Semua Bulan</option>
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
                        <div class="col-12 col-sm-6 col-md-2">
                            <label class="form-label small fw-bold mb-1">Tahun</label>
                            <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="all" {{ empty($selectedYear) ? 'selected' : '' }}>Semua Tahun</option>
                                @for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                                    <option value="{{ $y }}" {{ ($selectedYear ?? null) == $y ? 'selected' : '' }}>
                                        Tahun {{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        {{-- Search Input --}}
                        <div class="col-12 col-sm-8 col-md-3">
                            <label class="form-label small fw-bold mb-1">Pencarian</label>
                            <div class="input-group input-group-sm">
                                <input type="text" name="search" class="form-control" placeholder="Cari kode/pemohon/barang..."
                                    value="{{ $search ?? '' }}">
                                <button class="btn btn-primary" type="submit">Cari</button>
                            </div>
                        </div>

                        {{-- Reset --}}
                        <div class="col-12 col-sm-4 col-md-1 d-flex align-items-end">
                            <a href="{{ route('rekapitulasi-permintaan.index') }}" 
                               class="btn btn-sm btn-outline-secondary w-100" title="Reset Filter">Reset</a>
                        </div>
                    </form>
                </div>
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
                                            <small class="text-muted">{{ $req->created_at->format('d/m/Y H:i') }}</small>
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
                                                                        <strong>{{ $req->verifier->name ?? 'Subbag TU' }} ({{ $req->verified_at->format('d/m/Y H:i') }})</strong>
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

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <small class="text-muted">Menampilkan {{ $requests->firstItem() ?? 0 }} - {{ $requests->lastItem() ?? 0 }} dari {{ $requests->total() }} usulan</small>
                        {{ $requests->links() }}
                    </div>
            </div>
        </div>
    </div>
@endsection
