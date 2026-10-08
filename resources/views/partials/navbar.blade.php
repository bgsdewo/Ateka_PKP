<nav class="nav navbar navbar-expand-lg navbar-light iq-navbar">
    <div class="container-fluid navbar-inner">
        <a href="{{ route('dashboard') }}" class="navbar-brand">
            <div class="logo-main">
                <div class="logo-normal">
                    <img src="{{ asset('hope-ui/assets/images/auth/kemenkp.jpeg') }}" alt="Logo KemenKP"
                        style="width: 40px; height: 40px; object-fit: contain;">
                </div>
            </div>
            <h4 class="logo-title ms-2">Inventori ATK</h4>
        </a>
        <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
            <i class="icon">
                <svg width="20px" class="icon-20" viewBox="0 0 24 24">
                    <path fill="currentColor"
                        d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z" />
                </svg>
            </i>
        </div>
        <div class="input-group search-input d-none d-md-flex">
            <span class="input-group-text">
                <svg class="icon-18" width="18" viewBox="0 0 24 24" fill="none">
                    <circle cx="11.7669" cy="11.7666" r="8.98856" stroke="currentColor" stroke-width="1.5"></circle>
                    <path d="M18.0186 18.4851L21.5426 22" stroke="currentColor" stroke-width="1.5"></path>
                </svg>
            </span>
            <input type="search" id="navbar-search" class="form-control" placeholder="Cari data persediaan atau transaksi...">
        </div>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
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
                    $isPicDistribusi = $currentUser && $currentUser->isPicDistribusi();
                    $unreadNotifCount = $currentUser ? $currentUser->unread_notifications_count : 0;
                    $userNotifications = $currentUser ? $currentUser->unreadNotifications()->take(7)->get() : collect();

                    // Khusus Kasubag TU: Notifikasi verifikasi pengadaan dan permintaan ATK dipindahkan ke lonceng navbar
                    $pendingVerifications = collect();
                    $totalPendingVerifikasi = 0;

                    if ($isKasubagTU) {
                        $pendingItems = \App\Models\RequestItem::where('status', 'pending')
                            ->with(['user', 'details'])
                            ->latest()
                            ->take(6)
                            ->get();

                        $totalPendingVerifikasi = \App\Models\RequestItem::where('status', 'pending')->count();

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

                    // Khusus PIC Distribusi: Notifikasi barang siap serah/didistribusikan per divisi dipindahkan ke lonceng navbar
                    $pendingDistributions = collect();
                    $totalPendingDistribusi = 0;

                    if ($isPicDistribusi) {
                        $readyDistribusiQuery = \App\Models\RequestItem::where('status', 'approved')
                            ->where(function ($q) {
                                $q->where('is_procurement', false)->orWhereNull('is_procurement');
                            })
                            ->forPic($currentUser)
                            ->with(['user', 'details.product']);

                        $totalPendingDistribusi = (clone $readyDistribusiQuery)->count();
                        $readyItems = (clone $readyDistribusiQuery)->latest('updated_at')->latest('id')->take(6)->get();

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
                        : ($isPicDistribusi 
                            ? ($unreadNotifCount + $totalPendingDistribusi) 
                            : $unreadNotifCount);

                    $hasAnyNotifications = $pendingVerifications->isNotEmpty() || $pendingDistributions->isNotEmpty() || $userNotifications->isNotEmpty();
                @endphp
                <li class="nav-item dropdown me-2">
                    <a href="#" class="nav-link position-relative p-2 rounded-circle bg-light d-flex align-items-center justify-content-center text-secondary border"
                        id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 40px; height: 40px;" title="Notifikasi Sistem">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <span id="nav-notif-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ $navBadgeCount > 0 ? '' : 'd-none' }}" style="font-size: 0.65rem; padding: 3px 6px;">
                            {{ $navBadgeCount }}
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0" aria-labelledby="notificationDropdown" style="width: 360px; max-width: 90vw;">
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
                                    @elseif($isPicDistribusi && $totalPendingDistribusi > 0)
                                        {{ $totalPendingDistribusi }} siap diserahkan
                                    @else
                                        {{ $unreadNotifCount }} belum dibaca
                                    @endif
                                </small>
                            </div>
                            @if($unreadNotifCount > 0)
                                <button type="button" class="btn btn-sm btn-outline-light py-0 px-2" style="font-size: 0.72rem;" onclick="markAllNotificationsRead(event)">
                                    Tandai Dibaca
                                </button>
                            @endif
                        </div>

                        <div class="list-group list-group-flush overflow-auto" id="nav-notif-list" style="max-height: 380px;">
                            {{-- Item Verifikasi Pending untuk Kasubag TU --}}
                            @foreach($pendingVerifications as $pv)
                                <a href="{{ $pv->link }}" class="list-group-item list-group-item-action p-3 notif-item bg-light border-start border-warning border-4">
                                    <div class="d-flex align-items-start gap-2.5">
                                        <div class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0 {{ $pv->type === 'pending_procurement' ? 'bg-warning-subtle text-warning' : 'bg-info-subtle text-info' }}" style="width: 36px; height: 36px;">
                                            @if($pv->type === 'pending_procurement')
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                                    <line x1="3" y1="6" x2="21" y2="6"></line>
                                                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                                                </svg>
                                            @else
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"></path>
                                                    <rect x="9" y="3" width="6" height="4" rx="1"></rect>
                                                    <path d="M9 14l2 2 4-4"></path>
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <h6 class="mb-0 small fw-bold text-dark text-truncate">{{ $pv->title }}</h6>
                                                <span class="badge {{ $pv->badge_class }} ms-1" style="font-size: 0.6rem;">{{ $pv->badge_label }}</span>
                                            </div>
                                            <p class="mb-1 text-muted small text-break" style="font-size: 0.78rem; line-height: 1.35;">{{ $pv->message }}</p>
                                            <small class="text-secondary" style="font-size: 0.7rem;">
                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-0.5">
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
                                <a href="{{ $pd->link }}" class="list-group-item list-group-item-action p-3 notif-item bg-light border-start border-warning border-4">
                                    <div class="d-flex align-items-start gap-2.5">
                                        <div class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0 bg-warning-subtle text-warning" style="width: 36px; height: 36px;">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <h6 class="mb-0 small fw-bold text-dark text-truncate">{{ $pd->title }}</h6>
                                                <span class="badge {{ $pd->badge_class }} ms-1" style="font-size: 0.6rem;">{{ $pd->badge_label }}</span>
                                            </div>
                                            <p class="mb-1 text-muted small text-break" style="font-size: 0.78rem; line-height: 1.35;">{{ $pd->message }}</p>
                                            <small class="text-secondary" style="font-size: 0.7rem;">
                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-0.5">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <polyline points="12 6 12 12 16 14"></polyline>
                                                </svg>
                                                {{ $pd->created_at ? $pd->created_at->diffForHumans() : 'Siap Diserahkan' }}
                                            </small>
                                        </div>
                                    </div>
                                </a>
                            @endforeach

                            {{-- Notifikasi Pengguna --}}
                            @foreach($userNotifications as $notif)
                                <a href="{{ $notif->link ?: '#' }}" 
                                    class="list-group-item list-group-item-action p-3 notif-item {{ !$notif->is_read ? 'bg-light border-start border-primary border-4' : '' }}"
                                    onclick="markSingleNotificationRead({{ $notif->id }}, event, '{{ $notif->link }}')">
                                    <div class="d-flex align-items-start gap-2.5">
                                        <div class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0 {{ $notif->type === 'procurement_available' ? 'bg-success text-white' : 'bg-primary-subtle text-primary' }}" style="width: 36px; height: 36px;">
                                            @if($notif->type === 'procurement_available')
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                                    <line x1="3" y1="6" x2="21" y2="6"></line>
                                                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                                                </svg>
                                            @else
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <h6 class="mb-0 small fw-bold text-dark text-truncate">{{ $notif->title }}</h6>
                                                @if(!$notif->is_read)
                                                    <span class="badge bg-danger ms-1" style="font-size: 0.6rem;">Baru</span>
                                                @endif
                                            </div>
                                            <p class="mb-1 text-muted small text-break" style="font-size: 0.78rem; line-height: 1.35;">{{ $notif->message }}</p>
                                            <small class="text-secondary" style="font-size: 0.7rem;">
                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-0.5">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <polyline points="12 6 12 12 16 14"></polyline>
                                                </svg>
                                                {{ $notif->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                </a>
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
                                <a href="{{ route('persetujuan.index') }}" class="btn btn-link btn-sm text-primary py-0 small fw-bold text-decoration-none" style="font-size: 0.75rem;">
                                    Buka Menu Verifikasi &amp; Persetujuan &rarr;
                                </a>
                            @elseif($isPicDistribusi)
                                <a href="{{ route('distribusi.index') }}" class="btn btn-link btn-sm text-primary py-0 small fw-bold text-decoration-none" style="font-size: 0.75rem;">
                                    Buka Halaman Distribusi Barang &rarr;
                                </a>
                            @else
                                <a href="{{ route('katalog-atk') }}" class="btn btn-link btn-sm text-primary py-0 small fw-bold text-decoration-none" style="font-size: 0.75rem;">
                                    Buka Katalog &amp; Cek Stok ATK &rarr;
                                </a>
                            @endif
                        </div>
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <a class="py-0 nav-link d-flex align-items-center" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown">
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
                                        'arsiparis' => 'bg-info text-white',
                                        'pic_distribusi' => 'bg-warning text-dark',
                                        'subdit' => 'bg-success text-white',
                                        default => 'bg-secondary text-white',
                                    };
                                    $roleTitle = match ($userRole) {
                                        'kasubag_tu', 'admin' => 'Kasubag TU',
                                        'arsiparis' => 'Arsiparis',
                                        'pic_distribusi' => 'PIC Distribusi',
                                        'subdit' => 'Subdirektorat',
                                        default => 'Pegawai',
                                    };
                                @endphp
                                <span class="badge {{ $roleBadge }}" style="font-size: 0.68rem; padding: 2px 6px;">
                                    {{ $roleTitle }}
                                </span>
                            </div>
                        </div>
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
    function markSingleNotificationRead(id, event, link) {
        if (event) event.preventDefault();
        fetch("{{ url('/notifikasi') }}/" + id + "/baca", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        }).then(response => response.json())
        .then(data => {
            if (link && link !== '#' && link !== '') {
                window.location.href = link;
            } else {
                location.reload();
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
            const badge = document.getElementById('nav-notif-badge');
            const sub = document.getElementById('nav-notif-subtitle');
            if (badge) badge.classList.add('d-none');
            if (sub) sub.textContent = '0 belum dibaca';
            
            // Hapus semua elemen notifikasi pengguna dari dropdown
            document.querySelectorAll('.notif-item').forEach(el => {
                // Jangan hapus item pending verification Kasubag TU jika ada
                if (!el.classList.contains('border-warning')) {
                    el.remove();
                }
            });

            // Jika daftar sekarang kosong, tampilkan status kosong
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

            const btn = event ? event.target : null;
            if (btn) btn.remove();
        }).catch(err => console.error(err));
    }
</script>
