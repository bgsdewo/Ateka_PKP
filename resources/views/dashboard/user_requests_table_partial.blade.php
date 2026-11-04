            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-0 fw-bold">Permintaan ATK Terakhir Anda</h5>
                                <small class="text-muted">Status permohonan terkini Anda di sistem</small>
                            </div>
                            <a href="{{ route('pengajuan.index') }}" class="btn btn-sm btn-outline-primary">
                                Lihat Semua Riwayat
                            </a>
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
                                        @forelse ($userRequests as $req)
                                            <tr>
                                                <td style="white-space: nowrap;">
                                                    <div class="font-monospace fw-bold text-primary" style="font-size: 0.78rem; letter-spacing: -0.01em;">{{ $req->request_code }}</div>
                                                    @if ($req->is_procurement)
                                                        <span class="badge bg-warning-subtle text-dark border px-1.5 py-0.5 mt-0.5" style="font-size: 0.65rem;">Pengadaan</span>
                                                    @endif
                                                </td>
                                                <td><span class="badge {{ $req->type === 'subdit' ? 'bg-info text-white' : 'bg-secondary' }}" style="font-size: 0.68rem; padding: 0.2rem 0.45rem;">{{ ucfirst($req->type) }}</span></td>
                                                <td>
                                                    <div class="text-dark fw-semibold" style="font-size: 0.74rem;">{{ $req->created_at->timezone('Asia/Jakarta')->format('d/m/Y') }}</div>
                                                    <div class="text-muted" style="font-size: 0.68rem;">{{ $req->created_at->timezone('Asia/Jakarta')->format('H:i') }} WIB</div>
                                                </td>
                                                <td style="white-space: normal; word-break: break-word; overflow-wrap: anywhere;">
                                                    <ul class="mb-0 ps-3" style="font-size: 0.75rem; word-break: break-word; white-space: normal;">
                                                        @foreach ($req->details as $d)
                                                            <li class="mb-1" style="word-break: break-word;">
                                                                <div class="fw-bold text-dark" style="line-height: 1.3;">{{ $d->item_name }}</div>
                                                                <div class="mt-0.5">
                                                                    @if (in_array($req->status, ['approved', 'distributed']))
                                                                        <span class="badge bg-success-subtle text-success border px-1.5 py-0.5" style="font-size: 0.64rem;">
                                                                            Disetujui: {{ $d->effective_quantity }} {{ $d->item_unit }}
                                                                        </span>
                                                                    @else
                                                                        <span class="text-secondary small" style="font-size: 0.68rem;">({{ $d->quantity }} {{ $d->item_unit }})</span>
                                                                    @endif
                                                                </div>

                                                                @if ($d->specification)
                                                                    <div class="text-muted fst-italic mt-0.5" style="font-size: 0.68rem; line-height: 1.25;">Spesifikasi: {{ $d->specification }}</div>
                                                                @endif

                                                                @if ($d->verification_note)
                                                                    <div class="mt-1 p-1 rounded bg-light-subtle text-primary border-start border-2 border-primary ps-1.5" style="font-size: 0.68rem; line-height: 1.3;">
                                                                        <span class="fw-bold">Catatan TU:</span> "{{ $d->verification_note }}"
                                                                    </div>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>

                                                    @if ($req->verification_notes && (!isset($d) || $req->verification_notes !== $d->verification_note))
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
                                                                <div class="mt-0.5" style="word-break: break-word;">{{ $req->verification_notes }}</div>
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
                                                            'pending' => $req->is_procurement ? 'Pending Pengadaan' : 'Pending TU',
                                                            'approved' => $req->is_procurement ? ($req->is_purchased ? 'Tersedia' : 'Disetujui TU') : 'Disetujui TU',
                                                            'distributed' => 'Selesai',
                                                            'rejected' => 'Ditolak',
                                                            default => ucfirst($req->status),
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $req->status_badge }} px-1.5 py-0.5 text-nowrap" style="font-size: 0.68rem;" title="{{ $req->status_label }}">
                                                        {{ $shortStatus }}
                                                    </span>
                                                    @if ($req->is_procurement && $req->purchase_invoice)
                                                        <div class="mt-0.5">
                                                            <span class="badge bg-primary-subtle text-primary border text-truncate d-inline-block px-1.5 py-0.2" style="font-size: 0.64rem; max-width: 110px;" title="Faktur: {{ $req->purchase_invoice }}">
                                                                Faktur: {{ $req->purchase_invoice }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                    @if ($req->is_procurement && $req->is_purchased)
                                                        <div class="mt-0.5">
                                                            <a href="{{ route('katalog-atk') }}" class="badge bg-success-subtle text-success border text-decoration-none px-1.5 py-0.2" style="font-size: 0.64rem;">
                                                                Katalog &rarr;
                                                            </a>
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
                                                        <a href="{{ route('pengajuan.index') }}" class="btn btn-xs btn-outline-primary py-0.5 px-2 fw-semibold" style="font-size: 0.72rem; border-radius: 6px;">
                                                            Detail
                                                        </a>
                                                        @if ($req->status === 'distributed' && !auth()->user()->isPegawai())
                                                            <a href="{{ route('distribusi.bukti', $req->id) }}" target="_blank"
                                                                class="btn btn-xs btn-outline-secondary py-0.5 px-1.5 fw-semibold" style="font-size: 0.72rem; border-radius: 6px;">Cetak Bukti</a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">
                                                    Belum ada riwayat pengajuan pada periode {{ $selectedPeriodLabel }}. <a href="{{ route('pengajuan.create') }}">Buat pengajuan baru sekarang</a>.
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
