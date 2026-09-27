<!doctype html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Pencatatan Stok Barang')</title>
    <link rel="shortcut icon" href="{{ asset('hope-ui/assets/images/favicon.ico') }}" />
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/core/libs.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/vendor/aos/dist/aos.css') }}" />
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/hope-ui.min.css?v=2.0.0') }}" />
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/custom.min.css?v=2.0.0') }}" />
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/dark.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/customizer.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/rtl.min.css') }}" />
    @stack('styles')
</head>

<body class=" ">
    <div id="loading">
        <div class="loader simple-loader">
            <div class="loader-body"></div>
        </div>
    </div>
    @include('partials.sidebar')
    <main class="main-content">
        <div class="position-relative iq-banner">
            @include('partials.navbar')
            @yield('page-header')
        </div>
        @yield('content')
        @include('partials.footer')
    </main>

    @include('partials.scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('navbar-search');

            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    const keyword = e.target.value.toLowerCase();

                    // 1. JIKA HALAMAN MENGGUNAKAN PLUGIN DATATABLES (Kategori & Daftar Barang)
                    if (typeof $.fn.dataTable !== 'undefined' && $.fn.DataTable.isDataTable('table')) {
                        $('table').DataTable().search(keyword).draw();
                    }

                    // 2. JIKA HALAMAN MENGGUNAKAN TABEL HTML BIASA (Transaksi Masuk & Keluar)
                    else {
                        const rows = document.querySelectorAll('table tbody tr');

                        rows.forEach(function(row) {
                            // Lewati baris jika itu pesan kosong dari datatables
                            if (row.classList.contains('dataTables_empty')) return;

                            const text = row.innerText.toLowerCase();
                            if (text.includes(keyword)) {
                                row.style.display = ''; // Tampilkan
                            } else {
                                row.style.display = 'none'; // Sembunyikan
                            }
                        });
                    }
                });
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
