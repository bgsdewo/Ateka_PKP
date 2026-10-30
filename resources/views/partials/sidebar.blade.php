@php
    $currentUser = auth()->user();
    $role = $currentUser->role ?? 'pegawai';

    $isKasubagTU = in_array($role, ['kasubag_tu', 'admin']);
    $isPicDistribusi = $role === 'pic_distribusi';
    $isPegawaiGudang = $role === 'pegawai_gudang';
    $isRequester = in_array($role, ['pegawai', 'pic_distribusi', 'member']) || empty($role);

    $pendingApprovalsCount = $isKasubagTU ? \App\Models\RequestItem::where('status', 'pending')->count() : 0;
    $pendingProcurementCount = $isKasubagTU ? \App\Models\RequestItem::where('status', 'pending')->where('is_procurement', true)->count() : 0;
    $pendingStockCount = $isKasubagTU ? \App\Models\RequestItem::where('status', 'pending')->where(function($q) { $q->where('is_procurement', false)->orWhereNull('is_procurement'); })->count() : 0;
    $isDistributorGudang = $currentUser && $currentUser->isPegawaiGudang() && $currentUser->isDistributor();
    $readyDistribusiCount = $isPegawaiGudang ? \App\Models\RequestItem::where('status', 'approved')->count() : 0;
@endphp

