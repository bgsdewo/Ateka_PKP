<!doctype html>
<html lang="id" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Reset Kata Sandi | Sistem Inventori ATK - Kementerian PKP RI</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('hope-ui/assets/images/favicon.ico') }}" />
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Library / Plugin Css Build -->
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/core/libs.min.css') }}" />
    <!-- Hope Ui Design System Css -->
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/hope-ui.min.css?v=2.0.0') }}" />
    <!-- Custom Css -->
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/custom.min.css?v=2.0.0') }}" />
    <!-- Modern Ateka Enhancement CSS -->
    <link rel="stylesheet" href="{{ asset('css/ateka-modern.css') }}" />

    <style>
        body {
            background-color: #061224;
            background-image: 
                radial-gradient(circle at 15% 15%, rgba(2, 132, 199, 0.18) 0%, transparent 45%),
                radial-gradient(circle at 85% 85%, rgba(5, 150, 105, 0.12) 0%, transparent 45%),
                radial-gradient(circle at 50% 50%, rgba(15, 23, 42, 0.8) 0%, #061224 100%);
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-content {
            width: 100%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.25rem 1rem;
        }

        @keyframes authCardEnter {
            from {
                opacity: 0;
                transform: translateY(16px) scale(0.985);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .auth-card {
            border-radius: 1rem;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.7);
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.15) !important;
            animation: authCardEnter 0.45s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .form-control {
            border-radius: 0.55rem;
            padding: 0.52rem 0.8rem;
            font-size: 0.875rem;
            border: 1.5px solid #e2e8f0;
            background-color: #f8fafc;
            color: #0f172a;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .form-control:focus {
            background-color: #ffffff;
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
            outline: none;
        }

        .form-label {
            font-weight: 700;
            font-size: 0.72rem;
            letter-spacing: 0.04em;
            color: #0a213e;
            margin-bottom: 0.25rem;
        }

        .btn-primary {
            border-radius: 0.55rem !important;
            padding: 0.58rem 1rem !important;
            font-weight: 700 !important;
            font-size: 0.9rem;
            letter-spacing: 0.01em;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
            border: none !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .btn-primary:hover,
        .btn-primary:focus-visible {
            background: linear-gradient(135deg, #0369a1 0%, #075985 100%) !important;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(2, 132, 199, 0.38);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .brand-logo-img {
            max-height: 48px;
            width: auto;
            object-fit: contain;
        }

        .info-pill {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 0.55rem;
            padding: 0.6rem 0.85rem;
        }

        @media (prefers-reduced-motion: reduce) {
            .btn-primary, .form-control {
                transition: none !important;
                transform: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper w-100">
        <section class="auth-content">
            <div class="container px-3">
                <div class="row justify-content-center">
                    <div class="col-12 col-sm-10 col-md-7 col-lg-5 col-xl-4" style="max-width: 440px;">
                        <div class="card auth-card mb-0">
                            <div class="card-body p-3.5 p-sm-4">
                                <!-- Logo & Header -->
                                <div class="text-center mb-3">
                                    <img src="{{ asset('hope-ui/assets/images/auth/kemenpkp-logo.png') }}"
                                        class="brand-logo-img mb-2" alt="Logo Kementerian PKP RI" width="48" height="48">
                                    <h4 class="fw-bold text-dark mb-0.5" style="color: #0a213e !important; letter-spacing: -0.02em; font-size: 1.25rem;">
                                        Reset Kata Sandi
                                    </h4>
                                    <p class="text-muted small mb-0" style="font-size: 0.78rem;">
                                        Sistem Pencatatan Persediaan &amp; Pengajuan ATK
                                    </p>
                                </div>

                                <!-- Alert Notifikasi Sesi & Kesalahan -->
                                @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show shadow-sm py-2 px-3 mb-2.5" role="alert" style="font-size: 0.82rem;">
                                        <div class="d-flex align-items-center">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2 flex-shrink-0" aria-hidden="true">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                            </svg>
                                            <div>{{ session('error') }}</div>
                                        </div>
                                        <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Tutup"></button>
                                    </div>
                                @endif

                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show shadow-sm py-2 px-3 mb-2.5" role="alert" style="font-size: 0.82rem;">
                                        <div class="d-flex align-items-center mb-1">
                                            <strong class="me-2">Mohon perbaiki data berikut:</strong>
                                        </div>
                                        <ul class="mb-0 ps-3 small">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Tutup"></button>
                                    </div>
                                @endif

                                <!-- Form Reset Password -->
                                <form action="{{ route('password.reset.nip') }}" method="POST">
                                    @csrf

                                    <div class="row g-2">
                                        <!-- Nomor Induk Pegawai -->
                                        <div class="col-12">
                                            <div class="form-group mb-2">
                                                <label class="form-label text-uppercase">
                                                    Nomor Induk Pegawai (NIP) <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" name="nip" id="nip" class="form-control font-monospace"
                                                    value="{{ old('nip') }}" placeholder="19950510 202012 1 002" required autofocus>
                                            </div>
                                        </div>

                                        <!-- Password Baru -->
                                        <div class="col-12">
                                            <div class="form-group mb-2">
                                                <label class="form-label text-uppercase">
                                                    Password Baru <span class="text-danger">*</span>
                                                </label>
                                                <div class="position-relative">
                                                    <input type="password" name="password" id="password" class="form-control pe-5"
                                                        placeholder="Minimal 6 karakter" required minlength="6" autocomplete="new-password">
                                                    <button type="button" class="btn btn-sm btn-link position-absolute end-0 top-50 translate-middle-y text-muted text-decoration-none pe-2 toggle-password"
                                                        data-target="#password" title="Lihat/Sembunyikan password" aria-label="Lihat atau sembunyikan password" style="z-index: 5;">
                                                        <svg class="eye-open" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                            <circle cx="12" cy="12" r="3"></circle>
                                                        </svg>
                                                        <svg class="eye-closed d-none" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                                            <line x1="1" y1="1" x2="23" y2="23"></line>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Konfirmasi Password Baru -->
                                        <div class="col-12">
                                            <div class="form-group mb-2">
                                                <label class="form-label text-uppercase">
                                                    Ulangi Password Baru <span class="text-danger">*</span>
                                                </label>
                                                <div class="position-relative">
                                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control pe-5"
                                                        placeholder="Ketik ulang password baru" required minlength="6" autocomplete="new-password">
                                                    <button type="button" class="btn btn-sm btn-link position-absolute end-0 top-50 translate-middle-y text-muted text-decoration-none pe-2 toggle-password"
                                                        data-target="#password_confirmation" title="Lihat/Sembunyikan password" aria-label="Lihat atau sembunyikan password" style="z-index: 5;">
                                                        <svg class="eye-open" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                            <circle cx="12" cy="12" r="3"></circle>
                                                        </svg>
                                                        <svg class="eye-closed d-none" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                                            <line x1="1" y1="1" x2="23" y2="23"></line>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bantuan NIP Info -->
                                    <div class="info-pill my-2">
                                        <div class="d-flex align-items-start gap-2">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" class="flex-shrink-0 mt-0.5" aria-hidden="true">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                            </svg>
                                            <p class="text-success small mb-0" style="font-size: 0.75rem; line-height: 1.4;">
                                                Verifikasi NIP otomatis mereset kata sandi akun Anda secara langsung tanpa perlu tautan email.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="d-grid mt-3 mb-2">
                                        <button type="submit" class="btn btn-primary btn-block">
                                            Perbarui Kata Sandi
                                        </button>
                                    </div>

                                    <div class="text-center pt-1">
                                        <a href="{{ route('login') }}" class="small fw-bold text-decoration-none" style="color: #0284c7; font-size: 0.8rem;">
                                            &larr; Kembali ke Halaman Login
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Script Library -->
    <script src="{{ asset('hope-ui/assets/js/core/libs.min.js') }}"></script>
    <!-- Global Alert Dismiss & Auto-Dismiss System -->
    <script src="{{ asset('js/ateka-alerts.js') }}?v=1.0" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password toggle functionality
            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetSelector = this.getAttribute('data-target');
                    const input = document.querySelector(targetSelector);
                    const eyeOpen = this.querySelector('.eye-open');
                    const eyeClosed = this.querySelector('.eye-closed');

                    if (input.type === 'password') {
                        input.type = 'text';
                        eyeOpen.classList.add('d-none');
                        eyeClosed.classList.remove('d-none');
                    } else {
                        input.type = 'password';
                        eyeOpen.classList.remove('d-none');
                        eyeClosed.classList.add('d-none');
                    }
                });
            });

            // Prevent double form submission
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function() {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...';
                    }
                });
            }
        });
    </script>
</body>
</html>
