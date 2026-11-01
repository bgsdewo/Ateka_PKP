<!doctype html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Nonaktifkan Pengindeksan Mesin Pencari / SEO (Aplikasi Internal Kementerian PKP RI) -->
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <meta name="description" content="Sistem Informasi Pencatatan dan E-Katalog Persediaan ATK Kementerian Perumahan dan Kawasan Permukiman RI">
    <title>@yield('title', 'Pencatatan Stok Barang')</title>
    <link rel="shortcut icon" href="{{ asset('hope-ui/assets/images/favicon.ico') }}" />

    <!-- Google Fonts Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap">

    <!-- Core Styles -->
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/core/libs.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/hope-ui.min.css?v=2.0.0') }}" />
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/custom.min.css?v=2.0.0') }}" />
    <link rel="stylesheet" href="{{ asset('css/ateka-modern.css') }}?v={{ file_exists(public_path('css/ateka-modern.css')) ? filemtime(public_path('css/ateka-modern.css')) : '2.4' }}" />
    @stack('styles')
    <style>
        /* ======================================================== */
        /* STILISASI SIDEBAR KETIKA DITUTUP (MINI / COLLAPSED)      */
        /* Semua elemen (Logo, Avatar, Divider, Icon Utama, Subnav) */
        /* Sejajar lurus dan presisi di sumbu tengah (vertikal)    */
        /* ======================================================== */

        /* 1. Header & Logo Sidebar ketika Mini */
        .sidebar.sidebar-mini .sidebar-header {
            padding: 0.85rem 0.5rem !important;
            justify-content: center !important;
            position: relative !important;
            min-height: 68px !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }
        .sidebar.sidebar-mini .navbar-brand {
            padding: 0 !important;
            margin: 0 auto !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 100% !important;
        }
        .sidebar.sidebar-mini .navbar-brand .logo-main,
        .sidebar.sidebar-mini .navbar-brand .logo-normal {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 auto !important;
            width: auto !important;
        }
        .sidebar.sidebar-mini .navbar-brand .logo-normal img {
            width: 38px !important;
            height: 38px !important;
            max-width: 38px !important;
            max-height: 38px !important;
            object-fit: contain !important;
            display: block !important;
            margin: 0 auto !important;
            opacity: 1 !important;
            visibility: visible !important;
        }
        .sidebar.sidebar-mini .navbar-brand .ms-2,
        .sidebar.sidebar-mini .navbar-brand .logo-title,
        .sidebar.sidebar-mini .navbar-brand small,
        .sidebar.sidebar-mini .sidebar-subtext,
        .sidebar.sidebar-mini .sidebar-brand-text,
        .sidebar.sidebar-mini .sidebar-user-info {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: hidden !important;
            opacity: 0 !important;
            visibility: hidden !important;
            position: absolute !important;
            pointer-events: none !important;
        }
        .sidebar.sidebar-mini .sidebar-toggle {
            position: absolute !important;
            right: -12px !important;
            top: 18px !important;
            z-index: 1000 !important;
        }

        /* 2. Profil Avatar Pengguna */
        .sidebar.sidebar-mini .sidebar-user-item {
            padding: 0.6rem 0 !important;
            margin: 0 auto 0.25rem auto !important;
            display: flex !important;
            justify-content: center !important;
            width: 100% !important;
        }
        .sidebar.sidebar-mini .sidebar-user-card {
            background: transparent !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 auto !important;
            justify-content: center !important;
            width: auto !important;
            gap: 0 !important;
            box-shadow: none !important;
        }
        .sidebar.sidebar-mini .sidebar-user-avatar {
            width: 36px !important;
            height: 36px !important;
            min-width: 36px !important;
            margin: 0 auto !important;
            background-color: #13463a !important;
            border: 1.5px solid #34d399 !important;
            color: #ffffff !important;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2) !important;
        }

        /* 3. Garis Pemisah / Static Item Dash (-) */
        .sidebar.sidebar-mini .nav-item.static-item {
            padding: 0.25rem 0 !important;
            margin: 0 auto !important;
            text-align: center !important;
            width: 100% !important;
        }
        .sidebar.sidebar-mini .nav-item.static-item .nav-link {
            padding: 0 !important;
            margin: 0 auto !important;
            justify-content: center !important;
            text-align: center !important;
            width: 100% !important;
        }
        .sidebar.sidebar-mini .nav-item.static-item .default-icon {
            display: none !important;
        }
        .sidebar.sidebar-mini .nav-item.static-item .mini-icon {
            display: block !important;
            margin: 0 auto !important;
            text-align: center !important;
            font-size: 1.1rem !important;
            line-height: 1 !important;
            color: #adb5bd !important;
        }

        /* 4. Menu Utama (Dashboard, Persetujuan, Distribusi, Gudang, dll) */
        .sidebar.sidebar-mini .sidebar-body {
            padding-left: 0 !important;
            padding-right: 0 !important;
            overflow-x: hidden !important;
        }
        .sidebar.sidebar-mini .sidebar-list,
        .sidebar.sidebar-mini .iq-main-menu {
            padding-left: 0 !important;
            padding-right: 0 !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            width: 100% !important;
        }
        .sidebar.sidebar-mini .iq-main-menu > .nav-item {
            padding-left: 0 !important;
            padding-right: 0 !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            width: 100% !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .sidebar.sidebar-mini .iq-main-menu > .nav-item > .nav-link {
            width: 42px !important;
            height: 42px !important;
            min-width: 42px !important;
            max-width: 42px !important;
            padding: 0 !important;
            margin: 4px auto !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 10px !important;
            box-sizing: border-box !important;
            position: relative !important;
            transition: all 0.2s ease-in-out !important;
        }
        .sidebar.sidebar-mini .iq-main-menu > .nav-item > .nav-link:not(.active) {
            color: #8faea2 !important;
            background-color: transparent !important;
        }
        .sidebar.sidebar-mini .iq-main-menu > .nav-item > .nav-link:not(.active):hover {
            color: #ffffff !important;
            background-color: rgba(255, 255, 255, 0.08) !important;
        }
        .sidebar.sidebar-mini .iq-main-menu > .nav-item > .nav-link.active,
        .sidebar.sidebar-mini .iq-main-menu > .nav-item > .nav-link[aria-expanded="true"] {
            background-color: rgba(255, 255, 255, 0.16) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25) !important;
        }
        .sidebar.sidebar-mini .iq-main-menu > .nav-item > .nav-link .icon {
            margin: 0 auto !important;
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 20px !important;
            height: 20px !important;
            min-width: 20px !important;
            min-height: 20px !important;
            line-height: 1 !important;
        }
        .sidebar.sidebar-mini .iq-main-menu > .nav-item > .nav-link .icon svg,
        .sidebar.sidebar-mini .iq-main-menu > .nav-item > .nav-link .icon-20 {
            width: 20px !important;
            height: 20px !important;
            min-width: 20px !important;
            min-height: 20px !important;
            display: block !important;
            margin: 0 auto !important;
            opacity: 1 !important;
            visibility: visible !important;
        }
        .sidebar.sidebar-mini .iq-main-menu > .nav-item > .nav-link.active .icon,
        .sidebar.sidebar-mini .iq-main-menu > .nav-item > .nav-link.active .icon svg,
        .sidebar.sidebar-mini .iq-main-menu > .nav-item > .nav-link[aria-expanded="true"] .icon,
        .sidebar.sidebar-mini .iq-main-menu > .nav-item > .nav-link[aria-expanded="true"] .icon svg {
            color: #ffffff !important;
        }

        /* Sembunyikan teks label, panah dropdown, dan badge saat mini agar tidak mendorong ikon keluar batas */
        .sidebar.sidebar-mini .item-name,
        .sidebar.sidebar-mini .nav-link .item-name,
        .sidebar.sidebar-mini .nav-link > span:not(.sidenav-mini-icon):not(.mini-icon),
        .sidebar.sidebar-mini .right-icon,
        .sidebar.sidebar-mini .nav-link .right-icon,
        .sidebar.sidebar-mini .nav-link > .badge {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: hidden !important;
            opacity: 0 !important;
            visibility: hidden !important;
            position: absolute !important;
            pointer-events: none !important;
        }

        /* 5. Sub-Menu & Huruf (R, +, K, M, L, dll) */
        .sidebar.sidebar-mini .sub-nav {
            padding: 0.25rem 0 !important;
            margin: 2px auto !important;
            width: 100% !important;
            list-style: none !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
        }
        /* Sembunyikan sub-nav yang sedang collapsed / ditutup agar tidak bocor */
        .sidebar.sidebar-mini .sub-nav:not(.show) {
            display: none !important;
        }
        .sidebar.sidebar-mini .sub-nav .nav-item {
            padding: 0 !important;
            margin: 2px auto !important;
            width: 100% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .sidebar.sidebar-mini .sub-nav .nav-link {
            width: 34px !important;
            height: 34px !important;
            min-width: 34px !important;
            max-width: 34px !important;
            padding: 0 !important;
            margin: 0 auto !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 8px !important;
            background-color: rgba(255, 255, 255, 0.06) !important;
            color: #8faea2 !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            box-shadow: none !important;
            transition: all 0.2s ease-in-out !important;
        }
        .sidebar.sidebar-mini .sub-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.12) !important;
            color: #ffffff !important;
            border-color: rgba(52, 211, 153, 0.3) !important;
        }
        .sidebar.sidebar-mini .sub-nav .nav-link.active {
            background-color: #10b981 !important;
            color: #ffffff !important;
            border-color: #10b981 !important;
            box-shadow: 0 2px 6px rgba(16, 185, 129, 0.35) !important;
        }
        .sidebar.sidebar-mini .sub-nav .nav-link i.sidenav-mini-icon {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 100% !important;
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            font-style: normal !important;
            font-weight: 700 !important;
            font-size: 0.82rem !important;
            line-height: 1 !important;
            text-align: center !important;
            color: inherit !important;
            transform: none !important;
            -webkit-transform: none !important;
            opacity: 1 !important;
        }
        .sidebar.sidebar-mini .sub-nav .nav-link .icon,
        .sidebar.sidebar-mini .sub-nav .nav-link .item-name {
            display: none !important;
        }

        /* ======================================================== */
        /* STILISASI SIDEBAR EXPANDED (DEFAULT)                     */
        /* Menjaga teks menu panjang, badge angka, dan icon panah   */
        /* tetap rapi, proporsional, dan tidak terpotong (clipped)  */
        /* ======================================================== */
        @media (min-width: 1200px) {
            :root {
                --sidebar-width: 17.5rem;
            }

            .sidebar:not(.sidebar-mini) {
                width: 17.5rem !important;
                max-width: 17.5rem !important;
            }

            .sidebar:not(.sidebar-mini) + .main-content {
                margin-left: 17.5rem !important;
            }
        }

        .sidebar:not(.sidebar-mini) .nav-item .nav-link {
            padding: 0.5rem 0.85rem !important;
        }

        .sidebar:not(.sidebar-mini) .nav-item .nav-link .item-name {
            margin-left: 0.75rem !important;
            font-size: 0.88rem !important;
            min-width: 0 !important;
            flex: 1 1 auto !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .sidebar:not(.sidebar-mini) .nav-item .nav-link .right-icon {
            flex-shrink: 0 !important;
            margin-left: auto !important;
        }

        .sidebar:not(.sidebar-mini) .nav-item .nav-link .badge {
            flex-shrink: 0 !important;
        }
    </style>
</head>

<body class=" ">
    <div id="loading">
        <div class="loader simple-loader">
            <div class="loader-body"></div>
        </div>
    </div>
    @include('partials.sidebar')
    <main class="main-content" role="main" aria-label="Konten Utama">
        <div class="position-relative iq-banner">
            @include('partials.navbar')
            @yield('page-header')
        </div>
        @yield('content')
        @include('partials.footer')
    </main>

    @include('partials.scripts')

    @stack('scripts')
</body>

</html>
