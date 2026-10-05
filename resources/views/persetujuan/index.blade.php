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
                            <h1 class="text-white fw-bold mb-1">Verifikasi & Persetujuan Permintaan ATK</h1>
                            <p class="text-white mb-0" style="opacity: 0.9;">
                                Wewenang Kasubag TU untuk meninjau dan menyetujui usulan permintaan barang dari pegawai/subdit sebelum diproses pembelian secara offline.
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('rekapitulasi-permintaan.index') }}" class="btn btn-light text-primary fw-bold shadow-sm d-inline-flex align-items-center gap-1.5">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                                Rekapitulasi Permintaan
                            </a>
                            <span class="badge bg-primary text-white px-3 py-2 fw-bold">
                                Kasubag TU (Approver / Pimpinan)
                            </span>
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

        {{-- Pilihan Kategori Verifikasi (Pengadaan Beli Baru vs Permintaan Barang Stok) --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body p-2">
                <ul class="nav nav-pills nav-fill gap-2" id="category-switcher">
                    <li class="nav-item">
                        <a class="nav-link py-2.5 px-3 d-flex align-items-center justify-content-center gap-2 fw-bold {{ ($category ?? 'pengadaan') === 'pengadaan' ? 'active shadow-sm' : 'bg-light text-dark' }}" 
                           href="{{ route('persetujuan.index', array_merge(request()->except(['category', 'page']), ['category' => 'pengadaan'])) }}">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            <span>1. Verifikasi Pengadaan (Beli Baru)</span>
                            @if (($pendingProcurementCount ?? 0) > 0)
                                <span class="badge {{ ($category ?? 'pengadaan') === 'pengadaan' ? 'bg-danger text-white' : 'bg-danger text-white' }} rounded-pill ms-1">
                                    {{ $pendingProcurementCount }} Menunggu
                                </span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-2.5 px-3 d-flex align-items-center justify-content-center gap-2 fw-bold {{ ($category ?? 'pengadaan') === 'persediaan' ? 'active shadow-sm' : 'bg-light text-dark' }}" 
                           href="{{ route('persetujuan.index', array_merge(request()->except(['category', 'page']), ['category' => 'persediaan'])) }}">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                            </svg>
                            <span>2. Verifikasi Permintaan Stok (Persediaan)</span>
                            @if (($pendingStockCount ?? 0) > 0)
                                <span class="badge {{ ($category ?? 'pengadaan') === 'persediaan' ? 'bg-warning text-dark' : 'bg-warning text-dark' }} rounded-pill ms-1">
                                    {{ $pendingStockCount }} Menunggu
                                </span>
                            @endif
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- 4 Kartu KPI Kasubag TU --}}
        <div class="row g-3 mb-4" id="approval-metrics">
            <div class="col-6 col-md-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-secondary small fw-bold text-uppercase">Menunggu Verifikasi</span>
                            <span class="badge bg-warning text-dark rounded-pill">Pending</span>
                        </div>
                        <h2 class="fw-bold text-warning mb-0">{{ $pendingCount }}</h2>
                        <small class="text-muted">
                            {{ ($category ?? 'pengadaan') === 'persediaan' ? 'Permintaan dari stok gudang' : 'Perlu tindakan persetujuan' }}
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-secondary small fw-bold text-uppercase">Telah Disetujui</span>
                            <span class="badge bg-success text-white rounded-pill">Approved</span>
                        </div>
                        <h2 class="fw-bold text-success mb-0">{{ $approvedCount }}</h2>
                        <small class="text-muted">
                            {{ ($category ?? 'pengadaan') === 'persediaan' ? 'Siap diserahkan oleh PIC Distribusi' : 'Akan diproses pembelian TU' }}
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-secondary small fw-bold text-uppercase">Permintaan Ditolak</span>
                            <span class="badge bg-danger text-white rounded-pill">Rejected</span>
                        </div>
                        <h2 class="fw-bold text-danger mb-0">{{ $rejectedCount }}</h2>
                        <small class="text-muted">Tidak disetujui pimpinan</small>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-secondary small fw-bold text-uppercase">
                                Total {{ ($category ?? 'pengadaan') === 'persediaan' ? 'Permintaan Stok' : 'Usulan Pengadaan' }}
                            </span>
                            <span class="badge bg-primary text-white rounded-pill">Total</span>
                        </div>
                        <h2 class="fw-bold text-dark mb-0">{{ $totalCount }}</h2>
                        <small class="text-muted">Seluruh arsip kategori ini</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Tab & Tabel Persetujuan --}}
        <div class="row">
            <div class="col-sm-12">
                <div class="card shadow-sm border-0">
                    <div
                        class="card-header border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            @if (($category ?? 'pengadaan') === 'persediaan')
                                <h4 class="card-title mb-0 fw-bold">Daftar Antrean Permintaan Barang Stok (Persediaan)</h4>
                                <small class="text-muted">Periksa permohonan permintaan barang dari stok persediaan yang sudah tersedia sebelum diserahkan/didistribusikan oleh PIC Distribusi</small>
                            @else
                                <h4 class="card-title mb-0 fw-bold">Daftar Antrean Usulan Pengadaan (Beli Baru)</h4>
                                <small class="text-muted">Periksa kebutuhan barang baru dan kuantitas permintaan sebelum memberikan persetujuan untuk diproses pembelian offline oleh Kasubag TU</small>
                            @endif
                        </div>

                        {{-- Tombol Filter Status --}}
                        <div class="d-flex align-items-center gap-1.5 flex-wrap" id="status-filter-tabs">
                            <a href="{{ route('persetujuan.index', array_merge(request()->except(['status', 'page']), ['category' => $category ?? 'pengadaan'])) }}"
                                class="btn btn-sm {{ empty($statusFilter) || $statusFilter === 'all' ? 'btn-primary' : 'btn-light border' }} rounded-pill px-3">
                                Semua ({{ $totalCount }})
                            </a>
                            <a href="{{ route('persetujuan.index', array_merge(request()->except(['status', 'page']), ['category' => $category ?? 'pengadaan', 'status' => 'pending'])) }}"
                                class="btn btn-sm {{ $statusFilter === 'pending' ? 'btn-warning text-dark fw-bold' : 'btn-light border' }} rounded-pill px-3">
                                Menunggu Verifikasi ({{ $pendingCount }})
                            </a>
                            <a href="{{ route('persetujuan.index', array_merge(request()->except(['status', 'page']), ['category' => $category ?? 'pengadaan', 'status' => 'approved'])) }}"
                                class="btn btn-sm {{ $statusFilter === 'approved' ? 'btn-success text-white fw-bold' : 'btn-light border' }} rounded-pill px-3">
                                Disetujui ({{ $approvedCount }})
                            </a>
                            <a href="{{ route('persetujuan.index', array_merge(request()->except(['status', 'page']), ['category' => $category ?? 'pengadaan', 'status' => 'rejected'])) }}"
                                class="btn btn-sm {{ $statusFilter === 'rejected' ? 'btn-danger text-white fw-bold' : 'btn-light border' }} rounded-pill px-3">
                                Ditolak ({{ $rejectedCount }})
                            </a>
                        </div>
                    </div>

                    {{-- Filter Bar Periode, Tipe, & Pencarian --}}
                    <div class="p-3 bg-light border-bottom">
                        <form id="persetujuan-filter-form" action="{{ route('persetujuan.index') }}" method="GET"
                            class="row g-2 align-items-center">
                            <input type="hidden" name="category" value="{{ $category ?? 'pengadaan' }}">
                            @if ($statusFilter)
                                <input type="hidden" name="status" value="{{ $statusFilter }}">
                            @endif

                            {{-- Filter Tipe --}}
                            <div class="col-12 col-sm-6 col-md-2">
                                <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="">Semua Tipe</option>
                                    <option value="subdit" {{ ($typeFilter ?? '') === 'subdit' ? 'selected' : '' }}>Subdit
                                        (Kolektif)</option>
                                    <option value="individu" {{ ($typeFilter ?? '') === 'individu' ? 'selected' : '' }}>
                                        Individu (Pegawai)</option>
                                </select>
                            </div>

                            {{-- Filter Bulan --}}
                            <div class="col-12 col-sm-6 col-md-3">
                                <select name="month" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="all" {{ $isAllMonths ?? false ? 'selected' : '' }}>Semua Bulan
                                        (Setahun)</option>
                                    @php
                                        $allMonths = [
                                            1 => 'Januari',
                                            2 => 'Februari',
                                            3 => 'Maret',
                                            4 => 'April',
                                            5 => 'Mei',
                                            6 => 'Juni',
                                            7 => 'Juli',
                                            8 => 'Agustus',
                                            9 => 'September',
                                            10 => 'Oktober',
                                            11 => 'November',
                                            12 => 'Desember',
                                        ];
                                    @endphp
                                    @foreach ($allMonths as $num => $name)
                                        <option value="{{ $num }}"
                                            {{ !($isAllMonths ?? false) && ($selectedMonth ?? null) == $num ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filter Tahun --}}
                            <div class="col-12 col-sm-6 col-md-2">
                                <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="all" {{ empty($selectedYear) ? 'selected' : '' }}>Semua Tahun
                                    </option>
                                    @for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                                        <option value="{{ $y }}"
                                            {{ ($selectedYear ?? null) == $y ? 'selected' : '' }}>
                                            Tahun {{ $y }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            {{-- Search Input --}}
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="input-group input-group-sm">
                                    <input type="text" id="persetujuan-search" name="search" class="form-control"
                                        placeholder="Cari kode/pemohon..." value="{{ $search ?? '' }}"
                                        autocomplete="off">
                                    <button class="btn btn-outline-secondary" type="button" id="btn-clear-search"
                                        style="{{ empty($search) ? 'display: none;' : '' }}" title="Hapus pencarian">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </button>
                                    <button class="btn btn-primary d-inline-flex align-items-center"
                                        id="btn-persetujuan-search" type="submit" title="Cari">
                                        <span id="search-spinner" class="spinner-border spinner-border-sm me-1 d-none"
                                            role="status"></span>
                                        <svg id="search-icon" width="14" height="14" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                        </svg>
                                        <span class="d-none d-lg-inline ms-1">Cari</span>
                                    </button>
                                </div>
                            </div>

                            {{-- Reset Button --}}
                            <div class="col-12 col-sm-6 col-md-2 text-md-end" id="persetujuan-reset-container">
                                @if (
                                    !empty($typeFilter) ||
                                        !empty($search) ||
                                        !empty($selectedMonth) ||
                                        !empty($selectedYear) ||
                                        ($isAllMonths ?? false))
                                    @php
                                        $resetParams = ['category' => $category ?? 'pengadaan'];
                                        if (!empty($statusFilter)) {
                                            $resetParams['status'] = $statusFilter;
                                        }
                                    @endphp
                                    <a href="{{ route('persetujuan.index', $resetParams) }}"
                                        class="btn btn-sm btn-outline-secondary w-100" title="Reset Filter">
                                        Reset Filter
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                    <div class="card-body p-4" id="table-container">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th width="4%">No</th>
                                        <th>Kode & Tipe</th>
                                        <th>Pemohon / Unit Kerja</th>
                                        <th>Rincian Barang & Kuantitas Permintaan</th>
                                        <th>Catatan Pemohon</th>
                                        <th>Status</th>
                                        <th width="16%" class="text-end">Aksi Kasubag TU</th>
                                    </tr>
                                </thead>
                                <tbody id="persetujuan-table-body">
                                    @forelse($requests as $index => $req)
                                        <tr class="persetujuan-row"
                                            data-search-text="{{ strtolower($req->request_code . ' ' . ($req->user->name ?? '') . ' ' . ($req->user->position ?? '') . ' ' . $req->type . ' ' . $req->notes . ' ' . $req->details->pluck('product.name')->join(' ') . ' ' . $req->details->pluck('product.code')->join(' ')) }}">
                                            <td>{{ ($requests->currentPage() - 1) * $requests->perPage() + $index + 1 }}
                                            </td>
                                            <td>
                                                <strong
                                                    class="font-monospace text-primary">{{ $req->request_code }}</strong><br>
                                                <span
                                                    class="badge {{ $req->type === 'subdit' ? 'bg-info text-white' : 'bg-secondary' }} text-capitalize">
                                                    {{ $req->type }}
                                                </span>
                                                @if ($req->is_procurement)
                                                    <br><span class="badge bg-warning text-dark mt-1" style="font-size: 0.72rem;">
                                                        Pengadaan (Beli Baru)
                                                    </span>
                                                @endif
                                                <br>
                                                <small
                                                    class="text-muted">{{ $req->created_at->format('d/m/Y H:i') }}</small>
                                            </td>
                                            <td>
                                                <strong>{{ $req->user->name ?? '-' }}</strong><br>
                                                <small class="text-muted">{{ $req->user->position ?? '-' }}</small>
                                            </td>
                                            <td>
                                                <ul class="ps-3 mb-0 small">
                                                    @foreach ($req->details as $detail)
                                                        @php
                                                             $currentStock = $detail->product->stock ?? 0;
                                                             $isSufficient = $detail->product && ($currentStock >= $detail->quantity);
                                                        @endphp
                                                        <li class="mb-1">
                                                            <strong>{{ $detail->item_name }}</strong>:
                                                            <span class="badge bg-light text-dark border">Diminta: {{ $detail->quantity }} {{ $detail->item_unit }}</span>
                                                            @if (in_array($req->status, ['approved', 'distributed']))
                                                                <span class="badge bg-success-subtle text-success border ms-1">
                                                                    Disetujui: {{ $detail->effective_quantity }} {{ $detail->item_unit }}
                                                                </span>
                                                            @endif
                                                            @if ($req->status === 'pending')
                                                                @if ($detail->product)
                                                                    <span
                                                                        class="badge {{ $isSufficient ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} ms-1"
                                                                        style="font-size: 0.72rem;">
                                                                        (Sisa Stok Gudang: {{ $currentStock }} {{ $detail->item_unit }})
                                                                    </span>
                                                                @else
                                                                    <span class="badge bg-warning-subtle text-dark border ms-1" style="font-size: 0.72rem;">
                                                                        (Item Baru - Belum Ada di Gudang)
                                                                    </span>
                                                                @endif
                                                            @endif
                                                            @if ($detail->specification)
                                                                <div class="text-muted fst-italic" style="font-size: 0.72rem;">
                                                                    Spesifikasi: {{ $detail->specification }}
                                                                </div>
                                                            @endif
                                                            @if ($detail->verification_note)
                                                                <div class="text-muted fst-italic" style="font-size: 0.72rem;">
                                                                    Catatan: {{ $detail->verification_note }}
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
                                                    <small class="text-secondary">-</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($req->status == 'pending')
                                                    <span class="badge bg-warning text-dark">Menunggu Verifikasi</span>
                                                @elseif($req->status == 'approved')
                                                    <span class="badge bg-success">Disetujui Kasubag TU</span>
                                                    @if ($req->purchase_invoice)
                                                        <br><span class="badge bg-primary-subtle text-primary border mt-1" style="font-size: 0.72rem;" title="Pengadaan via Faktur">
                                                            Dibeli: {{ $req->purchase_invoice }}
                                                        </span>
                                                    @endif
                                                    @if ($req->is_procurement)
                                                        <br><small class="text-muted">Akan diproses pembelian offline TU</small>
                                                    @else
                                                        <br><small class="text-success fw-semibold">Siap diserahkan oleh PIC Distribusi</small>
                                                    @endif
                                                @elseif($req->status == 'distributed')
                                                    <span class="badge bg-primary">Selesai Didistribusikan</span>
                                                    @if ($req->purchase_invoice)
                                                        <br><span class="badge bg-info-subtle text-dark border mt-1" style="font-size: 0.72rem;">
                                                            Faktur: {{ $req->purchase_invoice }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-danger"
                                                        title="{{ $req->rejection_reason }}">Ditolak</span>
                                                    @if ($req->rejection_reason)
                                                        <br><small
                                                            class="text-danger">"{{ $req->rejection_reason }}"</small>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if ($req->status == 'pending')
                                                    <div class="d-flex justify-content-end gap-1.5 flex-nowrap">
                                                        <!-- Tombol Setujui (Kasubag TU) -->
                                                        <button type="button"
                                                            class="btn btn-success btn-sm d-inline-flex align-items-center gap-1"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#approveModal{{ $req->id }}"
                                                            title="Setujui Permintaan Barang">
                                                            <svg width="14" height="14" viewBox="0 0 24 24"
                                                                fill="none" stroke="currentColor" stroke-width="2">
                                                                <polyline points="20 6 9 17 4 12"></polyline>
                                                            </svg>
                                                            Setujui
                                                        </button>

                                                        <!-- Tombol Tolak -->
                                                        <button type="button"
                                                            class="btn btn-outline-danger btn-sm d-inline-flex align-items-center gap-1"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#rejectModal{{ $req->id }}"
                                                            title="Tolak Usulan Permintaan">
                                                            <svg width="14" height="14" viewBox="0 0 24 24"
                                                                fill="none" stroke="currentColor" stroke-width="2">
                                                                <line x1="18" y1="6" x2="6"
                                                                    y2="18"></line>
                                                                <line x1="6" y1="6" x2="18"
                                                                    y2="18"></line>
                                                            </svg>
                                                            Tolak
                                                        </button>
                                                    </div>

                                                    <!-- Modal Konfirmasi Persetujuan Kasubag TU -->
                                                    <div class="modal fade text-start"
                                                        id="approveModal{{ $req->id }}" tabindex="-1"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                                            <form action="{{ route('persetujuan.update', $req->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="status" value="approved">
                                                                <input type="hidden" name="category" value="{{ $category ?? '' }}">
                                                                <div class="modal-content" style="white-space: normal;">
                                                                    <div class="modal-header bg-success text-white">
                                                                        <h5 class="modal-title text-white">
                                                                            @if ($req->is_procurement)
                                                                                Setujui Usulan Pengadaan (Akan Dibeli Offline TU)
                                                                            @else
                                                                                Setujui Permintaan Barang Persediaan (Siap Didistribusikan)
                                                                            @endif
                                                                        </h5>
                                                                        <button type="button"
                                                                            class="btn-close btn-close-white"
                                                                            data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body p-4">
                                                                        <div class="row g-2 mb-3 bg-light border rounded p-3 small">
                                                                            <div class="col-sm-6">
                                                                                <strong>Nomor Tiket:</strong>
                                                                                <span class="font-monospace text-primary fw-bold">{{ $req->request_code }}</span><br>
                                                                                <strong>Pemohon:</strong> {{ $req->user->name ?? '-' }} ({{ $req->user->position ?? '-' }})
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <strong>Jenis Permintaan:</strong> {{ ucfirst($req->type) }}<br>
                                                                                <strong>Waktu Pengajuan:</strong> {{ $req->created_at->format('d/m/Y H:i') }} WIB
                                                                            </div>
                                                                            @if ($req->notes)
                                                                                <div class="col-12 mt-1 pt-1 border-top">
                                                                                    <strong>Catatan Pemohon:</strong> <em>"{{ $req->notes }}"</em>
                                                                                </div>
                                                                            @endif
                                                                        </div>

                                                                        <h6 class="fw-bold mb-2">Daftar Barang & Kuantitas yang Disetujui:</h6>
                                                                        <div class="table-responsive mb-3">
                                                                            <table class="table table-bordered table-sm align-middle">
                                                                                <thead class="table-light">
                                                                                    <tr>
                                                                                        <th>Nama Barang ATK</th>
                                                                                        <th width="15%" class="text-center">Diminta</th>
                                                                                        <th width="22%" class="text-center">Disetujui <span class="text-danger">*</span></th>
                                                                                        <th width="28%">Catatan Barang (Opsional)</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @foreach ($req->details as $detail)
                                                                                        <tr>
                                                                                            <td>
                                                                                                <strong>{{ $detail->item_name }}</strong><br>
                                                                                                @if ($detail->product)
                                                                                                    <small class="text-muted">{{ $detail->product->category->name ?? '-' }} ({{ $detail->product->code ?? '-' }})</small>
                                                                                                @elseif ($detail->specification)
                                                                                                    <small class="text-info fst-italic">Spesifikasi: {{ $detail->specification }}</small>
                                                                                                @else
                                                                                                    <small class="text-warning fw-semibold">Item Permintaan Baru</small>
                                                                                                @endif
                                                                                            </td>
                                                                                            <td class="text-center fw-bold">
                                                                                                {{ $detail->quantity }} {{ $detail->item_unit }}
                                                                                            </td>
                                                                                            <td>
                                                                                                <input type="number" 
                                                                                                       name="approved_quantity[{{ $detail->id }}]" 
                                                                                                       class="form-control form-control-sm text-center fw-bold" 
                                                                                                       min="0" 
                                                                                                       max="{{ $detail->quantity }}" 
                                                                                                       value="{{ $detail->quantity }}" 
                                                                                                       required>
                                                                                            </td>
                                                                                            <td>
                                                                                                <input type="text" 
                                                                                                       name="item_verification_notes[{{ $detail->id }}]" 
                                                                                                       class="form-control form-control-sm" 
                                                                                                       placeholder="Misal: Disetujui penuh">
                                                                                            </td>
                                                                                        </tr>
                                                                                    @endforeach
                                                                                </tbody>
                                                                            </table>
                                                                        </div>

                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-bold small">Catatan Persetujuan Subbag TU (Opsional):</label>
                                                                            <textarea name="verification_notes" class="form-control" rows="2"
                                                                                placeholder="{{ $req->is_procurement ? 'Contoh: Disetujui untuk diproses pembelian secara offline oleh Subbag TU.' : 'Contoh: Disetujui untuk diserahkan/didistribusikan dari stok gudang.' }}"></textarea>
                                                                        </div>

                                                                        <div class="alert alert-info py-2 px-3 small mb-0 d-flex align-items-center gap-2 text-start"
                                                                            style="border-left: 4px solid #0dcaf0; line-height: 1.4;">
                                                                            <svg class="text-info flex-shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                                                stroke="currentColor" stroke-width="2">
                                                                                <circle cx="12" cy="12" r="10"></circle>
                                                                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                                                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                                                            </svg>
                                                                            <div style="font-size: 0.8rem;">
                                                                                @if ($req->is_procurement)
                                                                                    <div>1. Menyetujui usulan ini menandakan bahwa barang disetujui untuk diproses pembelian oleh Kasubag TU secara offline.</div>
                                                                                    <div>2. Setelah barang dibeli offline, Kasubag TU menambahkan stok di <strong>Master Data &rarr; Daftar Barang ATK</strong>.</div>
                                                                                @else
                                                                                    <div>1. Menyetujui permintaan ini menandakan bahwa permohonan pengambilan barang dari stok persediaan disetujui oleh Kasubag TU.</div>
                                                                                    <div>2. Tiket ini selanjutnya akan diproses penyerahan fisiknya oleh <strong>PIC Distribusi</strong>.</div>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-bs-dismiss="modal">Batal</button>
                                                                        <button type="submit" class="btn btn-success">Setujui Permintaan</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>

                                                    <!-- Modal Alasan Penolakan -->
                                                    <div class="modal fade text-start"
                                                        id="rejectModal{{ $req->id }}" tabindex="-1"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <form action="{{ route('persetujuan.update', $req->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="status" value="rejected">
                                                                <input type="hidden" name="category" value="{{ $category ?? '' }}">
                                                                <div class="modal-content" style="white-space: normal;">
                                                                    <div class="modal-header bg-danger text-white">
                                                                        <h5 class="modal-title text-white">Tolak Permintaan
                                                                            ATK</h5>
                                                                        <button type="button"
                                                                            class="btn-close btn-close-white"
                                                                            data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-bold">Alasan
                                                                                Penolakan (Akan Dilihat oleh Pemohon): <span
                                                                                    class="text-danger">*</span></label>
                                                                            <textarea name="rejection_reason" class="form-control" rows="3" required
                                                                                placeholder="Contoh: Stok barang saat ini habis atau permohonan melebihi kuota bulanan unit kerja."></textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-bs-dismiss="modal">Batal</button>
                                                                        <button type="submit"
                                                                            class="btn btn-danger">Konfirmasi
                                                                            Tolak</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="d-flex justify-content-end gap-1">
                                                        <span
                                                            class="badge bg-light text-secondary border px-2 py-1.5 small">
                                                            Selesai Ditinjau
                                                        </span>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="table-initial-empty-row">
                                            <td colspan="7" class="text-center text-muted py-5">
                                                Tidak ada pengajuan permohonan ATK yang sesuai kriteria.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($requests->hasPages())
                            <div
                                class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2 pt-3 border-top">
                                <div class="text-muted small">
                                    Menampilkan <strong>{{ $requests->firstItem() }}</strong> -
                                    <strong>{{ $requests->lastItem() }}</strong> dari
                                    <strong>{{ $requests->total() }}</strong> pengajuan
                                </div>
                                <div>
                                    {{ $requests->links() }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('persetujuan-search');
            const clearBtn = document.getElementById('btn-clear-search');
            const filterForm = document.getElementById('persetujuan-filter-form');
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
                const rows = document.querySelectorAll('#persetujuan-table-body tr.persetujuan-row');
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
                        const tbody = document.getElementById('persetujuan-table-body');
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

                        const newTabs = doc.getElementById('status-filter-tabs');
                        const currentTabs = document.getElementById('status-filter-tabs');
                        if (newTabs && currentTabs) {
                            currentTabs.innerHTML = newTabs.innerHTML;
                        }

                        const newMetrics = doc.getElementById('approval-metrics');
                        const currentMetrics = document.getElementById('approval-metrics');
                        if (newMetrics && currentMetrics) {
                            currentMetrics.innerHTML = newMetrics.innerHTML;
                        }

                        const newReset = doc.getElementById('persetujuan-reset-container');
                        const currentReset = document.getElementById('persetujuan-reset-container');
                        if (newReset && currentReset) {
                            currentReset.innerHTML = newReset.innerHTML;
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
                            console.error('Error saat sinkronisasi pencarian:', err);
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

                // Dynamic change for dropdown selects (Tipe, Bulan, Tahun)
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

            // Intercept status filter tab clicks
            const tabsContainer = document.getElementById('status-filter-tabs');
            if (tabsContainer) {
                tabsContainer.addEventListener('click', function(e) {
                    const tabLink = e.target.closest('a');
                    if (tabLink && tabLink.href) {
                        e.preventDefault();
                        try {
                            const urlObj = new URL(tabLink.href, window.location.origin);
                            const status = urlObj.searchParams.get('status') || '';

                            let statusInput = filterForm.querySelector('input[name="status"]');
                            if (!statusInput) {
                                statusInput = document.createElement('input');
                                statusInput.type = 'hidden';
                                statusInput.name = 'status';
                                filterForm.appendChild(statusInput);
                            }
                            statusInput.value = status;
                        } catch (e) {}

                        performServerSearch(tabLink.href);
                    }
                });
            }
        });
    </script>
@endpush
