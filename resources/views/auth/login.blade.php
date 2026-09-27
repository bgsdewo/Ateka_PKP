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

    <!-- Tambahan CSS Custom: Penyesuaian Warna Tema Krem & Tombol Khas Instansi -->
    <style>
        body {
            /* Warna background krem hangat */
            background-color: #f7f4ec;
            background-image: linear-gradient(135deg, #f9f7f1 0%, #f0ece1 100%);
            font-family: 'Inter', sans-serif;
        }

        .login-content {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }

        .auth-card {
            border-radius: 1.25rem;
            box-shadow: 0 15px 35px rgba(20, 75, 89, 0.08) !important;
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.04);
            padding: 1.5rem;
        }

        .form-control {
            border-radius: 0.6rem;
            padding: 0.8rem 1rem;
            font-size: 0.95rem;
            border: 1px solid #d1d5db;
            background-color: #fcfcfc;
            color: #144b59;
        }

        .form-control:focus {
            background-color: #ffffff;
            border-color: #144b59;
            box-shadow: 0 0 0 4px rgba(20, 75, 89, 0.15);
        }

        /* Memaksa tombol menggunakan warna tema dengan !important */
        .btn-primary {
            border-radius: 0.6rem !important;
            padding: 0.8rem 1rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.5px;
            background-color: #144b59 !important;
            /* Biru Gelap/Teal */
            border-color: #144b59 !important;
            color: #ffffff !important;
            transition: all 0.3s ease-in-out;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: #fab300 !important;
            /* Kuning Emas */
            border-color: #fab300 !important;
            color: #144b59 !important;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(250, 179, 0, 0.3);
        }

        /* Logo diperbesar dan proporsional */
        .brand-logo-img {
            max-height: 150px;
            width: auto;
            object-fit: contain;
        }

        /* Mewarnai teks agar selaras dengan tema */
        .text-theme {
            color: #144b59 !important;
        }
    </style>
</head>

<body class="data-bs-spy='scroll'" data-bs-target="#elements-section" data-bs-offset="0" tabindex="0">
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
                    <div class="col-md-6 col-lg-5">
                        <div class="card auth-card mb-0">
                            <div class="card-body p-4">
                                <!-- Logo Kemenkp -->
                                <div class="text-center mb-4">
                                    <img src="{{ asset('hope-ui/assets/images/auth/kemenkp.jpeg') }}"
                                        class="brand-logo-img mb-3" alt="Logo Kemenkp">
                                    <h4 class="font-weight-bold text-theme mb-0" style="font-size: 1.3rem;">Inventori
                                        ATK</h4>
                                </div>

                                <h3 class="mb-2 text-center text-theme font-weight-bold">Login Admin</h3>
                                <p class="text-center text-muted small mb-4">Masuk untuk mengelola inventori dan
                                    pencatatan stok Alat Tulis Kantor.</p>

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
                                        <ul class="mb-0 mt-2">
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
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group mb-3">
                                                <label for="email"
                                                    class="form-label font-weight-bold text-theme">Email</label>
                                                <input type="email" name="email" class="form-control"
                                                    value="{{ old('email') }}" placeholder="Masukkan Email" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-group mb-4">
                                                <label for="password"
                                                    class="form-label font-weight-bold text-theme">Password</label>
                                                <input type="password" name="password" class="form-control"
                                                    placeholder="Masukkan Password" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-grid mb-3">
                                        <button type="submit" class="btn btn-primary btn-block shadow-sm">Masuk ke
                                            Sistem</button>
                                    </div>

                                    <div class="text-center pt-2 mb-3">
                                        <span class="text-muted small">Belum memiliki akun?</span>
                                        <a href="{{ route('register') }}" class="fw-bold ms-1" style="color: #144b59;">
                                            Daftar Akun Baru
                                        </a>
                                    </div>

                                    <!-- Bantuan Demo 4 Role -->
                                    <div class="card border bg-light shadow-none mb-3">
                                        <div class="card-body p-2 text-start">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small class="fw-bold text-theme" style="font-size: 0.75rem;">Akun Percobaan (4 Role):</small>
                                                <small class="text-muted" style="font-size: 0.7rem;">Klik untuk isi</small>
                                            </div>
                                            <div class="d-flex flex-wrap gap-1">
                                                <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2 quick-fill" 
                                                    style="font-size: 0.72rem;" data-email="utami@gmail.com">
                                                    Kasubag TU
                                                </button>
                                                <button type="button" class="btn btn-xs btn-outline-info py-0 px-2 quick-fill" 
                                                    style="font-size: 0.72rem;" data-email="devi@gmail.com">
                                                    Arsiparis
                                                </button>
                                                <button type="button" class="btn btn-xs btn-outline-warning text-dark py-0 px-2 quick-fill" 
                                                    style="font-size: 0.72rem;" data-email="fairuz@gmail.com">
                                                    PIC Distribusi
                                                </button>
                                                <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 quick-fill" 
                                                    style="font-size: 0.72rem;" data-email="sdk@gmail.com">
                                                    Pegawai/Subdit
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <p class="text-center text-muted small mb-0">Sistem Pencatatan Keluar Masuk
                                        Barang &copy; 2026</p>
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
    <!-- External Library Bundle Script -->
    <script src="{{ asset('hope-ui/assets/js/core/external.min.js') }}"></script>
    <!-- Widgetchart Script -->
    <script src="{{ asset('hope-ui/assets/js/charts/widgetcharts.js') }}"></script>
    <!-- mapchart Script -->
    <script src="{{ asset('hope-ui/assets/js/charts/vectore-chart.js') }}"></script>
    <script src="{{ asset('hope-ui/assets/js/charts/dashboard.js') }}"></script>
    <!-- fslightbox Script -->
    <script src="{{ asset('hope-ui/assets/js/plugins/fslightbox.js') }}"></script>
    <!-- Settings Script -->
    <script src="{{ asset('hope-ui/assets/js/plugins/setting.js') }}"></script>
    <!-- Slider-tab Script -->
    <script src="{{ asset('hope-ui/assets/js/plugins/slider-tabs.js') }}"></script>
    <!-- Form Wizard Script -->
    <script src="{{ asset('hope-ui/assets/js/plugins/form-wizard.js') }}"></script>
    <!-- App Script -->
    <script src="{{ asset('hope-ui/assets/js/hope-ui.js') }}" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>
</body>

</html>
