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
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/dark.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/customizer.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/rtl.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/ateka-modern.css') }}?v={{ file_exists(public_path('css/ateka-modern.css')) ? filemtime(public_path('css/ateka-modern.css')) : '2.4' }}" />
    <link rel="stylesheet" href="{{ asset('css/ateka-donezo.css') }}?v={{ file_exists(public_path('css/ateka-donezo.css')) ? filemtime(public_path('css/ateka-donezo.css')) : '1.0' }}" />
    <link rel="stylesheet" href="{{ asset('css/ateka-sidebar-aeux.css') }}?v={{ file_exists(public_path('css/ateka-sidebar-aeux.css')) ? filemtime(public_path('css/ateka-sidebar-aeux.css')) : '1.0' }}" />
    @stack('styles')
    <style>
        /* Responsive layout variables */
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
