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
                            <h1 class="text-white fw-bold mb-1">Persetujuan (Approval) Permintaan ATK</h1>
                            <p class="text-white mb-0" style="opacity: 0.9;">
                                Wewenang Kasubag TU untuk meninjau, menyetujui, atau menolak usulan permohonan ATK kantor.
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
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

        {{-- 4 Kartu KPI Kasubag TU --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-secondary small fw-bold text-uppercase">Menunggu Verifikasi</span>
                            <span class="badge bg-warning text-dark rounded-pill">Pending</span>
                        </div>
                        <h2 class="fw-bold text-warning mb-0">{{ $pendingCount }}</h2>
                        <small class="text-muted">Perlu tindakan persetujuan</small>
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
                        <small class="text-muted">Diteruskan ke PIC Gudang</small>
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
                            <span class="text-secondary small fw-bold text-uppercase">Total Pengajuan</span>
                            <span class="badge bg-primary text-white rounded-pill">Total</span>
                        </div>
                        <h2 class="fw-bold text-dark mb-0">{{ $totalCount }}</h2>
                        <small class="text-muted">Seluruh arsip permohonan</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Tab & Tabel Persetujuan --}}
        <div class="row">
            <div class="col-sm-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h4 class="card-title mb-0 fw-bold">Daftar Antrean Permintaan Masuk</h4>
                            <small class="text-muted">Periksa kebutuhan barang, jumlah, dan ketersediaan stok fisik gudang sebelum memberikan persetujuan</small>
                        </div>

                        {{-- Tombol Filter Status --}}
                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                            <a href="{{ route('persetujuan.index', array_merge(request()->except(['status', 'page']), [])) }}" 
                                class="btn btn-sm {{ empty($statusFilter) || $statusFilter === 'all' ? 'btn-primary' : 'btn-light border' }} rounded-pill px-3">
                                Semua ({{ $totalCount }})
                            </a>
                            <a href="{{ route('persetujuan.index', array_merge(request()->except(['status', 'page']), ['status' => 'pending'])) }}" 
                                class="btn btn-sm {{ $statusFilter === 'pending' ? 'btn-warning text-dark fw-bold' : 'btn-light border' }} rounded-pill px-3">
                                Menunggu Verifikasi ({{ $pendingCount }})
                            </a>
                            <a href="{{ route('persetujuan.index', array_merge(request()->except(['status', 'page']), ['status' => 'approved'])) }}" 
                                class="btn btn-sm {{ $statusFilter === 'approved' ? 'btn-success text-white fw-bold' : 'btn-light border' }} rounded-pill px-3">
                                Disetujui ({{ $approvedCount }})
                            </a>
                            <a href="{{ route('persetujuan.index', array_merge(request()->except(['status', 'page']), ['status' => 'rejected'])) }}" 
                                class="btn btn-sm {{ $statusFilter === 'rejected' ? 'btn-danger text-white fw-bold' : 'btn-light border' }} rounded-pill px-3">
                                Ditolak ({{ $rejectedCount }})
                            </a>
                        </div>
                    </div>

                    {{-- Filter Bar Periode, Tipe, & Pencarian --}}
                    <div class="p-3 bg-light border-bottom">
                        <form action="{{ route('persetujuan.index') }}" method="GET" class="row g-2 align-items-center">
                            @if ($statusFilter)
                                <input type="hidden" name="status" value="{{ $statusFilter }}">
                            @endif

                            {{-- Filter Tipe --}}
                            <div class="col-12 col-sm-6 col-md-2">
                                <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="">Semua Tipe</option>
                                    <option value="subdit" {{ ($typeFilter ?? '') === 'subdit' ? 'selected' : '' }}>Subdit (Kolektif)</option>
                                    <option value="individu" {{ ($typeFilter ?? '') === 'individu' ? 'selected' : '' }}>Individu (Pegawai)</option>
                                </select>
                            </div>

                            {{-- Filter Bulan --}}
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

                            {{-- Filter Tahun --}}
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

                            {{-- Search Input --}}
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" class="form-control" placeholder="Cari kode/pemohon..."
                                        value="{{ $search ?? '' }}">
                                    <button class="btn btn-primary" type="submit">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                        </svg>
                                        Cari
                                    </button>
                                </div>
                            </div>

                            {{-- Reset Button --}}
                            <div class="col-12 col-sm-6 col-md-2 text-md-end">
                                @if (!empty($typeFilter) || !empty($search) || !empty($selectedMonth) || !empty($selectedYear) || ($isAllMonths ?? false))
                                    <a href="{{ route('persetujuan.index', !empty($statusFilter) ? ['status' => $statusFilter] : []) }}" 
                                        class="btn btn-sm btn-outline-secondary w-100" title="Reset Filter">
                                        Reset Filter
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
                                        <th>Kode & Tipe</th>
                                        <th>Pemohon / Unit Kerja</th>
                                        <th>Rincian Barang, Qty & Ketersediaan Stok</th>
                                        <th>Catatan Pemohon</th>
                                        <th>Status</th>
                                        <th width="16%" class="text-end">Aksi Kasubag TU</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($requests as $index => $req)
                                        <tr>
                                            <td>{{ ($requests->currentPage() - 1) * $requests->perPage() + $index + 1 }}</td>
                                            <td>
                                                <strong class="font-monospace text-primary">{{ $req->request_code }}</strong><br>
                                                <span class="badge {{ $req->type === 'subdit' ? 'bg-info text-white' : 'bg-secondary' }} text-capitalize">
                                                    {{ $req->type }}
                                                </span><br>
                                                <small class="text-muted">{{ $req->created_at->format('d/m/Y H:i') }}</small>
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
                                                            $isSufficient = $currentStock >= $detail->quantity;
                                                        @endphp
                                                        <li class="mb-1">
                                                            <strong>{{ $detail->product->name ?? 'Barang Dihapus' }}</strong>: 
                                                            <span class="badge bg-light text-dark border">{{ $detail->quantity }} Pcs</span>
                                                            @if ($req->status === 'pending')
                                                                <span class="badge {{ $isSufficient ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} ms-1" style="font-size: 0.72rem;">
                                                                    (Sisa Stok Gudang: {{ $currentStock }} Pcs)
                                                                </span>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                            <td>
                                                <small class="text-secondary">{{ $req->notes ?? '-' }}</small>
                                            </td>
                                            <td>
                                                @if ($req->status == 'pending')
                                                    <span class="badge bg-warning text-dark">Menunggu Verifikasi</span>
                                                @elseif($req->status == 'approved')
                                                    <span class="badge bg-success">Disetujui Kasubag TU</span>
                                                    <br><small class="text-muted">Siap didistribusikan</small>
                                                @elseif($req->status == 'distributed')
                                                    <span class="badge bg-primary">Selesai Didistribusikan</span>
                                                @else
                                                    <span class="badge bg-danger" title="{{ $req->rejection_reason }}">Ditolak</span>
                                                    @if ($req->rejection_reason)
                                                        <br><small class="text-danger">"{{ $req->rejection_reason }}"</small>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if ($req->status == 'pending')
                                                    <div class="d-flex justify-content-end gap-1.5 flex-nowrap">
                                                        <!-- Tombol Setujui -->
                                                        <button type="button" class="btn btn-success btn-sm d-inline-flex align-items-center gap-1"
                                                            data-bs-toggle="modal" data-bs-target="#approveModal{{ $req->id }}">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <polyline points="20 6 9 17 4 12"></polyline>
                                                            </svg>
                                                            Setujui
                                                        </button>

                                                        <!-- Tombol Tolak -->
                                                        <button type="button" class="btn btn-danger btn-sm d-inline-flex align-items-center gap-1"
                                                            data-bs-toggle="modal" data-bs-target="#rejectModal{{ $req->id }}">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                                                <line x1="6" y1="6" x2="18" y2="18"></line>
                                                            </svg>
                                                            Tolak
                                                        </button>
                                                    </div>

                                                    <!-- Modal Konfirmasi Persetujuan -->
                                                    <div class="modal fade text-start" id="approveModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <form action="{{ route('persetujuan.update', $req->id) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="status" value="approved">
                                                                <div class="modal-content">
                                                                    <div class="modal-header bg-success text-white">
                                                                        <h5 class="modal-title text-white">Persetujuan Permintaan ATK</h5>
                                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p class="mb-2">Apakah Anda yakin ingin <strong>menyetujui (approve)</strong> pengajuan nomor <strong class="font-monospace">{{ $req->request_code }}</strong>?</p>
                                                                        
                                                                        <div class="card bg-light border p-2.5 mb-2 small">
                                                                            <strong>Pemohon:</strong> {{ $req->user->name ?? '-' }} ({{ $req->user->position ?? '-' }})<br>
                                                                            <strong>Jenis:</strong> {{ ucfirst($req->type) }}
                                                                        </div>

                                                                        <div class="alert alert-info py-2 small mb-0">
                                                                            Setelah disetujui, kuota stok gudang akan otomatis terpotong dan tiket diteruskan ke <strong>PIC Distribusi</strong> untuk diserahkan barang fisiknya.
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                        <button type="submit" class="btn btn-success">Ya, Setujui Permintaan</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>

                                                    <!-- Modal Alasan Penolakan -->
                                                    <div class="modal fade text-start" id="rejectModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <form action="{{ route('persetujuan.update', $req->id) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="status" value="rejected">
                                                                <div class="modal-content">
                                                                    <div class="modal-header bg-danger text-white">
                                                                        <h5 class="modal-title text-white">Tolak Permintaan ATK</h5>
                                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-bold">Alasan Penolakan (Akan Dilihat oleh Pemohon): <span class="text-danger">*</span></label>
                                                                            <textarea name="rejection_reason" class="form-control" rows="3" required
                                                                                placeholder="Contoh: Stok barang saat ini habis atau permohonan melebihi kuota bulanan unit kerja."></textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                        <button type="submit" class="btn btn-danger">Konfirmasi Tolak</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="d-flex justify-content-end gap-1">
                                                        @if (in_array($req->status, ['approved', 'distributed']))
                                                            <a href="{{ route('distribusi.bukti', $req->id) }}" target="_blank"
                                                                class="btn btn-sm btn-outline-secondary" title="Lihat Bukti Dokumen">
                                                                Dokumen
                                                            </a>
                                                        @endif
                                                        <span class="badge bg-light text-secondary border px-2 py-1.5 small">
                                                            Selesai Ditinjau
                                                        </span>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-5">
                                                Tidak ada pengajuan permohonan ATK yang sesuai kriteria.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($requests->hasPages())
                            <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2 pt-3 border-top">
                                <div class="text-muted small">
                                    Menampilkan <strong>{{ $requests->firstItem() }}</strong> - <strong>{{ $requests->lastItem() }}</strong> dari <strong>{{ $requests->total() }}</strong> pengajuan
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
