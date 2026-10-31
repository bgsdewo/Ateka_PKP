<!doctype html>
<html lang="id" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Nonaktifkan Pengindeksan Mesin Pencari (Aplikasi Internal Kementerian PKP RI) -->
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <meta name="description" content="Registrasi Akun Sistem Informasi Inventori Persediaan ATK (ATEKA) Kementerian PKP RI">
    <title>Registrasi Akun | Inventori ATK Kementerian PKP RI</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('hope-ui/assets/images/favicon.ico') }}" />

    <!-- Google Fonts Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap">

    <!-- Core Styles -->
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/core/libs.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/hope-ui.min.css?v=2.0.0') }}" />

    <style>
        :root {
            --bg-deep-green: #142e23;
            --card-dark-green: #18382a;
            --input-dark-green: #0f241a;
            --teal-btn: #478c80;
            --teal-btn-hover: #539d91;
            --accent-teal: #5eead4;
            --text-muted-green: #83a998;
        }

        html, body {
            min-height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-deep-green);
            background-image: 
                radial-gradient(circle at 10% 15%, rgba(255, 255, 255, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 85% 20%, rgba(255, 255, 255, 0.06) 0%, transparent 35%),
                radial-gradient(circle at 50% 85%, rgba(71, 140, 128, 0.12) 0%, transparent 50%),
                radial-gradient(circle at 90% 85%, rgba(255, 255, 255, 0.04) 0%, transparent 30%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            padding: 24px 16px;
            box-sizing: border-box;
        }

        /* Ambient Bokeh / Floating Soft Circles (Persis sesuai tema Login) */
        .bokeh-orb {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        .bokeh-1 {
            width: 280px;
            height: 280px;
            top: 4%;
            left: 3%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.02) 60%, transparent 80%);
        }

        .bokeh-2 {
            width: 220px;
            height: 220px;
            bottom: 6%;
            right: 4%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.06) 0%, transparent 75%);
        }

        .bokeh-3 {
            width: 90px;
            height: 90px;
            top: 18%;
            right: 12%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.07) 0%, transparent 70%);
        }

        .bokeh-4 {
            width: 140px;
            height: 140px;
            bottom: 12%;
            left: 10%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.05) 0%, transparent 70%);
        }

        /* Master Card Container */
        .register-master-card {
            width: 100%;
            max-width: 1140px;
            margin: auto;
            background-color: var(--card-dark-green);
            border-radius: 36px;
            box-shadow: 0 35px 80px -15px rgba(0, 0, 0, 0.65), 0 0 0 1px rgba(255, 255, 255, 0.08);
            overflow: hidden;
            position: relative;
            z-index: 10;
            display: flex;
            min-height: 640px;
        }

        /* Sisi Kiri: Panel Putih dengan Lengkungan Halus (Wave Curve) */
        .panel-brand-left {
            background-color: #ffffff;
            color: #0f172a;
            flex: 0.95;
            padding: 2.5rem 2.8rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            z-index: 2;
        }

        /* Lengkungan Organik (Wavy Curve) ke Sisi Kanan */
        .wave-curve-border {
            position: absolute;
            top: 0;
            right: -75px;
            width: 78px;
            height: 100%;
            pointer-events: none;
            z-index: 5;
        }

        /* Sisi Kanan: Panel Form Registrasi Hijau Tua */
        .panel-form-right {
            background-color: var(--card-dark-green);
            color: #ffffff;
            flex: 1.35;
            padding: 2.8rem 3.2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            z-index: 4;
        }

        /* Form Controls Sesuai Tema */
        .form-label-custom {
            font-size: 0.84rem;
            font-weight: 600;
            color: #d1e7dd;
            margin-bottom: 5px;
            display: block;
            letter-spacing: 0.2px;
        }

        .input-pill-dark {
            background-color: var(--input-dark-green) !important;
            border: 1.5px solid rgba(255, 255, 255, 0.12) !important;
            border-radius: 20px !important;
            color: #ffffff !important;
            padding: 0.65rem 1.15rem !important;
            font-size: 0.88rem !important;
            transition: all 0.2s ease !important;
            width: 100% !important;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.25) !important;
        }

        .input-pill-dark::placeholder {
            color: #5c7e6f !important;
            font-size: 0.82rem !important;
        }

        .input-pill-dark:focus {
            background-color: #0b1c14 !important;
            border-color: #4db6ac !important;
            box-shadow: 0 0 0 3px rgba(77, 182, 172, 0.25), inset 0 2px 4px rgba(0, 0, 0, 0.3) !important;
            outline: none !important;
        }

        .password-input-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .password-toggle-btn {
            position: absolute;
            right: 14px;
            background: transparent;
            border: none;
            color: #719987;
            padding: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s ease;
        }

        .password-toggle-btn:hover {
            color: #ffffff;
        }

        /* Tombol Pill Sesuai Tema */
        .btn-pill-teal {
            background: linear-gradient(135deg, #4fa395 0%, #3e8175 100%) !important;
            border: none !important;
            border-radius: 25px !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            font-size: 0.95rem !important;
            padding: 0.78rem 1.5rem !important;
            width: 100% !important;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25) !important;
            cursor: pointer;
            letter-spacing: 0.2px;
        }

        .btn-pill-teal:hover {
            background: linear-gradient(135deg, #59b5a7 0%, #469285 100%) !important;
            transform: translateY(-1.5px);
            box-shadow: 0 8px 20px rgba(62, 129, 117, 0.4) !important;
            color: #ffffff !important;
        }

        .btn-pill-teal:active {
            transform: scale(0.98);
        }

        .register-now-link {
            color: #5eead4;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .register-now-link:hover {
            color: #ffffff;
            text-decoration: underline;
        }

        .footer-terms-link {
            color: #729686;
            font-size: 0.75rem;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .footer-terms-link:hover {
            color: #c0ded1;
        }

        /* Alert Styling Gelap & Elegan */
        .alert-dark-custom {
            border-radius: 14px;
            font-size: 0.82rem;
            padding: 0.65rem 1rem;
            border: 1px solid rgba(239, 68, 68, 0.35);
            background: rgba(239, 68, 68, 0.18);
            color: #fecaca;
            margin-bottom: 1rem;
        }

        .alert-dark-success {
            border-radius: 14px;
            font-size: 0.82rem;
            padding: 0.65rem 1rem;
            border: 1px solid rgba(16, 185, 129, 0.35);
            background: rgba(16, 185, 129, 0.18);
            color: #a7f3d0;
            margin-bottom: 1rem;
        }

        /* Responsif untuk Layar Tablet dan Mobile */
        @media (max-width: 991.98px) {
            .register-master-card {
                flex-direction: column;
                border-radius: 28px;
            }

            .wave-curve-border {
                display: none;
            }

            .panel-brand-left {
                padding: 2rem 1.8rem;
                flex: none;
            }

            .panel-form-right {
                padding: 2.2rem 1.8rem;
                flex: none;
            }
        }
    </style>
</head>

<body>
    <!-- Floating Ambient Orbs (Persis sesuai tema Login) -->
    <div class="bokeh-orb bokeh-1"></div>
    <div class="bokeh-orb bokeh-2"></div>
    <div class="bokeh-orb bokeh-3"></div>
    <div class="bokeh-orb bokeh-4"></div>

    <!-- Master Register Card -->
    <div class="register-master-card">
        <!-- ============================================================= -->
        <!-- SISI KIRI: PANEL PUTIH (LOGO KEMENTERIAN, ILUSTRASI REGISTRASI)-->
        <!-- ============================================================= -->
        <div class="panel-brand-left">
            <!-- Header Brand: Logo Kementerian PKP & Identitas Satker -->
            <div class="d-flex align-items-center gap-3">
                <img src="{{ asset('hope-ui/assets/images/auth/kemenpkp-logo.png') }}" alt="Logo Kementerian PKP"
                    style="max-height: 52px; width: auto; object-fit: contain;">
                <div>
                    <h6 class="fw-bold mb-0 text-uppercase" style="font-size: 0.82rem; color: #144B59; letter-spacing: 0.3px; line-height: 1.25;">
                        Kementerian Perumahan dan Kawasan Permukiman
                    </h6>
                    <span class="d-block fw-bold text-uppercase" style="font-size: 0.72rem; color: #b8860b; letter-spacing: 0.5px;">
                        Republik Indonesia
                    </span>
                    <small class="text-muted" style="font-size: 0.7rem;">Direktorat Jenderal Kawasan Permukiman</small>
                </div>
            </div>

            <!-- Bagian Tengah: Ilustrasi Artistik Pendaftaran Akun Pegawai ASN -->
            <div class="text-center my-3 my-lg-2 position-relative d-flex flex-column align-items-center justify-content-center">
                <svg width="340" height="260" viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg" style="max-width: 100%; height: auto;">
                    <!-- Latar Belakang Blob Organik Halus Mint & Gelembung Mengambang -->
                    <path d="M70 140C50 80 110 30 180 35C260 40 330 70 345 130C360 190 320 250 250 265C170 280 85 240 70 180Z" fill="#e3f3ec"/>
                    
                    <!-- Gelembung / Bubbles Melayang Halus -->
                    <circle cx="85" cy="70" r="14" fill="#cfece1" opacity="0.6"/>
                    <circle cx="310" cy="80" r="18" fill="#cfece1" opacity="0.5"/>
                    <circle cx="330" cy="190" r="10" fill="#cfece1" opacity="0.6"/>
                    <circle cx="95" cy="220" r="16" fill="#cfece1" opacity="0.5"/>

                    <!-- Meja Kerja / Podium Kantor -->
                    <rect x="110" y="245" width="180" height="10" rx="5" fill="#144b59" opacity="0.85"/>
                    <rect x="130" y="255" width="140" height="5" rx="2.5" fill="#94a3b8" opacity="0.4"/>

                    <!-- Dokumen Pendaftaran / Berkas Pengajuan Akun di Belakang ID Card -->
                    <rect x="130" y="145" width="85" height="100" rx="8" fill="#1b4d3e"/>
                    <rect x="140" y="158" width="65" height="6" rx="3" fill="#ffffff" opacity="0.9"/>
                    <rect x="140" y="172" width="45" height="5" rx="2.5" fill="#5eead4" opacity="0.8"/>
                    <rect x="140" y="185" width="55" height="5" rx="2.5" fill="#ffffff" opacity="0.6"/>

                    <!-- Tali Lanyard ID Card Pegawai -->
                    <path d="M225 60C225 95 240 115 240 125" stroke="#0ea5e9" stroke-width="4" stroke-linecap="round"/>
                    <path d="M255 60C255 95 240 115 240 125" stroke="#0284c7" stroke-width="4" stroke-linecap="round"/>
                    <rect x="233" y="123" width="14" height="10" rx="2" fill="#94a3b8"/>

                    <!-- Kartu Tanda Pengenal Pegawai (ID Card ASN Kementerian PKP) -->
                    <rect x="200" y="130" width="80" height="115" rx="8" fill="#ffffff" stroke="#cbd5e1" stroke-width="2.5"/>
                    <!-- Header Kartu ID -->
                    <rect x="200" y="130" width="80" height="24" rx="8" fill="#144b59"/>
                    <circle cx="215" cy="142" r="5" fill="#b8860b"/>
                    <rect x="225" y="139" width="45" height="6" rx="2" fill="#ffffff" opacity="0.9"/>
                    
                    <!-- Foto Profil Pegawai -->
                    <circle cx="240" cy="172" r="14" fill="#e2e8f0"/>
                    <circle cx="240" cy="169" r="6" fill="#64748b"/>
                    <path d="M231 182C231 177 235 175 240 175C245 175 249 177 249 182" fill="#64748b"/>

                    <!-- Garis Teks Nama & NIP Pegawai -->
                    <line x1="215" y1="195" x2="265" y2="195" stroke="#334155" stroke-width="2.5" stroke-linecap="round"/>
                    <line x1="220" y1="203" x2="260" y2="203" stroke="#94a3b8" stroke-width="2" stroke-linecap="round"/>

                    <!-- Badge Verifikasi Akun Centang Hijau -->
                    <circle cx="265" cy="225" r="10" fill="#10b981"/>
                    <path d="M261 225L264 228L270 222" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

                    <!-- Pulpen Tanda Tangan -->
                    <rect x="120" y="200" width="8" height="45" rx="3" transform="rotate(-35 120 200)" fill="#f59e0b"/>
                    <path d="M133 191L137 184L141 195Z" fill="#1e293b"/>
                </svg>

                <div class="mt-1">
                    <h5 class="fw-bold mb-1" style="color: #144b59; font-size: 1.15rem; letter-spacing: -0.3px;">
                        Pendaftaran Akun Pegawai
                    </h5>
                    <p class="text-muted small mb-0" style="font-size: 0.8rem; line-height: 1.35;">
                        Registrasi mandiri untuk pegawai guna mempermudah permohonan ATK.
                    </p>
                </div>
            </div>

            <!-- Footer Sisi Kiri: Hak Cipta & Satker -->
            <div class="pt-2 border-top" style="border-color: #f1f5f9 !important;">
                <p class="mb-0 text-muted small" style="font-size: 0.72rem; line-height: 1.3;">
                    &copy; 2026 Kementerian Perumahan dan Kawasan Permukiman RI<br>
                    <span style="color: #64748b;">Biro Umum &amp; Pengadaan Barang &bull; Subbagian Tata Usaha</span>
                </p>
            </div>

            <!-- Lengkungan SVG Organik Halus (Wave Divider Sesuai Tema) -->
            <svg class="wave-curve-border" viewBox="0 0 100 600" preserveAspectRatio="none">
                <path d="M 0 0 L 25 0 C 10 120, 95 240, 95 360 C 95 490, 15 540, 0 600 Z" fill="#ffffff" />
            </svg>
        </div>

        <!-- ============================================================= -->
        <!-- SISI KANAN: PANEL FORM REGISTRASI HIJAU TUA                  -->
        <!-- ============================================================= -->
        <div class="panel-form-right">
            <!-- Judul Form Registrasi -->
            <div class="mb-3">
                <h1 class="fw-bold mb-1" style="font-size: 2.1rem; letter-spacing: -0.5px; color: #ffffff;">
                    <i>Register</i>
                </h1>
                <p class="mb-0" style="color: #8faea0; font-size: 0.86rem;">
                    Pendaftaran Akun Baru &bull; Sistem Inventori ATK (ATEKA)
                </p>
            </div>

            <!-- Alert Notifikasi Sesi & Kesalahan -->
            @if (session('success'))
                <div class="alert alert-dark-success d-flex align-items-center justify-content-between" role="alert">
                    <span>{{ session('success') }}</span>
                    <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="alert" aria-label="Close" style="font-size: 0.65rem;"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-dark-custom d-flex align-items-center justify-content-between" role="alert">
                    <span>{{ session('error') }}</span>
                    <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="alert" aria-label="Close" style="font-size: 0.65rem;"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-dark-custom" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Registrasi -->
            <form action="{{ route('register.store') }}" method="POST">
                @csrf

                <div class="row g-2 mb-2">
                    <!-- Baris 1: Nama Lengkap & Email -->
                    <div class="col-md-6 col-12">
                        <div class="mb-2">
                            <label for="name" class="form-label-custom">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="name" name="name" class="input-pill-dark"
                                value="{{ old('name') }}" placeholder="Budi Prasetyo" required autofocus autocomplete="name">
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <div class="mb-2">
                            <label for="email" class="form-label-custom">
                                Alamat <i>Email</i> <span class="text-danger">*</span>
                            </label>
                            <input type="email" id="email" name="email" class="input-pill-dark"
                                value="{{ old('email') }}" placeholder="nama@pkp.go.id" required autocomplete="email">
                        </div>
                    </div>

                    <!-- Baris 2: NIP & Gelar Akademik -->
                    <div class="col-md-6 col-12">
                        <div class="mb-2">
                            <label for="nip" class="form-label-custom">
                                Nomor Induk Pegawai (NIP) <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="nip" name="nip" class="input-pill-dark font-monospace"
                                value="{{ old('nip') }}" placeholder="19950510 202012 1 002" required>
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <div class="mb-2">
                            <label for="title" class="form-label-custom">
                                Gelar Akademik
                            </label>
                            <input type="text" id="title" name="title" class="input-pill-dark"
                                value="{{ old('title', old('gelar')) }}" placeholder="S.T. / M.Si. / S.Kom.">
                        </div>
                    </div>

                    <input type="hidden" name="role" value="pegawai">

                    <!-- Baris 4: Password & Konfirmasi Password -->
                    <div class="col-md-6 col-12">
                        <div class="mb-2">
                            <label for="password" class="form-label-custom">
                                <i>Password</i> <span class="text-danger">*</span>
                            </label>
                            <div class="password-input-group">
                                <input type="password" id="password" name="password" class="input-pill-dark"
                                    placeholder="Minimal 6 karakter" required autocomplete="new-password" style="padding-right: 42px !important;">
                                <button type="button" class="password-toggle-btn" id="togglePasswordBtn" aria-label="Lihat atau sembunyikan password">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="togglePasswordIcon">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <div class="mb-2">
                            <label for="password_confirmation" class="form-label-custom">
                                Ulangi <i>Password</i> <span class="text-danger">*</span>
                            </label>
                            <div class="password-input-group">
                                <input type="password" id="password_confirmation" name="password_confirmation" class="input-pill-dark"
                                    placeholder="Ketik ulang password" required autocomplete="new-password" style="padding-right: 42px !important;">
                                <button type="button" class="password-toggle-btn" id="toggleConfirmPasswordBtn" aria-label="Lihat atau sembunyikan konfirmasi password">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="toggleConfirmPasswordIcon">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Submit Register -->
                <div class="mt-3 mb-3">
                    <button type="submit" class="btn-pill-teal" id="submitRegisterBtn">
                        Daftarkan Akun (<i>Register</i>)
                    </button>
                </div>

                <!-- Tautan Kembali ke Login -->
                <div class="text-center pt-1 mb-3">
                    <span style="color: #a4c4b5; font-size: 0.85rem;">Sudah memiliki akun terdaftar?</span>
                    <a href="{{ route('login') }}" class="register-now-link ms-1" style="font-size: 0.85rem;">
                        <i>Login</i> di Sini
                    </a>
                </div>

                <!-- Footer Tautan Bawah -->
                <div class="d-flex justify-content-between align-items-center pt-3 border-top" style="border-color: rgba(255, 255, 255, 0.08) !important;">
                    <a href="#" class="footer-terms-link" onclick="alert('Sistem Informasi Inventori ATK dipergunakan khusus untuk pegawai dan unit kerja di lingkungan Direktorat Jenderal Kawasan Permukiman Kementerian PKP RI.'); return false;">
                        <i>Terms and Services</i>
                    </a>
                    <span style="font-size: 0.73rem; color: #729686;">
                        <i>Have a problem? Contact at</i> <a href="mailto:tu@perumahan.go.id" class="text-decoration-none" style="color: #4db6ac;">Subbag TU</a>
                    </span>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts Bundle -->
    <script src="{{ asset('hope-ui/assets/js/core/libs.min.js') }}" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password Show/Hide Toggle
            function setupPasswordToggle(btnId, inputId, iconId) {
                const btn = document.getElementById(btnId);
                const input = document.getElementById(inputId);
                const icon = document.getElementById(iconId);

                if (btn && input && icon) {
                    btn.addEventListener('click', function() {
                        const isPassword = input.getAttribute('type') === 'password';
                        input.setAttribute('type', isPassword ? 'text' : 'password');
                        if (isPassword) {
                            icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
                            btn.style.color = '#5eead4';
                        } else {
                            icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
                            btn.style.color = '#719987';
                        }
                    });
                }
            }

            setupPasswordToggle('togglePasswordBtn', 'password', 'togglePasswordIcon');
            setupPasswordToggle('toggleConfirmPasswordBtn', 'password_confirmation', 'toggleConfirmPasswordIcon');

            // Form Submit Loading State
            const form = document.querySelector('form');
            const submitBtn = document.getElementById('submitRegisterBtn');
            if (form && submitBtn) {
                form.addEventListener('submit', function() {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Mendaftarkan Akun...';
                });
            }

            // BFCache handling
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    window.location.reload();
                }
            });
        });
    </script>
</body>

</html>
