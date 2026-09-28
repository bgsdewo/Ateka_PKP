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
                            <h1 class="text-white fw-bold mb-1">Koordinasi & Distribusi Barang ATK</h1>
                            <p class="text-white mb-0" style="opacity: 0.9;">
                                Kelola pengelompokan data permintaan dari berbagai Subdirektorat, penyerahan fisik barang, dan cetak bukti tanda terima.
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-warning text-dark px-3 py-2 fw-bold">
                                Peran: PIC Distribusi (Gudang)
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
            <div class="alert alert-success alert-dismissible fade show shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-2" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    @if (session('print_id'))
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
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-secondary small fw-bold text-uppercase">Siap Didistribusikan</span>
                            <span class="badge bg-warning text-dark rounded-pill">Pending Pickup</span>
                        </div>
                        <h2 class="fw-bold text-warning mb-0">{{ number_format($totalReady) }}</h2>
                        <small class="text-muted">Telah disetujui Kasubag TU</small>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-secondary small fw-bold text-uppercase">Permintaan Subdit</span>
                            <span class="badge bg-info text-white rounded-pill">Kolektif</span>
                        </div>
                        <h2 class="fw-bold text-info mb-0">{{ number_format($totalSubditReady) }}</h2>
                        <small class="text-muted">Paket kolektif unit kerja</small>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-secondary small fw-bold text-uppercase">Permintaan Pegawai</span>
                            <span class="badge bg-primary text-white rounded-pill">Individu</span>
                        </div>
                        <h2 class="fw-bold text-primary mb-0">{{ number_format($totalIndividuReady) }}</h2>
                        <small class="text-muted">Kebutuhan staf perorangan</small>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-secondary small fw-bold text-uppercase">Selesai Diserahkan</span>
                            <span class="badge bg-success text-white rounded-pill">Distributed</span>
                        </div>
                        <h2 class="fw-bold text-success mb-0">{{ number_format($totalDistributed) }}</h2>
                        <small class="text-muted">Sudah diserahterimakan</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pengelompokan Kolektif Subdirektorat (Accordion Rapi) --}}
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
                                                                                <li>
                                                                                    {{ $d->product->name ?? 'Barang' }} 
                                                                                    <strong>({{ $d->quantity }} Pcs)</strong>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </td>
                                                                    <td><small class="text-muted">{{ $subItem->notes ?? '-' }}</small></td>
                                                                    <td class="text-end text-nowrap">
                                                                        <a href="{{ route('distribusi.bukti', $subItem->id) }}" target="_blank"
                                                                            class="btn btn-xs btn-outline-secondary py-1 px-2">
                                                                            Cetak Tanda Terima
                                                                        </a>
                                                                        <button type="button" class="btn btn-xs btn-success py-1 px-2"
                                                                            data-bs-toggle="modal" data-bs-target="#handoverModal{{ $subItem->id }}">
                                                                            Serahkan
                                                                        </button>
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

        {{-- Tabel Utama Distribusi Barang --}}
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h4 class="card-title mb-0 fw-bold">Daftar Penyerahan Barang ke Pemohon</h4>
                            <small class="text-muted">Data permintaan barang yang telah disetujui Kasubag TU dan siap diserahkan oleh PIC Gudang</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary rounded-pill px-3 py-1.5">
                                {{ $requests->total() }} Permintaan Ditampilkan
                            </span>
                        </div>
                    </div>

                    {{-- Filter Bar Status, Tipe, Periode Bulan & Tahun, serta Pencarian --}}
                    <div class="p-3 bg-light border-bottom">
                        <form action="{{ route('distribusi.index') }}" method="GET" class="row g-2 align-items-center">
                            {{-- Filter Status --}}
                            <div class="col-12 col-sm-6 col-md-2">
                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="all" {{ ($filterStatus ?? 'all') === 'all' ? 'selected' : '' }}>Semua Status</option>
                                    <option value="ready" {{ ($filterStatus ?? '') === 'ready' ? 'selected' : '' }}>Siap Distribusi</option>
                                    <option value="distributed" {{ ($filterStatus ?? '') === 'distributed' ? 'selected' : '' }}>Selesai Diserahkan</option>
                                </select>
                            </div>

                            {{-- Filter Tipe --}}
                            <div class="col-12 col-sm-6 col-md-2">
                                <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="">Semua Tipe</option>
                                    <option value="subdit" {{ ($filterType ?? '') === 'subdit' ? 'selected' : '' }}>Subdit (Kolektif)</option>
                                    <option value="individu" {{ ($filterType ?? '') === 'individu' ? 'selected' : '' }}>Individu (Pegawai)</option>
                                </select>
                            </div>

                            {{-- Filter Periode Bulan --}}
                            <div class="col-12 col-sm-6 col-md-3">
                                <select name="month" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="all" {{ ($isAllMonths ?? false) ? 'selected' : '' }}>Semua Bulan (Setahun)</option>
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
                            <div class="col-12 col-sm-6 col-md-2">
                                <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="all" {{ empty($selectedYear) ? 'selected' : '' }}>Semua Tahun</option>
                                    @for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                                        <option value="{{ $y }}" {{ (($selectedYear ?? null) == $y) ? 'selected' : '' }}>
                                            Tahun {{ $y }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            {{-- Input Pencarian --}}
                            <div class="col-12 col-sm-6 col-md-2">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" class="form-control" placeholder="Cari..."
                                        value="{{ $search ?? '' }}">
                                    <button class="btn btn-primary" type="submit">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Reset Button --}}
                            <div class="col-12 col-sm-6 col-md-1 text-md-end">
                                @if (!empty($filterType) || (($filterStatus ?? 'all') !== 'all') || !empty($search) || !empty($selectedMonth) || !empty($selectedYear) || ($isAllMonths ?? false))
                                    <a href="{{ route('distribusi.index') }}" class="btn btn-sm btn-outline-secondary w-100" title="Reset Filter">
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th width="4%">No</th>
                                        <th>Kode Pengajuan</th>
                                        <th>Tipe & Pemohon</th>
                                        <th>Daftar Barang & Qty</th>
                                        <th>Status Distribusi</th>
                                        <th>Waktu / Penerima</th>
                                        <th width="15%" class="text-end">Aksi PIC</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($requests as $index => $req)
                                        <tr>
                                            <td>{{ ($requests->currentPage() - 1) * $requests->perPage() + $index + 1 }}</td>
                                            <td>
                                                <strong class="font-monospace text-primary">{{ $req->request_code }}</strong><br>
                                                <small class="text-muted">{{ $req->created_at->format('d/m/Y H:i') }}</small>
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
                                                        <li>
                                                            {{ $detail->product->name ?? 'Barang' }}
                                                            (<strong>{{ $detail->quantity }} Pcs</strong>)
                                                        </li>
                                                    @endforeach
                                                </ul>
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
                                                    <small class="text-muted">{{ $req->distributed_at ? $req->distributed_at->format('d/m/Y H:i') : '-' }}</small>
                                                    @if ($req->distribution_notes)
                                                        <br><small class="text-secondary fst-italic">"{{ $req->distribution_notes }}"</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted small">Menunggu pengambilan</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end gap-1 flex-wrap">
                                                    <!-- Tombol Cetak Tanda Terima / Surat Jalan -->
                                                    <a href="{{ route('distribusi.bukti', $req->id) }}" target="_blank"
                                                        class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1"
                                                        title="Cetak Bukti Penyerahan Barang">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                                            <rect x="6" y="14" width="12" height="8"></rect>
                                                        </svg>
                                                        Bukti
                                                    </a>

                                                    @if ($req->status === 'approved')
                                                        <!-- Tombol Serahkan Barang (Buka Modal Handover) -->
                                                        <button type="button" class="btn btn-sm btn-success d-inline-flex align-items-center gap-1"
                                                            data-bs-toggle="modal" data-bs-target="#handoverModal{{ $req->id }}">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <polyline points="20 6 9 17 4 12"></polyline>
                                                            </svg>
                                                            Serahkan
                                                        </button>
                                                    @else
                                                        <span class="badge bg-light text-secondary border px-2 py-1 small">
                                                            Selesai
                                                        </span>
                                                    @endif
                                                </div>

                                                {{-- Modal Konfirmasi Penyerahan Barang --}}
                                                <div class="modal fade text-start" id="handoverModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <form action="{{ route('distribusi.handover', $req->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-content">
                                                                <div class="modal-header bg-success text-white">
                                                                    <h5 class="modal-title text-white">Konfirmasi Penyerahan ATK</h5>
                                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="alert alert-light border mb-3">
                                                                        <div class="d-flex justify-content-between">
                                                                            <strong class="font-monospace">{{ $req->request_code }}</strong>
                                                                            <span class="badge bg-info text-capitalize">{{ $req->type }}</span>
                                                                        </div>
                                                                        <div class="small text-muted mt-1">
                                                                            Pemohon: <strong>{{ $req->user->name ?? '-' }}</strong> ({{ $req->user->position ?? '-' }})
                                                                        </div>
                                                                    </div>

                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-bold">Nama Penerima Barang Fisik <span class="text-danger">*</span></label>
                                                                        <input type="text" name="receiver_name" class="form-control"
                                                                            value="{{ $req->user->name ?? '' }}" required
                                                                            placeholder="Nama pegawai yang mengambil barang">
                                                                        <small class="text-muted">Bisa diisi nama pemohon langsung atau perwakilan yang mengambil barang.</small>
                                                                    </div>

                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-bold">Catatan Penyerahan (Opsional)</label>
                                                                        <textarea name="distribution_notes" class="form-control" rows="2"
                                                                            placeholder="Contoh: Barang diserahkan lengkap dalam kondisi baik."></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-success">
                                                                        Simpan & Tandai Diserahkan
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
