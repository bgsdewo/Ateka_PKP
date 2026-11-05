<!doctype html>
<html lang="id" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <title>@yield('title') | ATEKA — Kementerian PKP RI</title>
    <link rel="shortcut icon" href="{{ asset('hope-ui/assets/images/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/ateka-auth.css') }}">
</head>
<body>
<div class="au-shell">
    <aside class="au-brand">
        <div class="au-org">
            <span class="au-logo-plate"><img src="{{ asset('hope-ui/assets/images/auth/kemenpkp-logo.png') }}" alt="Logo Kementerian PKP RI"></span>
            <div class="au-org-text">
                <strong>Kementerian PKP</strong>
                <span>Direktorat Jenderal Kawasan Permukiman</span>
            </div>
        </div>

        <div class="au-product">
            <h1>ATEKA</h1>
            <p class="au-tag">Sistem Informasi Inventori Persediaan ATK</p>
            <ul class="au-points">
                <li>Ajukan kebutuhan alat tulis kantor secara daring</li>
                <li>Pantau status persetujuan hingga barang diterima</li>
                <li>Stok, mutasi, dan laporan persediaan tercatat rapi</li>
            </ul>
        </div>

        <div class="au-foot">
            <b>Biro Umum &amp; Pengadaan Barang</b> &middot; Subbagian Tata Usaha<br>
            &copy; {{ date('Y') }} Kementerian Perumahan dan Kawasan Permukiman RI
        </div>
    </aside>

    <main class="au-main">
        <div class="au-card @yield('card_class')">
            <h2>@yield('heading')</h2>
            <p class="au-sub">@yield('subheading')</p>

            @if (session('success'))
                <div class="au-alert au-alert-ok" role="status">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="au-alert au-alert-error" role="alert">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="au-alert au-alert-error" role="alert">
                    <strong>@yield('error_title', 'Periksa kembali isian Anda:')</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>

<script>
    (function () {
        var eyeOn = '<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path><circle cx="12" cy="12" r="3"></circle>';
        var eyeOff = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 19c-7 0-11-7-11-7a18.5 18.5 0 0 1 5.06-5.94M9.9 5.24A9.12 9.12 0 0 1 12 5c7 0 11 7 11 7a18.5 18.5 0 0 1-2.16 3.19M1 1l22 22"></path>';
        document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
            var input = document.getElementById(btn.getAttribute('data-toggle-password'));
            var icon = btn.querySelector('svg');
            if (!input || !icon) return;
            btn.addEventListener('click', function () {
                var hidden = input.type === 'password';
                input.type = hidden ? 'text' : 'password';
                icon.innerHTML = hidden ? eyeOff : eyeOn;
                btn.setAttribute('aria-pressed', hidden ? 'true' : 'false');
            });
        });
        document.querySelectorAll('form[data-loading-text]').forEach(function (form) {
            form.addEventListener('submit', function () {
                var btn = form.querySelector('button[type="submit"]');
                if (!btn || btn.disabled) return;
                btn.disabled = true;
                btn.innerHTML = '<span class="au-spin"></span>' + form.getAttribute('data-loading-text');
            });
        });
    })();
</script>
</body>
</html>
