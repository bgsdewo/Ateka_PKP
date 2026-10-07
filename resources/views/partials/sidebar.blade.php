@php
    $currentUser = auth()->user();
    $role = $currentUser->role ?? 'pegawai';

    $isKasubagTU = in_array($role, ['kasubag_tu', 'admin']);
    $isArsiparis = $role === 'arsiparis';
    $isPicDistribusi = $role === 'pic_distribusi';
    $isSubdit = $role === 'subdit';
    $isRequester = in_array($role, ['pegawai', 'subdit', 'member']) || empty($role);

    $pendingApprovalsCount = $isKasubagTU ? \App\Models\RequestItem::where('status', 'pending')->count() : 0;
    $pendingProcurementCount = $isKasubagTU ? \App\Models\RequestItem::where('status', 'pending')->where('is_procurement', true)->count() : 0;
    $pendingStockCount = $isKasubagTU ? \App\Models\RequestItem::where('status', 'pending')->where(function($q) { $q->where('is_procurement', false)->orWhereNull('is_procurement'); })->count() : 0;
    $readyDistribusiCount = ($isPicDistribusi || $isKasubagTU) ? \App\Models\RequestItem::where('status', 'approved')->where(function($q) { $q->where('is_procurement', false)->orWhereNull('is_procurement'); })->count() : 0;
@endphp

