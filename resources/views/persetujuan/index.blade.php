@extends(request()->ajax() ? 'layouts.ajax' : 'layouts.app')

@section('page-header')
    <div class="iq-navbar-header py-0" style="height: auto; min-height: 0; background: transparent; box-shadow: none; border: none; padding: 1.25rem 2rem 0.5rem 2rem; margin-bottom: 0.75rem !important;">
        <div class="container-fluid iq-container p-0">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-2 pt-1">
                <div>
                    <h1 class="fw-bold mb-1" style="color: #0c2b27 !important; font-size: 2.25rem !important; letter-spacing: -0.035em !important; line-height: 1.15 !important; text-shadow: none !important;">
                        @if (($category ?? null) === 'persediaan')
                            Verifikasi Permintaan Stok (Persediaan)
                        @elseif (($category ?? null) === 'pengadaan')
                            Verifikasi Usulan Pengadaan (Beli Baru)
                        @else
                            Verifikasi &amp; Persetujuan Permintaan ATK
                        @endif
                    </h1>
                    <p class="mb-0 text-muted" style="font-size: 0.84rem !important; color: #64748b !important; text-shadow: none !important;">
                        Sistem Informasi Inventori Persediaan ATK (ATEKA) &bull; <strong style="color: #10b981;">
                            @if (($category ?? null) === 'persediaan')
                                Verifikasi Permintaan Stok
                            @elseif (($category ?? null) === 'pengadaan')
                                Verifikasi Usulan Pengadaan
                            @else
                                Verifikasi &amp; Persetujuan Permintaan
                            @endif
                        </strong>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid content-inner py-0" style="margin-top: 0 !important; padding-top: 0.75rem !important;">
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
                            <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2 py-0.5 fw-bold" style="font-size: 0.7rem; letter-spacing: 0.04em;">BERHASIL DISETUJUI</span>
                            <span class="text-muted" style="font-size: 0.72rem;">Inventori ATK &bull; Kemen PKP</span>
                        </div>
                        @php
                            $cleanedSuccess = trim(preg_replace('/\s+/', ' ', preg_replace('/\s*\(\s*REQ-[A-Za-z0-9\-]+\s*\)\s*/i', ' ', session('success'))));
                            if (str_contains(strtolower($cleanedSuccess), 'pengadaan')) {
                                $toastTitle = 'Permintaan Pengadaan Berhasil Disetujui!';
                            } elseif (str_contains(strtolower($cleanedSuccess), 'ditolak')) {
                                $toastTitle = 'Permintaan Berhasil Ditolak';
                            } else {
                                $toastTitle = 'Permintaan Berhasil Disetujui!';
                            }
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
                            <span class="text-muted" style="font-size: 0.72rem;">Inventori ATK &bull; Kemen PKP</span>
                        </div>
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.93rem;">Gagal Memproses Permintaan</h6>
                        <div class="text-secondary small toast-message" style="line-height: 1.45; font-size: 0.84rem;">
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

        {{-- 4 Kartu KPI Kasubbag TU --}}
        <div class="row g-3 mb-4" id="approval-metrics">
            <div class="col-6 col-md-3">
                <div class="kpi-widget-card h-100 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Menunggu Verifikasi</span>
                        <div class="kpi-icon-squircle" style="background-color: var(--accent-amber-light); color: var(--accent-amber);">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h2 class="fw-bold text-warning mb-0 fs-3">{{ $pendingCount }}</h2>
                        
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="kpi-widget-card h-100 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Telah Disetujui</span>
                        <div class="kpi-icon-squircle" style="background-color: var(--accent-emerald-light); color: var(--accent-emerald);">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h2 class="fw-bold text-success mb-0 fs-3">{{ $approvedCount }}</h2>
                        
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="kpi-widget-card h-100 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Permintaan Ditolak</span>
                        <div class="kpi-icon-squircle" style="background-color: var(--accent-rose-light); color: var(--accent-rose);">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="15" y1="9" x2="9" y2="15"></line>
                                <line x1="9" y1="9" x2="15" y2="15"></line>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h2 class="fw-bold text-danger mb-0 fs-3">{{ $rejectedCount }}</h2>
                        
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="kpi-widget-card h-100 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">
                            Total {{ ($category ?? null) === 'persediaan' ? 'Permintaan Stok' : (($category ?? null) === 'pengadaan' ? 'Usulan Pengadaan' : 'Permintaan ATK') }}
                        </span>
                        <div class="kpi-icon-squircle" style="background-color: var(--brand-primary-light); color: var(--brand-primary);">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h2 class="fw-bold text-dark mb-0 fs-3">{{ $totalCount }}</h2>
                        
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Tab & Tabel Persetujuan --}}
        <div class="row">
            <div class="col-sm-12">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 14px;">
                    <div class="card-header border-bottom bg-white" style="border-top-left-radius: 14px; border-top-right-radius: 14px; padding: 1.5rem 1.75rem 1.6rem 1.75rem !important;">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                            <div>
                                @if (($category ?? null) === 'persediaan')
                                    <h4 class="card-title mb-1 fw-bold" style="color: #0c2b27 !important; font-size: 1.2rem !important; letter-spacing: -0.025em !important;">Daftar Antrean Permintaan Barang Stok (Persediaan)</h4>
                                    <p class="text-muted mb-0" style="font-size: 0.84rem !important; color: #64748b !important;">Verifikasi permohonan pengeluaran barang dari stok fisik gudang.</p>
                                @elseif (($category ?? null) === 'pengadaan')
                                    <h4 class="card-title mb-1 fw-bold" style="color: #0c2b27 !important; font-size: 1.2rem !important; letter-spacing: -0.025em !important;">Daftar Antrean Usulan Pengadaan (Beli Baru)</h4>
                                    <p class="text-muted mb-0" style="font-size: 0.84rem !important; color: #64748b !important;">Verifikasi usulan belanja/pengadaan baru ATK.</p>
                                @else
                                    <h4 class="card-title mb-1 fw-bold" style="color: #0c2b27 !important; font-size: 1.2rem !important; letter-spacing: -0.025em !important;">Daftar Permintaan ATK</h4>
                                    <p class="text-muted mb-0" style="font-size: 0.84rem !important; color: #64748b !important;">Daftar permohonan persediaan stok dan usulan pengadaan barang.</p>
                                @endif
                            </div>
                        </div>

                        {{-- Kategori Pills Switcher (Semua, Stok Gudang, Perlu Pengadaan) --}}
                        <div id="category-filter-tabs" class="d-flex align-items-center gap-2 flex-wrap mb-3 p-2 rounded-3" style="background-color: #f1f5f9;">
                            <span class="small fw-bold text-muted me-1 d-none d-md-inline" style="font-size: 0.78rem;">Kategori Permintaan:</span>
                            <a href="{{ route('persetujuan.index', request()->except(['category', 'page'])) }}"
                                class="btn btn-sm {{ empty($category) ? 'btn-primary text-white shadow-sm' : 'btn-light border text-dark' }} rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.8rem;">
                                Semua Permintaan ({{ $totalAllCategoryCount ?? $totalCount }})
                                @if(($pendingAllCount ?? 0) > 0)
                                    <span class="badge {{ empty($category) ? 'bg-white text-primary' : 'bg-danger text-white' }} rounded-pill ms-1" style="font-size: 0.7rem;">{{ $pendingAllCount }}</span>
                                @endif
                            </a>
                            <a href="{{ route('persetujuan.index', array_merge(request()->except(['category', 'page']), ['category' => 'persediaan'])) }}"
                                class="btn btn-sm {{ ($category ?? '') === 'persediaan' ? 'btn-success text-white shadow-sm' : 'btn-light border text-dark' }} rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.8rem;">
                                Stok Gudang (Persediaan) ({{ $totalStockCategoryCount ?? 0 }})
                                @if(($pendingStockCount ?? 0) > 0)
                                    <span class="badge {{ ($category ?? '') === 'persediaan' ? 'bg-white text-success' : 'bg-danger text-white' }} rounded-pill ms-1" style="font-size: 0.7rem;">{{ $pendingStockCount }}</span>
                                @endif
                            </a>
                            <a href="{{ route('persetujuan.index', array_merge(request()->except(['category', 'page']), ['category' => 'pengadaan'])) }}"
                                class="btn btn-sm {{ ($category ?? '') === 'pengadaan' ? 'btn-warning text-dark shadow-sm' : 'btn-light border text-dark' }} rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.8rem;">
                                Usulan Pengadaan (Beli Baru) ({{ $totalProcurementCategoryCount ?? 0 }})
                                @if(($pendingProcurementCount ?? 0) > 0)
                                    <span class="badge {{ ($category ?? '') === 'pengadaan' ? 'bg-dark text-warning' : 'bg-danger text-white' }} rounded-pill ms-1" style="font-size: 0.7rem;">{{ $pendingProcurementCount }}</span>
                                @endif
                            </a>
                        </div>

                        {{-- Tombol Filter Status --}}
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1" id="status-filter-tabs">
                            <a href="{{ route('persetujuan.index', array_merge(request()->except(['status', 'page']), !empty($category) ? ['category' => $category] : [])) }}"
                                class="btn btn-sm {{ empty($statusFilter) || $statusFilter === 'all' ? 'btn-dark text-white shadow-sm' : 'btn-light border text-secondary' }} rounded-pill px-3.5 py-1 fw-semibold" style="font-size: 0.82rem;">
                                Semua Status ({{ $totalCount }})
                            </a>
                            <a href="{{ route('persetujuan.index', array_merge(request()->except(['status', 'page']), !empty($category) ? ['category' => $category, 'status' => 'pending'] : ['status' => 'pending'])) }}"
                                class="btn btn-sm {{ $statusFilter === 'pending' ? 'btn-warning text-dark fw-bold shadow-sm' : 'btn-light border text-secondary' }} rounded-pill px-3.5 py-1" style="font-size: 0.82rem;">
                                Menunggu Verifikasi ({{ $pendingCount }})
                            </a>
                            <a href="{{ route('persetujuan.index', array_merge(request()->except(['status', 'page']), !empty($category) ? ['category' => $category, 'status' => 'approved'] : ['status' => 'approved'])) }}"
                                class="btn btn-sm {{ $statusFilter === 'approved' ? 'btn-success text-white fw-bold shadow-sm' : 'btn-light border text-secondary' }} rounded-pill px-3.5 py-1" style="font-size: 0.82rem;">
                                Disetujui ({{ $approvedCount }})
                            </a>
                            <a href="{{ route('persetujuan.index', array_merge(request()->except(['status', 'page']), !empty($category) ? ['category' => $category, 'status' => 'rejected'] : ['status' => 'rejected'])) }}"
                                class="btn btn-sm {{ $statusFilter === 'rejected' ? 'btn-danger text-white fw-bold shadow-sm' : 'btn-light border text-secondary' }} rounded-pill px-3.5 py-1" style="font-size: 0.82rem;">
                                Ditolak ({{ $rejectedCount }})
                            </a>
                        </div>
                    </div>

                    {{-- Filter Bar Periode, Tipe, & Pencarian dengan ruang yang lega dan proporsional --}}
                    <div class="px-4 border-bottom" style="background-color: #f8fafc; padding-top: 1.25rem !important; padding-bottom: 1.25rem !important;">
                        <form id="persetujuan-filter-form" action="{{ route('persetujuan.index') }}" method="GET"
                            class="d-flex align-items-center flex-wrap gap-2.5">
                            @if(!empty($category))
                                <input type="hidden" name="category" value="{{ $category }}">
                            @endif
                            @if ($statusFilter)
                                <input type="hidden" name="status" value="{{ $statusFilter }}">
                            @endif

                            {{-- Filter Tipe --}}
                            <div style="min-width: 145px;">
                                <select name="type" class="form-select form-select-modern form-select-sm" onchange="this.form.submit()" style="font-size: 0.84rem; height: 38px; border-color: #cbd5e1;">
                                    <option value="">Semua Tipe</option>
                                    <option value="subdit" {{ ($typeFilter ?? '') === 'subdit' ? 'selected' : '' }}>Subdit</option>
                                    <option value="individu" {{ ($typeFilter ?? '') === 'individu' ? 'selected' : '' }}>Individu</option>
                                </select>
                            </div>

                            {{-- Filter Bulan --}}
                            <div style="min-width: 180px;">
                                <select name="month" class="form-select form-select-modern form-select-sm" onchange="this.form.submit()" style="font-size: 0.84rem; height: 38px; border-color: #cbd5e1;">
                                    <option value="all" {{ $isAllMonths ?? false ? 'selected' : '' }}>Semua Bulan (Setahun)</option>
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
                            <div style="min-width: 135px;">
                                <select name="year" class="form-select form-select-modern form-select-sm" onchange="this.form.submit()" style="font-size: 0.84rem; height: 38px; border-color: #cbd5e1;">
                                    <option value="all" {{ empty($selectedYear) ? 'selected' : '' }}>Semua Tahun</option>
                                    @for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                                        <option value="{{ $y }}"
                                            {{ ($selectedYear ?? null) == $y ? 'selected' : '' }}>
                                            Tahun {{ $y }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            {{-- Search Input --}}
                            <div class="flex-grow-1" style="min-width: 240px; max-width: 350px;">
                                <div class="input-group input-group-sm" style="height: 38px;">
                                    <input type="text" id="persetujuan-search" name="search" class="form-control"
                                        placeholder="Cari kode tiket / pemohon..." value="{{ $search ?? '' }}"
                                        autocomplete="off" style="font-size: 0.84rem; border-color: #cbd5e1;">
                                    <button class="btn btn-outline-secondary" type="button" id="btn-clear-search"
                                        style="{{ empty($search) ? 'display: none;' : '' }}; border-color: #cbd5e1;" title="Hapus pencarian">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </button>
                                    <button class="btn btn-primary d-inline-flex align-items-center px-3 shadow-sm"
                                        id="btn-persetujuan-search" type="submit" title="Cari">
                                        <span id="search-spinner" class="spinner-border spinner-border-sm me-1 d-none"
                                            role="status"></span>
                                        <svg id="search-icon" width="13" height="13" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2.5">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                        </svg>
                                        <span class="ms-1 fw-semibold d-none d-sm-inline">Cari</span>
                                    </button>
                                </div>
                            </div>

                            {{-- Reset Button --}}
                            <div id="persetujuan-reset-container">
                                @if (
                                    !empty($typeFilter) ||
                                        !empty($search) ||
                                        !empty($selectedMonth) ||
                                        !empty($selectedYear) ||
                                        ($isAllMonths ?? false))
                                    @php
                                        $resetParams = !empty($category) ? ['category' => $category] : [];
                                        if (!empty($statusFilter)) {
                                            $resetParams['status'] = $statusFilter;
                                        }
                                    @endphp
                                    <a href="{{ route('persetujuan.index', $resetParams) }}"
                                        class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1.5 text-nowrap px-3 shadow-none"
                                        style="font-size: 0.84rem; height: 38px; border-color: #cbd5e1;" title="Reset Filter">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                                            <path d="M3 3v5h5"></path>
                                        </svg>
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                    <div class="card-body p-2.5 p-md-3" id="table-container">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle table-dashboard-compact mb-0 w-100">
                                <colgroup>
                                    <col style="width: 3%;">
                                    <col style="width: 21%;">
                                    <col style="width: 20%;">
                                    <col style="width: 27%;">
                                    <col style="width: 13%;">
                                    <col style="width: 16%;">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode & Tipe</th>
                                        <th>Pemohon</th>
                                        <th>Rincian Barang</th>
                                        <th>Status</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="persetujuan-table-body">
                                    @forelse($requests as $index => $req)
                                        @php
                                            $userPastRequests = $req->user && $req->user->requests
                                                ? $req->user->requests->where('id', '!=', $req->id)
                                                : collect();

                                            // Khusus riwayat pengadaan pemohon
                                            $userPastProcurements = $userPastRequests->where('is_procurement', true);
                                            $totalProcurementsCount = $userPastProcurements->count();
                                            $approvedProcurementsCount = $userPastProcurements->whereIn('status', ['approved', 'distributed'])->count();
                                            $rejectedProcurementsCount = $userPastProcurements->where('status', 'rejected')->count();

                                            // History item untuk deteksi stocking/hoarding
                                            $reqMonth = $req->created_at ? $req->created_at->format('Y-m') : now()->format('Y-m');
                                            $currentItems = $req->details->map(function($d) {
                                                $spec = trim($d->specification ?? '');
                                                $name = trim($d->item_name);
                                                return !empty($spec) && !str_contains(strtolower($name), strtolower($spec))
                                                    ? strtolower($name . '__' . $spec)
                                                    : strtolower($name);
                                            })->toArray();

                                            $pastItemHistory = [];
                                            foreach ($userPastRequests as $pastReq) {
                                                foreach ($pastReq->details as $pd) {
                                                    $pSpec = trim($pd->specification ?? '');
                                                    $pName = trim($pd->item_name);
                                                    $normKey = !empty($pSpec) && !str_contains(strtolower($pName), strtolower($pSpec))
                                                        ? strtolower($pName . '__' . $pSpec)
                                                        : strtolower($pName);

                                                    if (!isset($pastItemHistory[$normKey])) {
                                                        $pastItemHistory[$normKey] = [];
                                                    }
                                                    $pastItemHistory[$normKey][] = [
                                                        'request_code' => $pastReq->request_code,
                                                        'is_procurement' => $pastReq->is_procurement,
                                                        'date' => $pastReq->created_at,
                                                        'status' => $pastReq->status,
                                                        'quantity' => $pd->effective_quantity ?? $pd->quantity,
                                                        'unit' => $pd->item_unit,
                                                        'invoice' => $pastReq->purchase_invoice,
                                                    ];
                                                }
                                            }

                                            // Cek apakah ada barang saat ini yang pernah diajukan sebelumnya oleh pemohon ini
                                            $hasRepeatedItemsSameMonth = false;
                                            $hasRepeatedItems = false;
                                            foreach ($currentItems as $cItem) {
                                                if (!empty($pastItemHistory[$cItem])) {
                                                    $hasRepeatedItems = true;
                                                    foreach ($pastItemHistory[$cItem] as $pih) {
                                                        if ($pih['date'] && $pih['date']->format('Y-m') === $reqMonth) {
                                                            $hasRepeatedItemsSameMonth = true;
                                                            break 2;
                                                        }
                                                    }
                                                }
                                            }
                                        @endphp
                                        <tr class="persetujuan-row"
                                            data-search-text="{{ strtolower($req->request_code . ' ' . ($req->user->name_with_title ?? ($req->user->name ?? '')) . ' ' . ($req->user->nip ?? '') . ' ' . ($req->user->position ?? '') . ' ' . $req->type . ' ' . $req->notes . ' ' . $req->details->pluck('product.name')->join(' ') . ' ' . $req->details->pluck('product.code')->join(' ')) }}">
                                            <td>{{ ($requests->currentPage() - 1) * $requests->perPage() + $index + 1 }}
                                            </td>
                                            <td>
                                                <div class="font-monospace fw-bold text-primary" style="font-size: 0.72rem; letter-spacing: -0.02em;">{{ $req->request_code }}</div>
                                                <div class="d-flex align-items-center gap-1 flex-wrap mt-0.5">
                                                    <span class="badge {{ $req->type === 'subdit' ? 'bg-info text-white' : 'bg-secondary' }} text-capitalize" style="font-size: 0.66rem; padding: 0.15rem 0.4rem;">
                                                        {{ $req->type }}
                                                    </span>
                                                    @if ($req->is_procurement)
                                                        <span class="badge bg-warning text-dark fw-semibold" style="font-size: 0.65rem; padding: 0.15rem 0.45rem;">
                                                            Pengadaan (Beli Baru)
                                                        </span>
                                                    @else
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold" style="font-size: 0.65rem; padding: 0.15rem 0.45rem;">
                                                            Stok Gudang
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="text-muted mt-0.5" style="font-size: 0.68rem;">
                                                    {{ $req->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB
                                                </div>
                                            </td>
                                            <td style="word-break: break-word;">
                                                <div class="fw-bold text-dark" style="font-size: 0.76rem; line-height: 1.25;">
                                                    {{ $req->user->name_with_title ?? ($req->user->name ?? '-') }}
                                                </div>
                                                <div class="text-muted" style="font-size: 0.68rem; line-height: 1.2;">
                                                    {{ $req->user->position ?? ($req->user->subdit_name ?? '-') }}
                                                </div>
                                                @if(!empty($req->user->nip))
                                                    <span class="badge bg-light text-secondary border mt-0.5" style="font-size: 0.65rem; padding: 0.12rem 0.35rem;">
                                                        NIP: {{ $req->user->nip }}
                                                    </span>
                                                @endif

                                                {{-- Tombol Lihat History Pemohon --}}
                                                <div class="mt-1 d-flex align-items-center gap-1 flex-wrap">
                                                    <button type="button" class="btn btn-xs btn-outline-info rounded-pill px-1.5 py-0.5 d-inline-flex align-items-center gap-1 shadow-none"
                                                        style="font-size: 0.66rem; line-height: 1.1;"
                                                        data-bs-toggle="modal" data-bs-target="#userHistoryModal{{ $req->id }}"
                                                        title="Lihat riwayat akun pemohon untuk pengawasan kebutuhan ATK">
                                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <circle cx="12" cy="12" r="10"></circle>
                                                            <polyline points="12 6 12 12 16 14"></polyline>
                                                        </svg>
                                                        Riwayat Akun Pemohon
                                                    </button>

                                                        @if ($hasRepeatedItemsSameMonth)
                                                            <span class="badge bg-danger-subtle text-danger border rounded-pill px-1.5 py-0.5 d-inline-flex align-items-center gap-0.5"
                                                                style="font-size: 0.64rem; line-height: 1.1;"
                                                                title="Peringatan: Pemohon ini pernah mengajukan barang yang sama di bulan ini!">
                                                                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-danger">
                                                                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                                                                    <line x1="12" y1="9" x2="12" y2="13"></line>
                                                                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                                                                </svg>
                                                                Pernah Minta Serupa (Bulan Ini)
                                                            </span>
                                                        @elseif ($hasRepeatedItems)
                                                            <span class="badge bg-warning-subtle text-dark border rounded-pill px-1.5 py-0.5 d-inline-flex align-items-center gap-0.5"
                                                                style="font-size: 0.64rem; line-height: 1.1;"
                                                                title="Pemohon pernah mengajukan barang ini pada periode bulan sebelumnya">
                                                                Pernah Minta Serupa (Bulan Lalu)
                                                            </span>
                                                        @endif
                                                    </div>
                                            </td>
                                            <td style="white-space: normal; word-break: break-word; overflow-wrap: anywhere;">
                                                <ul class="mb-0 ps-3" style="font-size: 0.74rem; word-break: break-word; white-space: normal;">
                                                    @foreach ($req->details as $detail)
                                                        @php
                                                             $currentStock = $detail->product->stock ?? 0;
                                                             $isSufficient = $detail->product && ($currentStock >= $detail->quantity);
                                                             $normName = strtolower(trim($detail->item_name));
                                                             $pastMatches = $pastItemHistory[$normName] ?? [];
                                                             $totalPastApproved = collect($pastMatches)->whereIn('status', ['approved', 'distributed'])->sum('quantity');
                                                             $lastPastMatch = collect($pastMatches)->sortByDesc('date')->first();
                                                        @endphp
                                                        <li class="mb-1" style="word-break: break-word;">
                                                            <span class="fw-bold text-dark">{{ $detail->item_name }}</span>:
                                                            <span class="badge bg-light text-dark border px-1.5 py-0.5" style="font-size: 0.65rem;">Diminta: {{ $detail->quantity }} {{ $detail->item_unit }}</span>
                                                            @if (in_array($req->status, ['approved', 'distributed']))
                                                                <span class="badge bg-success-subtle text-success border px-1.5 py-0.5 ms-0.5" style="font-size: 0.65rem;">
                                                                    Disetujui: {{ $detail->effective_quantity }} {{ $detail->item_unit }}
                                                                </span>
                                                            @endif
                                                            @if ($req->status === 'pending')
                                                                @if ($detail->product)
                                                                    <span class="badge {{ $isSufficient ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} border px-1.5 py-0.5 ms-0.5"
                                                                        style="font-size: 0.65rem;">
                                                                        (Sisa Stok: {{ $currentStock }} {{ $detail->item_unit }})
                                                                    </span>
                                                                @else
                                                                    <span class="badge bg-warning-subtle text-dark border px-1.5 py-0.5 ms-0.5" style="font-size: 0.65rem;">
                                                                        (Item Baru)
                                                                    </span>
                                                                @endif
                                                            @endif

                                                            @if (($category ?? 'pengadaan') !== 'persediaan' && !empty($pastMatches))
                                                                <div class="mt-0.5">
                                                                    <span class="badge bg-warning-subtle text-dark border py-0.5 px-1.5 d-inline-flex align-items-center gap-1" style="font-size: 0.64rem;"
                                                                        title="Riwayat: Pemohon pernah mengajukan barang ini sebelumnya">
                                                                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-warning">
                                                                            <circle cx="12" cy="12" r="10"></circle>
                                                                            <polyline points="12 6 12 12 16 14"></polyline>
                                                                        </svg>
                                                                        Pernah: {{ count($pastMatches) }}x (Disetujui: {{ $totalPastApproved }} {{ $detail->item_unit }})
                                                                        @if ($lastPastMatch)
                                                                            &bull; Terakhir: {{ \Carbon\Carbon::parse($lastPastMatch['date'])->format('d/m/Y') }}
                                                                        @endif
                                                                    </span>
                                                                </div>
                                                            @endif

                                                            @if ($detail->specification)
                                                                <div class="text-muted fst-italic mt-0.5" style="font-size: 0.68rem; line-height: 1.25;">
                                                                    Spesifikasi: {{ $detail->specification }}
                                                                </div>
                                                            @endif
                                                            @if ($detail->verification_note)
                                                                <div class="mt-0.5 p-1 rounded bg-light-subtle text-primary border-start border-2 border-primary ps-1.5" style="font-size: 0.68rem; line-height: 1.25;">
                                                                    <span class="fw-bold">Catatan TU:</span> "{{ $detail->verification_note }}"
                                                                </div>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>

                                                @if ($req->verification_notes)
                                                    <div class="mt-1 ps-3" style="font-size: 0.68rem; line-height: 1.35; word-break: break-word; white-space: normal;">
                                                        <div class="p-1 rounded bg-primary-subtle text-primary border border-primary-subtle">
                                                            <strong class="d-inline-flex align-items-center gap-1">
                                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                                                Catatan TU:
                                                            </strong>
                                                            <span class="ms-1">{{ $req->verification_notes }}</span>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if ($req->notes)
                                                    <div class="text-muted ps-3 mt-1" style="font-size: 0.7rem; line-height: 1.3; word-break: break-word;">
                                                        <span class="text-secondary fw-semibold">Keperluan:</span> {{ $req->notes }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td style="vertical-align: middle;">
                                                @if ($req->status == 'pending')
                                                    <span class="badge bg-warning text-dark px-1.5 py-0.5" style="font-size: 0.68rem;">Menunggu Verifikasi</span>
                                                @elseif($req->status == 'approved')
                                                    <span class="badge bg-success px-1.5 py-0.5" style="font-size: 0.68rem;">Disetujui TU</span>
                                                    @if ($req->purchase_invoice)
                                                        <div class="mt-0.5">
                                                            <span class="badge bg-primary-subtle text-primary border px-1.5 py-0.5" style="font-size: 0.65rem;" title="Pengadaan via Faktur">
                                                                Faktur: {{ $req->purchase_invoice }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                    @if ($req->is_procurement)
                                                        <div class="text-muted mt-0.5" style="font-size: 0.66rem; line-height: 1.2;">Proses pembelian offline TU</div>
                                                    @else
                                                        <div class="text-success fw-semibold mt-0.5" style="font-size: 0.66rem; line-height: 1.2;">Siap diserahkan PIC</div>
                                                    @endif
                                                @elseif($req->status == 'distributed')
                                                    <span class="badge bg-primary px-1.5 py-0.5" style="font-size: 0.68rem;">Selesai</span>
                                                    @if ($req->purchase_invoice)
                                                        <div class="mt-0.5">
                                                            <span class="badge bg-info-subtle text-dark border px-1.5 py-0.5" style="font-size: 0.65rem;">
                                                                Faktur: {{ $req->purchase_invoice }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                @else
                                                    <span class="badge bg-danger px-1.5 py-0.5" style="font-size: 0.68rem;" title="{{ $req->rejection_reason }}">Ditolak</span>
                                                    @if ($req->rejection_reason)
                                                        <div class="text-danger mt-0.5" style="font-size: 0.66rem; line-height: 1.2;">"{{ $req->rejection_reason }}"</div>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="text-end" style="vertical-align: middle; white-space: nowrap;">
                                                @if ($req->status == 'pending')
                                                    <div class="d-flex justify-content-end gap-1 flex-nowrap">
                                                        <!-- Tombol Setujui (Kasubbag TU) -->
                                                        <button type="button"
                                                            class="btn btn-success btn-sm d-inline-flex align-items-center gap-1 px-2 py-1 shadow-sm"
                                                            style="font-size: 0.72rem;"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#approveModal{{ $req->id }}"
                                                            title="Setujui Permintaan Barang">
                                                            <svg width="12" height="12" viewBox="0 0 24 24"
                                                                fill="none" stroke="currentColor" stroke-width="2">
                                                                <polyline points="20 6 9 17 4 12"></polyline>
                                                            </svg>
                                                            Setujui
                                                        </button>

                                                        <!-- Tombol Tolak -->
                                                        <button type="button"
                                                            class="btn btn-outline-danger btn-sm d-inline-flex align-items-center gap-1 px-2 py-1"
                                                            style="font-size: 0.72rem;"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#rejectModal{{ $req->id }}"
                                                            title="Tolak Usulan Permintaan">
                                                            <svg width="12" height="12" viewBox="0 0 24 24"
                                                                fill="none" stroke="currentColor" stroke-width="2">
                                                                <line x1="18" y1="6" x2="6"
                                                                    y2="18"></line>
                                                                <line x1="6" y1="6" x2="18"
                                                                    y2="18"></line>
                                                            </svg>
                                                            Tolak
                                                        </button>
                                                    </div>

                                                    <!-- Modal Konfirmasi Persetujuan Kasubbag TU -->
                                                    <div class="modal fade text-start"
                                                        id="approveModal{{ $req->id }}" tabindex="-1"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                                            <form action="{{ route('persetujuan.update', $req->id) }}"
                                                                method="POST" class="d-flex flex-column h-100 w-100 overflow-hidden">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="status" value="approved">
                                                                <input type="hidden" name="category" value="{{ $category ?? '' }}">
                                                                <div class="modal-content d-flex flex-column h-100 overflow-hidden" style="white-space: normal;">
                                                                    <div class="modal-header bg-success text-white flex-shrink-0">
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
                                                                    <div class="modal-body p-4 overflow-y-auto">
                                                                        <div class="row g-2 mb-3 bg-light border rounded p-3 small">
                                                                            <div class="col-sm-6">
                                                                                <strong>Nomor Tiket:</strong>
                                                                                <span class="font-monospace text-primary fw-bold">{{ $req->request_code }}</span><br>
                                                                                <strong>Pemohon:</strong> {{ $req->user->name_with_title ?? ($req->user->name ?? '-') }} ({{ $req->user->position ?? '-' }})
                                                @if(!empty($req->user->nip))
                                                    <span class="badge bg-light text-secondary border ms-1" style="font-size: 0.72rem;">NIP: {{ $req->user->nip }}</span>
                                                @endif
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

                                                                        {{-- Alert Pencegahan Penimbunan / Stocking Barang --}}
                                                                        @if (($category ?? 'pengadaan') !== 'persediaan' && ($totalProcurementsCount > 0 || $hasRepeatedItems))
                                                                            <div class="alert {{ $hasRepeatedItemsSameMonth ? 'alert-danger border-danger' : 'alert-warning border-warning' }} border mb-3 p-3 small rounded-3" style="background: {{ $hasRepeatedItemsSameMonth ? '#fff5f5' : '#fffbeb' }};">
                                                                                <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-2">
                                                                                    <div class="d-flex align-items-center gap-1.5 fw-bold {{ $hasRepeatedItemsSameMonth ? 'text-danger' : 'text-dark' }}">
                                                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                                            <circle cx="12" cy="12" r="10"></circle>
                                                                                            <polyline points="12 6 12 12 16 14"></polyline>
                                                                                        </svg>
                                                                                        Kontrol Stok Pemohon (Riwayat Pengadaan):
                                                                                    </div>
                                                                                    <button type="button" class="btn btn-xs btn-outline-dark rounded-pill px-2.5 py-0.5"
                                                                                        data-bs-toggle="modal" data-bs-target="#userHistoryModal{{ $req->id }}"
                                                                                        title="Buka rekap riwayat lengkap">
                                                                                        Buka History Lengkap &rarr;
                                                                                    </button>
                                                                                </div>
                                                                                <div class="text-secondary">
                                                                                    Pemohon ini tercatat telah memiliki <strong>{{ $totalProcurementsCount }} usulan pengadaan</strong> sebelumnya 
                                                                                    ({{ $approvedProcurementsCount }} disetujui, {{ $rejectedProcurementsCount }} ditolak).
                                                                                    @if ($hasRepeatedItemsSameMonth)
                                                                                        <div class="mt-1 text-danger fw-bold">
                                                                                            ⚠️ Perhatian: Pemohon pernah mengajukan barang yang sama pada bulan ini ({{ $req->created_at->translatedFormat('F Y') }}). Harap tinjau kebutuhan riil agar tidak terjadi penumpukan stok barang di unit kerja pemohon.
                                                                                        </div>
                                                                                    @elseif ($hasRepeatedItems)
                                                                                        <div class="mt-1 text-dark fw-medium">
                                                                                            ℹ️ Informasi: Pemohon pernah mengajukan barang serupa pada periode bulan sebelumnya.
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        @endif

                                                                        <h6 class="fw-bold mb-2">Daftar Barang & Kuantitas yang Disetujui:</h6>
                                                                        <div class="table-responsive mb-3">
                                                                            <table class="table table-bordered table-sm align-middle">
                                                                                <thead class="table-light">
                                                                                    <tr>
                                                                                        <th>Nama Barang ATK</th>
                                                                                        <th width="15%" class="text-center">Diminta</th>
                                                                                        <th width="24%" class="text-center">Disetujui <span class="text-danger">*</span></th>
                                                                                        <th width="26%">Catatan Barang (Opsional)</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @foreach ($req->details as $detail)
                                                                                        @php
                                                                                            $productStock = $detail->product ? (int) $detail->product->stock : null;
                                                                                            $defaultApproved = (!$req->is_procurement && $productStock !== null)
                                                                                                ? min($detail->quantity, $productStock)
                                                                                                : $detail->quantity;
                                                                                        @endphp
                                                                                        <tr>
                                                                                            <td>
                                                                                                <strong>{{ $detail->item_name }}</strong><br>
                                                                                                @if ($detail->product)
                                                                                                    <small class="text-muted">{{ $detail->product->category->name ?? '-' }} ({{ $detail->product->code ?? '-' }})</small>
                                                                                                    <div class="mt-1">
                                                                                                        <span class="badge {{ ($productStock ?? 0) > 0 ? 'bg-info-subtle text-info border' : 'bg-danger-subtle text-danger border' }}" style="font-size: 0.72rem;">
                                                                                                            Sisa Stok Gudang: <strong>{{ $productStock ?? 0 }}</strong> {{ $detail->item_unit }}
                                                                                                        </span>
                                                                                                    </div>
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
                                                                                                <div class="input-group input-group-sm">
                                                                                                    <input type="number" 
                                                                                                           name="approved_quantity[{{ $detail->id }}]" 
                                                                                                           class="form-control form-control-sm text-center fw-bold" 
                                                                                                           min="0" 
                                                                                                           @if(!$req->is_procurement && $productStock !== null)
                                                                                                               max="{{ $productStock }}"
                                                                                                           @endif
                                                                                                           value="{{ $defaultApproved }}" 
                                                                                                           required>
                                                                                                    <span class="input-group-text small">{{ $detail->item_unit }}</span>
                                                                                                </div>
                                                                                                <small class="text-muted d-block text-center mt-1" style="font-size: 0.7rem;">
                                                                                                    @if(!$req->is_procurement && $productStock !== null)
                                                                                                        Bebas tentukan (Maks: {{ $productStock }} {{ $detail->item_unit }})
                                                                                                    @else
                                                                                                        Bebas tentukan kuantitas
                                                                                                    @endif
                                                                                                </small>
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
                                                                                placeholder="{{ $req->is_procurement ? 'Disetujui untuk diproses pembelian secara offline oleh Subbag TU.' : 'Disetujui untuk diserahkan/didistribusikan dari stok gudang.' }}"></textarea>
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
                                                                                    <div>1. Menyetujui usulan ini menandakan bahwa barang disetujui untuk diproses pengadaan oleh Subbag TU &amp; Gudang.</div>
                                                                                    <div>2. Setelah barang fisik dibeli/tiba, barang langsung masuk antrean distribusi untuk diserahkan ke pemohon.</div>
                                                                                    <div>3. <strong>Pemohon tidak perlu mengajukan ulang</strong> karena permintaan ini otomatis terintegrasi ke serah terima barang.</div>
                                                                                @else
                                                                                    <div>1. Menyetujui permintaan ini menandakan bahwa kuantitas barang disetujui untuk diserahkan kepada pemohon.</div>
                                                                                    <div>2. Tiket selanjutnya masuk ke antrean <strong>Distribusi Barang</strong> untuk diserahkan fisiknya oleh Petugas Gudang Distribusi.</div>
                                                                                    <div>3. <strong>Stok gudang dan mutasi Barang Keluar akan otomatis tercatat</strong> saat Petugas Gudang Distribusi melakukan serah terima fisik barang.</div>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer flex-shrink-0 bg-white border-top">
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
                                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                                            <form action="{{ route('persetujuan.update', $req->id) }}"
                                                                method="POST" class="d-flex flex-column h-100 w-100 overflow-hidden">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="status" value="rejected">
                                                                <input type="hidden" name="category" value="{{ $category ?? '' }}">
                                                                <div class="modal-content d-flex flex-column h-100 overflow-hidden" style="white-space: normal;">
                                                                    <div class="modal-header bg-danger text-white flex-shrink-0">
                                                                        <h5 class="modal-title text-white">Tolak Permintaan ATK</h5>
                                                                        <button type="button"
                                                                            class="btn-close btn-close-white"
                                                                            data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body overflow-y-auto">
                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-bold">Alasan
                                                                                Penolakan (Akan Dilihat oleh Pemohon): <span
                                                                                    class="text-danger">*</span></label>
                                                                            <textarea name="rejection_reason" class="form-control" rows="3" required
                                                                                placeholder="Stok barang saat ini habis atau permohonan melebihi kuota bulanan unit kerja."></textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer flex-shrink-0 bg-white border-top">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-bs-dismiss="modal">Batal</button>
                                                                        <button type="submit"
                                                                            class="btn btn-danger">Konfirmasi Tolak</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="d-flex justify-content-end align-items-center">
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-info d-inline-flex align-items-center gap-1 px-2.5 py-1"
                                                            style="font-size: 0.72rem;"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#detailModal{{ $req->id }}"
                                                            title="Lihat Rincian Pengajuan">
                                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <circle cx="12" cy="12" r="10"></circle>
                                                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                                            </svg>
                                                            Detail
                                                        </button>
                                                    </div>
                                                        </button>
                                                    </div>

                                                    {{-- Modal Detail Permintaan / Pengadaan Kasubbag TU --}}
                                                    <div class="modal fade text-start" id="detailModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                                            <div class="modal-content d-flex flex-column h-100 overflow-hidden">
                                                                <div class="modal-header bg-light flex-shrink-0">
                                                                    <h5 class="modal-title d-flex align-items-center gap-2">
                                                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-primary">
                                                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                                            <polyline points="14 2 14 8 20 8"></polyline>
                                                                            <line x1="16" y1="13" x2="8" y2="13"></line>
                                                                            <line x1="16" y1="17" x2="8" y2="17"></line>
                                                                            <polyline points="10 9 9 9 8 9"></polyline>
                                                                        </svg>
                                                                        Rincian Pengajuan &mdash; <span class="font-monospace text-primary">{{ $req->request_code }}</span>
                                                                    </h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body p-4 overflow-y-auto">
                                                                    {{-- Status & Pemohon Info --}}
                                                                    <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom flex-wrap gap-2">
                                                                        <div>
                                                                            <h6 class="fw-bold mb-1 text-dark">{{ $req->user->name_with_title ?? ($req->user->name ?? 'Pemohon') }}</h6>
                                                                            <small class="text-muted d-block">{{ $req->user->position ?? ($req->type === 'subdit' ? 'Subdirektorat' : 'Pegawai') }} &bull; NIP: {{ $req->user->nip ?: '-' }}</small>
                                                                            <small class="text-muted">Diajukan: {{ $req->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB</small>
                                                                        </div>
                                                                        <div class="text-end">
                                                                            <span class="badge {{ $req->status_badge }} px-2.5 py-1.5">{{ $req->status_label }}</span>
                                                                            @if ($req->is_procurement)
                                                                                <span class="badge bg-warning-subtle text-dark border px-2 py-1.5 ms-1">Usulan Pengadaan</span>
                                                                            @else
                                                                                <span class="badge bg-info-subtle text-info border px-2 py-1.5 ms-1">Barang Persediaan</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>

                                                                    {{-- Tabel Rincian Barang --}}
                                                                    <h6 class="fw-bold mb-2 small text-uppercase text-secondary">Daftar Barang yang Diajukan:</h6>
                                                                    <div class="table-responsive mb-3">
                                                                        <table class="table table-bordered table-sm align-middle mb-0">
                                                                            <thead class="table-light">
                                                                                <tr>
                                                                                    <th style="width: 5%;">No</th>
                                                                                    <th>Nama Barang</th>
                                                                                    <th style="width: 20%;" class="text-center">Diminta</th>
                                                                                    <th style="width: 25%;" class="text-center">Disetujui Kasubbag TU</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @foreach ($req->details as $idx => $d)
                                                                                    <tr>
                                                                                        <td class="text-center">{{ $idx + 1 }}</td>
                                                                                        <td>
                                                                                            <strong>{{ $d->item_name }}</strong>
                                                                                            @if ($d->specification)
                                                                                                <br><small class="text-muted fst-italic">Spesifikasi: {{ $d->specification }}</small>
                                                                                            @endif
                                                                                            @if ($d->verification_note)
                                                                                                <br><small class="text-primary fst-italic">Catatan: "{{ $d->verification_note }}"</small>
                                                                                            @endif
                                                                                        </td>
                                                                                        <td class="text-center">
                                                                                            <span class="badge bg-secondary-subtle text-dark border">
                                                                                                {{ $d->quantity }} {{ $d->item_unit }}
                                                                                            </span>
                                                                                        </td>
                                                                                        <td class="text-center">
                                                                                            @if (in_array($req->status, ['approved', 'distributed']))
                                                                                                <span class="badge bg-success-subtle text-success border fw-bold">
                                                                                                    {{ $d->effective_quantity }} {{ $d->item_unit }}
                                                                                                </span>
                                                                                            @elseif ($req->status == 'rejected')
                                                                                                <span class="badge bg-danger-subtle text-danger border">Ditolak</span>
                                                                                            @else
                                                                                                <span class="text-muted">-</span>
                                                                                            @endif
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                    </div>

                                                                    {{-- Catatan Pemohon & Kasubbag TU --}}
                                                                    <div class="row g-2 mb-2">
                                                                        <div class="col-md-6">
                                                                            <div class="p-2.5 rounded bg-light border h-100">
                                                                                <small class="text-muted fw-bold d-block mb-1">Catatan / Keperluan Pemohon:</small>
                                                                                <span class="text-dark small">{{ $req->notes ?: '-' }}</span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="p-2.5 rounded bg-light border h-100">
                                                                                <small class="text-muted fw-bold d-block mb-1">Catatan Verifikasi Kasubbag TU:</small>
                                                                                <span class="text-dark small">{{ $req->verification_notes ?: ($req->rejection_reason ?: '-') }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    @if ($req->purchase_invoice)
                                                                        <div class="alert alert-success py-2 px-3 small mt-2 mb-0">
                                                                            <strong>Informasi Pembelian:</strong> Nomor Faktur: <strong>{{ $req->purchase_invoice }}</strong>
                                                                            @if ($req->purchase_date)
                                                                                &bull; Tanggal: {{ date('d/m/Y', strtotime($req->purchase_date)) }}
                                                                            @endif
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="modal-footer d-flex justify-content-between flex-shrink-0 bg-white border-top">
                                                                    <button type="button" class="btn btn-outline-info btn-sm d-inline-flex align-items-center gap-1"
                                                                        data-bs-toggle="modal" data-bs-target="#userHistoryModal{{ $req->id }}">
                                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                            <circle cx="12" cy="12" r="10"></circle>
                                                                            <polyline points="12 6 12 12 16 14"></polyline>
                                                                        </svg>
                                                                        Lihat Riwayat Pemohon
                                                                    </button>
                                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- Modal Riwayat Pengadaan & Permintaan Pemohon (Pencegahan Stocking / Hoarding) --}}
                                                <div class="modal fade text-start" id="userHistoryModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                                        <div class="modal-content d-flex flex-column h-100 overflow-hidden">
                                                            <div class="modal-header bg-primary text-white flex-shrink-0">
                                                                <div>
                                                                    <h5 class="modal-title text-white d-flex align-items-center gap-2 mb-1">
                                                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                            <circle cx="12" cy="12" r="10"></circle>
                                                                            <polyline points="12 6 12 12 16 14"></polyline>
                                                                        </svg>
                                                                        Riwayat Pengadaan &amp; Permintaan Pemohon
                                                                    </h5>
                                                                    <small class="text-white opacity-75">
                                                                        {{ $req->user->name_with_title ?? ($req->user->name ?? '-') }} &bull; {{ $req->user->position ?? ($req->user->subdit_name ?? '-') }}
                                                                        @if(!empty($req->user->nip))
                                                                            &bull; NIP: {{ $req->user->nip }}
                                                                        @endif
                                                                    </small>
                                                                </div>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body p-4 overflow-y-auto">
                                                                {{-- Banner Edukasi / Kontrol Kasubbag TU --}}
                                                                <div class="alert alert-info py-2 px-3 small mb-3 border-0 rounded-3 d-flex align-items-center gap-2" style="background: #e0f2fe; border-left: 4px solid #0284c7 !important;">
                                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-info flex-shrink-0">
                                                                        <circle cx="12" cy="12" r="10"></circle>
                                                                        <line x1="12" y1="16" x2="12" y2="12"></line>
                                                                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                                                    </svg>
                                                                    <div>
                                                                        <strong>Pengawasan Stok:</strong> Pantau permintaan barang yang sama secara berulang dalam bulan ini.
                                                                    </div>
                                                                </div>

                                                                {{-- 4 Stat Cards Ringkasan Pemohon --}}
                                                                <div class="row g-2 mb-4 text-center">
                                                                    <div class="col-6 col-md-3">
                                                                        <div class="card border bg-light h-100 shadow-none mb-0">
                                                                            <div class="card-body p-2.5">
                                                                                <small class="text-muted d-block fw-bold" style="font-size: 0.72rem;">TOTAL PENGADAAN</small>
                                                                                <h4 class="fw-bold text-dark mb-0 mt-1">{{ $totalProcurementsCount }}</h4>
                                                                                <small class="text-muted" style="font-size: 0.7rem;">Usulan diajukan</small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-6 col-md-3">
                                                                        <div class="card border bg-light h-100 shadow-none mb-0">
                                                                            <div class="card-body p-2.5">
                                                                                <small class="text-success d-block fw-bold" style="font-size: 0.72rem;">DISETUJUI / SELESAI</small>
                                                                                <h4 class="fw-bold text-success mb-0 mt-1">{{ $approvedProcurementsCount }}</h4>
                                                                                <small class="text-muted" style="font-size: 0.7rem;">Barang disetujui</small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-6 col-md-3">
                                                                        <div class="card border bg-light h-100 shadow-none mb-0">
                                                                            <div class="card-body p-2.5">
                                                                                <small class="text-danger d-block fw-bold" style="font-size: 0.72rem;">DITOLAK PIMPINAN</small>
                                                                                <h4 class="fw-bold text-danger mb-0 mt-1">{{ $rejectedProcurementsCount }}</h4>
                                                                                <small class="text-muted" style="font-size: 0.7rem;">Tidak disetujui</small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-6 col-md-3">
                                                                        <div class="card border bg-light h-100 shadow-none mb-0">
                                                                            <div class="card-body p-2.5">
                                                                                <small class="text-primary d-block fw-bold" style="font-size: 0.72rem;">TOTAL SEMUA PERMINTAAN</small>
                                                                                <h4 class="fw-bold text-primary mb-0 mt-1">{{ $userPastRequests->count() }}</h4>
                                                                                <small class="text-muted" style="font-size: 0.7rem;">Pengadaan + Stok</small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                {{-- Seksi 1: Ringkasan Akumulasi Barang yang Pernah Diterima Pemohon --}}
                                                                @php
                                                                    $itemSummary = [];
                                                                    $reqMonth = $req->created_at ? $req->created_at->format('Y-m') : now()->format('Y-m');
                                                                    $reqMonthName = $req->created_at ? $req->created_at->translatedFormat('F Y') : now()->translatedFormat('F Y');

                                                                    // Kumpulkan item dari riwayat lampau pemohon
                                                                    foreach ($userPastRequests as $pReq) {
                                                                        foreach ($pReq->details as $pDetail) {
                                                                            $pSpec = trim($pDetail->specification ?? '');
                                                                            $pName = trim($pDetail->item_name);

                                                                            if (!empty($pSpec) && !str_contains(strtolower($pName), strtolower($pSpec))) {
                                                                                $displayName = $pName . ' (' . $pSpec . ')';
                                                                                $k = strtolower($pName . '__' . $pSpec);
                                                                            } else {
                                                                                $displayName = $pName;
                                                                                $k = strtolower($pName);
                                                                            }

                                                                            $pReqDate = $pReq->created_at;
                                                                            $isThisMonth = ($pReqDate && $pReqDate->format('Y-m') === $reqMonth);

                                                                            if (!isset($itemSummary[$k])) {
                                                                                $itemSummary[$k] = [
                                                                                    'name' => $displayName,
                                                                                    'unit' => $pDetail->item_unit,
                                                                                    'total_requested' => 0,
                                                                                    'total_approved' => 0,
                                                                                    'frequency' => 0,
                                                                                    'last_date' => $pReqDate,
                                                                                    'last_status' => $pReq->status,
                                                                                    'same_month_count' => 0,
                                                                                    'same_month_dates' => collect(),
                                                                                    'is_current_ticket_only' => false,
                                                                                ];
                                                                            }

                                                                            $itemSummary[$k]['total_requested'] += $pDetail->quantity;
                                                                            if (in_array($pReq->status, ['approved', 'distributed'])) {
                                                                                $itemSummary[$k]['total_approved'] += ($pDetail->effective_quantity ?? $pDetail->quantity);
                                                                            }
                                                                            $itemSummary[$k]['frequency']++;

                                                                            if ($isThisMonth && $pReqDate) {
                                                                                $itemSummary[$k]['same_month_count']++;
                                                                                $itemSummary[$k]['same_month_dates']->push($pReqDate->format('d/m'));
                                                                            }

                                                                            if ($pReqDate && $pReqDate > $itemSummary[$k]['last_date']) {
                                                                                $itemSummary[$k]['last_date'] = $pReqDate;
                                                                                $itemSummary[$k]['last_status'] = $pReq->status;
                                                                            }
                                                                        }
                                                                    }

                                                                    // Masukkan juga item dari permohonan saat ini jika belum ada di riwayat
                                                                    foreach ($req->details as $cDetail) {
                                                                        $cSpec = trim($cDetail->specification ?? '');
                                                                        $cName = trim($cDetail->item_name);

                                                                        if (!empty($cSpec) && !str_contains(strtolower($cName), strtolower($cSpec))) {
                                                                            $cDisplay = $cName . ' (' . $cSpec . ')';
                                                                            $cKey = strtolower($cName . '__' . $cSpec);
                                                                        } else {
                                                                            $cDisplay = $cName;
                                                                            $cKey = strtolower($cName);
                                                                        }

                                                                        if (!isset($itemSummary[$cKey])) {
                                                                            $itemSummary[$cKey] = [
                                                                                'name' => $cDisplay,
                                                                                'unit' => $cDetail->item_unit,
                                                                                'total_requested' => $cDetail->quantity,
                                                                                'total_approved' => 0,
                                                                                'frequency' => 1,
                                                                                'last_date' => $req->created_at,
                                                                                'last_status' => $req->status,
                                                                                'same_month_count' => 0,
                                                                                'same_month_dates' => collect([$req->created_at ? $req->created_at->format('d/m') : now()->format('d/m')]),
                                                                                'is_current_ticket_only' => true,
                                                                            ];
                                                                        }
                                                                    }
                                                                @endphp

                                                                <div class="card border mb-4">
                                                                    <div class="card-header bg-light py-2.5 d-flex justify-content-between align-items-center">
                                                                        <h6 class="fw-bold mb-0 text-dark small text-uppercase">
                                                                            Akumulasi Barang yang Pernah Diajukan &amp; Diterima oleh Pemohon Ini
                                                                        </h6>
                                                                        <span class="badge bg-secondary rounded-pill">{{ count($itemSummary) }} Jenis Barang</span>
                                                                    </div>
                                                                    <div class="card-body p-0">
                                                                        @if (count($itemSummary) > 0)
                                                                            <div class="table-responsive">
                                                                                <table class="table table-sm table-hover mb-0 align-middle">
                                                                                    <thead class="table-light small">
                                                                                        <tr>
                                                                                            <th>Nama Barang ATK</th>
                                                                                            <th class="text-center">Total Diminta</th>
                                                                                            <th class="text-center">Total Disetujui</th>
                                                                                            <th class="text-center">Frekuensi Permintaan</th>
                                                                                            <th>Terakhir Diajukan</th>
                                                                                            <th class="text-center">Indikator Kontrol Stok</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody class="small">
                                                                                        @foreach ($itemSummary as $normKey => $iSum)
                                                                                            @php
                                                                                                $isCurrentlyRequested = in_array($normKey, $currentItems);
                                                                                                $hasPastInSameMonth = ($iSum['same_month_count'] > 0);
                                                                                                $isRepeatedThisMonth = $isCurrentlyRequested && $hasPastInSameMonth;
                                                                                            @endphp
                                                                                            <tr class="{{ $isRepeatedThisMonth ? 'table-danger' : ($isCurrentlyRequested ? 'table-warning' : '') }}">
                                                                                                <td>
                                                                                                    <strong class="text-dark">{{ $iSum['name'] }}</strong>
                                                                                                    @if ($isRepeatedThisMonth)
                                                                                                        <span class="badge bg-danger text-white ms-1" style="font-size: 0.68rem;">
                                                                                                            ⚠️ Minta Berulang di Bulan Ini ({{ $iSum['same_month_dates']->unique()->join(', ') }})
                                                                                                        </span>
                                                                                                    @elseif ($isCurrentlyRequested)
                                                                                                        <span class="badge bg-info-subtle text-primary border ms-1" style="font-size: 0.68rem;">
                                                                                                            Sedang Diajukan (Tiket Ini)
                                                                                                        </span>
                                                                                                    @endif
                                                                                                </td>
                                                                                                <td class="text-center">{{ $iSum['total_requested'] }} {{ $iSum['unit'] }}</td>
                                                                                                <td class="text-center fw-bold text-success">{{ $iSum['total_approved'] }} {{ $iSum['unit'] }}</td>
                                                                                                <td class="text-center">
                                                                                                    {{ $iSum['frequency'] }} kali
                                                                                                    @if ($isRepeatedThisMonth)
                                                                                                        <small class="text-danger d-block fw-semibold" style="font-size: 0.72rem;">
                                                                                                            &bull; {{ $iSum['same_month_count'] + 1 }}x di {{ $reqMonthName }}
                                                                                                        </small>
                                                                                                    @endif
                                                                                                </td>
                                                                                                <td>{{ \Carbon\Carbon::parse($iSum['last_date'])->format('d/m/Y H:i') }}</td>
                                                                                                <td class="text-center">
                                                                                                    @if ($isRepeatedThisMonth)
                                                                                                        <span class="badge bg-danger-subtle text-danger border" style="font-size: 0.7rem;">
                                                                                                            Waspada Penimbunan Stok (Bulan Ini)
                                                                                                        </span>
                                                                                                        <small class="text-muted d-block mt-0.5" style="font-size: 0.68rem;">
                                                                                                            Pernah diajukan tgl {{ $iSum['same_month_dates']->unique()->join(', ') }}
                                                                                                        </small>
                                                                                                    @elseif ($isCurrentlyRequested && $iSum['total_approved'] > 0)
                                                                                                        <span class="badge bg-danger-subtle text-danger border" style="font-size: 0.7rem;">
                                                                                                            Waspada Penimbunan Stok
                                                                                                        </span>
                                                                                                    @elseif ($iSum['total_approved'] > 0)
                                                                                                        <span class="badge bg-success-subtle text-success border" style="font-size: 0.7rem;">
                                                                                                            Pernah Diterima
                                                                                                        </span>
                                                                                                    @elseif (!empty($iSum['is_current_ticket_only']))
                                                                                                        <span class="badge bg-primary-subtle text-primary border" style="font-size: 0.7rem;">
                                                                                                            Pengajuan Baru Bulan Ini
                                                                                                        </span>
                                                                                                    @else
                                                                                                        <span class="badge bg-light text-muted border" style="font-size: 0.7rem;">
                                                                                                            Belum Pernah Disetujui
                                                                                                        </span>
                                                                                                    @endif
                                                                                                </td>
                                                                                            </tr>
                                                                                        @endforeach
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        @else
                                                                            <div class="p-3 text-center text-muted small">
                                                                                Belum ada riwayat akumulasi barang sebelumnya untuk pemohon ini.
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                @if (($category ?? null) !== 'persediaan')
                                                                {{-- Seksi 2: Tabel Arsip Seluruh Riwayat Pengadaan Pemohon --}}
                                                                <div class="card border mb-0">
                                                                    <div class="card-header bg-light py-2.5 d-flex justify-content-between align-items-center">
                                                                        <h6 class="fw-bold mb-0 text-dark small text-uppercase">
                                                                            Arsip Usulan Pengadaan (Beli Baru) Sebelumnya oleh Pemohon Ini
                                                                        </h6>
                                                                        <span class="badge bg-primary rounded-pill">{{ $userPastProcurements->count() }} Usulan</span>
                                                                    </div>
                                                                    <div class="card-body p-0">
                                                                        @if ($userPastProcurements->count() > 0)
                                                                            <div class="table-responsive">
                                                                                <table class="table table-sm table-striped table-hover mb-0 align-middle">
                                                                                    <thead class="table-light small">
                                                                                        <tr>
                                                                                            <th width="4%">No</th>
                                                                                            <th>Kode Usulan &amp; Tanggal</th>
                                                                                            <th>Rincian Barang yang Diminta</th>
                                                                                            <th>Status Kasubbag TU</th>
                                                                                            <th>Informasi Faktur / Pembelian</th>
                                                                                            <th>Catatan Pemohon &amp; TU</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody class="small">
                                                                                        @foreach ($userPastProcurements as $pIdx => $pastP)
                                                                                            <tr>
                                                                                                <td>{{ $loop->iteration }}</td>
                                                                                                <td>
                                                                                                    <strong class="font-monospace text-primary">{{ $pastP->request_code }}</strong><br>
                                                                                                    <small class="text-muted">{{ $pastP->created_at->format('d/m/Y H:i') }}</small>
                                                                                                </td>
                                                                                                <td>
                                                                                                    <ul class="ps-3 mb-0">
                                                                                                        @foreach ($pastP->details as $pDetail)
                                                                                                            <li>
                                                                                                                <strong>{{ $pDetail->item_name }}</strong>:
                                                                                                                <span class="badge bg-light text-dark border">Diminta: {{ $pDetail->quantity }} {{ $pDetail->item_unit }}</span>
                                                                                                                @if (in_array($pastP->status, ['approved', 'distributed']))
                                                                                                                    <span class="badge bg-success-subtle text-success border">
                                                                                                                        Disetujui: {{ $pDetail->effective_quantity }} {{ $pDetail->item_unit }}
                                                                                                                    </span>
                                                                                                                @endif
                                                                                                                @if ($pDetail->specification)
                                                                                                                    <small class="text-muted d-block fst-italic">Spesifikasi: {{ $pDetail->specification }}</small>
                                                                                                                @endif
                                                                                                            </li>
                                                                                                        @endforeach
                                                                                                    </ul>
                                                                                                </td>
                                                                                                <td>
                                                                                                    <span class="badge {{ $pastP->status_badge }}">{{ $pastP->status_label }}</span>
                                                                                                </td>
                                                                                                <td>
                                                                                                    @if ($pastP->purchase_invoice)
                                                                                                        <strong class="text-dark font-monospace">{{ $pastP->purchase_invoice }}</strong><br>
                                                                                                        <small class="text-muted">{{ $pastP->purchase_date ? date('d/m/Y', strtotime($pastP->purchase_date)) : '-' }}</small>
                                                                                                    @else
                                                                                                        <span class="text-muted">-</span>
                                                                                                    @endif
                                                                                                </td>
                                                                                                <td>
                                                                                                    @if ($pastP->notes)
                                                                                                        <small class="d-block text-dark"><strong>Pemohon:</strong> {{ $pastP->notes }}</small>
                                                                                                    @endif
                                                                                                    @if ($pastP->verification_notes)
                                                                                                        <small class="d-block text-primary"><strong>TU:</strong> {{ $pastP->verification_notes }}</small>
                                                                                                    @endif
                                                                                                    @if ($pastP->rejection_reason)
                                                                                                        <small class="d-block text-danger"><strong>Alasan Tolak:</strong> {{ $pastP->rejection_reason }}</small>
                                                                                                    @endif
                                                                                                    @if (!$pastP->notes && !$pastP->verification_notes && !$pastP->rejection_reason)
                                                                                                        <span class="text-muted">-</span>
                                                                                                    @endif
                                                                                                </td>
                                                                                            </tr>
                                                                                        @endforeach
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        @else
                                                                            <div class="p-4 text-center text-muted">
                                                                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-muted mb-2">
                                                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                                                    <polyline points="14 2 14 8 20 8"></polyline>
                                                                                </svg>
                                                                                <p class="mb-0 fw-semibold">Belum ada usulan pengadaan lain yang pernah diajukan oleh pemohon ini.</p>
                                                                                <small class="text-muted">Ini adalah pengajuan pengadaan pertama kali oleh pemohon ini di sistem.</small>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer flex-shrink-0 bg-white border-top">
                                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup Riwayat</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="table-initial-empty-row">
                                            <td colspan="6" class="text-center text-muted py-5">
                                                Tidak ada pengajuan permohonan ATK yang sesuai kriteria.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2" style="border-bottom-left-radius: 14px; border-bottom-right-radius: 14px;">
                            <div class="text-muted small">
                                Menampilkan <strong>{{ $requests->firstItem() ?? 0 }}</strong> -
                                <strong>{{ $requests->lastItem() ?? 0 }}</strong> dari
                                <strong>{{ $requests->total() }}</strong> pengajuan (10 per halaman)
                            </div>
                            @if ($requests->hasPages())
                                <div class="ms-auto d-flex justify-content-end align-items-center">
                                    {{ $requests->links('pagination::bootstrap-4') }}
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
                        <td colspan="6" class="text-center text-muted py-4">
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

                        const newCatTabs = doc.getElementById('category-filter-tabs');
                        const currentCatTabs = document.getElementById('category-filter-tabs');
                        if (newCatTabs && currentCatTabs) {
                            currentCatTabs.innerHTML = newCatTabs.innerHTML;
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

            // Intercept category filter tab clicks
            const catTabsContainer = document.getElementById('category-filter-tabs');
            if (catTabsContainer) {
                catTabsContainer.addEventListener('click', function(e) {
                    const catLink = e.target.closest('a');
                    if (catLink && catLink.href) {
                        e.preventDefault();
                        performServerSearch(catLink.href);
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

            // Modern Floating Toast auto dismiss and countdown progress bar
            const toast = document.getElementById('modern-toast-notification');
            if (toast && toast.parentElement !== document.body) {
                document.body.appendChild(toast);
            }
            const progressBar = document.getElementById('toast-progress-bar');
            
            if (toast && progressBar) {
                const totalDuration = 6000; // 6 detik durasi tampilan
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

            // Loading state on approval form submissions
            document.querySelectorAll('form[action*="/persetujuan/"], form[action*="/verifikasi/"]').forEach(form => {
                form.addEventListener('submit', function() {
                    const btn = this.querySelector('button[type="submit"]');
                    if (btn && !btn.disabled) {
                        btn.disabled = true;
                        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status"></span> Menyimpan...`;
                    }
                });
            });
        });
    </script>
@endpush
