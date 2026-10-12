<!doctype html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login | Pencatatan Stok Barang</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('hope-ui/assets/images/favicon.ico') }}" />
    <!-- Library / Plugin Css Build -->
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/core/libs.min.css') }}" />
    <!-- Hope Ui Design System Css -->
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/hope-ui.min.css?v=2.0.0') }}" />
    <!-- Custom Css -->
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/custom.min.css?v=2.0.0') }}" />
    <!-- Dark Css -->
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/dark.min.css') }}" />
    <!-- Customizer Css -->
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/customizer.min.css') }}" />
    <!-- RTL Css -->
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/rtl.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('css/ateka-modern.css') }}?v=2.1" />

    <!-- Penyesuaian UI/UX: Official Kementerian PKP RI Palette & Modern GovTech Styling -->
    <style>
        body {
            background: linear-gradient(135deg, #061224 0%, #0a213e 40%, #0c3d69 75%, #075985 100%);
            min-height: 100vh;
            font-family: var(--font-sans, 'Plus Jakarta Sans', sans-serif);
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient Living Glow */
        body::before {
            content: '';
            position: absolute;
            width: 550px;
            height: 550px;
            right: -100px;
            top: -150px;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.28) 0%, rgba(14, 165, 233, 0.12) 40%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        body::after {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            left: -120px;
            bottom: -150px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.20) 0%, transparent 65%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        .login-content {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1rem;
            position: relative;
            z-index: 1;
        }

        @keyframes authCardEnter {
            from {
                opacity: 0;
                transform: translateY(22px) scale(0.98);
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
            animation: authCardEnter 0.55s cubic-bezier(0.16, 1, 0.3, 1) both;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }


        .form-control {
            border-radius: 0.65rem;
            padding: 0.75rem 1rem;
            font-size: 0.92rem;
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
            padding: 0.78rem 1rem !important;
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
            max-height: 85px;
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
                    <div class="col-md-7 col-lg-5">
                        <div class="card auth-card mb-0">
                            <div class="card-body p-4 p-sm-5">
                                <!-- Logo & Identity -->
                                <div class="text-center mb-4">
                                    <div class="mb-3">
                                        <img src="{{ asset('hope-ui/assets/images/auth/kemenpkp-logo.png') }}"
                                            class="brand-logo-img" alt="Logo Kementerian PKP">
                                    </div>
                                    <h3 class="fw-bold mb-1" style="color: #0f172a; letter-spacing: -0.025em;">
                                        Inventori ATK
                                    </h3>
                                    <p class="text-muted small mb-0">
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
                                        <label for="email" class="form-label small fw-bold text-dark text-uppercase" style="letter-spacing: 0.04em;">
                                            Alamat Email
                                        </label>
                                        <div class="input-group">
                                            <input type="email" id="email" name="email" class="form-control"
                                                value="{{ old('email') }}" placeholder="nama@perumahan.go.id" required autocomplete="email">
                                        </div>
                                    </div>

                                    <div class="form-group mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label for="password" class="form-label small fw-bold text-dark text-uppercase mb-0" style="letter-spacing: 0.04em;">
                                                Password
                                            </label>
                                            <a href="{{ route('password.forgot') }}" class="small fw-semibold text-primary" style="text-decoration: none;">
                                                Lupa Password?
                                            </a>
                                        </div>
                                        <div class="input-group">
                                            <input type="password" id="password" name="password" class="form-control"
                                                placeholder="Masukkan password Anda" required autocomplete="current-password">
                                            <button type="button" class="password-toggle-btn border border-start-0 rounded-end bg-light" id="togglePasswordBtn" aria-label="Tampilkan atau sembunyikan password">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="togglePasswordIcon">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="d-grid mb-3">
                                        <button type="submit" class="btn btn-primary btn-block shadow-sm">
                                            Masuk ke Sistem
                                        </button>
                                    </div>

                                    <div class="text-center pt-2 mb-3">
                                        <span class="text-muted small">Belum memiliki akun?</span>
                                        <a href="{{ route('register') }}" class="fw-bold ms-1 text-primary text-decoration-none">
                                            Daftar Akun Baru
                                        </a>
                                    </div>

                                    <!-- Bantuan Demo Akun Kasubag TU -->
                                    <div class="card border rounded-3 bg-light shadow-none mb-3">
                                        <div class="card-body p-2.5 text-start">
                                            <div class="d-flex justify-content-between align-items-center mb-1.5">
                                                <small class="fw-bold text-dark" style="font-size: 0.76rem;">Akun Uji Coba Cepat:</small>
                                                <small class="text-muted" style="font-size: 0.7rem;">Klik untuk isi instan</small>
                                            </div>
                                            <div class="d-flex flex-wrap gap-1.5">
                                                <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2.5 rounded-pill quick-fill d-inline-flex align-items-center gap-1" 
                                                    style="font-size: 0.74rem;" data-email="utami@gmail.com">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                        <circle cx="12" cy="7" r="4"></circle>
                                                    </svg>
                                                    Kasubag TU (Approver)
                                                </button>
                                            </div>
                                        </div>
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
    <script src="{{ asset('hope-ui/assets/js/core/libs.min.js') }}"></script>
    <!-- App Script -->
    <script src="{{ asset('hope-ui/assets/js/hope-ui.js') }}" defer></script>
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

            // Quick Fill Button
            document.querySelectorAll('.quick-fill').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const email = this.getAttribute('data-email');
                    const emailInput = document.querySelector('input[name="email"]');
                    const passInput = document.querySelector('input[name="password"]');
                    if (emailInput && passInput) {
                        emailInput.value = email;
                        passInput.value = 'password123';
                    }
                });
            });

            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    window.location.reload();
                }
            });
        });
    </script>
</body>

</html>
