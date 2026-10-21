<nav class="nav navbar navbar-expand-lg navbar-light iq-navbar">
    <div class="container-fluid navbar-inner">
        <a href="{{ route('dashboard') }}" class="navbar-brand">
            <div class="logo-main">
                <div class="logo-normal">
                    <img src="{{ asset('hope-ui/assets/images/auth/kemenkp.jpeg') }}" alt="Logo KemenKP"
                        width="40" height="40" style="width: 40px; height: 40px; object-fit: contain;">
                </div>
            </div>
            <div class="ms-2 d-flex flex-column justify-content-center">
                <span class="logo-title fw-bold text-dark mb-0 fs-6" style="letter-spacing: -0.02em; line-height: 1.2;">Inventori ATK</span>
                <span class="text-muted d-none d-sm-block" style="font-size: 0.68rem; font-weight: 500; line-height: 1;">Kementerian PKP RI</span>
            </div>
        </a>
        <div class="sidebar-toggle" data-toggle="sidebar" data-active="true" role="button" tabindex="0" aria-label="Buka tutup sidebar">
            <i class="icon">
                <svg width="20px" class="icon-20" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill="currentColor"
                        d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z" />
                </svg>
            </i>
        </div>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-label="Buka navigasi mobile">
            <span class="navbar-toggler-icon">
                <span class="mt-2 navbar-toggler-bar bar1"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
            </span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="mb-2 navbar-nav ms-auto align-items-center navbar-list mb-lg-0">
                {{-- Dropdown Notifikasi --}}
                @php
                    $currentUser = auth()->user();
                    $isKasubagTU = $currentUser && $currentUser->isKasubagTU();
                    $isDistributorGudang = $currentUser && $currentUser->isPegawaiGudang() && $currentUser->isDistributor();
                    $isPicDistribusi = $currentUser && $currentUser->isPicDistribusi();
                    $unreadNotifCount = $currentUser ? $currentUser->unread_notifications_count : 0;
                    
                    // Grouping & Deduplication Notifikasi Pengguna (Requesters / Users)
                    $rawUserNotifications = $currentUser ? $currentUser->unreadNotifications()->take(20)->get() : collect();
                    $userNotifications = $rawUserNotifications->groupBy(function($item) {
                        return $item->type . '_' . ($item->product_id ?? '') . '_' . $item->message;
                    })->map(function($group) {
                        $first = $group->first();
                        $first->duplicate_count = $group->count();
                        $first->all_ids = $group->pluck('id')->join(',');
                        return $first;
                    })->values()->take(5);

                    // Khusus Kasubag TU: Ringkasan & Antrean Verifikasi Prioritas
                    $pendingVerifications = collect();
                    $totalPendingVerifikasi = 0;
                    $pendingProcurementCount = 0;
                    $pendingStockCount = 0;

                    if ($isKasubagTU) {
                        $pendingProcurementCount = \App\Models\RequestItem::where('status', 'pending')->where('is_procurement', true)->count();
                        $pendingStockCount = \App\Models\RequestItem::where('status', 'pending')->where(function($q) { $q->where('is_procurement', false)->orWhereNull('is_procurement'); })->count();
                        $totalPendingVerifikasi = $pendingProcurementCount + $pendingStockCount;

                        $pendingItems = \App\Models\RequestItem::where('status', 'pending')
                            ->with(['user', 'details'])
                            ->latest()
                            ->take(3)
                            ->get();

                        foreach ($pendingItems as $pi) {
                            $pemohonName = $pi->user ? ($pi->user->name_with_title ?: $pi->user->name) : 'Pegawai';
                            $firstDetail = $pi->details->first();
                            $itemName = $firstDetail ? ($firstDetail->item_name ?: 'Barang ATK') : 'Barang ATK';
                            $detailCount = $pi->details->count();
                            $itemSummary = $detailCount > 1 ? ($itemName . ' (+ ' . ($detailCount - 1) . ' item)') : $itemName;

                            if ($pi->is_procurement) {
                                $pendingVerifications->push((object)[
                                    'title' => 'Verifikasi Pengadaan',
                                    'message' => 'Usulan "' . $itemSummary . '" dari ' . $pemohonName . ' menunggu verifikasi.',
                                    'link' => route('persetujuan.index', ['category' => 'pengadaan']),
                                    'type' => 'pending_procurement',
                                    'badge_label' => 'Pengadaan',
                                    'badge_class' => 'bg-warning text-dark',
                                    'created_at' => $pi->created_at,
                                ]);
                            } else {
                                $pendingVerifications->push((object)[
                                    'title' => 'Verifikasi Permintaan',
                                    'message' => 'Permintaan ATK dari ' . $pemohonName . ' menunggu verifikasi.',
                                    'link' => route('persetujuan.index', ['category' => 'persediaan']),
                                    'type' => 'pending_stock',
                                    'badge_label' => 'Persediaan',
                                    'badge_class' => 'bg-info text-white',
                                    'created_at' => $pi->created_at,
                                ]);
                            }
                        }
                    }

                    // Khusus Petugas Distribusi Gudang: Ringkasan & Antrean Serah Terima Barang
                    $pendingDistributions = collect();
                    $totalPendingDistribusi = 0;

                    if ($isDistributorGudang) {
                        $readyDistribusiQuery = \App\Models\RequestItem::where('status', 'approved')
                            ->where(function ($q) {
                                $q->where('is_procurement', false)->orWhereNull('is_procurement');
                            })
                            ->with(['user', 'details.product']);

                        $totalPendingDistribusi = (clone $readyDistribusiQuery)->count();
                        $readyItems = (clone $readyDistribusiQuery)->latest('updated_at')->latest('id')->take(5)->get();

                        foreach ($readyItems as $ri) {
                            $pemohonName = $ri->user ? ($ri->user->name_with_title ?: $ri->user->name) : 'Pemohon';
                            $firstDetail = $ri->details->first();
                            $itemName = $firstDetail ? ($firstDetail->item_name ?: ($firstDetail->product->name ?? 'Barang ATK')) : 'Barang ATK';
                            $detailCount = $ri->details->count();
                            $itemSummary = $detailCount > 1 ? ($itemName . ' (+ ' . ($detailCount - 1) . ' item)') : $itemName;

                            $pendingDistributions->push((object)[
                                'title' => 'Distribusi Barang',
                                'message' => 'Permintaan "' . $itemSummary . '" dari ' . $pemohonName . ' (' . $ri->request_code . ') siap diserahkan.',
                                'link' => route('distribusi.index', ['status' => 'ready']),
                                'type' => 'ready_distribution',
                                'badge_label' => $ri->type === 'subdit' ? 'Kolektif Subdit' : 'Perorangan',
                                'badge_class' => $ri->type === 'subdit' ? 'bg-success text-white' : 'bg-primary text-white',
                                'created_at' => $ri->updated_at ?: $ri->created_at,
                            ]);
                        }
                    }

                    $navBadgeCount = $isKasubagTU 
                        ? ($unreadNotifCount + $totalPendingVerifikasi) 
                        : ($isDistributorGudang 
                            ? ($unreadNotifCount + $totalPendingDistribusi) 
                            : $unreadNotifCount);

                    $hasAnyNotifications = $totalPendingVerifikasi > 0 || $totalPendingDistribusi > 0 || $userNotifications->isNotEmpty();
                @endphp
                <li class="nav-item dropdown me-2">
                    <a href="#" class="nav-link position-relative p-2 rounded-circle bg-light d-flex align-items-center justify-content-center text-secondary border"
                        id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifikasi Sistem" style="width: 40px; height: 40px;" title="Notifikasi Sistem">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <span id="nav-notif-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ $navBadgeCount > 0 ? '' : 'd-none' }}" style="font-size: 0.65rem; padding: 3px 6px;">
                            {{ $navBadgeCount }}
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0" aria-labelledby="notificationDropdown" style="width: 375px; max-width: 92vw;">
                        <div class="p-3 bg-primary text-white rounded-top d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0 text-white fw-bold d-flex align-items-center gap-1.5">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                    </svg>
                                    Notifikasi
                                </h6>
                                <small class="text-white-50" id="nav-notif-subtitle">
                                    @if($isKasubagTU && $totalPendingVerifikasi > 0)
                                        {{ $totalPendingVerifikasi }} menunggu verifikasi
                                    @elseif($isDistributorGudang && $totalPendingDistribusi > 0)
                                        {{ $totalPendingDistribusi }} siap diserahkan
                                    @else
                                        {{ $unreadNotifCount }} belum dibaca
                                    @endif
                                </small>
                            </div>
                            @if($unreadNotifCount > 0)
                                <button type="button" class="btn btn-sm btn-outline-light py-0.5 px-2.5 rounded-pill" style="font-size: 0.72rem;" onclick="markAllNotificationsRead(event)">
                                    Tandai Dibaca
                                </button>
                            @endif
                        </div>

                        {{-- Card Ringkasan Khusus Kasubag TU --}}
                        @if($isKasubagTU && $totalPendingVerifikasi > 0)
                            <div class="p-2.5 m-2.5 mb-1 rounded-3 border" style="background: #f8fafc;">
                                <div class="d-flex align-items-center justify-content-between mb-1.5">
                                    <span class="small fw-bold text-dark d-flex align-items-center gap-1" style="font-size: 0.76rem;">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-primary">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                        Antrean Verifikasi Kasubag TU
                                    </span>
                                    <span class="badge bg-primary text-white rounded-pill px-2" style="font-size: 0.65rem;">{{ $totalPendingVerifikasi }} Antrean</span>
                                </div>
                                <div class="d-flex gap-1.5">
                                    @if($pendingProcurementCount > 0)
                                        <a href="{{ route('persetujuan.index', ['category' => 'pengadaan']) }}" class="btn btn-xs btn-warning text-dark fw-bold rounded-pill flex-fill py-1 text-center text-truncate" style="font-size: 0.72rem;">
                                            Pengadaan ({{ $pendingProcurementCount }}) &rarr;
                                        </a>
                                    @endif
                                    @if($pendingStockCount > 0)
                                        <a href="{{ route('persetujuan.index', ['category' => 'persediaan']) }}" class="btn btn-xs btn-info text-white fw-bold rounded-pill flex-fill py-1 text-center text-truncate" style="font-size: 0.72rem;">
                                            Persediaan ({{ $pendingStockCount }}) &rarr;
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Card Ringkasan Khusus Petugas Distribusi Gudang --}}
                        @if($isDistributorGudang && $totalPendingDistribusi > 0)
                            <div class="p-2.5 m-2.5 mb-1 rounded-3 border" style="background: #fffbeb; border-color: #fef3c7 !important;">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="small fw-bold text-dark d-flex align-items-center gap-1" style="font-size: 0.76rem;">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-warning">
                                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                        </svg>
                                        Antrean Distribusi Barang
                                    </span>
                                    <span class="badge bg-warning text-dark fw-bold rounded-pill px-2" style="font-size: 0.65rem;">{{ $totalPendingDistribusi }} Siap</span>
                                </div>
                                <small class="text-muted d-block mb-1.5" style="font-size: 0.72rem;">Barang telah disetujui TU dan siap diserahkan ke unit kerja/pemohon.</small>
                                <a href="{{ route('distribusi.index', ['status' => 'ready']) }}" class="btn btn-xs btn-warning text-dark fw-bold rounded-pill w-100 py-1 text-center" style="font-size: 0.72rem;">
                                    Buka Antrean Distribusi &rarr;
                                </a>
                            </div>
                        @endif

                        <div class="list-group list-group-flush overflow-auto" id="nav-notif-list" style="max-height: 360px;">
                            {{-- Item Verifikasi Pending untuk Kasubag TU --}}
                            @foreach($pendingVerifications as $pv)
                                <a href="{{ $pv->link }}" class="list-group-item list-group-item-action p-2.5 notif-item bg-light border-start border-warning border-4">
                                    <div class="d-flex align-items-start gap-2">
                                        <div class="rounded-circle p-1.5 d-flex align-items-center justify-content-center flex-shrink-0 {{ $pv->type === 'pending_procurement' ? 'bg-warning-subtle text-warning' : 'bg-info-subtle text-info' }}" style="width: 32px; height: 32px;">
                                            @if($pv->type === 'pending_procurement')
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                                    <line x1="3" y1="6" x2="21" y2="6"></line>
                                                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                                                </svg>
                                            @else
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"></path>
                                                    <rect x="9" y="3" width="6" height="4" rx="1"></rect>
                                                    <path d="M9 14l2 2 4-4"></path>
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="d-flex justify-content-between align-items-center mb-0.5">
                                                <h6 class="mb-0 small fw-bold text-dark text-truncate" style="font-size: 0.8rem;">{{ $pv->title }}</h6>
                                                <span class="badge {{ $pv->badge_class }} ms-1" style="font-size: 0.58rem;">{{ $pv->badge_label }}</span>
                                            </div>
                                            <p class="mb-1 text-muted small text-break" style="font-size: 0.76rem; line-height: 1.3;">{{ $pv->message }}</p>
                                            <small class="text-secondary" style="font-size: 0.68rem;">
                                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-0.5">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <polyline points="12 6 12 12 16 14"></polyline>
                                                </svg>
                                                {{ $pv->created_at ? $pv->created_at->diffForHumans() : 'Menunggu' }}
                                            </small>
                                        </div>
                                    </div>
                                </a>
                            @endforeach

                            {{-- Item Siap Distribusi untuk PIC Distribusi --}}
                            @foreach($pendingDistributions as $pd)
                                <a href="{{ $pd->link }}" class="list-group-item list-group-item-action p-2.5 notif-item bg-light border-start border-warning border-4">
                                    <div class="d-flex align-items-start gap-2">
                                        <div class="rounded-circle p-1.5 d-flex align-items-center justify-content-center flex-shrink-0 bg-warning-subtle text-warning" style="width: 32px; height: 32px;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="d-flex justify-content-between align-items-center mb-0.5">
                                                <h6 class="mb-0 small fw-bold text-dark text-truncate" style="font-size: 0.8rem;">{{ $pd->title }}</h6>
                                                <span class="badge {{ $pd->badge_class }} ms-1" style="font-size: 0.58rem;">{{ $pd->badge_label }}</span>
                                            </div>
                                            <p class="mb-1 text-muted small text-break" style="font-size: 0.76rem; line-height: 1.3;">{{ $pd->message }}</p>
                                            <small class="text-secondary" style="font-size: 0.68rem;">
                                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-0.5">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <polyline points="12 6 12 12 16 14"></polyline>
                                                </svg>
                                                {{ $pd->created_at ? $pd->created_at->diffForHumans() : 'Siap Diserahkan' }}
                                            </small>
                                        </div>
                                    </div>
                                </a>
                            @endforeach

                            {{-- Notifikasi Pengguna (Requesters / Users - Grouped & Clean) --}}
                            @foreach($userNotifications as $notif)
                                <div class="list-group-item list-group-item-action p-2.5 notif-item {{ !$notif->is_read ? 'bg-light border-start border-primary border-4' : '' }}" id="notif-item-{{ $notif->id }}">
                                    <div class="d-flex align-items-start gap-2">
                                        <div class="rounded-circle p-1.5 d-flex align-items-center justify-content-center flex-shrink-0 {{ $notif->type === 'procurement_available' ? 'bg-success text-white' : 'bg-primary-subtle text-primary' }}" style="width: 32px; height: 32px;">
                                            @if($notif->type === 'procurement_available')
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                                    <line x1="3" y1="6" x2="21" y2="6"></line>
                                                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                                                </svg>
                                            @else
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1 min-w-0" style="cursor: pointer;" onclick="markSingleNotificationRead('{{ $notif->all_ids ?? $notif->id }}', event, '{{ $notif->link }}')">
                                            <div class="d-flex justify-content-between align-items-center mb-0.5">
                                                <h6 class="mb-0 small fw-bold text-dark text-truncate" style="font-size: 0.8rem;">{{ $notif->title }}</h6>
                                                <div class="d-flex align-items-center gap-1 ms-1">
                                                    @if(($notif->duplicate_count ?? 1) > 1)
                                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill" style="font-size: 0.6rem;">{{ $notif->duplicate_count }}x</span>
                                                    @endif
                                                    @if(!$notif->is_read)
                                                        <span class="badge bg-danger rounded-pill" style="font-size: 0.58rem;">Baru</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <p class="mb-1 text-muted small text-break" style="font-size: 0.76rem; line-height: 1.3;">{{ $notif->message }}</p>
                                            <small class="text-secondary" style="font-size: 0.68rem;">
                                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-0.5">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <polyline points="12 6 12 12 16 14"></polyline>
                                                </svg>
                                                {{ $notif->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        {{-- Tombol Tutup / Dismiss Cepat per Item --}}
                                        <button type="button" class="btn btn-link text-muted p-0 ms-1 flex-shrink-0 text-decoration-none" style="font-size: 1.1rem; line-height: 1;" onclick="dismissDropdownItem('{{ $notif->all_ids ?? $notif->id }}', 'notif-item-{{ $notif->id }}', event)" title="Tandai Sudah Dibaca">
                                            &times;
                                        </button>
                                    </div>
                                </div>
                            @endforeach

                            @if(!$hasAnyNotifications)
                                <div class="text-center py-4 px-3 text-muted" id="nav-notif-empty">
                                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-muted mb-2">
                                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                    </svg>
                                    <p class="mb-0 small">Belum ada notifikasi baru saat ini.</p>
                                </div>
                            @endif
                        </div>

                        <div class="p-2 border-top text-center bg-light rounded-bottom">
                            @if($isKasubagTU)
                                <a href="{{ route('persetujuan.index') }}" class="btn btn-link btn-sm text-primary p-0 small fw-bold text-decoration-none" style="font-size: 0.76rem;">
                                    Menu Verifikasi &rarr;
                                </a>
                            @elseif($isDistributorGudang)
                                <a href="{{ route('distribusi.index') }}" class="btn btn-link btn-sm text-primary p-0 small fw-bold text-decoration-none" style="font-size: 0.76rem;">
                                    Menu Distribusi &rarr;
                                </a>
                            @else
                                <a href="{{ route('katalog-atk') }}" class="btn btn-link btn-sm text-primary p-0 small fw-bold text-decoration-none" style="font-size: 0.76rem;">
                                    Cek Stok ATK &rarr;
                                </a>
                            @endif
                        </div>
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <a class="py-0 nav-link d-flex align-items-center" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-label="Menu akun pengguna">
                        <div class="avatar avatar-40 rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center border border-primary-subtle">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="caption ms-3 d-none d-md-block text-start">
                            <h6 class="mb-0 caption-title fw-bold text-dark">{{ auth()->user()->name ?? 'Pengguna' }}</h6>
                            <div class="d-flex align-items-center gap-1 mt-0.5">
                                @php
                                    $userRole = auth()->user()->role ?? 'pegawai';
                                    $roleBadge = match ($userRole) {
                                        'kasubag_tu', 'admin' => 'bg-primary text-white',
                                        'pic_distribusi' => 'bg-warning text-dark',
                                        'pegawai_gudang' => 'bg-dark text-white',
                                        default => 'bg-secondary text-white',
                                    };
                                    $roleTitle = match ($userRole) {
                                        'kasubag_tu', 'admin' => 'Kasubag TU',
                                        'pic_distribusi' => 'PIC Distribusi',
                                        'pegawai_gudang' => 'Pegawai Gudang',
                                        default => 'Pegawai',
                                    };
                                @endphp
                                <span class="badge {{ $roleBadge }}" style="font-size: 0.68rem; padding: 2px 6px;">
                                    {{ $roleTitle }}
                                </span>
                            </div>
                        </div>
                        <svg class="ms-2 text-muted dropdown-arrow-icon d-none d-md-block" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="transition: transform 0.2s ease;">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="navbarDropdown">
                        <li class="px-3 py-2 border-bottom d-md-none">
                            <strong>{{ auth()->user()->name }}</strong>
                            <div class="small text-muted">{{ $roleTitle }}</div>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('profile.edit') }}">Profil Saya</a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('profile.password') }}">Ubah Password</a>
                        </li>
                        <li>
                            <hr class="dropdown-divider my-1">
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="mb-0">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger">
                                    Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
    function dismissDropdownItem(ids, elementId, event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        const el = document.getElementById(elementId);
        if (el) {
            el.style.transition = 'all 0.25s ease';
            el.style.opacity = '0';
            el.style.transform = 'translateX(10px)';
            setTimeout(() => {
                el.remove();
                checkIfDropdownEmpty();
            }, 250);
        }

        fetch("{{ route('notifications.markBatchAsRead') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ ids: ids })
        }).then(res => res.json())
        .then(data => {
            updateNavBadgeCount(data.unread_count);
        }).catch(err => console.error(err));
    }

    function markSingleNotificationRead(id, event, link) {
        if (event && (!link || link === '#' || link === '')) {
            event.preventDefault();
        }

        const isBatch = String(id).includes(',');
        const endpoint = isBatch ? "{{ route('notifications.markBatchAsRead') }}" : ("{{ url('/notifikasi') }}/" + id + "/baca");
        const options = isBatch ? {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ ids: id })
        } : {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        };

        fetch(endpoint, options).then(response => response.json())
        .then(data => {
            if (link && link !== '#' && link !== '') {
                window.location.href = link;
            } else {
                updateNavBadgeCount(data.unread_count);
            }
        }).catch(() => {
            if (link && link !== '#' && link !== '') {
                window.location.href = link;
            }
        });
    }

    function markAllNotificationsRead(event) {
        if (event) event.preventDefault();
        fetch("{{ route('notifications.markAllAsRead') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        }).then(response => response.json())
        .then(data => {
            updateNavBadgeCount(0);
            
            // Hapus semua elemen notifikasi pengguna dari dropdown
            document.querySelectorAll('.notif-item').forEach(el => {
                if (!el.classList.contains('border-warning')) {
                    el.remove();
                }
            });

            checkIfDropdownEmpty();

            const btn = event ? event.target : null;
            if (btn) btn.remove();
        }).catch(err => console.error(err));
    }

    function updateNavBadgeCount(count) {
        const badge = document.getElementById('nav-notif-badge');
        const sub = document.getElementById('nav-notif-subtitle');
        if (badge) {
            if (count > 0) {
                badge.textContent = count;
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        }
        if (sub) {
            sub.textContent = (count || 0) + ' belum dibaca';
        }
    }

    function checkIfDropdownEmpty() {
        const list = document.getElementById('nav-notif-list');
        if (list && list.querySelectorAll('.notif-item').length === 0) {
            list.innerHTML = `
                <div class="text-center py-4 px-3 text-muted" id="nav-notif-empty">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-muted mb-2">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <p class="mb-0 small">Belum ada notifikasi baru saat ini.</p>
                </div>
            `;
        }
    }
</script>
