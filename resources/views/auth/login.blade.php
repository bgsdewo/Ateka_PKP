<!doctype html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Nonaktifkan Pengindeksan Mesin Pencari / SEO (Aplikasi Internal Kementerian PKP RI) -->
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <meta name="description" content="Login Sistem Informasi Pencatatan dan E-Katalog Persediaan ATK Kementerian PKP RI">
    <title>Login | Pencatatan Stok Barang</title>
    <!-- Favicon -->
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

    <!-- Penyesuaian UI/UX: Official Kementerian PKP RI Palette & Modern GovTech Styling -->
    <style>
        html {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            background: linear-gradient(135deg, #061224 0%, #0a213e 40%, #0c3d69 75%, #075985 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
            font-family: var(--font-sans, 'Plus Jakarta Sans', sans-serif);
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
        }

        /* Ambient Living Glow (Fixed agar tidak menambah tinggi halaman / sisa ruang kosong) */
        body::before {
            content: '';
            position: fixed;
            width: 480px;
            height: 480px;
            right: -80px;
            top: -120px;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.24) 0%, rgba(14, 165, 233, 0.08) 40%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        body::after {
            content: '';
            position: fixed;
            width: 450px;
            height: 450px;
            left: -80px;
            bottom: -100px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.16) 0%, transparent 65%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        .wrapper {
            min-height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-content {
            width: 100%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 1rem;
            box-sizing: border-box;
            position: relative;
            z-index: 1;
        }

        @keyframes authCardEnter {
            from {
                opacity: 0;
                transform: translateY(16px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .auth-card {
            border-radius: 1.25rem;
            box-shadow: 0 25px 50px -12px rgba(6, 18, 36, 0.50), 0 0 0 1px rgba(255, 255, 255, 0.15) !important;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border: none;
            animation: authCardEnter 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .form-control {
            border-radius: 0.65rem;
            padding: 0.72rem 1rem;
            font-size: 0.9rem;
            border: 1.5px solid #e2e8f0;
            background-color: #f8fafc;
            color: #0f172a;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            background-color: #ffffff;
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
            outline: none;
        }

        .btn-primary {
            border-radius: 0.65rem !important;
            padding: 0.75rem 1rem !important;
            font-weight: 700 !important;
            letter-spacing: -0.01em;
            background: linear-gradient(135deg, #0b345b 0%, #174e84 45%, #0284c7 100%) !important;
            border: none !important;
            color: #ffffff !important;
            box-shadow: 0 4px 14px -2px rgba(15, 76, 129, 0.35) !important;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #082643 0%, #113d68 45%, #0369a1 100%) !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -2px rgba(15, 76, 129, 0.45) !important;
        }

        .btn-primary:active {
            transform: scale(0.98) !important;
        }

        .brand-logo-img {
            max-height: 72px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.03));
            transition: transform 0.25s ease;
        }

        .brand-logo-img:hover {
            transform: scale(1.03);
        }

        .text-theme {
            color: #0f4c81 !important;
        }

        .password-toggle-btn {
            background: transparent;
            border: none;
            color: #64748b;
            padding: 0 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-toggle-btn:hover {
            color: #0f172a;
        }
    </style>
</head>

<body>
    <!-- loader Start -->
    <div id="loading">
        <div class="loader simple-loader">
            <div class="loader-body"></div>
        </div>
    </div>
    <!-- loader END -->

    <div class="wrapper">
        <section class="login-content">
            <div class="container">
                <div class="row justify-content-center align-items-center">
                    <div class="col-11 col-sm-9 col-md-6 col-lg-4" style="max-width: 430px;">
                        <div class="card auth-card mb-0">
                            <div class="card-body" style="padding: 2.75rem 2.25rem !important;">
                                <!-- Logo & Identity -->
                                <div class="text-center mb-4">
                                    <div class="mb-3">
                                        <img src="{{ asset('hope-ui/assets/images/auth/kemenpkp-logo.png') }}"
                                            class="brand-logo-img" alt="Logo Kementerian PKP" style="max-height: 78px; width: auto;">
                                    </div>
                                    <h3 class="fw-bold mb-1" style="color: #0f172a; letter-spacing: -0.025em; font-size: 1.55rem;">
                                        Inventori ATK
                                    </h3>
                                    <p class="text-muted small mb-0" style="font-size: 0.82rem; line-height: 1.45;">
                                        Sistem Pencatatan &amp; Pengelolaan Stok Barang Kantor
                                    </p>
                                </div>

                                <!-- Notifikasi Alert -->
                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show shadow-sm"
                                        role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif

                                @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show shadow-sm"
                                        role="alert">
                                        {{ session('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif

                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show shadow-sm"
                                        role="alert">
                                        <strong>Terjadi kesalahan:</strong>
                                        <ul class="mb-0 mt-2 ps-3">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif

                                <!-- Form Login -->
                                <form action="{{ route('login.process') }}" method="POST">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label for="email" class="form-label small fw-bold text-dark text-uppercase mb-1.5" style="font-size: 0.74rem; letter-spacing: 0.04em;">
                                            Alamat Email
                                        </label>
                                        <div class="input-group">
                                            <input type="email" id="email" name="email" class="form-control"
                                                value="{{ old('email') }}" placeholder="nama@perumahan.go.id" required autocomplete="email"
                                                style="padding: 0.78rem 1rem; border-radius: 0.65rem; font-size: 0.92rem;">
                                        </div>
                                    </div>

                                    <div class="form-group mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-1.5">
                                            <label for="password" class="form-label small fw-bold text-dark text-uppercase mb-0" style="font-size: 0.74rem; letter-spacing: 0.04em;">
                                                Password
                                            </label>
                                            <a href="{{ route('password.forgot') }}" class="small fw-semibold text-primary" style="font-size: 0.78rem; text-decoration: none;">
                                                Lupa Password?
                                            </a>
                                        </div>
                                        <div class="input-group">
                                            <input type="password" id="password" name="password" class="form-control"
                                                placeholder="Masukkan password Anda" required autocomplete="current-password"
                                                style="padding: 0.78rem 1rem; border-top-left-radius: 0.65rem; border-bottom-left-radius: 0.65rem; font-size: 0.92rem;">
                                            <button type="button" class="password-toggle-btn border border-start-0 rounded-end bg-light" id="togglePasswordBtn" aria-label="Tampilkan atau sembunyikan password">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="togglePasswordIcon">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="d-grid mb-3 pt-1">
                                        <button type="submit" class="btn btn-primary btn-block shadow-sm" style="padding: 0.82rem 1rem !important; border-radius: 0.65rem !important; font-size: 0.95rem;">
                                            Masuk ke Sistem
                                        </button>
                                    </div>

                                    <div class="text-center pt-2 mb-3">
                                        <span class="text-muted small" style="font-size: 0.84rem;">Belum memiliki akun?</span>
                                        <a href="{{ route('register') }}" class="fw-bold ms-1 text-primary text-decoration-none" style="font-size: 0.84rem;">
                                            Daftar Akun Baru
                                        </a>
                                    </div>

                                    <p class="text-center text-muted small mb-0" style="font-size: 0.75rem;">
                                        &copy; 2026 Kementerian PKP RI &bull; All Rights Reserved
                                    </p>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Library Bundle Script -->
    <script src="{{ asset('hope-ui/assets/js/core/libs.min.js') }}" defer></script>
    <!-- App Script -->
    <script src="{{ asset('hope-ui/assets/js/hope-ui.js') }}" defer></script>
    <!-- Global Alert Dismiss & Auto-Dismiss System -->
    <script src="{{ asset('js/ateka-alerts.js') }}?v=1.0" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password Show/Hide Toggle
            const togglePasswordBtn = document.getElementById('togglePasswordBtn');
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePasswordIcon');

            if (togglePasswordBtn && passwordInput && toggleIcon) {
                togglePasswordBtn.addEventListener('click', function() {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    if (isPassword) {
                        toggleIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
                    } else {
                        toggleIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
                    }
                });
            }

            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    window.location.reload();
                }
            });
        });
    </script>
</body>

</html>
