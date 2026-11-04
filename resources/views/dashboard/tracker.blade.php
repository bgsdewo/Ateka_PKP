{{-- ============================================================
     PELACAKAN STATUS PENGAJUAN ATK
     Alur:
       Pengadaan  : Pengajuan -> Verifikasi TU -> Input Stok Gudang -> Penyerahan -> Selesai (bukti TTD)
       Stok ada   : Pengajuan -> Verifikasi TU -> Penyerahan -> Selesai (bukti TTD)
     Variabel dari controller: $trackedRequests, $selectedPeriodLabel
     ============================================================ --}}
@php
    $trkUser = auth()->user();
    $trkTime = fn($dt) => $dt ? $dt->copy()->timezone('Asia/Jakarta')->format('d-m-Y H:i') . ' WIB' : null;
    $trkQty = fn($d) => $d->effective_quantity;
    $trkUnit = fn($d) => $d->item_unit;

    /**
     * Susun langkah tracker untuk satu tiket ($r). Jika $item diberikan, hanya untuk barang itu.
     */
    $buildSteps = function ($r, $item = null) use ($trkTime, $trkQty, $trkUnit, $trkUser) {
        $details = $item ? collect([$item]) : $r->details;
        $n = $details->count();
        $st = $r->status;
        $isProc = (bool) $r->is_procurement;
        $rejected = $st === 'rejected';
        $approved = in_array($st, ['approved', 'distributed'], true);
        $distributed = $st === 'distributed';
        $signed = $distributed && !empty($r->signed_proof);

        $reqQty = (int) $details->sum('quantity');
        $apprQty = (int) $details->sum(fn($d) => $trkQty($d));
        $fulfilledCnt = $details->filter(fn($d) => (bool) $d->is_fulfilled)->count();
        $allFilled = $distributed || ($n > 0 && $fulfilledCnt >= $n);
        $someFilled = $fulfilledCnt > 0;
        $filledAt = $details->pluck('fulfilled_at')->filter()->max();

        $qtyLabel = function ($q) use ($item, $trkUnit) {
            return $item ? ($q . ' ' . $trkUnit($item)) : ($q . ' unit');
        };

        $steps = [];

        // 1. Pengajuan dibuat
        $steps[] = [
            'icon' => 'doc',
            'state' => $rejected ? 'done-rejected' : 'done',
            'title' => 'Pengajuan Dibuat',
            'subtitle' => '(Diminta ' . $qtyLabel($reqQty) . ($item ? '' : ' / ' . $n . ' barang') . ')',
            'time' => $trkTime($r->created_at),
        ];

        // 2. Verifikasi Kasubbag TU
        if ($rejected) {
            $steps[] = ['icon' => 'x', 'state' => 'rejected', 'title' => 'Ditolak Kasubbag TU',
                'subtitle' => '(Lihat alasan di bawah)', 'time' => $trkTime($r->verified_at ?? $r->updated_at)];
        } elseif ($st === 'pending') {
            $steps[] = ['icon' => 'check', 'state' => 'current', 'title' => 'Verifikasi Kasubbag TU',
                'subtitle' => '(Sedang Ditinjau)', 'time' => 'Menunggu...'];
        } else {
            $sub = $apprQty !== $reqQty
                ? '(Disetujui ' . $qtyLabel($apprQty) . ' dari ' . $reqQty . ')'
                : '(Disetujui ' . $qtyLabel($apprQty) . ')';
            $steps[] = ['icon' => 'check', 'state' => 'done', 'title' => 'Disetujui Kasubbag TU',
                'subtitle' => $sub, 'time' => $trkTime($r->verified_at ?? $r->updated_at)];
        }

        // 3. Input stok gudang (hanya alur pengadaan)
        if ($isProc) {
            if (!$approved) {
                $steps[] = ['icon' => 'box', 'state' => 'upcoming', 'title' => 'Input Stok Gudang',
                    'subtitle' => '(Menunggu Persetujuan)', 'time' => null];
            } elseif (!$allFilled) {
                $steps[] = ['icon' => 'box', 'state' => 'current', 'title' => 'Input Stok Gudang',
                    'subtitle' => $someFilled ? "({$fulfilledCnt}/{$n} Barang Terisi)" : '(Menunggu Pemeriksa Gudang)',
                    'time' => null];
            } else {
                $steps[] = ['icon' => 'box', 'state' => 'done', 'title' => 'Stok Terisi di Gudang',
                    'subtitle' => '(Sesuai Jumlah Disetujui)',
                    'time' => $trkTime($filledAt ?? $r->updated_at)];
            }
        }

        // 4. Penyerahan barang
        $readyToHandOver = $approved && (!$isProc || $allFilled);
        if ($distributed) {
            $steps[] = ['icon' => 'truck', 'state' => 'done', 'title' => 'Barang Diserahkan',
                'subtitle' => '(Penerima: ' . ($r->receiver_name ?: ($r->user->name ?? '-')) . ')',
                'time' => $trkTime($r->distributed_at ?? $r->updated_at)];
        } elseif ($readyToHandOver) {
            $steps[] = ['icon' => 'truck', 'state' => 'current', 'title' => 'Penyerahan Barang',
                'subtitle' => '(Siap Diserahkan Petugas Distribusi)', 'time' => null];
        } else {
            $steps[] = ['icon' => 'truck', 'state' => 'upcoming', 'title' => 'Penyerahan Barang',
                'subtitle' => $approved ? '(Menunggu Stok Terisi)' : '(Menunggu Antrean)', 'time' => null];
        }

        // 5. Selesai: bukti serah terima ditandatangani & diunggah
        if ($signed) {
            $steps[] = ['icon' => 'flag', 'state' => 'final', 'title' => 'Selesai',
                'subtitle' => '(Bukti Serah Terima Lengkap)',
                'time' => $trkTime($r->signed_at ?? $r->updated_at)];
        } elseif ($distributed) {
            $isPic = $trkUser && ($trkUser->isPicDistribusi() || $r->type === 'subdit');
            $steps[] = ['icon' => 'flag', 'state' => 'waiting', 'title' => 'Menunggu Bukti TTD',
                'subtitle' => $isPic ? '(Unduh & Unggah Bukti Serah Terima)' : '(PIC Distribusi Mengunggah Bukti)',
                'time' => null];
        } else {
            $steps[] = ['icon' => 'flag', 'state' => 'upcoming', 'title' => 'Selesai',
                'subtitle' => '(Bukti Serah Terima)', 'time' => null];
        }

        return $steps;
    };

    $trkBadge = function ($r) {
        $st = $r->status;
        $isProc = (bool) $r->is_procurement;
        if ($st === 'rejected') return ['Ditolak Kasubbag TU', 'bg-danger text-white'];
        if ($st === 'pending') return ['Menunggu Review Kasubbag TU', 'bg-warning text-dark'];
        if ($st === 'distributed') {
            return empty($r->signed_proof)
                ? ['Diserahkan - Menunggu Bukti TTD', 'bg-warning text-dark']
                : ['Selesai', 'bg-success text-white'];
        }
        if ($isProc) {
            $d = $r->details;
            $all = $d->count() > 0 && $d->every(fn($x) => (bool) $x->is_fulfilled);
            return $all ? ['Stok Terisi - Siap Diserahkan', 'bg-info text-dark'] : ['Disetujui - Menunggu Input Stok', 'bg-primary-subtle text-primary border border-primary-subtle'];
        }
        return ['Disetujui - Siap Diserahkan', 'bg-info text-dark'];
    };
