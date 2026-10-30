<!doctype html>
<html lang="id" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Nonaktifkan Pengindeksan Mesin Pencari (Aplikasi Internal Kementerian PKP RI) -->
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <meta name="description" content="Reset Kata Sandi Sistem Informasi Inventori Persediaan ATK (ATEKA) Kementerian PKP RI">
    <title>Reset Kata Sandi | Inventori ATK Kementerian PKP RI</title>

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

        /* Ambient Bokeh / Floating Soft Circles (Persis sesuai tema Login & Register) */
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
        .forgot-master-card {
            width: 100%;
            max-width: 1040px;
            margin: auto;
            background-color: var(--card-dark-green);
            border-radius: 36px;
            box-shadow: 0 35px 80px -15px rgba(0, 0, 0, 0.65), 0 0 0 1px rgba(255, 255, 255, 0.08);
            overflow: hidden;
            position: relative;
            z-index: 10;
            display: flex;
            min-height: 610px;
        }

        /* Sisi Kiri: Panel Putih dengan Lengkungan Halus (Wave Curve) */
        .panel-brand-left {
            background-color: #ffffff;
            color: #0f172a;
            flex: 1.05;
            padding: 2.6rem 2.8rem;
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

        /* Sisi Kanan: Panel Form Reset Kata Sandi Hijau Tua */
        .panel-form-right {
            background-color: var(--card-dark-green);
            color: #ffffff;
            flex: 1.05;
            padding: 2.8rem 3.2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            z-index: 4;
        }

        /* Form Controls Sesuai Tema */
        .form-label-custom {
            font-size: 0.82rem;
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
            padding: 0.68rem 1.2rem !important;
            font-size: 0.88rem !important;
            transition: all 0.2s ease !important;
            width: 100% !important;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.25) !important;
        }

        .input-pill-dark::placeholder {
            color: #5c7e6f !important;
            font-size: 0.84rem !important;
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
            padding: 0.76rem 1.5rem !important;
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

        .forgot-password-link {
            color: #4db6ac;
            font-size: 0.82rem;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .forgot-password-link:hover {
            color: #5eead4;
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

        .info-pill-dark {
            background: rgba(77, 182, 172, 0.08);
            border: 1px solid rgba(77, 182, 172, 0.22);
            border-radius: 14px;
            padding: 0.65rem 0.95rem;
            margin: 0.85rem 0;
        }

        /* Responsif Layar Tablet dan Mobile */
        @media (max-width: 991.98px) {
            .forgot-master-card {
                flex-direction: column;
                margin: 16px;
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
    <!-- Floating Ambient Orbs (Sesuai Tema Login & Register) -->
    <div class="bokeh-orb bokeh-1"></div>
    <div class="bokeh-orb bokeh-2"></div>
    <div class="bokeh-orb bokeh-3"></div>
    <div class="bokeh-orb bokeh-4"></div>

    <!-- Main Master Forgot Password Card -->
    <div class="forgot-master-card">
        <!-- ============================================================= -->
        <!-- SISI KIRI: PANEL PUTIH (LOGO KEMENTERIAN, ILUSTRASI ATK, WAVE) -->
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

            <!-- Bagian Tengah: Ilustrasi Artistik Inventori ATK & Keamanan Akun -->
            <div class="text-center my-3 my-lg-2 position-relative d-flex flex-column align-items-center justify-content-center">
                <svg width="340" height="250" viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg" style="max-width: 100%; height: auto;">
                    <!-- Latar Belakang Blob Mint Organik -->
                    <path d="M70 140C50 80 110 30 180 35C260 40 330 70 345 130C360 190 320 250 250 265C170 280 85 240 70 180Z" fill="#e3f3ec"/>
                    
                    <!-- Gelembung / Bubbles Melayang Halus -->
                    <circle cx="85" cy="70" r="14" fill="#cfece1" opacity="0.6"/>
                    <circle cx="310" cy="80" r="18" fill="#cfece1" opacity="0.5"/>
                    <circle cx="330" cy="190" r="10" fill="#cfece1" opacity="0.6"/>
                    <circle cx="95" cy="220" r="16" fill="#cfece1" opacity="0.5"/>

                    <!-- Meja & Rak Persediaan -->
                    <rect x="110" y="240" width="180" height="12" rx="6" fill="#144b59" opacity="0.85"/>
                    <rect x="130" y="252" width="140" height="6" rx="3" fill="#94a3b8" opacity="0.4"/>

                    <!-- Simbol Perisai Keamanan Akun & Kunci Sandi -->
                    <path d="M200 70L250 90V140C250 180 200 215 200 215C200 215 150 180 150 140V90L200 70Z" fill="#1b4d3e"/>
                    <path d="M200 80L240 96V138C240 172 200 202 200 202C200 202 160 172 160 138V96L200 80Z" fill="#144b59"/>
                    
                    <!-- Ikon Gembok di Dalam Perisai -->
                    <rect x="187" y="132" width="26" height="22" rx="4" fill="#5eead4"/>
                    <path d="M193 132V124C193 120.134 196.134 117 200 117C203.866 117 207 120.134 207 124V132" stroke="#ffffff" stroke-width="3" stroke-linecap="round"/>
                    <circle cx="200" cy="141" r="2.5" fill="#0f241a"/>
                    <line x1="200" y1="143.5" x2="200" y2="148" stroke="#0f241a" stroke-width="2" stroke-linecap="round"/>

                    <!-- Elemen Berkas Dokumen & Verifikasi NIP -->
                    <rect x="120" y="160" width="50" height="70" rx="4" fill="#ffffff" stroke="#cbd5e1" stroke-width="2"/>
                    <line x1="128" y1="175" x2="158" y2="175" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round"/>
                    <line x1="128" y1="187" x2="152" y2="187" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round"/>
                    <circle cx="145" cy="207" r="7" fill="#10b981"/>
                    <path d="M142 207L144.5 209.5L149 204.5" stroke="#ffffff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>

                    <!-- Tumpukan Box File & Perlengkapan Kantor -->
                    <rect x="235" y="165" width="55" height="65" rx="5" fill="#d97706"/>
                    <rect x="242" y="175" width="41" height="12" rx="2" fill="#ffffff" opacity="0.9"/>
                    <rect x="242" y="195" width="22" height="5" rx="2" fill="#fef3c7"/>
                    <circle cx="262.5" cy="216" r="6" fill="#f8fafc" opacity="0.9"/>
                </svg>

                <div class="mt-1">
                    <h5 class="fw-bold mb-1" style="color: #144b59; font-size: 1.15rem; letter-spacing: -0.3px;">
                        Verifikasi Mandiri Pegawai
                    </h5>
                    <p class="text-muted small mb-0" style="font-size: 0.8rem; line-height: 1.35;">
                        Pemulihan akses kata sandi instan berbasis NIP dan email kedinasan terdaftar yang terintegrasi.
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
        <!-- SISI KANAN: PANEL FORM RESET KATA SANDI HIJAU TUA            -->
        <!-- ============================================================= -->
        <div class="panel-form-right">
            <!-- Judul Form Reset Kata Sandi -->
            <div class="mb-3">
                <h1 class="fw-bold mb-1" style="font-size: 2.1rem; letter-spacing: -0.5px; color: #ffffff;">
                    <i>Reset Kata Sandi</i>
                </h1>
                <p class="mb-0" style="color: #8faea0; font-size: 0.88rem;">
                    Sistem Pencatatan Persediaan &amp; Pengajuan ATK
                </p>
            </div>

            <!-- Alert Notifikasi Feedback -->
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

            <!-- Form Reset Password -->
            <form action="{{ route('password.reset.nip') }}" method="POST">
                @csrf

                <!-- Input NIP -->
                <div class="mb-2.5">
                    <label for="nip" class="form-label-custom">
                        NOMOR INDUK PEGAWAI (NIP) <span style="color: #f87171;">*</span>
                    </label>
                    <input type="text" name="nip" id="nip" class="form-control input-pill-dark font-monospace"
                        value="{{ old('nip') }}" placeholder="19950510 202012 1 002" required autofocus>
                </div>

                <!-- Input Email Dinas -->
                <div class="mb-2.5">
                    <label for="email" class="form-label-custom">
                        EMAIL DINAS TERDAFTAR <span style="color: #f87171;">*</span>
                    </label>
                    <input type="email" name="email" id="email" class="form-control input-pill-dark"
                        value="{{ old('email') }}" placeholder="nama.pegawai@pupr.go.id" required autocomplete="email">
                    <small class="d-block mt-1" style="color: #729686; font-size: 0.74rem;">
                        Verifikasi memerlukan kecocokan NIP &amp; Email dinas aktif Anda.
                    </small>
                </div>

                <!-- Input Password Baru & Konfirmasi (Side by Side di Desktop) -->
                <div class="row g-2 mb-2">
                    <div class="col-12 col-md-6">
                        <label for="password" class="form-label-custom">
                            PASSWORD BARU <span style="color: #f87171;">*</span>
                        </label>
                        <div class="password-input-group">
                            <input type="password" name="password" id="password" class="form-control input-pill-dark pe-5"
                                placeholder="Minimal 6 karakter" required minlength="6" autocomplete="new-password">
                            <button type="button" class="password-toggle-btn" id="togglePasswordBtn"
                                aria-label="Lihat atau sembunyikan password baru" tabindex="-1">
                                <svg id="togglePasswordIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="password_confirmation" class="form-label-custom">
                            ULANGI PASSWORD BARU <span style="color: #f87171;">*</span>
                        </label>
                        <div class="password-input-group">
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control input-pill-dark pe-5"
                                placeholder="Ketik ulang password baru" required minlength="6" autocomplete="new-password">
                            <button type="button" class="password-toggle-btn" id="toggleConfirmPasswordBtn"
                                aria-label="Lihat atau sembunyikan konfirmasi password" tabindex="-1">
                                <svg id="toggleConfirmPasswordIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Bantuan NIP Info Pill (Elegan Gelap) -->
                <div class="info-pill-dark">
                    <div class="d-flex align-items-center gap-2">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#5eead4" stroke-width="2" class="flex-shrink-0" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                        <span style="font-size: 0.77rem; color: #a4c4b5; line-height: 1.4;">
                            Verifikasi NIP otomatis mereset kata sandi akun Anda secara langsung tanpa perlu tautan email.
                        </span>
                    </div>
                </div>

                <!-- Tombol Submit Reset Password -->
                <div class="mt-3 mb-2.5">
                    <button type="submit" class="btn-pill-teal" id="submitResetBtn">
                        Simpan &amp; Perbarui Password
                    </button>
                </div>

                <!-- Tautan Kembali ke Login -->
                <div class="text-center pt-1 mb-2">
                    <a href="{{ route('login') }}" class="forgot-password-link" style="font-size: 0.84rem; color: #5eead4;">
                        &larr; Kembali ke Halaman <i>Login</i>
                    </a>
                </div>

                <!-- Footer Tautan Bawah -->
                <div class="d-flex justify-content-between align-items-center pt-2.5 border-top" style="border-color: rgba(255, 255, 255, 0.08) !important;">
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
            const submitBtn = document.getElementById('submitResetBtn');
            if (form && submitBtn) {
                form.addEventListener('submit', function() {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses Reset Kata Sandi...';
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