<aside class="sidebar sidebar-default sidebar-base navs-rounded-all">
    <!-- Header Sidebar: Logo Kementerian & Identitas Instansi -->
    <div class="sidebar-header d-flex align-items-center justify-content-between">
        <a href="{{ route('dashboard') }}" class="navbar-brand">
            <div class="sidebar-brand-logo">
                <img src="{{ asset('hope-ui/assets/images/auth/kemenkp.jpeg') }}" alt="Logo Kementerian PKP" width="34" height="34" style="border-radius: 8px; object-fit: contain;">
            </div>
            <div class="sidebar-brand-text">
                <h5 class="logo-title">Inventori ATK</h5>
                <span class="sidebar-subtext">Kementerian PKP RI</span>
            </div>
        </a>
        <div class="sidebar-toggle" data-toggle="sidebar" data-active="true" role="button" tabindex="0" aria-label="Tutup sidebar">
            <i class="icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </i>
        </div>
    </div>

    <!-- Body Menu Sidebar -->
    <div class="sidebar-body pt-0 data-scrollbar flex-grow-1">
        <div class="sidebar-list">
            <ul class="navbar-nav iq-main-menu" id="sidebar-menu">

                <!-- Label Kategori Menu -->
                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#" tabindex="-1">
                        <span class="default-icon">Menu Utama</span>
                        <span class="mini-icon">-</span>
                    </a>
                </li>

                <!-- 1. Dashboard (Semua Role) -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" title="Dashboard">
                        <div class="sidebar-icon-squircle">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                        </div>
                        <span class="item-name">Dashboard</span>
                    </a>
                </li>

                <!-- 2. Verifikasi & Persetujuan (Kasubag TU & Admin) -->
                @if ($isKasubagTU)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('persetujuan.*') ? 'active' : '' }}" href="{{ route('persetujuan.index') }}" title="Verifikasi & Persetujuan">
                            <div class="sidebar-icon-squircle">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 11l3 3L22 4"></path>
                                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                </svg>
                            </div>
                            <span class="item-name">Verifikasi &amp; Persetujuan</span>
                            @if ($pendingApprovalsCount > 0)
                                <span class="sidebar-pill-badge">{{ $pendingApprovalsCount }}</span>
                            @endif
                        </a>
                    </li>
                @endif

                <!-- 3. Distribusi Barang (Pegawai Gudang / Petugas Penyerahan) -->
                @if ($isPegawaiGudang)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('distribusi.*') ? 'active' : '' }}" href="{{ route('distribusi.index') }}" title="Distribusi Barang">
                            <div class="sidebar-icon-squircle">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                </svg>
                            </div>
                            <span class="item-name">Distribusi Barang</span>
                            @if ($readyDistribusiCount > 0)
                                <span class="sidebar-pill-badge">{{ $readyDistribusiCount }}</span>
                            @endif
                        </a>
                    </li>
                @endif

                <!-- 4. Gudang & Stok (Kasubag TU, Pegawai Gudang) -->
                @if ($isKasubagTU || $isPegawaiGudang)
                    @php
                        $transaksiActive = request()->routeIs('barang-masuk') || request()->routeIs('barang-keluar');
                    @endphp
                    <li class="nav-item">
                        <a class="nav-link {{ $transaksiActive ? 'active' : '' }}" data-bs-toggle="collapse"
                            href="#transaksi-menu" role="button" aria-expanded="{{ $transaksiActive ? 'true' : 'false' }}" title="Gudang & Stok">
                            <div class="sidebar-icon-squircle">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                                </svg>
                            </div>
                            <span class="item-name">Gudang &amp; Stok</span>
                            <i class="right-icon">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </i>
                        </a>

                        <ul class="sub-nav collapse {{ $transaksiActive ? 'show' : '' }}" id="transaksi-menu" data-bs-parent="#sidebar-menu">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('barang-masuk') ? 'active' : '' }}" href="{{ route('barang-masuk') }}" title="Barang Masuk (Pengadaan)">
                                    <span class="item-name">Barang Masuk (Pengadaan)</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('barang-keluar') ? 'active' : '' }}" href="{{ route('barang-keluar') }}" title="Barang Keluar">
                                    <span class="item-name">Barang Keluar</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                <!-- 5. Permintaan ATK (Pegawai & PIC Requester) -->
                @if ($isRequester && !$isKasubagTU)
                    @php
                        $pengajuanActive = request()->routeIs('pengajuan.*') || request()->routeIs('katalog-atk');
                    @endphp
                    <li class="nav-item">
                        <a class="nav-link {{ $pengajuanActive ? 'active' : '' }}" data-bs-toggle="collapse"
                            href="#pengajuan-menu" role="button" aria-expanded="{{ $pengajuanActive ? 'true' : 'false' }}" title="Permintaan ATK">
                            <div class="sidebar-icon-squircle">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                            </div>
                            <span class="item-name">Permintaan ATK</span>
                            <i class="right-icon">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </i>
                        </a>

                        <ul class="sub-nav collapse {{ $pengajuanActive ? 'show' : '' }}" id="pengajuan-menu" data-bs-parent="#sidebar-menu">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('katalog-atk') ? 'active' : '' }}" href="{{ route('katalog-atk') }}" title="Katalog Persediaan">
                                    <span class="item-name">Katalog Persediaan</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('pengajuan.create') ? 'active' : '' }}" href="{{ route('pengajuan.create') }}" title="Buat Pengajuan Barang">
                                    <span class="item-name">Buat Pengajuan Barang</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('pengajuan.pengadaan.*') ? 'active' : '' }}" href="{{ route('pengajuan.pengadaan.create') }}" title="Usulan Pengadaan">
                                    <span class="item-name">Usulan Pengadaan</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('pengajuan.index') ? 'active' : '' }}" href="{{ route('pengajuan.index') }}" title="Riwayat Pengajuan">
                                    <span class="item-name">Riwayat Pengajuan</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                <!-- 6. Laporan Persediaan (Kasubag TU, Pegawai Gudang) -->
                @if ($isKasubagTU || $isPegawaiGudang)
                    @php
                        $laporanActive = request()->routeIs('reports.*');
                    @endphp
                    <li class="nav-item">
                        <a class="nav-link {{ $laporanActive ? 'active' : '' }}" data-bs-toggle="collapse"
                            href="#laporan-menu" role="button" aria-expanded="{{ $laporanActive ? 'true' : 'false' }}" title="Laporan Persediaan">
                            <div class="sidebar-icon-squircle">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="20" x2="18" y2="10"></line>
                                    <line x1="12" y1="20" x2="12" y2="4"></line>
                                    <line x1="6" y1="20" x2="6" y2="14"></line>
                                </svg>
                            </div>
                            <span class="item-name">Laporan Persediaan</span>
                            <i class="right-icon">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </i>
                        </a>
                        <ul class="sub-nav collapse {{ $laporanActive ? 'show' : '' }}" id="laporan-menu" data-bs-parent="#sidebar-menu">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('reports.index') || request()->routeIs('reports.print') ? 'active' : '' }}"
                                    href="{{ route('reports.index') }}" title="Mutasi Persediaan ATK">
                                    <span class="item-name">Mutasi Persediaan</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('reports.kartu-persediaan*') ? 'active' : '' }}"
                                    href="{{ route('reports.kartu-persediaan') }}" title="Kartu Persediaan Barang">
                                    <span class="item-name">Kartu Persediaan</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('reports.rekapitulasi') ? 'active' : '' }}"
                                    href="{{ route('reports.rekapitulasi') }}" title="Rekapitulasi Kategori">
                                    <span class="item-name">Rekapitulasi Kategori</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('reports.berita-acara') ? 'active' : '' }}"
                                    href="{{ route('reports.berita-acara') }}" title="Berita Acara">
                                    <span class="item-name">Berita Acara</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                <!-- 7. Master Data (Kasubag TU, Pegawai Gudang) -->
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
                            <div class="sidebar-icon-squircle">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                </svg>
                            </div>
                            <span class="item-name">Master Data</span>
                            <i class="right-icon">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </i>
                        </a>
                        <ul class="sub-nav collapse {{ $masterActive ? 'show' : '' }}" id="master-data" data-bs-parent="#sidebar-menu">
                            @if ($isKasubagTU)
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('master-data.kategori-barang.*') ? 'active' : '' }}"
                                        href="{{ route('master-data.kategori-barang.index') }}" title="Kategori Barang">
                                        <span class="item-name">Kategori Barang</span>
                                    </a>
                                </li>
                            @endif

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('master-data.daftar-barang.*') ? 'active' : '' }}"
                                    href="{{ route('master-data.daftar-barang.index') }}" title="Daftar Barang ATK">
                                    <span class="item-name">Daftar Barang ATK</span>
                                </a>
                            </li>

                            @if ($isKasubagTU)
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('master-data.manajemen-pengguna.*') ? 'active' : '' }}"
                                        href="{{ route('master-data.manajemen-pengguna.index') }}" title="Manajemen Pengguna">
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

    <!-- 8. Bagian Bawah: Profil Pengguna (Resmi & Elegan) -->
    <div class="sidebar-user-footer">
        <div class="sidebar-user-card">
            <div class="sidebar-user-avatar">
                {{ strtoupper(substr($currentUser->name ?? 'U', 0, 1)) }}
            </div>
            <div class="sidebar-user-details">
                <span class="sidebar-user-name" title="{{ $currentUser->name ?? 'Pengguna' }}">
                    {{ $currentUser->name ?? 'Pengguna' }}
                </span>
                <span class="sidebar-user-role" title="{{ $currentUser->nip ? 'NIP. ' . $currentUser->nip : ucfirst($role) }}">
                    @if($currentUser && $currentUser->nip)
                        NIP. {{ $currentUser->nip }}
                    @else
                        {{ match ($role) {
                            'kasubag_tu', 'admin' => 'Kasubbag Tata Usaha',
                            'pic_distribusi' => 'PIC Distribusi',
                            'pegawai_gudang' => 'Petugas Gudang',
                            default => 'Pegawai Pemohon',
                        } }}
                    @endif
                </span>
            </div>
        </div>
    </div>
</aside>
