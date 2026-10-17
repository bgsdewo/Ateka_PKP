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
        }

        .auth-content {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1rem;
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
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.7);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.15) !important;
            animation: authCardEnter 0.55s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .form-control {
            border-radius: 0.65rem;
            padding: 0.75rem 1rem;
            font-size: 0.925rem;
            border: 1.5px solid #e2e8f0;
            background-color: #f8fafc;
            color: #0f172a;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .form-control:focus {
            background-color: #ffffff;
            border-color: #0284c7;
            box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.15);
            outline: none;
        }

        .form-label {
            font-weight: 700;
            font-size: 0.78rem;
            letter-spacing: 0.04em;
            color: #0a213e;
            margin-bottom: 0.4rem;
        }

        .btn-primary {
            border-radius: 0.65rem !important;
            padding: 0.85rem 1.25rem !important;
            font-weight: 700 !important;
            font-size: 0.95rem;
            letter-spacing: 0.02em;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
            border: none !important;
            color: #ffffff !important;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.32);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .btn-primary:hover,
        .btn-primary:focus-visible {
            background: linear-gradient(135deg, #0369a1 0%, #075985 100%) !important;
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(2, 132, 199, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .brand-logo-img {
            max-height: 84px;
            width: auto;
            object-fit: contain;
        }

        .badge-dept {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.75rem;
            background: rgba(2, 132, 199, 0.08);
            color: #0284c7;
            border: 1px solid rgba(2, 132, 199, 0.2);
            border-radius: 9999px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .info-pill {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 0.65rem;
            padding: 0.75rem 1rem;
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
    <div class="wrapper">
        <section class="auth-content">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-md-10">
                        <div class="card auth-card mb-0">
                            <div class="card-body p-4 p-md-5">
                                <!-- Logo & Header -->
                                <div class="text-center mb-4">
                                    <div class="mb-3">
                                        <span class="badge-dept">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                            </svg>
                                            Kementerian Perumahan & Kawasan Permukiman RI
                                        </span>
                                    </div>
                                    <img src="{{ asset('hope-ui/assets/images/auth/kemenkp.jpeg') }}"
                                        class="brand-logo-img mb-3" alt="Logo Kementerian PKP RI">
                                    <h3 class="fw-bold text-dark mb-1" style="color: #0a213e !important; letter-spacing: -0.02em;">Reset Kata Sandi</h3>
                                    <p class="text-muted small mb-0">Sistem Pencatatan Persediaan & Pengajuan Alat Tulis Kantor (ATK)</p>
                                </div>

                                <!-- Alert Notifikasi Sesi & Kesalahan -->
                                @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                                        <div class="d-flex align-items-center">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2 flex-shrink-0" aria-hidden="true">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                            </svg>
                                            <div>{{ session('error') }}</div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                                    </div>
                                @endif

                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                                        <div class="d-flex align-items-center mb-1">
                                            <strong class="me-2">Mohon perbaiki data berikut:</strong>
                                        </div>
                                        <ul class="mb-0 ps-3 small">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                                    </div>
                                @endif

                                <!-- Form Reset Password -->
                                <form action="{{ route('password.reset.nip') }}" method="POST">
                                    @csrf

                                    <div class="row g-3">
                                        <!-- Nomor Induk Pegawai (NIP) -->
                                        <div class="col-md-6 col-12">
                                            <div class="form-group mb-2">
                                                <label class="form-label text-uppercase">
                                                    Nomor Induk Pegawai (NIP) <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" name="nip" id="nip" class="form-control font-monospace"
                                                    value="{{ old('nip') }}" placeholder="Contoh: 19950510 202012 1 002" required autofocus>
                                                <small class="text-muted d-block mt-1" style="font-size: 0.78rem;">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1 text-primary" aria-hidden="true">
                                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                                    </svg>
                                                    Identitas dinas & verifikasi <strong>akun resmi</strong>.
                                                </small>
                                            </div>
                                        </div>

                                        <!-- Verifikasi Keamanan Informasi -->
                                        <div class="col-md-6 col-12">
                                            <div class="form-group mb-2">
                                                <label class="form-label text-uppercase">
                                                    Verifikasi Keamanan <span class="text-muted fw-normal">(Otomatis)</span>
                                                </label>
                                                <div class="info-pill d-flex align-items-center gap-2">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" class="flex-shrink-0 text-success" aria-hidden="true">
                                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                                    </svg>
                                                    <small class="text-success-emphasis mb-0" style="font-size: 0.78rem; line-height: 1.35; color: #166534;">
                                                        Sistem memvalidasi <strong>NIP</strong> terdaftar untuk memulihkan akses kata sandi secara instan.
                                                    </small>
                                                </div>
                                                <small class="text-muted d-block mt-1" style="font-size: 0.78rem;">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1 text-success" aria-hidden="true">
                                                        <polyline points="20 6 9 17 4 12"></polyline>
                                                    </svg>
                                                    Verifikasi langsung tanpa kode email.
                                                </small>
                                            </div>
                                        </div>

                                        <!-- Password Baru -->
                                        <div class="col-md-6 col-12">
                                            <div class="form-group mb-2">
                                                <label class="form-label text-uppercase">
                                                    Password Baru <span class="text-danger">*</span>
                                                </label>
                                                <div class="position-relative">
                                                    <input type="password" name="password" id="password" class="form-control pe-5"
                                                        placeholder="Minimal 6 karakter" required minlength="6" autocomplete="new-password">
                                                    <button type="button" class="btn btn-sm btn-link position-absolute end-0 top-50 translate-middle-y text-muted text-decoration-none pe-3 toggle-password"
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
                                                <small class="text-muted d-block mt-1" style="font-size: 0.78rem;">Minimal 6 karakter kombinasi.</small>
                                            </div>
                                        </div>

                                        <!-- Konfirmasi Password Baru -->
                                        <div class="col-md-6 col-12">
                                            <div class="form-group mb-2">
                                                <label class="form-label text-uppercase">
                                                    Ulangi Password Baru <span class="text-danger">*</span>
                                                </label>
                                                <div class="position-relative">
                                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control pe-5"
                                                        placeholder="Ketik ulang password baru" required minlength="6" autocomplete="new-password">
                                                    <button type="button" class="btn btn-sm btn-link position-absolute end-0 top-50 translate-middle-y text-muted text-decoration-none pe-3 toggle-password"
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
                                                <small class="text-muted d-block mt-1" style="font-size: 0.78rem;">Pastikan sama dengan password baru.</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-grid mt-4 mb-3">
                                        <button type="submit" class="btn btn-primary btn-block">
                                            Simpan & Perbarui Password
                                        </button>
                                    </div>

                                    <div class="text-center pt-2">
                                        <span class="text-muted small">Ingat kata sandi Anda?</span>
                                        <a href="{{ route('login') }}" class="fw-bold ms-1" style="color: #0284c7; text-decoration: none;">
                                            Masuk di Sini
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
            // Toggle Password Visibility
            document.querySelectorAll('.toggle-password').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetSelector = this.getAttribute('data-target');
                    const input = document.querySelector(targetSelector);
                    const openIcon = this.querySelector('.eye-open');
                    const closedIcon = this.querySelector('.eye-closed');

                    if (input) {
                        if (input.type === 'password') {
                            input.type = 'text';
                            if (openIcon && closedIcon) {
                                openIcon.classList.add('d-none');
                                closedIcon.classList.remove('d-none');
                            }
                        } else {
                            input.type = 'password';
                            if (openIcon && closedIcon) {
                                openIcon.classList.remove('d-none');
                                closedIcon.classList.add('d-none');
                            }
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>
