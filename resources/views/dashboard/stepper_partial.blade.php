            {{-- Widget Pelacakan Status Pengajuan ATK (Horizontal Stepper) --}}
            @if(isset($trackedRequests) && $trackedRequests->isNotEmpty())
                <style>
                    .atk-stepper-wrapper {
                        position: relative;
                        display: flex;
                        justify-content: space-between;
                        align-items: flex-start;
                        padding: 12px 8px 4px;
                        overflow-x: auto;
                    }
                    .atk-stepper-item {
                        position: relative;
                        flex: 1;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        text-align: center;
                        min-width: 145px;
                        z-index: 1;
                    }
                    .atk-stepper-item:not(:last-child)::after {
                        content: '';
                        position: absolute;
                        top: 28px;
                        left: calc(50% + 28px);
                        width: calc(100% - 56px);
                        height: 4px;
                        background-color: #e2e8f0;
                        z-index: 0;
                        transition: background-color 0.3s ease;
                        border-radius: 2px;
                    }
                    .atk-stepper-item.line-completed:not(:last-child)::after {
                        background-color: #22c55e;
                    }
                    .atk-stepper-item.line-rejected:not(:last-child)::after {
                        background-color: #ef4444;
                    }
                    .atk-step-circle {
                        width: 56px;
                        height: 56px;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        background-color: #ffffff;
                        border: 3px solid #cbd5e1;
                        color: #94a3b8;
                        margin-bottom: 12px;
                        transition: all 0.3s ease;
                        position: relative;
                        z-index: 2;
                    }
                    /* Tahap yang sudah terlewati: lingkaran putih dengan garis & ikon hijau */
                    .atk-stepper-item.step-done .atk-step-circle {
                        border-color: #22c55e;
                        color: #22c55e;
                        background-color: #ffffff;
                        box-shadow: 0 4px 10px rgba(34, 197, 94, 0.12);
                    }
                    /* Tahap aktif saat ini / tahap akhir selesai: lingkaran hijau penuh dengan ikon putih */
                    .atk-stepper-item.step-current .atk-step-circle {
                        border-color: #22c55e;
                        background-color: #22c55e;
                        color: #ffffff;
                        box-shadow: 0 6px 16px rgba(34, 197, 94, 0.32);
                    }
                    /* Tahap ditolak: lingkaran merah penuh dengan ikon putih */
                    .atk-stepper-item.step-rejected .atk-step-circle {
                        border-color: #ef4444;
                        background-color: #ef4444;
                        color: #ffffff;
                        box-shadow: 0 6px 16px rgba(239, 68, 68, 0.28);
                    }
                    .atk-step-title {
                        font-size: 0.86rem;
                        font-weight: 700;
                        color: #334155;
                        margin-bottom: 2px;
                        line-height: 1.3;
                    }
                    .atk-stepper-item.step-upcoming .atk-step-title {
                        color: #94a3b8;
                        font-weight: 600;
                    }
                    .atk-stepper-item.step-rejected .atk-step-title {
                        color: #dc2626;
                    }
                    .atk-step-subtitle {
                        font-size: 0.78rem;
                        color: #475569;
                        font-weight: 500;
                        margin-bottom: 2px;
                        max-width: 190px;
                    }
                    .atk-step-time {
                        font-size: 0.72rem;
                        color: #94a3b8;
                        font-family: monospace;
                    }
                </style>

                <div class="card border-0 shadow-sm mb-4 bg-white rounded-3">
                    <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2.5">
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-circle p-2 d-flex align-items-center justify-content-center">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                                </svg>
                            </span>
                            <div>
                                <h5 class="mb-0 fw-bold text-dark" style="font-size: 1rem;">Pelacakan Status Pengajuan ATK</h5>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            @if($trackedRequests->count() > 1)
                                <label for="trackerRequestSelector" class="small text-muted fw-semibold mb-0">Pilih Pengajuan:</label>
                                <select id="trackerRequestSelector" class="form-select form-select-sm fw-semibold text-dark" style="width: auto; min-width: 230px; border-radius: 8px;" onchange="switchTrackedRequest(this.value)">
                                    @foreach($trackedRequests as $idx => $tItem)
                                        <option value="{{ $tItem->id }}" {{ $idx === 0 ? 'selected' : '' }}>
                                            {{ $tItem->request_code }} &mdash; {{ $tItem->details->first()->item_name ?? 'ATK' }}{{ $tItem->details->count() > 1 ? ' (+' . ($tItem->details->count() - 1) . ' lainnya)' : '' }} ({{ $tItem->status_label }})
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                            <a href="{{ route('pengajuan.index') }}" class="btn btn-sm btn-outline-primary fw-semibold" style="border-radius: 8px;">
                                Detail Riwayat &rarr;
                            </a>
                        </div>
                    </div>

                    <div class="card-body px-4 py-4">
                        @foreach($trackedRequests as $idx => $tReq)
                            @php
                                $status = $tReq->status; // pending, approved, distributed, rejected
                                $itemCount = $tReq->details->count();
                                $firstItemName = $tReq->details->first()->item_name ?? 'Barang ATK';
                                $summaryItems = $itemCount > 1 ? "{$firstItemName} (+".($itemCount - 1)." barang)" : $firstItemName;

                                if ($tReq->is_procurement) {
                                    $fulfilledCount = $tReq->details->where('is_fulfilled', true)->count();
                                    $hasAnyFulfilled = $fulfilledCount > 0 || $tReq->is_purchased;
                                    $hasAllFulfilled = ($itemCount > 0 && $fulfilledCount >= $itemCount) || $tReq->is_purchased;

                                    // WORKFLOW PENGADAAN BARANG BARU (RESTOCK)
                                    // Step 1: Usulan Diajukan
                                    $s1Class = ($status === 'pending') ? 'step-done line-completed' : (($status === 'rejected') ? 'step-done line-rejected' : 'step-done line-completed');

                                    // Step 2: Verifikasi Kasubbag TU
                                    if ($status === 'pending') {
                                        $s2Class = 'step-current';
                                    } elseif ($status === 'rejected') {
                                        $s2Class = 'step-rejected';
                                    } else {
                                        $s2Class = 'step-done line-completed';
                                    }

                                    // Step 3: Proses Pembelian Supplier
                                    if ($status === 'approved') {
                                        $s3Class = $hasAnyFulfilled ? 'step-done line-completed' : 'step-current';
                                    } else {
                                        $s3Class = 'step-upcoming';
                                    }

                                    // Step 4: Tersedia di Katalog Persediaan
                                    if ($status === 'approved' && $hasAnyFulfilled) {
                                        $s4Class = $hasAllFulfilled ? 'step-done' : 'step-current';
                                    } else {
                                        $s4Class = 'step-upcoming';
                                    }
                                } else {
                                    // WORKFLOW PERMINTAAN STOK GUDANG (DISTRIBUSI)
                                    // Step 1: Pengajuan Dibuat
                                    $s1Class = ($status === 'pending') ? 'step-done line-completed' : (($status === 'rejected') ? 'step-done line-rejected' : 'step-done line-completed');

                                    // Step 2: Verifikasi Kasubbag TU
                                    if ($status === 'pending') {
                                        $s2Class = 'step-current';
                                    } elseif ($status === 'rejected') {
                                        $s2Class = 'step-rejected';
                                    } else {
                                        $s2Class = 'step-done line-completed';
                                    }

                                    // Step 3: Diproses Gudang
                                    if ($status === 'approved') {
                                        $s3Class = 'step-current';
                                    } elseif ($status === 'distributed') {
                                        $s3Class = 'step-done line-completed';
                                    } else {
                                        $s3Class = 'step-upcoming';
                                    }

                                    // Step 4: Selesai Diserahterimakan
                                    if ($status === 'distributed') {
                                        $s4Class = 'step-current';
                                    } else {
                                        $s4Class = 'step-upcoming';
                                    }
                                }
                            @endphp

                            <div class="tracked-request-panel {{ $idx === 0 ? '' : 'd-none' }}" id="tracked-panel-{{ $tReq->id }}">
                                {{-- Info Bar Pengajuan & Dropdown Pilihan Barang --}}
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 pb-2.5 border-bottom border-light">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="font-monospace fw-bold text-primary fs-6">{{ $tReq->request_code }}</span>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill py-0.5 px-2" style="font-size: 0.72rem;">
                                            {{ $tReq->is_procurement ? 'Pengadaan Barang Baru' : 'Permintaan Stok ATK' }}
                                        </span>
                                    </div>

                                    @if($itemCount > 1)
                                        <div class="d-flex align-items-center gap-2 ms-auto ms-sm-0">
                                            <label for="itemSelect-{{ $tReq->id }}" class="small text-secondary fw-bold text-uppercase mb-0 d-none d-md-inline" style="font-size: 0.73rem; letter-spacing: 0.03em;">
                                                Pantau Barang:
                                            </label>
                                            <select id="itemSelect-{{ $tReq->id }}" 
                                                class="form-select form-select-sm fw-bold text-dark border-primary-subtle shadow-xs item-tracker-selector" 
                                                style="width: auto; min-width: 250px; max-width: 440px; border-radius: 8px; font-size: 0.8125rem; background-color: #f8fafc;"
                                                onchange="switchTrackedItem('{{ $tReq->id }}', this.value)">
                                                @foreach($tReq->details as $dIdx => $dItem)
                                                    @php
                                                        $dItemName = $dItem->custom_product_name ?? ($dItem->product->name ?? 'Barang ATK');
                                                        $dItemQty = ($dItem->approved_quantity ?: $dItem->quantity) . ' ' . ($dItem->unit ?? 'Buah');
                                                        $dItemStatus = $tReq->is_procurement
                                                            ? ($dItem->is_fulfilled ? '✓ Tersedia di Katalog' : '⏳ Menunggu Pengadaan')
                                                            : ($tReq->status === 'distributed' ? '✓ Diserahterimakan' : '⏳ Diproses Gudang');
                                                    @endphp
                                                    <option value="item-{{ $dItem->id }}" {{ $dIdx === 0 ? 'selected' : '' }}>
                                                        📦 {{ $dItemName }} ({{ $dItemQty }}) &mdash; {{ $dItemStatus }}
                                                    </option>
                                                @endforeach
                                                <option value="all">
                                                    📊 Ringkasan Semua Barang ({{ $itemCount }} Jenis Barang)
                                                </option>
                                            </select>
                                        </div>
                                    @endif
                                </div>

                                {{-- PANEL PER-BARANG (Dipantau Masing-Masing) --}}
                                @foreach($tReq->details as $dIdx => $dItem)
                                    @php
                                        $dName = $dItem->custom_product_name ?? ($dItem->product->name ?? 'Barang ATK');
                                        $dQty = ($dItem->approved_quantity ?: $dItem->quantity) . ' ' . ($dItem->unit ?? 'Buah');
                                        $dFulfilled = (bool) $dItem->is_fulfilled;
                                        $dFulfilledAt = $dItem->fulfilled_at ?? ($dFulfilled ? $tReq->updated_at : null);

                                        if ($tReq->is_procurement) {
                                            // WORKFLOW PENGADAAN BARANG BARU (RESTOCK) PER ITEM
                                            // Step 1: Usulan Diajukan
                                            $itemS1Class = ($status === 'pending') ? 'step-done line-completed' : (($status === 'rejected') ? 'step-done line-rejected' : 'step-done line-completed');

                                            // Step 2: Verifikasi Kasubbag TU
                                            if ($status === 'pending') {
                                                $itemS2Class = 'step-current';
                                            } elseif ($status === 'rejected') {
                                                $itemS2Class = 'step-rejected';
                                            } else {
                                                $itemS2Class = 'step-done line-completed';
                                            }

                                            // Step 3: Pembelian Supplier
                                            if ($status === 'approved') {
                                                $itemS3Class = $dFulfilled ? 'step-done line-completed' : 'step-current';
                                            } else {
                                                $itemS3Class = 'step-upcoming';
                                            }

                                            // Step 4: Tersedia di Katalog
                                            if ($status === 'approved' && $dFulfilled) {
                                                $itemS4Class = 'step-done';
                                            } else {
                                                $itemS4Class = 'step-upcoming';
                                            }
                                        } else {
                                            // WORKFLOW DISTRIBUSI PER ITEM
                                            $itemS1Class = ($status === 'pending') ? 'step-done line-completed' : (($status === 'rejected') ? 'step-done line-rejected' : 'step-done line-completed');
                                            $itemS2Class = ($status === 'pending') ? 'step-current' : (($status === 'rejected') ? 'step-rejected' : 'step-done line-completed');
                                            $itemS3Class = ($status === 'approved') ? 'step-current' : (($status === 'distributed') ? 'step-done line-completed' : 'step-upcoming');
                                            $itemS4Class = ($status === 'distributed') ? 'step-current' : 'step-upcoming';
                                        }
                                    @endphp

                                    <div class="item-view-panel item-view-{{ $tReq->id }} {{ $dIdx === 0 ? '' : 'd-none' }}" id="item-panel-{{ $tReq->id }}-item-{{ $dItem->id }}">
                                        {{-- Sub-bar Info Barang & Status --}}
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 px-1">
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <span class="fw-bold text-dark fs-6">{{ $dName }}</span>
                                                <span class="badge bg-light text-secondary border fw-medium" style="font-size: 0.75rem;">
                                                    Kebutuhan: {{ $dQty }}
                                                </span>
                                            </div>

                                            <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">
                                                @if($tReq->is_procurement)
                                                    @if($status === 'rejected')
                                                        <span class="badge bg-danger text-white">Ditolak Kasubbag TU</span>
                                                    @elseif($status === 'pending')
                                                        <span class="badge bg-warning text-dark">Menunggu Review Kasubbag TU</span>
                                                    @elseif($dFulfilled)
                                                        <span class="badge bg-success text-white py-1 px-2.5 d-inline-flex align-items-center gap-1 shadow-xs" style="font-size: 0.78rem;">
                                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                            Tersedia di Katalog ATK
                                                        </span>
                                                        <a href="{{ route('katalog-atk') }}"
                                                            class="btn btn-xs btn-success py-1 px-2.5 small fw-bold d-inline-flex align-items-center gap-1 shadow-sm"
                                                            title="Barang telah dibeli dan tersedia di katalog. Klik untuk membuat pengajuan permintaan barang ini.">
                                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <circle cx="9" cy="21" r="1"></circle>
                                                                <circle cx="20" cy="21" r="1"></circle>
                                                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                                            </svg>
                                                            Pesan di Katalog ATK &rarr;
                                                        </a>
                                                    @else
                                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle py-1 px-2.5 d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;">
                                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                                            Sedang Pembelian Supplier
                                                        </span>
                                                    @endif
                                                @else
                                                    {{-- Alur Distribusi Stok --}}
                                                    <span class="badge {{ $tReq->status_badge }}">{{ $tReq->status_label }}</span>
                                                    @if(in_array($tReq->status, ['approved', 'distributed']) && !auth()->user()->isPegawai())
                                                        <a href="{{ route('distribusi.bukti', $tReq->id) }}" target="_blank"
                                                            class="btn btn-xs btn-outline-success py-0.5 px-2 small fw-semibold d-inline-flex align-items-center gap-1"
                                                            title="Cetak Dokumen Resmi Bukti Penyerahan ATK">
                                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                            Cetak Bukti
                                                        </a>
                                                    @endif
                                                    @if($tReq->status === 'distributed' && !$tReq->signed_proof && !auth()->user()->isPegawai())
                                                        <a href="{{ route('pengajuan.index') }}"
                                                            class="btn btn-xs btn-warning text-dark py-0.5 px-2 small fw-bold d-inline-flex align-items-center gap-1"
                                                            title="Unggah berkas serah terima bertanda tangan agar pelacakan selesai">
                                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                                <polyline points="17 8 12 3 7 8"></polyline>
                                                                <line x1="12" y1="3" x2="12" y2="15"></line>
                                                            </svg>
                                                            Upload Berkas TTD
                                                        </a>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>

                                        {{-- 4-Step Horizontal Stepper --}}
                                        <div class="atk-stepper-wrapper">
                                            {{-- STEP 1: Pengajuan Dibuat / Usulan Diajukan --}}
                                            <div class="atk-stepper-item {{ $itemS1Class }}">
                                                <div class="atk-step-circle">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                        <polyline points="14 2 14 8 20 8"></polyline>
                                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                                        <polyline points="10 9 9 9 8 9"></polyline>
                                                    </svg>
                                                </div>
                                                <div class="atk-step-title">
                                                    {{ $tReq->is_procurement ? 'Usulan Pengadaan' : 'Pengajuan Dibuat' }}
                                                </div>
                                                <div class="atk-step-subtitle">
                                                    ({{ $dQty }})
                                                </div>
                                                <div class="atk-step-time">{{ $tReq->created_at->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB</div>
                                            </div>

                                            {{-- STEP 2: Verifikasi Kasubbag TU --}}
                                            <div class="atk-stepper-item {{ $itemS2Class }}">
                                                <div class="atk-step-circle">
                                                    @if($status === 'rejected')
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                            <circle cx="12" cy="12" r="10"></circle>
                                                            <line x1="15" y1="9" x2="9" y2="15"></line>
                                                            <line x1="9" y1="9" x2="15" y2="15"></line>
                                                        </svg>
                                                    @else
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M9 11l3 3L22 4"></path>
                                                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div class="atk-step-title">
                                                    @if($status === 'rejected')
                                                        Ditolak Kasubbag TU
                                                    @elseif($status === 'pending')
                                                        Menunggu Review TU
                                                    @else
                                                        Disetujui Kasubbag TU
                                                    @endif
                                                </div>
                                                <div class="atk-step-subtitle">
                                                    @if($status === 'pending')
                                                        (Sedang Ditinjau)
                                                    @elseif($status === 'rejected')
                                                        ({{ \Illuminate\Support\Str::limit($tReq->rejection_reason ?? 'Tidak disetujui', 32) }})
                                                    @else
                                                        {{ $tReq->is_procurement ? '(Disetujui untuk Dibeli)' : '(Telah Diverifikasi)' }}
                                                    @endif
                                                </div>
                                                <div class="atk-step-time">
                                                    @if($status !== 'pending')
                                                        {{ ($tReq->verified_at ?? $tReq->updated_at)->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB
                                                    @else
                                                        Menunggu...
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- STEP 3: Diproses Gudang (Stok) / Pembelian Supplier (Pengadaan) --}}
                                            <div class="atk-stepper-item {{ $itemS3Class }}">
                                                <div class="atk-step-circle">
                                                    @if($tReq->is_procurement)
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <circle cx="9" cy="21" r="1"></circle>
                                                            <circle cx="20" cy="21" r="1"></circle>
                                                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                                        </svg>
                                                    @else
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <rect x="1" y="3" width="15" height="13"></rect>
                                                            <polygon points="16 8 20 8 23 11 23 16 16 16 8"></polygon>
                                                            <circle cx="5.5" cy="18.5" r="2.5"></circle>
                                                            <circle cx="18.5" cy="18.5" r="2.5"></circle>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div class="atk-step-title">
                                                    {{ $tReq->is_procurement ? 'Pembelian Supplier' : 'Diproses Gudang' }}
                                                </div>
                                                <div class="atk-step-subtitle">
                                                    @if($tReq->is_procurement)
                                                        @if($status === 'approved')
                                                            {{ $dFulfilled ? '(Telah Dibeli Subbag TU)' : '(Sedang Dibeli Kasubbag TU)' }}
                                                        @else
                                                            (Menunggu Persetujuan)
                                                        @endif
                                                    @else
                                                        @if($status === 'approved')
                                                            (Siap Diserahterimakan)
                                                        @elseif($status === 'distributed')
                                                            (Telah Disiapkan Gudang)
                                                        @else
                                                            (Menunggu Antrean)
                                                        @endif
                                                    @endif
                                                </div>
                                                <div class="atk-step-time">
                                                    @if($tReq->is_procurement)
                                                        @if($dFulfilled)
                                                            {{ ($dFulfilledAt ?? $tReq->updated_at)->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB
                                                        @else
                                                            -
                                                        @endif
                                                    @else
                                                        @if($status === 'distributed' || $status === 'approved')
                                                            {{ ($tReq->updated_at ?? $tReq->verified_at)->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB
                                                        @else
                                                            -
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- STEP 4: Selesai Diterima (Stok) / Tersedia di Katalog (Pengadaan) --}}
                                            <div class="atk-stepper-item {{ $itemS4Class }}">
                                                <div class="atk-step-circle">
                                                    @if($tReq->is_procurement)
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                                            <line x1="12" y1="22.08" x2="12" y2="12"></line>
                                                        </svg>
                                                    @else
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline>
                                                            <path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div class="atk-step-title">
                                                    {{ $tReq->is_procurement ? 'Tersedia di Katalog' : 'Selesai Diterima' }}
                                                </div>
                                                <div class="atk-step-subtitle">
                                                    @if($tReq->is_procurement)
                                                        @if($status === 'approved' && $dFulfilled)
                                                            (Stok Masuk &amp; Siap Diminta)
                                                        @else
                                                            (Menunggu Barang Tiba)
                                                        @endif
                                                    @else
                                                        @if($status === 'distributed')
                                                            @if(!$tReq->signed_proof && !auth()->user()->isPegawai())
                                                                <span class="text-warning fw-semibold">(Menunggu Berkas TTD)</span>
                                                            @else
                                                                (Penerima: {{ $tReq->receiver_name ?? $user->name }})
                                                            @endif
                                                        @else
                                                            (Bukti Serah Terima)
                                                        @endif
                                                    @endif
                                                </div>
                                                <div class="atk-step-time">
                                                    @if($tReq->is_procurement)
                                                        @if($status === 'approved' && $dFulfilled)
                                                            {{ ($dFulfilledAt ?? $tReq->updated_at)->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB
                                                        @else
                                                            -
                                                        @endif
                                                    @else
                                                        @if($status === 'distributed' && $tReq->distributed_at)
                                                            {{ $tReq->distributed_at->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB
                                                        @else
                                                            -
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- PANEL RINGKASAN SEMUA BARANG (Jika Pengajuan Multi-Barang) --}}
                                @if($itemCount > 1)
                                    <div class="item-view-panel item-view-{{ $tReq->id }} d-none" id="item-panel-{{ $tReq->id }}-all">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 px-1">
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <span class="fw-bold text-dark fs-6">Ringkasan Keseluruhan Pengajuan</span>
                                                <span class="badge bg-light text-secondary border fw-medium" style="font-size: 0.75rem;">
                                                    Total: {{ $itemCount }} Jenis Barang
                                                </span>
                                            </div>

                                            <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">
                                                <span class="badge {{ $tReq->status_badge }}">{{ $tReq->status_label }}</span>
                                                @if($tReq->is_procurement)
                                                    @if($hasAnyFulfilled)
                                                        <a href="{{ route('katalog-atk') }}"
                                                            class="btn btn-xs btn-success py-1 px-2.5 small fw-bold d-inline-flex align-items-center gap-1 shadow-sm"
                                                            title="Barang telah dibeli dan tersedia di katalog. Klik untuk membuat pengajuan permintaan barang ini.">
                                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <circle cx="9" cy="21" r="1"></circle>
                                                                <circle cx="20" cy="21" r="1"></circle>
                                                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                                            </svg>
                                                            Pesan di Katalog ATK &rarr;
                                                        </a>
                                                    @endif
                                                @else
                                                    @if(in_array($tReq->status, ['approved', 'distributed']) && !auth()->user()->isPegawai())
                                                        <a href="{{ route('distribusi.bukti', $tReq->id) }}" target="_blank"
                                                            class="btn btn-xs btn-outline-success py-0.5 px-2 small fw-semibold d-inline-flex align-items-center gap-1"
                                                            title="Cetak Dokumen Resmi Bukti Penyerahan ATK">
                                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <polyline points="20 6 9 17 4 12"></polyline>
                                                            </svg>
                                                            Cetak Bukti
                                                        </a>
                                                    @endif
                                                    @if($tReq->status === 'distributed' && !$tReq->signed_proof && !auth()->user()->isPegawai())
                                                        <a href="{{ route('pengajuan.index') }}"
                                                            class="btn btn-xs btn-warning text-dark py-0.5 px-2 small fw-bold d-inline-flex align-items-center gap-1">
                                                            Upload Berkas TTD
                                                        </a>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>

                                        {{-- 4-Step Horizontal Stepper --}}
                                        <div class="atk-stepper-wrapper">
                                            {{-- STEP 1: Pengajuan Dibuat / Usulan Diajukan --}}
                                            <div class="atk-stepper-item {{ $s1Class }}">
                                                <div class="atk-step-circle">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                        <polyline points="14 2 14 8 20 8"></polyline>
                                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                                        <polyline points="10 9 9 9 8 9"></polyline>
                                                    </svg>
                                                </div>
                                                <div class="atk-step-title">
                                                    {{ $tReq->is_procurement ? 'Usulan Pengadaan' : 'Pengajuan Dibuat' }}
                                                </div>
                                                <div class="atk-step-subtitle">
                                                    ({{ $itemCount }} {{ $tReq->is_procurement ? 'Jenis Barang Baru' : 'Jenis Barang' }})
                                                </div>
                                                <div class="atk-step-time">{{ $tReq->created_at->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB</div>
                                            </div>

                                            {{-- STEP 2: Verifikasi Kasubbag TU --}}
                                            <div class="atk-stepper-item {{ $s2Class }}">
                                                <div class="atk-step-circle">
                                                    @if($status === 'rejected')
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                            <circle cx="12" cy="12" r="10"></circle>
                                                            <line x1="15" y1="9" x2="9" y2="15"></line>
                                                            <line x1="9" y1="9" x2="15" y2="15"></line>
                                                        </svg>
                                                    @else
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M9 11l3 3L22 4"></path>
                                                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div class="atk-step-title">
                                                    @if($status === 'rejected')
                                                        Ditolak Kasubbag TU
                                                    @elseif($status === 'pending')
                                                        Menunggu Review TU
                                                    @else
                                                        Disetujui Kasubbag TU
                                                    @endif
                                                </div>
                                                <div class="atk-step-subtitle">
                                                    @if($status === 'pending')
                                                        (Sedang Ditinjau)
                                                    @elseif($status === 'rejected')
                                                        ({{ \Illuminate\Support\Str::limit($tReq->rejection_reason ?? 'Tidak disetujui', 32) }})
                                                    @else
                                                        {{ $tReq->is_procurement ? '(Disetujui untuk Dibeli)' : '(Telah Diverifikasi)' }}
                                                    @endif
                                                </div>
                                                <div class="atk-step-time">
                                                    @if($status !== 'pending')
                                                        {{ ($tReq->verified_at ?? $tReq->updated_at)->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB
                                                    @else
                                                        Menunggu...
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- STEP 3: Diproses Gudang (Stok) / Pembelian Supplier (Pengadaan) --}}
                                            <div class="atk-stepper-item {{ $s3Class }}">
                                                <div class="atk-step-circle">
                                                    @if($tReq->is_procurement)
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <circle cx="9" cy="21" r="1"></circle>
                                                            <circle cx="20" cy="21" r="1"></circle>
                                                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                                        </svg>
                                                    @else
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <rect x="1" y="3" width="15" height="13"></rect>
                                                            <polygon points="16 8 20 8 23 11 23 16 16 16 8"></polygon>
                                                            <circle cx="5.5" cy="18.5" r="2.5"></circle>
                                                            <circle cx="18.5" cy="18.5" r="2.5"></circle>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div class="atk-step-title">
                                                    {{ $tReq->is_procurement ? 'Pembelian Supplier' : 'Diproses Gudang' }}
                                                </div>
                                                <div class="atk-step-subtitle">
                                                    @if($tReq->is_procurement)
                                                        @if($status === 'approved')
                                                            @if($hasAllFulfilled)
                                                                (Telah Dibeli Subbag TU)
                                                            @elseif($hasAnyFulfilled)
                                                                (Sebagian Barang Telah Dibeli)
                                                            @else
                                                                (Sedang Dibeli Kasubbag TU)
                                                            @endif
                                                        @else
                                                            (Menunggu Persetujuan)
                                                        @endif
                                                    @else
                                                        @if($status === 'approved')
                                                            (Siap Diserahterimakan)
                                                        @elseif($status === 'distributed')
                                                            (Telah Disiapkan Gudang)
                                                        @else
                                                            (Menunggu Antrean)
                                                        @endif
                                                    @endif
                                                </div>
                                                <div class="atk-step-time">
                                                    @if($tReq->is_procurement)
                                                        @if($hasAnyFulfilled)
                                                            {{ ($tReq->updated_at ?? $tReq->verified_at)->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB
                                                        @else
                                                            -
                                                        @endif
                                                    @else
                                                        @if($status === 'distributed' || $status === 'approved')
                                                            {{ ($tReq->updated_at ?? $tReq->verified_at)->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB
                                                        @else
                                                            -
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- STEP 4: Selesai Diterima (Stok) / Tersedia di Katalog (Pengadaan) --}}
                                            <div class="atk-stepper-item {{ $s4Class }}">
                                                <div class="atk-step-circle">
                                                    @if($tReq->is_procurement)
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                                            <line x1="12" y1="22.08" x2="12" y2="12"></line>
                                                        </svg>
                                                    @else
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline>
                                                            <path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div class="atk-step-title">
                                                    {{ $tReq->is_procurement ? 'Tersedia di Katalog' : 'Selesai Diterima' }}
                                                </div>
                                                <div class="atk-step-subtitle">
                                                    @if($tReq->is_procurement)
                                                        @if($hasAllFulfilled)
                                                            (Semua Barang Tersedia di Katalog)
                                                        @elseif($hasAnyFulfilled)
                                                            ({{ $fulfilledCount }}/{{ $itemCount }} Barang Tersedia di Katalog)
                                                        @else
                                                            (Menunggu Barang Tiba)
                                                        @endif
                                                    @else
                                                        @if($status === 'distributed')
                                                            @if(!$tReq->signed_proof && !auth()->user()->isPegawai())
                                                                <span class="text-warning fw-semibold">(Menunggu Berkas TTD)</span>
                                                            @else
                                                                (Penerima: {{ $tReq->receiver_name ?? $user->name }})
                                                            @endif
                                                        @else
                                                            (Bukti Serah Terima)
                                                        @endif
                                                    @endif
                                                </div>
                                                <div class="atk-step-time">
                                                    @if($tReq->is_procurement)
                                                        @if($hasAnyFulfilled)
                                                            {{ ($tReq->updated_at ?? $tReq->verified_at)->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB
                                                        @else
                                                            -
                                                        @endif
                                                    @else
                                                        @if($status === 'distributed' && $tReq->distributed_at)
                                                            {{ $tReq->distributed_at->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB
                                                        @else
                                                            -
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
