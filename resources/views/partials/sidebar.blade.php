@php
    $currentUser = auth()->user();
    $role = $currentUser->role ?? 'pegawai';

    $isKasubagTU = in_array($role, ['kasubag_tu', 'admin']);
    $isPicDistribusi = $role === 'pic_distribusi';
    $isPegawaiGudang = $role === 'pegawai_gudang';
    $isRequester = in_array($role, ['pegawai', 'pic_distribusi', 'subdit', 'member']) || empty($role);

    if ($isKasubagTU) {
        $pendingSummary = \App\Models\RequestItem::getPendingSummary();
        $pendingApprovalsCount = $pendingSummary['total'];
        $pendingProcurementCount = $pendingSummary['procurement'];
        $pendingStockCount = $pendingSummary['stock'];
    } else {
        $pendingApprovalsCount = 0;
        $pendingProcurementCount = 0;
        $pendingStockCount = 0;
    }
    $isDistributorGudang = $currentUser && $currentUser->isPegawaiGudang() && $currentUser->isDistributor();
    $readyDistribusiCount = $isPegawaiGudang ? \App\Models\RequestItem::getReadyDistribusiCount() : 0;
@endphp

<aside class="sidebar sidebar-default sidebar-dark sidebar-base navs-rounded-all">
    <div class="sidebar-header d-flex align-items-center justify-content-start">
        <a href="{{ route('dashboard') }}" class="navbar-brand">
            <div class="logo-main">
                <div class="logo-normal d-flex align-items-center">
                    <img src="{{ asset('hope-ui/assets/images/auth/kemenkp.jpeg') }}" alt="Logo KemenKP" class="img-fluid rounded-2"
                        width="44" height="44" style="width: 44px; height: 44px; object-fit: contain; background: #ffffff; padding: 2px;">
                </div>
            </div>
            <div class="ms-2 sidebar-brand-text">
                <h5 class="logo-title mb-0 fw-bold" style="color: #ffffff; letter-spacing: -0.02em; font-size: 1.12rem;">Inventori ATK</h5>
                <small class="sidebar-subtext d-block" style="font-size: 0.68rem; color: #7ba597; white-space: nowrap; margin-top: 2px;">Kementerian PKP RI</small>
            </div>
        </a>
        <div class="sidebar-toggle" data-toggle="sidebar" data-active="true" role="button" tabindex="0" aria-label="Tutup sidebar">
            <i class="icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M19 12H5"></path>
                    <path d="m12 19-7-7 7-7"></path>
                </svg>
            </i>
        </div>
    </div>

    <div class="sidebar-body pt-0 data-scrollbar">
        <div class="sidebar-list">
            <ul class="navbar-nav iq-main-menu" id="sidebar-menu">


                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#" tabindex="-1">
                        <span class="default-icon">Navigation</span>
                        <span class="mini-icon">-</span>
                    </a>
                </li>

                <!-- Dashboard (Semua Role) -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" title="Dashboard">
                        <i class="icon">
                            <svg class="icon-20" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                                <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                                <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                                <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                            </svg>
                        </i>
                        <span class="item-name">Dashboard</span>
                    </a>
                </li>

                <!-- ======================================================== -->
                <!-- 1. MENU KHUSUS KASUBBAG TU (Approver / Pimpinan) -->
                <!-- ======================================================== -->
                @if ($isKasubagTU)
                    @php
                        $verifikasiActive = request()->routeIs('persetujuan.*') || request()->routeIs('verifikasi.*');
                        $totalPendingVerifikasi = $pendingApprovalsCount;
                    @endphp

                    <li class="nav-item">
                        <a class="nav-link {{ $verifikasiActive ? 'active' : '' }}" href="{{ route('persetujuan.index') }}" title="Verifikasi & Persetujuan">
                            <i class="icon">
                                <svg class="icon-20" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                    <rect x="9" y="2" width="6" height="4" rx="1"></rect>
                                    <path d="m9 14 2 2 4-4"></path>
                                </svg>
                            </i>
                            <span class="item-name">Verifikasi & Persetujuan</span>
                            @if ($totalPendingVerifikasi > 0)
                                <span class="badge rounded-pill bg-danger text-white ms-auto fw-bold" style="font-size: 0.68rem; padding: 0.25em 0.6em;">
                                    {{ $totalPendingVerifikasi }}
                                </span>
                            @endif
                        </a>
                    </li>
                @endif

                <!-- ======================================================== -->
                <!-- 2. MENU DISTRIBUSI BARANG (Pegawai Gudang) -->
                <!-- ======================================================== -->
                @if ($isPegawaiGudang)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('distribusi.*') ? 'active' : '' }}" href="{{ route('distribusi.index') }}" title="Distribusi Barang">
                            <i class="icon">
                                <svg class="icon-20" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"></path>
                                    <path d="M15 18H9"></path>
                                    <path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.62l-3.48-4.35A1 1 0 0 0 17.52 8H14v10"></path>
                                    <circle cx="17" cy="18" r="2"></circle>
                                    <circle cx="7" cy="18" r="2"></circle>
                                </svg>
                            </i>
                            <span class="item-name">Distribusi Barang</span>
                            @if ($readyDistribusiCount > 0)
                                <span class="badge rounded-pill bg-warning text-dark ms-auto fw-bold" style="font-size: 0.68rem; padding: 0.25em 0.6em;">
                                    {{ $readyDistribusiCount }}
                                </span>
                            @endif
                        </a>
                    </li>
                @endif

                <!-- ======================================================== -->
                <!-- 3. MENU OPERASIONAL GUDANG (Kasubag TU, Pegawai Gudang) -->
                <!-- ======================================================== -->
                @if ($isKasubagTU || $isPegawaiGudang)
                    @php
                        $transaksiActive = request()->routeIs('barang-masuk') || request()->routeIs('barang-keluar');
                    @endphp
                    <li class="nav-item">
                        <a class="nav-link {{ $transaksiActive ? 'active' : '' }}" data-bs-toggle="collapse"
                            href="#transaksi-menu" role="button" aria-expanded="{{ $transaksiActive ? 'true' : 'false' }}" title="Gudang & Stok">
                            <i class="icon">
                                <svg class="icon-20" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m7.5 4.27 9 5.15"></path>
                                    <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path>
                                    <path d="m3.3 7 8.7 5 8.7-5"></path>
                                    <path d="M12 22V12"></path>
                                </svg>
                            </i>
                            <span class="item-name">Gudang & Stok</span>
                            <i class="right-icon">
                                <svg class="icon-16" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m9 18 6-6-6-6"></path>
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
                <!-- 4. MENU PENGAJUAN ATK (Pegawai & PIC) -->
                <!-- ======================================================== -->
                @if ($isRequester && !$isKasubagTU)
                    @php
                        $pengajuanActive = request()->routeIs('pengajuan.*') || request()->routeIs('katalog-atk');
                    @endphp
                    <li class="nav-item">
                        <a class="nav-link {{ $pengajuanActive ? 'active' : '' }}" data-bs-toggle="collapse"
                            href="#pengajuan-menu" role="button" aria-expanded="{{ $pengajuanActive ? 'true' : 'false' }}" title="Permintaan ATK">
                            <i class="icon">
                                <svg class="icon-20" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
                                    <path d="M3 6h18"></path>
                                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                                </svg>
                            </i>
                            <span class="item-name">Permintaan ATK</span>
                            <i class="right-icon">
                                <svg class="icon-16" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m9 18 6-6-6-6"></path>
                                </svg>
                            </i>
                        </a>

                        <ul class="sub-nav collapse {{ $pengajuanActive ? 'show' : '' }}" id="pengajuan-menu" data-bs-parent="#sidebar-menu">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('katalog-atk') ? 'active' : '' }}" href="{{ route('katalog-atk') }}" title="Katalog Persediaan">
                                    <i class="sidenav-mini-icon">K</i>
                                    <span class="item-name">Katalog Persediaan</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('pengajuan.create') ? 'active' : '' }}" href="{{ route('pengajuan.create') }}" title="Buat Pengajuan Barang (Tersedia di Katalog)">
                                    <i class="sidenav-mini-icon">+</i>
                                    <span class="item-name">Buat Pengajuan Barang</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('pengajuan.pengadaan.*') ? 'active' : '' }}" href="{{ route('pengajuan.pengadaan.create') }}" title="Usulan Pengadaan (Barang Belum Tersedia)">
                                    <i class="sidenav-mini-icon">P</i>
                                    <span class="item-name">Usulan Pengadaan</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('pengajuan.index') ? 'active' : '' }}" href="{{ route('pengajuan.index') }}" title="Riwayat Pengajuan">
                                    <i class="sidenav-mini-icon">R</i>
                                    <span class="item-name">Riwayat Pengajuan</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                <!-- ======================================================== -->
                <!-- 5. MENU LAPORAN PERSEDIAAN (Kasubag TU, Pegawai Gudang) -->
                <!-- ======================================================== -->
                @if ($isKasubagTU || $isPegawaiGudang)
                    @php
                        $laporanActive = request()->routeIs('reports.*');
                    @endphp
                    <li class="nav-item">
                        <a class="nav-link {{ $laporanActive ? 'active' : '' }}" data-bs-toggle="collapse"
                            href="#laporan-menu" role="button" aria-expanded="{{ $laporanActive ? 'true' : 'false' }}" title="Laporan Persediaan & Kartu Stok">
                            <i class="icon">
                                <svg class="icon-20" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <line x1="10" y1="9" x2="8" y2="9"></line>
                                </svg>
                            </i>
                            <span class="item-name">Laporan Persediaan</span>
                            <i class="right-icon">
                                <svg class="icon-16" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m9 18 6-6-6-6"></path>
                                </svg>
                            </i>
                        </a>
                        <ul class="sub-nav collapse {{ $laporanActive ? 'show' : '' }}" id="laporan-menu" data-bs-parent="#sidebar-menu">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('reports.index') || request()->routeIs('reports.print') ? 'active' : '' }}"
                                    href="{{ route('reports.index') }}" title="Mutasi Persediaan ATK">
                                    <i class="sidenav-mini-icon">M</i>
                                    <span class="item-name">Mutasi Persediaan</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('reports.kartu-persediaan*') ? 'active' : '' }}"
                                    href="{{ route('reports.kartu-persediaan') }}" title="Kartu Persediaan Barang">
                                    <i class="sidenav-mini-icon">K</i>
                                    <span class="item-name">Kartu Persediaan</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                <!-- ======================================================== -->
                <!-- 6. MASTER DATA (Kasubag TU, Pegawai Gudang) -->
                <!-- ======================================================== -->
                @if ($isKasubagTU || $isPegawaiGudang)
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
                                <svg class="icon-20" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                    <path d="M3 5V19A9 3 0 0 0 21 19V5"></path>
                                    <path d="M3 12A9 3 0 0 0 21 12"></path>
                                </svg>
                            </i>
                            <span class="item-name">Master Data</span>
                            <i class="right-icon">
                                <svg class="icon-16" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m9 18 6-6-6-6"></path>
                                </svg>
                            </i>
                        </a>
                        <ul class="sub-nav collapse {{ $masterActive ? 'show' : '' }}" id="master-data" data-bs-parent="#sidebar-menu">
                            @if ($isKasubagTU)
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('master-data.kategori-barang.*') ? 'active' : '' }}"
                                        href="{{ route('master-data.kategori-barang.index') }}" title="Kategori Barang">
                                        <i class="sidenav-mini-icon">K</i>
                                        <span class="item-name">Kategori Barang</span>
                                    </a>
                                </li>
                            @endif

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('master-data.daftar-barang.*') ? 'active' : '' }}"
                                    href="{{ route('master-data.daftar-barang.index') }}" title="Daftar Barang ATK">
                                    <i class="sidenav-mini-icon">B</i>
                                    <span class="item-name">Daftar Barang ATK</span>
                                </a>
                            </li>

                            @if ($isKasubagTU)
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('master-data.manajemen-pengguna.*') ? 'active' : '' }}"
                                        href="{{ route('master-data.manajemen-pengguna.index') }}" title="Manajemen Pengguna">
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
    
    {{-- User Account Widget dinonaktifkan untuk semua role sesuai permintaan user --}}
    <div class="sidebar-user-item d-none" style="display: none !important;">
        <div class="sidebar-user-card">
            <div class="sidebar-user-avatar"></div>
        </div>
    </div>
</aside>
