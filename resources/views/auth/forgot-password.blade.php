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
            max-width: 1040px;
            background-color: var(--card-dark);
            border-radius: 32px;
            box-shadow: 0 35px 80px -15px rgba(0, 0, 0, 0.65), 0 0 0 1px rgba(255, 255, 255, 0.08);
            overflow: hidden;
            position: relative;
            display: flex;
            min-height: 590px;
        }

        /* Sisi Kiri: Panel Putih Bersih */
        .panel-brand-left {
            background-color: #ffffff;
            color: #1e293b;
            flex: 1.05;
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

        /* Sisi Kanan: Panel Form Reset Password */
        .panel-form-right {
            background-color: var(--card-dark);
            color: #ffffff;
            flex: 1.05;
            padding: 3rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            z-index: 4;
        }

        .form-header {
            margin-bottom: 1.5rem;
        }

        .form-title {
            font-size: 2.1rem;
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

        /* Form Controls */
        .form-group {
            margin-bottom: 1.15rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px 14px;
            margin-bottom: 1.15rem;
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
            padding: 0.7rem 1.2rem;
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
            font-size: 0.83rem;
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
            margin-top: 0.4rem;
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

        .back-callout {
            text-align: center;
            margin-top: 1.4rem;
            font-size: 0.85rem;
        }

        .link-back {
            color: var(--accent-mint);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .link-back:hover {
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
    <!-- Main Master Reset Password Card -->
    <div class="auth-master-card">
        <!-- ============================================================= -->
        <!-- SISI KIRI: IDENTITAS RESMI & ILUSTRASI KEAMANAN AKUN          -->
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

            <!-- Bagian Tengah: Visual Keamanan & Pemulihan Akun -->
            <div class="brand-illustration-wrapper">
                <svg class="brand-illustration-svg" viewBox="0 0 360 260" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Podium / Landasan Halus -->
                    <ellipse cx="180" cy="225" rx="140" ry="20" fill="#e8f4f0" />
                    <ellipse cx="180" cy="223" rx="110" ry="14" fill="#d3ebe2" />

                    <!-- Perisai Perlindungan Akun / Security Shield -->
                    <path d="M180 50L245 78V140C245 185 180 215 180 215C180 215 115 185 115 140V78L180 50Z" fill="#144b59"/>
                    <path d="M180 62L233 85V138C233 175 180 201 180 201C180 201 127 175 127 138V85L180 62Z" fill="#185666"/>
                    <path d="M180 62V201C180 201 233 175 233 138V85L180 62Z" fill="#ffffff" opacity="0.08"/>

                    <!-- Gembok / Padlock Emas Terverifikasi di Tengah Perisai -->
                    <rect x="160" y="125" width="40" height="32" rx="7" fill="#f59e0b"/>
                    <path d="M168 125V112C168 105.373 173.373 100 180 100C186.627 100 192 105.373 192 112V125" stroke="#ffffff" stroke-width="4.5" stroke-linecap="round"/>
                    <circle cx="180" cy="138" r="4" fill="#78350f"/>
                    <path d="M180 142V148" stroke="#78350f" stroke-width="2.5" stroke-linecap="round"/>

                    <!-- Badge Centang Hijau Pemulihan Sukses -->
                    <circle cx="218" cy="182" r="14" fill="#10b981"/>
                    <circle cx="218" cy="182" r="11" fill="#059669"/>
                    <path d="M212 182L216 186L224 178" stroke="#ffffff" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>

                <div class="brand-caption">
                    <h2>Pemulihan Kata Sandi Akun</h2>
                    <p>Layanan mandiri pembaruan kata sandi bagi pegawai Kementerian PKP RI secara aman dan terverifikasi.</p>
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
        <!-- SISI KANAN: PANEL FORM RESET PASSWORD                         -->
        <!-- ============================================================= -->
        <div class="panel-form-right">
            <div class="form-header">
                <h2 class="form-title">Reset Password</h2>
                <p class="form-subtitle">Pembaruan Kata Sandi &bull; Sistem Inventori ATK (ATEKA)</p>
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
                    <strong>Terdapat kendala:</strong>
                    <ul>
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
                <div class="form-group">
                    <label for="nip" class="form-label">Nomor Induk Pegawai (NIP) <span class="req">*</span></label>
                    <input type="text" name="nip" id="nip" class="input-pill font-mono"
                        value="{{ old('nip') }}" placeholder="19950510 202012 1 002" required autofocus>
                </div>

                <!-- Input Email Dinas -->
                <div class="form-group">
                    <label for="email" class="form-label">Email Dinas Terdaftar <span class="req">*</span></label>
                    <input type="email" name="email" id="email" class="input-pill"
                        value="{{ old('email') }}" placeholder="nama.pegawai@pkp.go.id" required autocomplete="email">
                </div>

                <!-- Password Baru & Konfirmasi (Side by Side di Desktop) -->
                <div class="form-grid">
                    <div class="form-group">
                        <label for="password" class="form-label">Password Baru <span class="req">*</span></label>
                        <div class="password-wrap">
                            <input type="password" name="password" id="password" class="input-pill"
                                placeholder="Minimal 6 karakter" required minlength="6" autocomplete="new-password">
                            <button type="button" class="password-toggle" id="togglePasswordBtn" aria-label="Lihat atau sembunyikan password baru" tabindex="-1">
                                <svg id="togglePasswordIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Ulangi Password <span class="req">*</span></label>
                        <div class="password-wrap">
                            <input type="password" name="password_confirmation" id="password_confirmation" class="input-pill"
                                placeholder="Ketik ulang password baru" required minlength="6" autocomplete="new-password">
                            <button type="button" class="password-toggle" id="toggleConfirmPasswordBtn" aria-label="Lihat atau sembunyikan konfirmasi password" tabindex="-1">
                                <svg id="toggleConfirmPasswordIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="btn-submit" id="submitResetBtn">
                    Simpan &amp; Perbarui Password
                </button>

                <!-- Callout Kembali ke Login -->
                <div class="back-callout">
                    <a href="{{ route('login') }}" class="link-back">
                        &larr; Kembali ke Halaman Login
                    </a>
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
                            icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
                            icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
                            btn.style.color = '#6e9482';
                        }
                    });
                }
            }

            setupPasswordToggle('togglePasswordBtn', 'password', 'togglePasswordIcon');
            setupPasswordToggle('toggleConfirmPasswordBtn', 'password_confirmation', 'toggleConfirmPasswordIcon');

            // Form Submit Instant Feedback State
            const resetForm = document.querySelector('form');
            const submitBtn = document.getElementById('submitResetBtn');
            if (resetForm && submitBtn) {
                resetForm.addEventListener('submit', function() {
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
