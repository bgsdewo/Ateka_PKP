<!doctype html>
<html lang="id" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Reset Kata Sandi | Sistem Inventori ATK</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('hope-ui/assets/images/favicon.ico') }}" />
    <!-- Library / Plugin Css Build -->
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/core/libs.min.css') }}" />
    <!-- Hope Ui Design System Css -->
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/hope-ui.min.css?v=2.0.0') }}" />
    <!-- Custom Css -->
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/custom.min.css?v=2.0.0') }}" />

    <style>
        body {
            background-color: #f7f4ec;
            background-image: linear-gradient(135deg, #f9f7f1 0%, #f0ece1 100%);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }

        .auth-content {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 0;
        }

        .auth-card {
            border-radius: 1.25rem;
            box-shadow: 0 20px 40px rgba(20, 75, 89, 0.08) !important;
            background: #ffffff;
            border: 1px solid rgba(20, 75, 89, 0.08);
        }

        .form-control, .form-select {
            border-radius: 0.6rem;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            border: 1px solid #d1d5db;
            background-color: #fcfcfc;
            color: #144b59;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            background-color: #ffffff;
            border-color: #144b59;
            box-shadow: 0 0 0 4px rgba(20, 75, 89, 0.15);
        }

        .btn-primary {
            border-radius: 0.6rem !important;
            padding: 0.8rem 1rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.5px;
            background-color: #144b59 !important;
            border-color: #144b59 !important;
            color: #ffffff !important;
            transition: all 0.3s ease-in-out;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: #fab300 !important;
            border-color: #fab300 !important;
            color: #144b59 !important;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(250, 179, 0, 0.3);
        }

        .brand-logo-img {
            max-height: 100px;
            width: auto;
            object-fit: contain;
        }

        .text-theme {
            color: #144b59 !important;
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
                                    <img src="{{ asset('hope-ui/assets/images/auth/kemenkp.jpeg') }}"
                                        class="brand-logo-img mb-3" alt="Logo KemenKP">
                                    <h4 class="font-weight-bold text-theme mb-1">Reset Kata Sandi</h4>
                                    <p class="text-muted small mb-0">Sistem Pencatatan Persediaan & Pengajuan Alat Tulis Kantor (ATK)</p>
                                </div>

                                <!-- Alert Notifikasi Sesi & Kesalahan -->
                                @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                                        <div class="d-flex align-items-center">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2 flex-shrink-0">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                            </svg>
                                            <div>{{ session('error') }}</div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                <!-- Form Reset Password -->
                                <form action="{{ route('password.reset.nip') }}" method="POST">
                                    @csrf

                                    <div class="row g-3">
                                        <!-- Nomor Induk Pegawai (NIP) -->
                                        <div class="col-md-6 col-12">
                                            <div class="form-group mb-2">
                                                <label class="form-label font-weight-bold text-theme small text-uppercase">
                                                    Nomor Induk Pegawai (NIP) <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" name="nip" id="nip" class="form-control"
                                                    value="{{ old('nip') }}" placeholder="Contoh: 19950510 202012 1 002" required autofocus>
                                                <small class="text-muted d-block mt-1" style="font-size: 0.78rem;">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1 text-primary">
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
                                                <label class="form-label font-weight-bold text-theme small text-uppercase">
                                                    Verifikasi Keamanan <span class="text-muted font-normal">(Otomatis)</span>
                                                </label>
                                                <div class="p-2.5 rounded-3 border bg-light d-flex align-items-center gap-2" style="border-radius: 0.6rem !important; min-height: 47px; background-color: #f8fafc !important; border-color: #d1d5db !important;">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#144b59" stroke-width="2" class="flex-shrink-0 text-primary">
                                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                                    </svg>
                                                    <small class="text-muted mb-0" style="font-size: 0.78rem; line-height: 1.35;">
                                                        Sistem memvalidasi <strong>NIP</strong> terdaftar untuk memulihkan akses kata sandi secara instan.
                                                    </small>
                                                </div>
                                                <small class="text-muted d-block mt-1" style="font-size: 0.78rem;">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1 text-success">
                                                        <polyline points="20 6 9 17 4 12"></polyline>
                                                    </svg>
                                                    Verifikasi langsung tanpa kode email.
                                                </small>
                                            </div>
                                        </div>

                                        <!-- Password Baru -->
                                        <div class="col-md-6 col-12">
                                            <div class="form-group mb-2">
                                                <label class="form-label font-weight-bold text-theme small text-uppercase">
                                                    Password Baru <span class="text-danger">*</span>
                                                </label>
                                                <div class="position-relative">
                                                    <input type="password" name="password" id="password" class="form-control pe-5"
                                                        placeholder="Minimal 6 karakter" required minlength="6">
                                                    <button type="button" class="btn btn-sm btn-link position-absolute end-0 top-50 translate-middle-y text-muted text-decoration-none pe-3 toggle-password"
                                                        data-target="#password" title="Lihat/Sembunyikan password" style="z-index: 5;">
                                                        <svg class="eye-open" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                            <circle cx="12" cy="12" r="3"></circle>
                                                        </svg>
                                                        <svg class="eye-closed d-none" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
                                                <label class="form-label font-weight-bold text-theme small text-uppercase">
                                                    Ulangi Password Baru <span class="text-danger">*</span>
                                                </label>
                                                <div class="position-relative">
                                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control pe-5"
                                                        placeholder="Ketik ulang password baru" required minlength="6">
                                                    <button type="button" class="btn btn-sm btn-link position-absolute end-0 top-50 translate-middle-y text-muted text-decoration-none pe-3 toggle-password"
                                                        data-target="#password_confirmation" title="Lihat/Sembunyikan password" style="z-index: 5;">
                                                        <svg class="eye-open" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                            <circle cx="12" cy="12" r="3"></circle>
                                                        </svg>
                                                        <svg class="eye-closed d-none" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
                                        <button type="submit" class="btn btn-primary btn-block shadow-sm">
                                            Simpan & Perbarui Password
                                        </button>
                                    </div>

                                    <div class="text-center pt-2">
                                        <span class="text-muted small">Ingat kata sandi Anda?</span>
                                        <a href="{{ route('login') }}" class="fw-bold ms-1" style="color: #144b59;">
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