@endphp

@if (isset($trackedRequests) && $trackedRequests->isNotEmpty())
    <style>
        .atk-stepper-wrapper { position: relative; display: flex; justify-content: space-between; align-items: flex-start; padding: 12px 8px 4px; }
        .atk-stepper-item { position: relative; flex: 1 1 0; display: flex; flex-direction: column; align-items: center; text-align: center; min-width: 0; padding: 0 .25rem; z-index: 1; }
        .atk-stepper-item:not(:last-child)::after { content: ''; position: absolute; top: 24px; left: calc(50% + 26px); width: calc(100% - 52px); height: 4px; background-color: #e2e8f0; z-index: 0; border-radius: 2px; transition: background-color .3s ease; }
        .atk-stepper-item.line-completed:not(:last-child)::after { background-color: #22c55e; }
        .atk-stepper-item.line-rejected:not(:last-child)::after { background-color: #ef4444; }
        .atk-step-circle { width: 52px; height: 52px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #fff; border: 3px solid #cbd5e1; color: #94a3b8; margin-bottom: 10px; position: relative; z-index: 2; transition: all .3s ease; }
        .atk-stepper-item.step-done .atk-step-circle { border-color: #22c55e; color: #22c55e; box-shadow: 0 4px 10px rgba(34, 197, 94, .12); }
        .atk-stepper-item.step-current .atk-step-circle { border-color: #22c55e; background: #22c55e; color: #fff; box-shadow: 0 6px 16px rgba(34, 197, 94, .32); }
        .atk-stepper-item.step-waiting .atk-step-circle { border-color: #f59e0b; background: #fffbeb; color: #b45309; box-shadow: 0 6px 16px rgba(245, 158, 11, .25); }
        .atk-stepper-item.step-rejected .atk-step-circle { border-color: #ef4444; background: #ef4444; color: #fff; box-shadow: 0 6px 16px rgba(239, 68, 68, .28); }
        .atk-step-title { font-size: .82rem; font-weight: 700; color: #334155; margin-bottom: 2px; line-height: 1.3; }
        .atk-stepper-item.step-upcoming .atk-step-title { color: #94a3b8; font-weight: 600; }
        .atk-stepper-item.step-rejected .atk-step-title { color: #dc2626; }
        .atk-stepper-item.step-waiting .atk-step-title { color: #b45309; }
        .atk-step-subtitle { font-size: .74rem; color: #475569; font-weight: 500; margin-bottom: 2px; }
        .atk-step-time { font-size: .68rem; color: #94a3b8; font-family: monospace; }

        /* HP & layar sempit: langkah menjadi vertikal, tanpa geser samping */
        @media (max-width: 767.98px) {
            .atk-stepper-wrapper { flex-direction: column; align-items: stretch; padding: 4px 0; }
            .atk-stepper-item { flex-direction: row; align-items: flex-start; text-align: left; gap: .75rem; padding: 0 0 1rem; }
            .atk-step-circle { width: 42px; height: 42px; flex: 0 0 42px; margin: 0; border-width: 3px; }
            .atk-step-circle svg { width: 20px; height: 20px; }
            .atk-stepper-item:not(:last-child)::after { top: 42px; left: 19px; width: 4px; height: calc(100% - 42px); }
            .atk-step-text { padding-top: .1rem; min-width: 0; }
        }
    </style>

    <div class="card border-0 shadow-sm mb-4 bg-white rounded-3">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-circle p-2 d-flex align-items-center justify-content-center">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                </span>
                <h5 class="mb-0 fw-bold text-dark" style="font-size: 1rem;">Pelacakan Status Pengajuan ATK</h5>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if ($trackedRequests->count() > 1)
                    <label for="trackerRequestSelector" class="small text-muted fw-semibold mb-0">Pilih Pengajuan:</label>
                    <select id="trackerRequestSelector" class="form-select form-select-sm fw-semibold text-dark" style="width: auto; min-width: 230px; max-width: 100%; border-radius: 8px;" onchange="switchTrackedRequest(this.value)">
                        @foreach ($trackedRequests as $idx => $tItem)
                            <option value="{{ $tItem->id }}" {{ $idx === 0 ? 'selected' : '' }}>
                                {{ $tItem->request_code }} &mdash; {{ $tItem->details->first()->item_name ?? 'ATK' }}{{ $tItem->details->count() > 1 ? ' (+' . ($tItem->details->count() - 1) . ' lainnya)' : '' }}
                            </option>
                        @endforeach
                    </select>
                @endif
                <a href="{{ route('pengajuan.index') }}" class="btn btn-sm btn-outline-primary fw-semibold" style="border-radius: 8px;">Detail Riwayat &rarr;</a>
            </div>
        </div>
        <div class="card-body px-4 py-4">
            @foreach ($trackedRequests as $idx => $tReq)
                @php
                    $itemCount = $tReq->details->count();
                    [$badgeText, $badgeCls] = $trkBadge($tReq);
                    $rejReason = $tReq->status === 'rejected' ? ($tReq->rejection_reason ?: 'Tidak ada catatan dari Kasubbag TU.') : null;
                @endphp
                <div class="tracked-request-panel {{ $idx === 0 ? '' : 'd-none' }}" id="tracked-panel-{{ $tReq->id }}">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 pb-2 border-bottom border-light">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="font-monospace fw-bold text-primary fs-6">{{ $tReq->request_code }}</span>
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill" style="font-size: .72rem;">
                                {{ $tReq->is_procurement ? 'Pengadaan Barang Baru' : 'Permintaan Stok ATK' }}
                            </span>
                            <span class="badge {{ $badgeCls }}" style="font-size: .74rem;">{{ $badgeText }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">
                            @if ($tReq->status === 'distributed' && !$trkUser->isPegawai())
                                <a href="{{ route('distribusi.bukti', $tReq->id) }}" target="_blank" class="btn btn-sm btn-outline-success fw-semibold py-0 px-2" style="font-size: .74rem;">Cetak Bukti</a>
                                @if (!$tReq->signed_proof)
                                    <a href="{{ route('pengajuan.index') }}" class="btn btn-sm btn-warning text-dark fw-bold py-0 px-2" style="font-size: .74rem;">Upload Berkas TTD</a>
                                @endif
                            @endif
                            @if ($itemCount > 1)
                                <label for="itemSelect-{{ $tReq->id }}" class="small text-secondary fw-bold text-uppercase mb-0 d-none d-md-inline" style="font-size: .7rem;">Pantau Barang:</label>
                                <select id="itemSelect-{{ $tReq->id }}" class="form-select form-select-sm fw-bold text-dark item-tracker-selector" style="width: auto; min-width: 220px; max-width: 100%; border-radius: 8px; font-size: .8rem;" onchange="switchTrackedItem('{{ $tReq->id }}', this.value)">
                                    <option value="all" selected>Ringkasan Semua Barang ({{ $itemCount }} Jenis)</option>
                                    @foreach ($tReq->details as $dItem)
                                        <option value="item-{{ $dItem->id }}">{{ $dItem->item_name ?? 'Barang ATK' }} ({{ $trkQty($dItem) }} {{ $trkUnit($dItem) }})</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                    </div>

                    {{-- Ringkasan semua barang --}}
                    <div class="item-view-panel item-view-{{ $tReq->id }}" id="item-panel-{{ $tReq->id }}-all">
                        @if ($itemCount === 1)
                            @php $only = $tReq->details->first(); @endphp
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-2 px-1">
                                <span class="fw-bold text-dark">{{ $only->item_name ?? 'Barang ATK' }}</span>
                                <span class="badge bg-light text-secondary border fw-medium" style="font-size: .74rem;">Diminta: {{ $only->quantity }} {{ $trkUnit($only) }}</span>
                            </div>
                        @endif
                        @include('dashboard.tracker_steps', ['steps' => $buildSteps($tReq), 'rejectedReason' => $rejReason])
                    </div>

                    {{-- Per barang --}}
                    @if ($itemCount > 1)
                        @foreach ($tReq->details as $dItem)
                            <div class="item-view-panel item-view-{{ $tReq->id }} d-none" id="item-panel-{{ $tReq->id }}-item-{{ $dItem->id }}">
                                <div class="d-flex align-items-center gap-2 flex-wrap mb-2 px-1">
                                    <span class="fw-bold text-dark">{{ $dItem->item_name ?? 'Barang ATK' }}</span>
                                    <span class="badge bg-light text-secondary border fw-medium" style="font-size: .74rem;">Diminta: {{ $dItem->quantity }} {{ $trkUnit($dItem) }}</span>
                                </div>
                                @include('dashboard.tracker_steps', ['steps' => $buildSteps($tReq, $dItem), 'rejectedReason' => $rejReason])
                            </div>
                        @endforeach
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@else
    <div class="card border-0 shadow-sm mb-4 rounded-3 p-4" style="background: #ffffff; border-left: 4px solid #10b981 !important;">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background: #ecfdf5; color: #059669;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
            </div>
            <div>
                <h6 class="fw-bold mb-1 text-dark" style="font-size: .98rem;">Tidak Ada Pengajuan yang Sedang Diproses</h6>
                <p class="text-muted small mb-0" style="font-size: .8rem;">
                    Seluruh permohonan ATK Anda telah selesai atau Anda belum membuat pengajuan baru pada periode {{ $selectedPeriodLabel ?? '' }}.
                </p>
            </div>
        </div>
    </div>
@endif
