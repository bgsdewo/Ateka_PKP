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

    <!-- Inlined Critical CSS - Zero External Dependencies for Instant LCP (< 100ms) -->
    <style>
        :root {
            --bg-deep: #0e261d;
            --card-dark: #15382a;
            --input-dark: #0d241a;
            --input-border: rgba(255, 255, 255, 0.12);
            --primary-emerald: #10b981;
            --btn-teal: #429485;
            --accent-mint: #5eead4;
            --text-muted: #8aa99a;
            --brand-navy: #133e38;
            --brand-gold: #b45309;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: var(--bg-deep);
            background-image: 
                radial-gradient(circle at 50% 12%, rgba(16, 185, 129, 0.08) 0%, transparent 55%),
                radial-gradient(circle at 85% 85%, rgba(66, 148, 133, 0.1) 0%, transparent 45%),
                radial-gradient(rgba(255, 255, 255, 0.04) 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 30px 30px;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }

        /* Master Authentication Card */
        .auth-master-card {
            width: 100%;
            max-width: 1140px;
            background-color: var(--card-dark);
            border-radius: 32px;
            box-shadow: 0 35px 80px -15px rgba(0, 0, 0, 0.65), 0 0 0 1px rgba(255, 255, 255, 0.08);
            overflow: hidden;
            position: relative;
            display: flex;
            min-height: 630px;
        }

        /* Sisi Kiri: Panel Putih Bersih */
        .panel-brand-left {
            background-color: #ffffff;
            color: #1e293b;
            flex: 0.95;
            padding: 2.5rem 2.8rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            z-index: 2;
        }

        /* Lengkungan Halus SVG */
        .wave-curve-border {
            position: absolute;
            top: 0;
            right: -72px;
            width: 75px;
            height: 100%;
            pointer-events: none;
            z-index: 5;
        }

        /* Header Brand Institusi */
        .brand-header {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .brand-logo {
            max-height: 52px;
            width: auto;
            object-fit: contain;
        }

        .brand-text h1 {
            font-size: 0.82rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--brand-navy);
            letter-spacing: 0.3px;
            line-height: 1.28;
        }

        .brand-text .country {
            display: block;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--brand-gold);
            letter-spacing: 0.6px;
            margin-top: 1px;
        }

        .brand-text .satker {
            font-size: 0.7rem;
            color: #64748b;
            margin-top: 2px;
            display: block;
        }

        /* Visual Illustration Centerpiece */
        .brand-illustration-wrapper {
            text-align: center;
            margin: 1.5rem 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .brand-illustration-svg {
            max-width: 280px;
            height: auto;
            filter: drop-shadow(0 12px 24px rgba(19, 62, 56, 0.08));
        }

        .brand-caption h2 {
            color: var(--brand-navy);
            font-size: 1.22rem;
            font-weight: 700;
            letter-spacing: -0.3px;
            margin-top: 1rem;
            margin-bottom: 0.4rem;
        }

        .brand-caption p {
            color: #64748b;
            font-size: 0.82rem;
            line-height: 1.45;
            max-width: 360px;
            margin: 0 auto;
        }

        /* Footer Sisi Kiri */
        .brand-footer {
            padding-top: 1rem;
            border-top: 1px solid #f1f5f9;
            font-size: 0.73rem;
            color: #64748b;
            line-height: 1.4;
        }

        .brand-footer strong {
            color: #334155;
            font-weight: 600;
        }

        /* Sisi Kanan: Panel Form Registrasi */
        .panel-form-right {
            background-color: var(--card-dark);
            color: #ffffff;
            flex: 1.15;
            padding: 3rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            z-index: 4;
        }

        .form-header {
            margin-bottom: 1.4rem;
        }

        .form-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
        }

        .form-subtitle {
            color: var(--text-muted);
            font-size: 0.86rem;
            line-height: 1.4;
        }

        /* Form Grid */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px 14px;
            margin-bottom: 1.2rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: #d1fae5;
            margin-bottom: 5px;
            letter-spacing: 0.15px;
        }

        .form-label .req {
            color: #f87171;
            font-weight: bold;
        }

        .input-pill {
            width: 100%;
            background-color: var(--input-dark);
            border: 1.5px solid var(--input-border);
            border-radius: 22px;
            color: #ffffff;
            padding: 0.68rem 1.2rem;
            font-size: 0.88rem;
            font-family: inherit;
            transition: all 0.22s ease;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.25);
            outline: none;
        }

        .input-pill.font-mono {
            font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
            letter-spacing: 0.5px;
        }

        .input-pill::placeholder {
            color: #587c6c;
            font-size: 0.82rem;
        }

        .input-pill:focus {
            border-color: var(--accent-mint);
            box-shadow: 0 0 0 3px rgba(94, 234, 212, 0.22), inset 0 2px 4px rgba(0, 0, 0, 0.3);
            background-color: #081711;
        }

        .password-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .password-wrap .input-pill {
            padding-right: 44px;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            color: #6e9482;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s ease;
        }

        .password-toggle:hover {
            color: #ffffff;
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #449586 0%, #327568 100%);
            border: none;
            border-radius: 25px;
            color: #ffffff;
            font-size: 0.96rem;
            font-weight: 600;
            font-family: inherit;
            padding: 0.82rem 1.5rem;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.3);
            display: block;
            letter-spacing: 0.2px;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #4fa998 0%, #3a8778 100%);
            transform: translateY(-1.5px);
            box-shadow: 0 10px 24px rgba(50, 117, 104, 0.45);
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .login-callout {
            text-align: center;
            margin-top: 1.3rem;
            font-size: 0.86rem;
            color: #9cbdb0;
        }

        .link-login {
            color: var(--accent-mint);
            font-weight: 600;
            text-decoration: none;
            margin-left: 6px;
            transition: all 0.2s ease;
        }

        .link-login:hover {
            color: #ffffff;
            text-decoration: underline;
        }

        /* Alerts */
        .alert-box {
            border-radius: 14px;
            font-size: 0.82rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1.25rem;
            line-height: 1.4;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.16);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: #fecaca;
        }

        .alert-error ul {
            padding-left: 1.2rem;
            margin-top: 4px;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.16);
            border: 1px solid rgba(16, 185, 129, 0.35);
            color: #a7f3d0;
        }

        /* Responsive Breakpoints */
        @media (max-width: 960px) {
            .auth-master-card {
                flex-direction: column;
                max-width: 540px;
                border-radius: 24px;
            }

            .wave-curve-border {
                display: none;
            }

            .panel-brand-left {
                padding: 2.2rem 2rem;
                flex: none;
            }

            .panel-form-right {
                padding: 2.2rem 2rem;
                flex: none;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/ateka-auth-fit.css') }}?v={{ @filemtime(public_path('css/ateka-auth-fit.css')) }}">
</head>

<body>
    <!-- Main Master Register Card -->
    <div class="auth-master-card">
        <!-- ============================================================= -->
        <!-- SISI KIRI: IDENTITAS RESMI & ILUSTRASI ID CARD ASN           -->
        <!-- ============================================================= -->
        <div class="panel-brand-left">
            <!-- Header Brand: Logo & Identitas Satker -->
            <div class="brand-header">
                <img src="{{ asset('hope-ui/assets/images/auth/kemenpkp-logo.png') }}" alt="Logo Kementerian PKP RI" class="brand-logo">
                <div class="brand-text">
                    <h1>Kementerian Perumahan dan Kawasan Permukiman</h1>
                    <span class="country">Republik Indonesia</span>
                    <span class="satker">Direktorat Jenderal Kawasan Permukiman</span>
                </div>
            </div>

            <!-- Bagian Tengah: Visual ID Card ASN Kementerian PKP RI -->
            <div class="brand-illustration-wrapper">
                <svg class="brand-illustration-svg" viewBox="0 0 360 260" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Podium / Landasan Halus -->
                    <ellipse cx="180" cy="225" rx="140" ry="20" fill="#e8f4f0" />
                    <ellipse cx="180" cy="223" rx="110" ry="14" fill="#d3ebe2" />

                    <!-- Tali Lanyard ID Card Pegawai -->
                    <path d="M165 40C165 80 180 100 180 110" stroke="#0ea5e9" stroke-width="3.5" stroke-linecap="round"/>
                    <path d="M195 40C195 80 180 100 180 110" stroke="#0284c7" stroke-width="3.5" stroke-linecap="round"/>
                    <rect x="173" y="108" width="14" height="10" rx="3" fill="#64748b"/>

                    <!-- Kartu Tanda Pengenal Pegawai ASN -->
                    <rect x="135" y="115" width="90" height="118" rx="10" fill="#ffffff" stroke="#cbd5e1" stroke-width="2.5"/>
                    <!-- Header Kartu ID -->
                    <rect x="135" y="115" width="90" height="26" rx="10" fill="#144b59"/>
                    <circle cx="152" cy="128" r="5" fill="#b8860b"/>
                    <rect x="162" y="125" width="50" height="6" rx="2" fill="#ffffff" opacity="0.9"/>

                    <!-- Foto Profil Siluet ASN -->
                    <circle cx="180" cy="158" r="15" fill="#e2e8f0"/>
                    <circle cx="180" cy="154" r="6.5" fill="#64748b"/>
                    <path d="M170 168C170 163 174 161 180 161C186 161 190 163 190 168" fill="#64748b"/>

                    <!-- Baris Teks Nama & NIP Pegawai -->
                    <line x1="150" y1="182" x2="210" y2="182" stroke="#334155" stroke-width="2.5" stroke-linecap="round"/>
                    <line x1="156" y1="190" x2="204" y2="190" stroke="#94a3b8" stroke-width="2" stroke-linecap="round"/>

                    <!-- Badge Verifikasi Akun Centang Hijau -->
                    <circle cx="212" cy="214" r="11" fill="#10b981"/>
                    <path d="M207 214L211 218L218 211" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>

                    <!-- Pulpen Tanda Tangan Kedinasan -->
                    <rect x="85" y="170" width="8" height="48" rx="3" transform="rotate(-35 85 170)" fill="#f59e0b"/>
                    <path d="M99 161L103 154L107 165Z" fill="#1e293b"/>
                </svg>

                <div class="brand-caption">
                    <h2>Pendaftaran Akun Pegawai</h2>
                    <p>Registrasi mandiri untuk pegawai guna mempermudah permohonan ATK dan pemantauan distribusi persediaan.</p>
                </div>
            </div>

            <!-- Footer Sisi Kiri -->
            <div class="brand-footer">
                <strong>&copy; 2026 Kementerian Perumahan dan Kawasan Permukiman RI</strong><br>
                Biro Umum &amp; Pengadaan Barang &bull; Subbagian Tata Usaha
            </div>

            <!-- Lengkungan Halus SVG -->
            <svg class="wave-curve-border" viewBox="0 0 100 600" preserveAspectRatio="none">
                <path d="M 0 0 L 25 0 C 10 120, 95 240, 95 360 C 95 490, 15 540, 0 600 Z" fill="#ffffff" />
            </svg>
        </div>

        <!-- ============================================================= -->
        <!-- SISI KANAN: PANEL FORM REGISTRASI                            -->
        <!-- ============================================================= -->
        <div class="panel-form-right">
            <div class="form-header">
                <h2 class="form-title">Register</h2>
                <p class="form-subtitle">Pendaftaran Akun Baru &bull; Sistem Inventori ATK (ATEKA)</p>
            </div>

            <!-- Feedback Alerts -->
            @if (session('success'))
                <div class="alert-box alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert-box alert-error" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert-box alert-error" role="alert">
                    <strong>Terdapat kendala pendaftaran:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Registrasi -->
            <form action="{{ route('register.store') }}" method="POST">
                @csrf
                <input type="hidden" name="role" value="pegawai">

                <div class="form-grid">
                    <!-- Nama Lengkap -->
                    <div class="form-group">
                        <label for="name" class="form-label">Nama Lengkap <span class="req">*</span></label>
                        <input type="text" id="name" name="name" class="input-pill"
                            value="{{ old('name') }}" placeholder="Budi Prasetyo" required autofocus autocomplete="name">
                    </div>

                    <!-- Email Dinas -->
                    <div class="form-group">
                        <label for="email" class="form-label">Alamat Email <span class="req">*</span></label>
                        <input type="email" id="email" name="email" class="input-pill"
                            value="{{ old('email') }}" placeholder="nama@pkp.go.id" required autocomplete="email">
                    </div>

                    <!-- Nomor Induk Pegawai (NIP) -->
                    <div class="form-group">
                        <label for="nip" class="form-label">Nomor Induk Pegawai (NIP) <span class="req">*</span></label>
                        <input type="text" id="nip" name="nip" class="input-pill font-mono"
                            value="{{ old('nip') }}" placeholder="19950510 202012 1 002" required>
                    </div>

                    <!-- Gelar Akademik -->
                    <div class="form-group">
                        <label for="title" class="form-label">Gelar Akademik</label>
                        <input type="text" id="title" name="title" class="input-pill"
                            value="{{ old('title', old('gelar')) }}" placeholder="S.T. / M.Si. / S.Kom.">
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="password" class="form-label">Password <span class="req">*</span></label>
                        <div class="password-wrap">
                            <input type="password" id="password" name="password" class="input-pill"
                                placeholder="Minimal 6 karakter" required autocomplete="new-password">
                            <button type="button" class="password-toggle" id="togglePasswordBtn" aria-label="Lihat atau sembunyikan password">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="togglePasswordIcon">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Ulangi Password <span class="req">*</span></label>
                        <div class="password-wrap">
                            <input type="password" id="password_confirmation" name="password_confirmation" class="input-pill"
                                placeholder="Ketik ulang password" required autocomplete="new-password">
                            <button type="button" class="password-toggle" id="toggleConfirmPasswordBtn" aria-label="Lihat atau sembunyikan konfirmasi password">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="toggleConfirmPasswordIcon">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="btn-submit" id="submitRegisterBtn">
                    Daftarkan Akun (Register)
                </button>

                <!-- Callout Kembali ke Login -->
                <div class="login-callout">
                    Sudah memiliki akun terdaftar?
                    <a href="{{ route('login') }}" class="link-login">Login di Sini</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Minimal Native Password Visibility Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
                            btn.style.color = '#6e9482';
                        }
                    });
                }
            }

            setupPasswordToggle('togglePasswordBtn', 'password', 'togglePasswordIcon');
            setupPasswordToggle('toggleConfirmPasswordBtn', 'password_confirmation', 'toggleConfirmPasswordIcon');

            // Form Submit Instant Feedback State
            const registerForm = document.querySelector('form');
            const submitBtn = document.getElementById('submitRegisterBtn');
            if (registerForm && submitBtn) {
                registerForm.addEventListener('submit', function() {
                    submitBtn.disabled = true;
                    submitBtn.style.opacity = '0.85';
                    submitBtn.style.pointerEvents = 'none';
                    submitBtn.innerHTML = '<span style="display:inline-block;width:15px;height:15px;border:2px solid #ffffff;border-top-color:transparent;border-radius:50%;animation:spin 0.6s linear infinite;margin-right:8px;vertical-align:middle;"></span>Mendaftarkan Akun...';
                });
            }
        });
    </script>
</body>

</html>
