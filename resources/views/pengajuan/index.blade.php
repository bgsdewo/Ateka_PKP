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
                            <h1 class="text-white fw-bold mb-1">Pengajuan Kebutuhan ATK</h1>
                            <p class="text-white mb-0" style="opacity: 0.9;">
                                Riwayat dan pemantauan status permohonan Alat Tulis Kantor (Individu & Subdirektorat).
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

        {{-- Ringkasan Status Pengajuan --}}
        @php
            $countPending = $requests->where('status', 'pending')->count();
            $countApproved = $requests->where('status', 'approved')->count();
            $countDistributed = $requests->where('status', 'distributed')->count();
            $countRejected = $requests->where('status', 'rejected')->count();
        @endphp
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-3">
                        <span class="text-secondary small fw-bold text-uppercase d-block mb-1">Menunggu Review</span>
                        <h3 class="fw-bold text-warning mb-0">{{ $countPending }}</h3>
                        <small class="text-muted">Kasubag TU</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-3">
                        <span class="text-secondary small fw-bold text-uppercase d-block mb-1">Disetujui</span>
                        <h3 class="fw-bold text-info mb-0">{{ $countApproved }}</h3>
                        <small class="text-muted">Siap diambil di gudang</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-3">
                        <span class="text-secondary small fw-bold text-uppercase d-block mb-1">Selesai Diterima</span>
                        <h3 class="fw-bold text-success mb-0">{{ $countDistributed }}</h3>
                        <small class="text-muted">Telah diserahterimakan</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-3">
                        <span class="text-secondary small fw-bold text-uppercase d-block mb-1">Ditolak</span>
                        <h3 class="fw-bold text-danger mb-0">{{ $countRejected }}</h3>
                        <small class="text-muted">Belum disetujui</small>
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
                            <small class="text-muted">Pantau status persetujuan dari Kasubag TU hingga penyerahan fisik barang oleh PIC Gudang</small>
                        </div>
                        <a href="{{ route('pengajuan.create') }}" class="btn btn-primary btn-sm">
                            + Buat Pengajuan Baru
                        </a>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th width="4%">No</th>
                                        <th>Kode Pengajuan</th>
                                        <th>Tipe & Tanggal</th>
                                        <th>Barang yang Diminta</th>
                                        <th>Keperluan</th>
                                        <th>Status Pengajuan</th>
                                        <th width="14%" class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($requests as $index => $req)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong class="font-monospace text-primary">{{ $req->request_code }}</strong>
                                            </td>
                                            <td>
                                                @if ($req->type === 'subdit')
                                                    <span class="badge bg-info-subtle text-info border px-2 py-0.5 rounded-pill">
                                                        Kolektif Subdit
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-0.5 rounded-pill">
                                                        Individu
                                                    </span>
                                                @endif
                                                <br>
                                                <small class="text-muted">{{ $req->created_at->format('d/m/Y H:i') }}</small>
                                            </td>
                                            <td>
                                                <ul class="ps-3 mb-0 small">
                                                    @foreach ($req->details as $detail)
                                                        <li>
                                                            {{ $detail->product->name ?? 'Barang' }}
                                                            (<strong>{{ $detail->quantity }} Pcs</strong>)
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                            <td>
                                                <small class="text-secondary">{{ $req->notes ?? '-' }}</small>
                                            </td>
                                            <td>
                                                @if ($req->status == 'pending')
                                                    <span class="badge bg-warning text-dark">
                                                        Menunggu Kasubag TU
                                                    </span>
                                                @elseif($req->status == 'approved')
                                                    <span class="badge bg-info text-white">
                                                        Disetujui - Siap Ambil di Gudang
                                                    </span>
                                                @elseif($req->status == 'distributed')
                                                    <span class="badge bg-success">
                                                        Selesai Diserahterimakan
                                                    </span>
                                                    @if ($req->receiver_name)
                                                        <br><small class="text-muted">Penerima: {{ $req->receiver_name }}</small>
                                                    @endif
                                                @else
                                                    <span class="badge bg-danger">Ditolak</span>
                                                    @if ($req->rejection_reason)
                                                        <br><small class="text-danger">Alasan: "{{ $req->rejection_reason }}"</small>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end gap-1 flex-wrap">
                                                    <!-- Tombol Detail Modal -->
                                                    <button type="button" class="btn btn-sm btn-outline-info"
                                                        data-bs-toggle="modal" data-bs-target="#detailModal{{ $req->id }}">
                                                        Detail
                                                    </button>

                                                    <!-- Tombol Cetak Bukti Serah Terima (Jika sudah disetujui / didistribusikan) -->
                                                    @if (in_array($req->status, ['approved', 'distributed']))
                                                        <a href="{{ route('distribusi.bukti', $req->id) }}" target="_blank"
                                                            class="btn btn-sm btn-outline-secondary" title="Cetak Bukti Permintaan">
                                                            Bukti
                                                        </a>
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
                                                                        <small class="text-muted">Diajukan: {{ $req->created_at->format('d F Y, H:i') }}</small>
                                                                    </div>
                                                                    <span class="badge {{ $req->status_badge }}">{{ $req->status_label }}</span>
                                                                </div>

                                                                <h6 class="fw-bold mb-2 small text-uppercase text-secondary">Daftar Barang yang Diminta:</h6>
                                                                <ul class="list-group mb-3">
                                                                    @foreach ($req->details as $d)
                                                                        <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                                                            <div>
                                                                                <span class="fw-bold text-dark">{{ $d->product->name ?? 'Barang' }}</span>
                                                                                <small class="text-muted d-block font-monospace">{{ $d->product->code ?? '-' }}</small>
                                                                            </div>
                                                                            <span class="badge bg-primary rounded-pill">{{ $d->quantity }} Pcs</span>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>

                                                                <div class="card bg-light border-0 p-2.5 mb-2">
                                                                    <small class="text-muted d-block">Keperluan / Keterangan:</small>
                                                                    <strong class="text-dark">{{ $req->notes ?? '-' }}</strong>
                                                                </div>

                                                                @if ($req->status === 'distributed')
                                                                    <div class="alert alert-success py-2 mb-0 small">
                                                                        <strong>Telah Diterima:</strong> {{ $req->receiver_name ?? '-' }}<br>
                                                                        <strong>Waktu Penyerahan:</strong> {{ $req->distributed_at ? $req->distributed_at->format('d/m/Y H:i') : '-' }}<br>
                                                                        @if ($req->distribution_notes)
                                                                            <strong>Catatan Gudang:</strong> {{ $req->distribution_notes }}
                                                                        @endif
                                                                    </div>
                                                                @elseif ($req->status === 'rejected')
                                                                    <div class="alert alert-danger py-2 mb-0 small">
                                                                        <strong>Alasan Penolakan:</strong> {{ $req->rejection_reason ?? '-' }}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer">
                                                                @if (in_array($req->status, ['approved', 'distributed']))
                                                                    <a href="{{ route('distribusi.bukti', $req->id) }}" target="_blank"
                                                                        class="btn btn-secondary btn-sm">
                                                                        Cetak Bukti Tanda Terima
                                                                    </a>
                                                                @endif
                                                                <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-5">
                                                Belum ada riwayat pengajuan permintaan barang. Silakan buat pengajuan baru.
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
    </div>
@endsection