<aside class="sidebar sidebar-default sidebar-white sidebar-base navs-rounded-all">
    <div class="sidebar-header d-flex align-items-center justify-content-start">
        <a href="{{ route('dashboard') }}" class="navbar-brand">
            <div class="logo-main">
                <div class="logo-normal">
                    <img src="{{ asset('hope-ui/assets/images/auth/kemenkp.jpeg') }}" alt="Logo KemenKP" class="img-fluid"
                        style="width: 50px; height: 50px; object-fit: contain;">
                </div>
            </div>
            <div class="ms-2 sidebar-brand-text">
                <h5 class="logo-title mb-0 fw-bold" style="color: #144b59;">Inventori ATK</h5>
                <small class="text-muted sidebar-subtext" style="font-size: 0.7rem; white-space: nowrap;">Kementerian PKP RI</small>
            </div>
        </a>
        <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
            <i class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M4.25 12.2744L19.25 12.2744" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M10.2998 18.2988L4.2498 12.2748L10.2998 6.24976" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </i>
        </div>
    </div>

    <div class="sidebar-body pt-0 data-scrollbar">
        <div class="sidebar-list">
            <ul class="navbar-nav iq-main-menu" id="sidebar-menu">

                {{-- Role Badge Indicator in Sidebar --}}
                <li class="nav-item mb-2 px-3 pt-3 sidebar-user-item">
                    <div class="p-2.5 rounded-3 bg-light border d-flex align-items-center gap-2 sidebar-user-card">
                        <div class="avatar avatar-35 rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center sidebar-user-avatar">
                            {{ strtoupper(substr($currentUser->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="sidebar-user-info" style="line-height: 1.2; overflow: hidden;">
                            <div class="fw-bold text-dark text-truncate" style="font-size: 0.85rem;">{{ $currentUser->name ?? 'Pengguna' }}</div>
                            <small class="text-primary fw-semibold" style="font-size: 0.72rem;">
                                @if ($isKasubagTU)
                                    Kasubag TU (Approver)
                                @elseif ($isArsiparis)
                                    Arsiparis (Pelaporan)
                                @elseif ($isPicDistribusi)
                                    {{ $currentUser->position ?? ('PIC Distribusi (' . ($currentUser->subdit_name ?? 'Gudang') . ')') }}
                                @elseif ($isSubdit)
                                    Subdirektorat (Pemohon Kolektif)
                                @else
                                    Pegawai (Pemohon Individu)
                                @endif
                            </small>
                        </div>
                    </div>
                </li>

                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#" tabindex="-1">
                        <span class="default-icon">Menu Navigasi</span>
                        <span class="mini-icon">-</span>
                    </a>
                </li>

                <!-- Dashboard (Semua Role) -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" title="Dashboard">
                        <i class="icon">
                            <svg width="20" viewBox="0 0 24 24" fill="none" class="icon-20">
                                <path opacity="0.4"
                                    d="M16.0756 2H19.4616C20.8639 2 22.0001 3.14585 22.0001 4.55996V7.97452C22.0001 9.38864 20.8639 10.5345 19.4616 10.5345H16.0756C14.6734 10.5345 13.5371 9.38864 13.5371 7.97452V4.55996C13.5371 3.14585 14.6734 2 16.0756 2Z"
                                    fill="currentColor"></path>
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M4.53852 2H7.92449C9.32676 2 10.463 3.14585 10.463 4.55996V7.97452C10.463 9.38864 9.32676 10.5345 7.92449 10.5345H4.53852C3.13626 10.5345 2 9.38864 2 7.97452V4.55996C2 3.14585 3.13626 2 4.53852 2ZM4.53852 13.4655H7.92449C9.32676 13.4655 10.463 14.6114 10.463 16.0255V19.44C10.463 20.8532 9.32676 22 7.92449 22H4.53852C3.13626 22 2 20.8532 2 19.44V16.0255C2 14.6114 3.13626 13.4655 4.53852 13.4655ZM19.4615 13.4655H16.0755C14.6732 13.4655 13.537 14.6114 13.537 16.0255V19.44C13.537 20.8532 14.6732 22 16.0755 22H19.4615C20.8637 22 22 20.8532 22 19.44V16.0255C22 14.6114 20.8637 13.4655 19.4615 13.4655Z"
                                    fill="currentColor"></path>
                            </svg>
                        </i>
                        <span class="item-name">Dashboard</span>
                    </a>
                </li>

                <!-- ======================================================== -->
                <!-- 1. MENU KHUSUS KASUBAG TU (Approver / Pimpinan) -->
                <!-- ======================================================== -->
                @if ($isKasubagTU)
                    @php
                        $activeCategory = request('category', 'pengadaan');
                        $isPersediaanRoute = request()->routeIs('persetujuan.*') && $activeCategory === 'persediaan';
                        $isPengadaanRoute = (request()->routeIs('persetujuan.*') || request()->routeIs('verifikasi.*')) && $activeCategory !== 'persediaan';
                        $isRekapRoute = request()->routeIs('rekapitulasi-permintaan.*');
                        $verifikasiActive = request()->routeIs('persetujuan.*') || request()->routeIs('verifikasi.*') || $isRekapRoute;
                        $totalPendingVerifikasi = ($pendingProcurementCount ?? 0) + ($pendingStockCount ?? 0);
                    @endphp

                    {{-- Menu Group: Verifikasi & Rekapitulasi (Collapsible) --}}
                    <li class="nav-item">
                        <a class="nav-link {{ $verifikasiActive ? 'active' : '' }}" data-bs-toggle="collapse"
                            href="#verifikasi-menu" role="button" aria-expanded="{{ $verifikasiActive ? 'true' : 'false' }}" title="Verifikasi & Rekapitulasi">
                            <i class="icon">
                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-6 9l2 2 4-4" />
                                </svg>
                            </i>
                            <span class="item-name">Verifikasi & Rekapitulasi</span>
                            <i class="right-icon" style="flex-shrink: 0;">
                                <svg class="icon-18" xmlns="http://www.w3.org/2000/svg" width="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </i>
                        </a>

                        <ul class="sub-nav collapse {{ $verifikasiActive ? 'show' : '' }}" id="verifikasi-menu" data-bs-parent="#sidebar-menu">
                            {{-- 1A. Verifikasi Usulan Pengadaan (Beli Baru) --}}
                            <li class="nav-item">
                                <a class="nav-link {{ $isPengadaanRoute ? 'active' : '' }}" 
                                   href="{{ route('persetujuan.index', ['category' => 'pengadaan']) }}" 
                                   title="Verifikasi Pengadaan (Beli Baru)">
                                    <i class="sidenav-mini-icon">P</i>
                                    <span class="item-name">Verifikasi Pengadaan</span>
                                </a>
                            </li>

                            {{-- 1B. Verifikasi Permintaan Barang Stok (Persediaan) --}}
                            <li class="nav-item">
                                <a class="nav-link {{ $isPersediaanRoute ? 'active' : '' }}" 
                                   href="{{ route('persetujuan.index', ['category' => 'persediaan']) }}" 
                                   title="Verifikasi Permintaan Stok">
                                    <i class="sidenav-mini-icon">V</i>
                                    <span class="item-name">Verifikasi Permintaan</span>
                                </a>
                            </li>

                            {{-- 1C. Rekapitulasi Permintaan --}}
                            <li class="nav-item">
                                <a class="nav-link {{ $isRekapRoute ? 'active' : '' }}" 
                                   href="{{ route('rekapitulasi-permintaan.index') }}" 
                                   title="Rekapitulasi Permintaan">
                                    <i class="sidenav-mini-icon">R</i>
                                    <span class="item-name">Rekapitulasi Permintaan</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('distribusi.*') ? 'active' : '' }}" href="{{ route('distribusi.index') }}" title="Distribusi Barang">
                            <i class="icon">
                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                </svg>
                            </i>
                            <span class="item-name">Distribusi Barang</span>
                            @if ($readyDistribusiCount > 0)
                                <span class="badge bg-warning text-dark rounded-pill ms-auto px-2">{{ $readyDistribusiCount }}</span>
                            @endif
                        </a>
                    </li>
                @endif

                <!-- ======================================================== -->
                <!-- 2. MENU KHUSUS PIC DISTRIBUSI (Pengelola / Gudang) -->
                <!-- ======================================================== -->
                @if ($isPicDistribusi && !$isKasubagTU)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('distribusi.*') ? 'active' : '' }}" href="{{ route('distribusi.index') }}" title="Distribusi Barang">
                            <i class="icon">
                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                </svg>
                            </i>
                            <span class="item-name">Distribusi Barang</span>
                            @if ($readyDistribusiCount > 0)
                                <span class="badge bg-warning text-dark rounded-pill ms-auto px-2">{{ $readyDistribusiCount }}</span>
                            @endif
                        </a>
                    </li>
                @endif

                <!-- ======================================================== -->
                <!-- 3. MENU OPERASIONAL GUDANG (Kasubag TU & PIC Distribusi) -->
                <!-- ======================================================== -->
                @if ($isKasubagTU || $isPicDistribusi)
                    @php
                        $transaksiActive = request()->routeIs('barang-masuk') || request()->routeIs('barang-keluar');
                    @endphp
                    <li class="nav-item">
                        <a class="nav-link {{ $transaksiActive ? 'active' : '' }}" data-bs-toggle="collapse"
                            href="#transaksi-menu" role="button" aria-expanded="{{ $transaksiActive ? 'true' : 'false' }}" title="Gudang & Stok">
                            <i class="icon">
                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.4" d="M3 7.5L12 2.5L21 7.5V16.5L12 21.5L3 16.5V7.5Z" fill="currentColor"></path>
                                    <path d="M12 12L21 7.5L12 2.5L3 7.5L12 12Z" fill="currentColor"></path>
                                </svg>
                            </i>
                            <span class="item-name">Gudang & Stok</span>
                            <i class="right-icon">
                                <svg class="icon-18" xmlns="http://www.w3.org/2000/svg" width="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </i>
                        </a>

                        <ul class="sub-nav collapse {{ $transaksiActive ? 'show' : '' }}" id="transaksi-menu" data-bs-parent="#sidebar-menu">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('barang-masuk') ? 'active' : '' }}" href="{{ route('barang-masuk') }}" title="Barang Masuk (Pengadaan)">
                                    <i class="sidenav-mini-icon">M</i>
                                    <span class="item-name">Barang Masuk (Pengadaan)</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('barang-keluar') ? 'active' : '' }}" href="{{ route('barang-keluar') }}" title="Barang Keluar">
                                    <i class="sidenav-mini-icon">K</i>
                                    <span class="item-name">Barang Keluar</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                <!-- ======================================================== -->
                <!-- 4. MENU PENGAJUAN ATK (Pegawai & Subdirektorat) -->
                <!-- ======================================================== -->
                @if ($isRequester || $isKasubagTU || $isPicDistribusi)
                    @php
                        $pengajuanActive = request()->routeIs('pengajuan.*') || request()->routeIs('katalog-atk');
                    @endphp
                    <li class="nav-item">
                        <a class="nav-link {{ $pengajuanActive ? 'active' : '' }}" data-bs-toggle="collapse"
                            href="#pengajuan-menu" role="button" aria-expanded="{{ $pengajuanActive ? 'true' : 'false' }}" title="Permintaan ATK">
                            <i class="icon">
                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.4"
                                        d="M19 5H5C3.89543 5 3 5.89543 3 7V17C3 18.1046 3.89543 19 5 19H19C20.1046 19 21 18.1046 21 17V7C21 5.89543 20.1046 5 19 5Z"
                                        fill="currentColor"></path>
                                    <path d="M12 9V15M9 12H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                                </svg>
                            </i>
                            <span class="item-name">Permintaan ATK</span>
                            <i class="right-icon">
                                <svg class="icon-18" xmlns="http://www.w3.org/2000/svg" width="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </i>
                        </a>

                        <ul class="sub-nav collapse {{ $pengajuanActive ? 'show' : '' }}" id="pengajuan-menu" data-bs-parent="#sidebar-menu">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('pengajuan.index') ? 'active' : '' }}" href="{{ route('pengajuan.index') }}" title="Riwayat Pengajuan">
                                    <i class="sidenav-mini-icon">R</i>
                                    <span class="item-name">Riwayat Pengajuan</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('pengajuan.create') ? 'active' : '' }}" href="{{ route('pengajuan.create') }}" title="Buat Pengajuan Baru">
                                    <i class="sidenav-mini-icon">+</i>
                                    <span class="item-name">Buat Pengajuan Baru</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('pengajuan.pengadaan.*') ? 'active' : '' }}" href="{{ route('pengajuan.pengadaan.create') }}" title="Permintaan Pengadaan (Beli Baru)">
                                    <i class="sidenav-mini-icon">P</i>
                                    <span class="item-name">Permintaan Pengadaan (Beli)</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('katalog-atk') ? 'active' : '' }}" href="{{ route('katalog-atk') }}" title="Katalog & Ketersediaan">
                                    <i class="sidenav-mini-icon">K</i>
                                    <span class="item-name">Katalog & Ketersediaan</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                <!-- ======================================================== -->
                <!-- 5. MENU LAPORAN & ARSIP (Kasubag TU, Arsiparis, PIC Distribusi) -->
                <!-- ======================================================== -->
                @if ($isKasubagTU || $isArsiparis || $isPicDistribusi)
                    @php
                        $laporanActive = request()->routeIs('reports.*');
                    @endphp
                    <li class="nav-item">
                        <a class="nav-link {{ $laporanActive ? 'active' : '' }}" data-bs-toggle="collapse"
                            href="#laporan-menu" role="button" aria-expanded="{{ $laporanActive ? 'true' : 'false' }}" title="Laporan & Arsip">
                            <i class="icon">
                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.4"
                                        d="M16.191 2H7.81C4.77 2 3 3.78 3 6.83V17.16C3 20.26 4.77 22 7.81 22H16.191C19.28 22 21 20.26 21 17.16V6.83C21 3.78 19.28 2 16.191 2Z"
                                        fill="currentColor"></path>
                                    <path d="M8 8H16M8 12H16M8 16H12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                                </svg>
                            </i>
                            <span class="item-name">Laporan & Arsip</span>
                            <i class="right-icon">
                                <svg class="icon-18" xmlns="http://www.w3.org/2000/svg" width="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </i>
                        </a>

                        <ul class="sub-nav collapse {{ $laporanActive ? 'show' : '' }}" id="laporan-menu" data-bs-parent="#sidebar-menu">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('reports.index') ? 'active' : '' }}" href="{{ route('reports.index') }}" title="Mutasi Persediaan">
                                    <i class="sidenav-mini-icon">L</i>
                                    <span class="item-name">Mutasi Persediaan</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('reports.rekapitulasi') ? 'active' : '' }}" href="{{ route('reports.rekapitulasi') }}" title="Rekapitulasi Arsip">
                                    <i class="sidenav-mini-icon">R</i>
                                    <span class="item-name">Rekapitulasi Arsip</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                <!-- ======================================================== -->
                <!-- 6. MASTER DATA (Kasubag TU & PIC Distribusi/Arsiparis untuk view) -->
                <!-- ======================================================== -->
                @if ($isKasubagTU || $isPicDistribusi || $isArsiparis)
                    @php
                        $masterActive =
                            request()->routeIs('master-data.kategori-barang.*') ||
                            request()->routeIs('master-data.daftar-barang.*') ||
                            request()->routeIs('master-data.manajemen-pengguna.*');
                    @endphp
                    <li class="nav-item">
                        <a class="nav-link {{ $masterActive ? 'active' : '' }}" data-bs-toggle="collapse"
                            href="#master-data" role="button" aria-expanded="{{ $masterActive ? 'true' : 'false' }}" title="Master Data">
                            <i class="icon">
                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.4"
                                        d="M16.191 2H7.81C4.77 2 3 3.78 3 6.83V17.16C3 20.26 4.77 22 7.81 22H16.191C19.28 22 21 20.26 21 17.16V6.83C21 3.78 19.28 2 16.191 2Z"
                                        fill="currentColor"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M8.08 6.65C7.65 6.65 7.3 7 7.3 7.44C7.3 7.87 7.65 8.22 8.08 8.22H15.92C16.35 8.22 16.7 7.87 16.7 7.44C16.7 7 16.35 6.65 15.92 6.65H8.08ZM8.08 11.18C7.65 11.18 7.3 11.53 7.3 11.96C7.3 12.39 7.65 12.74 8.08 12.74H15.92C16.35 12.74 16.7 12.39 16.7 11.96C16.7 11.53 16.35 11.18 15.92 11.18H8.08ZM8.08 15.74C7.65 15.74 7.3 16.09 7.3 16.53C7.3 16.96 7.65 17.31 8.08 17.31H15.92C16.35 17.31 16.7 16.96 16.7 16.53C16.7 16.09 16.35 15.74 15.92 15.74H8.08Z"
                                        fill="currentColor"></path>
                                </svg>
                            </i>
                            <span class="item-name">Master Data</span>
                            <i class="right-icon">
                                <svg class="icon-18" xmlns="http://www.w3.org/2000/svg" width="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </i>
                        </a>
                        <ul class="sub-nav collapse {{ $masterActive ? 'show' : '' }}" id="master-data" data-bs-parent="#sidebar-menu">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('master-data.kategori-barang.*') ? 'active' : '' }}"
                                    href="{{ route('master-data.kategori-barang.index') }}" title="Kategori Barang">
                                    <i class="icon">
                                        <svg class="icon-10" width="10" viewBox="0 0 24 24" fill="currentColor">
                                            <circle cx="12" cy="12" r="8" />
                                        </svg>
                                    </i>
                                    <i class="sidenav-mini-icon">K</i>
                                    <span class="item-name">Kategori Barang</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('master-data.daftar-barang.*') ? 'active' : '' }}"
                                    href="{{ route('master-data.daftar-barang.index') }}" title="Daftar Barang ATK">
                                    <i class="icon">
                                        <svg class="icon-10" width="10" viewBox="0 0 24 24" fill="currentColor">
                                            <circle cx="12" cy="12" r="8" />
                                        </svg>
                                    </i>
                                    <i class="sidenav-mini-icon">B</i>
                                    <span class="item-name">Daftar Barang ATK</span>
                                </a>
                            </li>

                            @if ($isKasubagTU)
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('master-data.manajemen-pengguna.*') ? 'active' : '' }}"
                                        href="{{ route('master-data.manajemen-pengguna.index') }}" title="Manajemen Pengguna">
                                        <i class="icon">
                                            <svg class="icon-10" width="10" viewBox="0 0 24 24" fill="currentColor">
                                                <circle cx="12" cy="12" r="8" />
                                            </svg>
                                        </i>
                                        <i class="sidenav-mini-icon">U</i>
                                        <span class="item-name">Manajemen Pengguna</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

            </ul>
        </div>
    </div>
    <div class="sidebar-footer"></div>
</aside>
