<!doctype html>
<html lang="id" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <title>@yield('title') | ATEKA — Kementerian PKP RI</title>
    <link rel="shortcut icon" href="{{ asset('hope-ui/assets/images/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('fonts/fonts.css') }}?v={{ @filemtime(public_path('fonts/fonts.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/ateka-auth.css') }}?v={{ @filemtime(public_path('css/ateka-auth.css')) }}">
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
            <h1>ATEKA<i>.</i></h1>
            <p class="au-tag">Sistem Informasi Inventori Persediaan ATK</p>
            <ul class="au-feats">
                <li>
                    <span class="au-feat-ico"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="9" y1="13" x2="15" y2="13"></line><line x1="9" y1="17" x2="13" y2="17"></line></svg></span>
                    <div><b>Pengajuan daring</b><span>Ajukan kebutuhan ATK tanpa formulir kertas.</span></div>
                </li>
                <li>
                    <span class="au-feat-ico"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg></span>
                    <div><b>Status terpantau</b><span>Lacak dari verifikasi hingga barang diterima.</span></div>
                </li>
                <li>
                    <span class="au-feat-ico"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg></span>
                    <div><b>Persediaan tercatat</b><span>Stok, mutasi, dan laporan tersusun otomatis.</span></div>
                </li>
            </ul>
        </div>

        <div class="au-foot">
            <b>Biro Umum &amp; Pengadaan Barang</b> &middot; Subbagian Tata Usaha<br>
            &copy; {{ date('Y') }} Kementerian Perumahan dan Kawasan Permukiman RI
        </div>
    </aside>

    <main class="au-main">
        <div class="au-card @yield('card_class')">
            <div class="au-head">
                <span class="au-tile @yield('icon', 'au-t-shield')" aria-hidden="true"></span>
                <div>
                    <h2>@yield('heading')</h2>
                    <p class="au-sub">@yield('subheading')</p>
                </div>
            </div>

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
        <p class="au-help">Kendala akses akun? Hubungi Subbagian Tata Usaha.</p>
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
