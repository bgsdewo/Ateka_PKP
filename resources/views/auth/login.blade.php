<!doctype html>
<html lang="id" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Nonaktifkan Pengindeksan Mesin Pencari (Aplikasi Internal Kementerian PKP RI) -->
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <meta name="description" content="Login Sistem Informasi Inventori Persediaan ATK (ATEKA) Kementerian PKP RI">
    <title>Login | Inventori ATK Kementerian PKP RI</title>

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
            --primary-emerald-hover: #34d399;
            --btn-teal: #429485;
            --btn-teal-hover: #4ea898;
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
            max-width: 1040px;
            background-color: var(--card-dark);
            border-radius: 32px;
            box-shadow: 0 35px 80px -15px rgba(0, 0, 0, 0.65), 0 0 0 1px rgba(255, 255, 255, 0.08);
            overflow: hidden;
            position: relative;
            display: flex;
            min-height: 590px;
        }

        /* Sisi Kiri: Panel Putih Bersih & Wibawa Institusional */
        .panel-brand-left {
            background-color: #ffffff;
            color: #1e293b;
            flex: 1.15;
            padding: 2.5rem 2.8rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            z-index: 2;
        }

        /* Lengkungan Halus Organik Menghubungkan Sisi Kiri & Kanan */
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
            max-width: 290px;
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
            max-width: 380px;
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

        /* Sisi Kanan: Panel Form Login Hijau Gelap Elegan */
        .panel-form-right {
            background-color: var(--card-dark);
            color: #ffffff;
            flex: 0.95;
            padding: 3.2rem 3.2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            z-index: 4;
        }

        .form-header {
            margin-bottom: 1.8rem;
        }

        .form-title {
            font-size: 2.3rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
        }

        .form-subtitle {
            color: var(--text-muted);
            font-size: 0.88rem;
            line-height: 1.4;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #d1fae5;
            margin-bottom: 6px;
            letter-spacing: 0.15px;
        }

        .input-pill {
            width: 100%;
            background-color: var(--input-dark);
            border: 1.5px solid var(--input-border);
            border-radius: 24px;
            color: #ffffff;
            padding: 0.74rem 1.25rem;
            font-size: 0.9rem;
            font-family: inherit;
            transition: all 0.22s ease;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.25);
            outline: none;
        }

        .input-pill::placeholder {
            color: #587c6c;
            font-size: 0.85rem;
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
            padding-right: 48px;
        }

        .password-toggle {
            position: absolute;
            right: 14px;
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

        /* Actions & Links */
        .form-actions-sub {
            display: flex;
            justify-content: flex-end;
            margin-top: -6px;
            margin-bottom: 1.6rem;
        }

        .link-forgot {
            color: var(--accent-mint);
            font-size: 0.82rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .link-forgot:hover {
            color: #ffffff;
            text-decoration: underline;
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

        .register-callout {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.86rem;
            color: #9cbdb0;
        }

        .link-register {
            color: var(--accent-mint);
            font-weight: 600;
            text-decoration: none;
            margin-left: 6px;
            transition: all 0.2s ease;
        }

        .link-register:hover {
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
                max-width: 520px;
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
                padding: 2.4rem 2rem;
                flex: none;
            }
        }
    </style>
</head>

<body>
    <!-- Main Master Login Card -->
    <div class="auth-master-card">
        <!-- ============================================================= -->
        <!-- SISI KIRI: IDENTITAS RESMI & ILUSTRASI PERSAMAAN LOGISTIK     -->
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

            <!-- Bagian Tengah: Visual Ilustrasi Clean & Berwibawa -->
            <div class="brand-illustration-wrapper">
                <svg class="brand-illustration-svg" viewBox="0 0 360 260" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Podium / Landasan Halus -->
                    <ellipse cx="180" cy="225" rx="140" ry="20" fill="#e8f4f0" />
                    <ellipse cx="180" cy="223" rx="110" ry="14" fill="#d3ebe2" />

                    <!-- Kotak Persediaan Barang ATK Masuk / Distribusi BMN -->
                    <rect x="200" y="125" width="80" height="85" rx="10" fill="#d97706"/>
                    <path d="M200 148H280" stroke="#b45309" stroke-width="3"/>
                    <rect x="228" y="125" width="24" height="85" fill="#fef3c7" opacity="0.45"/>
                    <!-- Label Barcode / Kode BMN -->
                    <rect x="212" y="162" width="30" height="20" rx="4" fill="#ffffff"/>
                    <line x1="216" y1="168" x2="238" y2="168" stroke="#334155" stroke-width="1.8"/>
                    <line x1="216" y1="172" x2="232" y2="172" stroke="#334155" stroke-width="1.8"/>
                    <line x1="216" y1="176" x2="235" y2="176" stroke="#334155" stroke-width="1.8"/>

                    <!-- Kotak File / Ordner Arsip Persediaan -->
                    <rect x="105" y="110" width="65" height="100" rx="8" fill="#144b59"/>
                    <rect x="113" y="122" width="49" height="18" rx="4" fill="#ffffff" opacity="0.95"/>
                    <rect x="113" y="148" width="32" height="6" rx="3" fill="#5eead4" opacity="0.85"/>
                    <circle cx="137" cy="184" r="8" fill="#f8fafc" opacity="0.95"/>

                    <!-- Papan Uji / Clipboard Verifikasi Digital -->
                    <rect x="150" y="65" width="75" height="108" rx="10" fill="#ffffff" stroke="#cbd5e1" stroke-width="2.5"/>
                    <rect x="172" y="55" width="32" height="16" rx="5" fill="#144b59"/>
                    <circle cx="188" cy="62" r="3" fill="#ffffff" opacity="0.9"/>
                    <!-- Baris Garis Dokumen & Checklist Sukses -->
                    <line x1="164" y1="88" x2="210" y2="88" stroke="#94a3b8" stroke-width="3" stroke-linecap="round"/>
                    <line x1="164" y1="104" x2="210" y2="104" stroke="#94a3b8" stroke-width="3" stroke-linecap="round"/>
                    <line x1="164" y1="120" x2="198" y2="120" stroke="#94a3b8" stroke-width="3" stroke-linecap="round"/>
                    <!-- Badge Centang Hijau Verifikasi TU -->
                    <circle cx="204" cy="140" r="11" fill="#10b981"/>
                    <path d="M199 140L203 144L209 137" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>

                    <!-- Pulpen Kedinasan Menyilang -->
                    <rect x="80" y="165" width="9" height="52" rx="3" transform="rotate(-32 80 165)" fill="#0284c7"/>
                    <path d="M96 156L101 148L105 160Z" fill="#f59e0b"/>
                </svg>

                <div class="brand-caption">
                    <h2>Inventori &amp; Distribusi ATK</h2>
                    <p>Pengelolaan persediaan, mutasi stok, dan pencatatan e-katalog alat tulis kantor secara transparan &amp; akuntabel.</p>
                </div>
            </div>

            <!-- Footer Sisi Kiri -->
            <div class="brand-footer">
                <strong>&copy; 2026 Kementerian Perumahan dan Kawasan Permukiman RI</strong><br>
                Biro Umum &amp; Pengadaan Barang &bull; Subbagian Tata Usaha
            </div>

            <!-- Lengkungan Halus SVG ke Sisi Kanan -->
            <svg class="wave-curve-border" viewBox="0 0 100 600" preserveAspectRatio="none">
                <path d="M 0 0 L 25 0 C 10 120, 95 240, 95 360 C 95 490, 15 540, 0 600 Z" fill="#ffffff" />
            </svg>
        </div>

        <!-- ============================================================= -->
        <!-- SISI KANAN: PANEL FORM LOGIN                                  -->
        <!-- ============================================================= -->
        <div class="panel-form-right">
            <div class="form-header">
                <h2 class="form-title">Login</h2>
                <p class="form-subtitle">Masuk ke Sistem Inventori ATK (ATEKA)</p>
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
                    <strong>Terdapat kendala masuk:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Autentikasi -->
            <form action="{{ route('login.process') }}" method="POST">
                @csrf

                <!-- Input Username / Email / NIP -->
                <div class="form-group">
                    <label for="email" class="form-label">Username atau Email</label>
                    <input type="text" id="email" name="email" class="input-pill"
                        value="{{ old('email') }}" placeholder="Enter your username or email" required autocomplete="username" autofocus>
                </div>

                <!-- Input Password -->
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-wrap">
                        <input type="password" id="password" name="password" class="input-pill"
                            placeholder="Enter your password" required autocomplete="current-password">
                        <button type="button" class="password-toggle" id="togglePasswordBtn" aria-label="Lihat atau sembunyikan password">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="togglePasswordIcon">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Link Lupa Password -->
                <div class="form-actions-sub">
                    <a href="{{ route('password.forgot') }}" class="link-forgot">Forgot Password?</a>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="btn-submit">
                    Login
                </button>

                <!-- Callout Registrasi -->
                <div class="register-callout">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="link-register">Register Now</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Minimal Native Password Visibility Script (0 external libraries) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('togglePasswordBtn');
            const passInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePasswordIcon');

            if (toggleBtn && passInput && toggleIcon) {
                toggleBtn.addEventListener('click', function() {
                    const isPassword = passInput.getAttribute('type') === 'password';
                    passInput.setAttribute('type', isPassword ? 'text' : 'password');
                    if (isPassword) {
                        toggleIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
                        toggleBtn.style.color = '#5eead4';
                    } else {
                        toggleIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
                        toggleBtn.style.color = '#6e9482';
                    }
                });
            }

            // Form Submit Instant Feedback State
            const loginForm = document.querySelector('form');
            const submitBtn = document.querySelector('.btn-submit');
            if (loginForm && submitBtn) {
                loginForm.addEventListener('submit', function() {
                    submitBtn.disabled = true;
                    submitBtn.style.opacity = '0.85';
                    submitBtn.style.pointerEvents = 'none';
                    submitBtn.innerHTML = '<span style="display:inline-block;width:15px;height:15px;border:2px solid #ffffff;border-top-color:transparent;border-radius:50%;animation:spin 0.6s linear infinite;margin-right:8px;vertical-align:middle;"></span>Memproses...';
                });
            }
        });
    </script>
</body>

</html>
