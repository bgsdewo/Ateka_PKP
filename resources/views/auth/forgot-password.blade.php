<!doctype html>
<html lang="id" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Lupa Password | Pencatatan Stok Barang</title>
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
            box-shadow: 0 15px 35px rgba(20, 75, 89, 0.08) !important;
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.04);
        }

        .form-control {
            border-radius: 0.6rem;
            padding: 0.75rem 1rem;
            font-size: 0.92rem;
            border: 1px solid #d1d5db;
            background-color: #fcfcfc;
            color: #144b59;
        }

        .form-control:focus {
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
            max-height: 130px;
            width: auto;
            object-fit: contain;
        }

        .text-theme {
            color: #144b59 !important;
        }

        .badge-nip-info {
            background-color: #e6f4f6;
            color: #144b59;
            border-radius: 0.5rem;
            padding: 0.6rem 0.8rem;
            font-size: 0.82rem;
            border: 1px dashed #7fc1ca;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <section class="auth-content">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-7 col-lg-5">
                        <div class="card auth-card mb-0">
                            <div class="card-body p-4 p-md-5">
                                <!-- Logo & Header -->
                                <div class="text-center mb-4">
                                    <img src="{{ asset('hope-ui/assets/images/auth/kemenkp.jpeg') }}"
                                        class="brand-logo-img mb-3" alt="Logo KemenKP">
                                    <h4 class="font-weight-bold text-theme mb-1">Reset Kata Sandi</h4>
                                    <p class="text-muted small mb-0">Verifikasi NIP & Email terdaftar untuk mengatur ulang password Anda</p>
                                </div>

                                <!-- Alert Pemberitahuan / Error -->
                                @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                                        <div class="d-flex align-items-center">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2 flex-shrink-0">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                            </svg>
                                            <div class="small">{{ session('error') }}</div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                                        <div class="d-flex align-items-center mb-1">
                                            <strong class="me-2 small">Periksa kembali data Anda:</strong>
                                        </div>
                                        <ul class="mb-0 ps-3 small">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                <!-- Info Card NIP Security -->
                                <div class="badge-nip-info mb-4">
                                    <div class="d-flex align-items-start">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2 text-primary flex-shrink-0 mt-0.5">
                                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                        </svg>
                                        <div>
                                            <strong>Verifikasi Keamanan Pegawai/Subdit:</strong><br>
                                            Sistem akan mencocokkan Email dan NIP yang Anda daftarkan sebelumnya sebagai verifikasi kepemilikan akun resmi.
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Reset Password -->
                                <form action="{{ route('password.reset.nip') }}" method="POST">
                                    @csrf

                                    <div class="form-group mb-3">
                                        <label class="form-label font-weight-bold text-theme small text-uppercase">
                                            Alamat Email Terdaftar <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" name="email" class="form-control"
                                            value="{{ old('email') }}" placeholder="nama@perumahan.go.id" required autofocus>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label font-weight-bold text-theme small text-uppercase">
                                            Nomor Induk Pegawai (NIP) <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="nip" class="form-control"
                                            value="{{ old('nip') }}" placeholder="Contoh: 19950510 202012 1 002" required>
                                        <small class="text-muted" style="font-size: 0.78rem;">Masukkan NIP yang tertera di profil akun Anda.</small>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label font-weight-bold text-theme small text-uppercase">
                                            Password Baru <span class="text-danger">*</span>
                                        </label>
                                        <input type="password" name="password" class="form-control"
                                            placeholder="Minimal 6 karakter" required>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="form-label font-weight-bold text-theme small text-uppercase">
                                            Konfirmasi Password Baru <span class="text-danger">*</span>
                                        </label>
                                        <input type="password" name="password_confirmation" class="form-control"
                                            placeholder="Ketik ulang password baru" required>
                                    </div>

                                    <div class="d-grid mb-3">
                                        <button type="submit" class="btn btn-primary btn-block shadow-sm">
                                            Simpan & Perbarui Password
                                        </button>
                                    </div>

                                    <div class="text-center pt-2">
                                        <a href="{{ route('login') }}" class="fw-semibold text-decoration-none small text-theme d-inline-flex align-items-center">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                                                <line x1="19" y1="12" x2="5" y2="12"></line>
                                                <polyline points="12 19 5 12 12 5"></polyline>
                                            </svg>
                                            Kembali ke Halaman Login
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

    <!-- Library Bundle Script -->
    <script src="{{ asset('hope-ui/assets/js/core/libs.min.js') }}"></script>
    <script src="{{ asset('hope-ui/assets/js/hope-ui.js') }}" defer></script>
</body>

</html>
