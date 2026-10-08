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
                        <span class="text-secondary small fw-bold text-uppercase d-block mb-1">Disetujui TU (Proses Beli)</span>
                        <h3 class="fw-bold text-info mb-0">{{ $countApproved }}</h3>
                        <small class="text-muted">Akan dibeli Subbag TU</small>
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
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('pengajuan.pengadaan.create') }}" class="btn btn-success btn-sm fw-bold d-inline-flex align-items-center gap-1 shadow-sm" title="Ajukan kebutuhan barang yang belum ada di gudang agar dibeli oleh Subbag TU">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                    <line x1="3" y1="6" x2="21" y2="6"></line>
                                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                                </svg>
                                + Permintaan Pengadaan (Beli Baru)
                            </a>
                            <a href="{{ route('pengajuan.create') }}" class="btn btn-primary btn-sm" title="Pengajuan dari stok gudang">
                                + Buat Pengajuan (Stok)
                            </a>
                        </div>
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
                                                @if ($req->is_procurement)
                                                    <br><span class="badge bg-warning-subtle text-dark border px-2 py-0.5 mt-1" style="font-size: 0.72rem;">
                                                        Pengadaan (Beli TU)
                                                    </span>
                                                @endif
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
                                                        <li class="mb-1">
                                                            <strong>{{ $detail->item_name }}</strong>
                                                            @if (in_array($req->status, ['approved', 'distributed']))
                                                                @if ($detail->approved_quantity !== null && $detail->approved_quantity != $detail->quantity)
                                                                    <span class="badge bg-success-subtle text-success border px-1.5 py-0.5">Disetujui: {{ $detail->effective_quantity }} {{ $detail->item_unit }}</span>
                                                                    <small class="text-muted">(Diminta: {{ $detail->quantity }} {{ $detail->item_unit }})</small>
                                                                @else
                                                                    <span class="badge bg-success-subtle text-success border px-1.5 py-0.5">Disetujui: {{ $detail->effective_quantity }} {{ $detail->item_unit }}</span>
                                                                @endif
                                                            @else
                                                                <span class="text-secondary">({{ $detail->quantity }} {{ $detail->item_unit }})</span>
                                                            @endif

                                                            @if ($detail->specification)
                                                                <small class="text-muted d-block fst-italic">Spesifikasi: {{ $detail->specification }}</small>
                                                            @endif

                                                            @if ($detail->verification_note)
                                                                <small class="text-primary d-block fw-semibold fst-italic mt-0.5">
                                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                                                    Catatan Kasubag TU: "{{ $detail->verification_note }}"
                                                                </small>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>

                                                @if ($req->verification_notes)
                                                    <div class="mt-1 pt-1 border-top small text-primary fst-italic" style="font-size: 0.75rem;">
                                                        <strong>Catatan Kasubag TU:</strong> {{ $req->verification_notes }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-secondary">{{ $req->notes ?? '-' }}</small>
                                            </td>
                                            <td>
                                                @if ($req->status == 'pending')
                                                    <span class="badge bg-warning text-dark">
                                                        {{ $req->is_procurement ? 'Menunggu Pembelian TU' : 'Menunggu Kasubag TU' }}
                                                    </span>
                                                @elseif($req->status == 'approved')
                                                    <span class="badge bg-info text-white">
                                                        Disetujui TU (Akan Dibeli)
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

                                                    <!-- Tombol Cetak Bukti Serah Terima (Hanya jika sudah diserahterimakan/didistribusikan) -->
                                                    @if ($req->status === 'distributed')
                                                        <a href="{{ route('distribusi.bukti', $req->id) }}" target="_blank"
                                                            class="btn btn-sm btn-outline-secondary" title="Lihat Bukti Foto Serah Terima">
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
                                                                        <small class="text-success fw-bold d-block">Barang Telah Dibeli Subbag TU &amp; Masuk Daftar Barang:</small>
                                                                        <span class="text-dark small">Nomor Faktur: <strong>{{ $req->purchase_invoice }}</strong> ({{ $req->purchase_date ? $req->purchase_date->format('d/m/Y') : '-' }})</span>
                                                                        <div class="mt-1 pt-1 border-top border-success-subtle">
                                                                            <a href="{{ route('katalog-atk') }}" class="badge bg-success text-white text-decoration-none py-1 px-2">
                                                                                Cek Ketersediaan di Katalog ATK &rarr;
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                @if ($req->verification_notes)
                                                                    <div class="card bg-info-subtle border-0 p-2.5 mb-2">
                                                                        <small class="text-primary fw-bold d-block">Catatan Verifikasi Subbag TU:</small>
                                                                        <span class="text-dark small">{{ $req->verification_notes }}</span>
                                                                    </div>
                                                                @endif

                                                                @if ($req->status === 'distributed')
                                                                    <div class="alert alert-success py-2 mb-0 small">
                                                                        <strong>Telah Diterima:</strong> {{ $req->receiver_name ?? '-' }}<br>
                                                                        <strong>Waktu Penyerahan:</strong> {{ $req->distributed_at ? $req->distributed_at->format('d/m/Y H:i') : '-' }}<br>
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
                                                            </div>
                                                            <div class="modal-footer">
                                                                @if ($req->status === 'distributed')
                                                                    <a href="{{ route('distribusi.bukti', $req->id) }}" target="_blank"
                                                                        class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1">
                                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                                                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                                                            <polyline points="21 15 16 10 5 21"></polyline>
                                                                        </svg>
                                                                        Lihat Bukti Foto PIC
                                                                    </a>
                                                                @elseif ($req->is_procurement && $req->status === 'approved')
                                                                    <span class="badge bg-warning-subtle text-dark border me-auto py-2 px-2.5 d-inline-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                            <circle cx="12" cy="12" r="10"></circle>
                                                                            <polyline points="12 6 12 12 16 14"></polyline>
                                                                        </svg>
                                                                        Barang dalam proses pembelian Subbag TU
                                                                    </span>
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
