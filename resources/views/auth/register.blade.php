<!doctype html>
<html lang="id" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registrasi Akun | Sistem Inventori ATK - Kementerian PKP RI</title>
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
            flex-direction: column;
            overflow-y: auto;
        }

        .auth-content {
            width: 100%;
            margin: auto;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem 1rem;
            box-sizing: border-box;
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
            width: 100% !important;
            padding: 0 !important;
            border-radius: 1rem;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.7);
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.15) !important;
            animation: authCardEnter 0.45s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .form-control, .form-select {
            border-radius: 0.55rem;
            padding: 0.52rem 0.8rem;
            font-size: 0.875rem;
            border: 1.5px solid #e2e8f0;
            background-color: #f8fafc;
            color: #0f172a;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .form-control:focus, .form-select:focus {
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

        .cursor-pointer {
            cursor: pointer;
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
                    <div class="col-12 col-sm-10 col-md-9 col-lg-7 col-xl-6" style="max-width: 580px;">
                        <div class="card auth-card mb-0">
                            <div class="card-body p-3.5 p-sm-4">
                                <!-- Logo & Header -->
                                <div class="text-center mb-3">
                                    <img src="{{ asset('hope-ui/assets/images/auth/kemenpkp-logo.png') }}"
                                        class="brand-logo-img mb-2" alt="Logo Kementerian PKP RI" width="48" height="48">
                                    <h4 class="fw-bold text-dark mb-0.5" style="color: #0a213e !important; letter-spacing: -0.02em; font-size: 1.25rem;">
                                        Pendaftaran Akun Baru
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

                                @if (session('info'))
                                    <div class="alert alert-info alert-dismissible fade show shadow-sm py-2 px-3 mb-2.5" role="alert" style="font-size: 0.82rem;">
                                        <div class="d-flex align-items-center">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2 flex-shrink-0" aria-hidden="true">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                            </svg>
                                            <div>{{ session('info') }}</div>
                                        </div>
                                        <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Tutup"></button>
                                    </div>
                                @endif

                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show shadow-sm py-2 px-3 mb-2.5" role="alert" style="font-size: 0.82rem;">
                                        <div class="d-flex align-items-center">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2 flex-shrink-0" aria-hidden="true">
                                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                            </svg>
                                            <div>{{ session('success') }}</div>
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

                                <!-- Form Registrasi -->
                                <form action="{{ route('register.store') }}" method="POST">
                                    @csrf

                                    <div class="row g-2">
                                        <!-- Nama Lengkap -->
                                        <div class="col-md-6 col-12">
                                            <div class="form-group mb-1.5">
                                                <label class="form-label text-uppercase">
                                                    Nama Lengkap <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" name="name" class="form-control"
                                                    value="{{ old('name') }}" placeholder="Budi Prasetyo" required autofocus autocomplete="name">
                                            </div>
                                        </div>

                                        <!-- Email Dinas / Pribadi -->
                                        <div class="col-md-6 col-12">
                                            <div class="form-group mb-1.5">
                                                <label class="form-label text-uppercase">
                                                    Alamat Email <span class="text-danger">*</span>
                                                </label>
                                                <input type="email" name="email" class="form-control"
                                                    value="{{ old('email') }}" placeholder="nama@pkp.go.id" required autocomplete="email">
                                            </div>
                                        </div>

                                        <!-- Nomor Induk Pegawai -->
                                        <div class="col-md-6 col-12">
                                            <div class="form-group mb-1.5">
                                                <label class="form-label text-uppercase">
                                                    Nomor Induk Pegawai (NIP) <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" name="nip" id="nip" class="form-control font-monospace"
                                                    value="{{ old('nip') }}" placeholder="19950510 202012 1 002" required>
                                            </div>
                                        </div>

                                        <!-- Gelar Akademik -->
                                        <div class="col-md-6 col-12">
                                            <div class="form-group mb-1.5">
                                                <label class="form-label text-uppercase">
                                                    Gelar Akademik
                                                </label>
                                                <input type="text" name="title" id="title" class="form-control"
                                                    value="{{ old('title', old('gelar')) }}" placeholder="S.T. / M.Si. / S.Kom.">
                                            </div>
                                        </div>

                                        <!-- Jabatan Khusus Pegawai -->
                                        <div class="col-md-6 col-12" id="pegawai-position-container">
                                            <div class="form-group mb-1.5">
                                                <label class="form-label text-uppercase">
                                                    Jabatan
                                                </label>
                                                <input type="text" name="position" id="position" class="form-control"
                                                    value="{{ old('position') }}" placeholder="Staf Keuangan / Analis Kebijakan">
                                            </div>
                                        </div>

                                        <!-- Peran Akun (Kompak) -->
                                        <div class="col-md-6 col-12">
                                            <div class="form-group mb-1.5">
                                                <label class="form-label text-uppercase">
                                                    Peran Akun <span class="text-danger">*</span>
                                                </label>
                                                <div class="d-flex align-items-center gap-2 px-2.5 py-1.5 border rounded-2 bg-light" style="min-height: 38px;">
                                                    <input type="radio" id="role_pegawai" name="role" value="pegawai" class="form-check-input mt-0" checked required>
                                                    <label for="role_pegawai" class="small fw-semibold text-dark mb-0 cursor-pointer" style="font-size: 0.8rem;">
                                                        Pegawai (Pemohon ATK)
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Password -->
                                        <div class="col-md-6 col-12">
                                            <div class="form-group mb-1.5">
                                                <label class="form-label text-uppercase">
                                                    Password <span class="text-danger">*</span>
                                                </label>
                                                <input type="password" name="password" class="form-control"
                                                    placeholder="Minimal 6 karakter" required autocomplete="new-password">
                                            </div>
                                        </div>

                                        <!-- Konfirmasi Password -->
                                        <div class="col-md-6 col-12">
                                            <div class="form-group mb-1.5">
                                                <label class="form-label text-uppercase">
                                                    Ulangi Password <span class="text-danger">*</span>
                                                </label>
                                                <input type="password" name="password_confirmation" class="form-control"
                                                    placeholder="Ketik ulang password" required autocomplete="new-password">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-grid mt-3 mb-2">
                                        <button type="submit" class="btn btn-primary btn-block">
                                            Daftarkan Akun
                                        </button>
                                    </div>

                                    <div class="text-center pt-1">
                                        <span class="text-muted small" style="font-size: 0.8rem;">Sudah memiliki akun terdaftar?</span>
                                        <a href="{{ route('login') }}" class="fw-bold ms-1" style="color: #0284c7; text-decoration: none; font-size: 0.8rem;">
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
            const form = document.querySelector('form');
            const submitBtn = form ? form.querySelector('button[type="submit"]') : null;

            // Cegah isu BFCache:
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    window.location.reload();
                }
            });

            // Cegah double submission
            if (form && submitBtn) {
                form.addEventListener('submit', function() {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Mendaftarkan Akun...';
                });
            }
        });
    </script>
</body>
</html>
