<!doctype html>
<html lang="id" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Registrasi Akun | Sistem Inventori ATK</title>
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

        .role-radio-card {
            border: 1.5px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 0.9rem;
            cursor: pointer;
            transition: all 0.2s ease;
            background-color: #ffffff;
            height: 100%;
        }

        .role-radio-card:hover {
            border-color: #144b59;
            background-color: #f8fafc;
        }

        .role-radio-card.active {
            border-color: #144b59;
            background-color: #f0f7f8;
            box-shadow: 0 4px 12px rgba(20, 75, 89, 0.1);
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
                                    <h4 class="font-weight-bold text-theme mb-1">Pendaftaran Akun Baru</h4>
                                    <p class="text-muted small mb-0">Sistem Pencatatan Persediaan & Pengajuan Alat Tulis Kantor (ATK)</p>
                                </div>

                                <!-- Alert Kesalahan -->
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

                                <!-- Form Registrasi -->
                                <form action="{{ route('register.store') }}" method="POST">
                                    @csrf

                                    <div class="row g-3">
                                        <!-- Nama Lengkap -->
                                        <div class="col-md-6">
                                            <div class="form-group mb-2">
                                                <label class="form-label font-weight-bold text-theme small text-uppercase">
                                                    Nama Lengkap <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" name="name" class="form-control"
                                                    value="{{ old('name') }}" placeholder="Contoh: Budi Prasetyo" required autofocus>
                                            </div>
                                        </div>

                                        <!-- Email Dinas / Pribadi -->
                                        <div class="col-md-6">
                                            <div class="form-group mb-2">
                                                <label class="form-label font-weight-bold text-theme small text-uppercase">
                                                    Alamat Email <span class="text-danger">*</span>
                                                </label>
                                                <input type="email" name="email" class="form-control"
                                                    value="{{ old('email') }}" placeholder="nama@perumahan.go.id" required>
                                            </div>
                                        </div>

                                        <!-- Nomor Induk Pegawai (NIP) -->
                                        <div class="col-12">
                                            <div class="form-group mb-2">
                                                <label class="form-label font-weight-bold text-theme small text-uppercase">
                                                    Nomor Induk Pegawai (NIP) <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" name="nip" id="nip" class="form-control"
                                                    value="{{ old('nip') }}" placeholder="Contoh: 19950510 202012 1 002" required>
                                                <small class="text-muted" style="font-size: 0.78rem;">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1 text-primary">
                                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                                    </svg>
                                                    NIP digunakan untuk identitas dinas & verifikasi keamanan saat <strong>lupa password</strong>.
                                                </small>
                                            </div>
                                        </div>

                                        <!-- Pilihan Role (Hanya Subdit dan Pegawai) -->
                                        <div class="col-12">
                                            <label class="form-label font-weight-bold text-theme small text-uppercase mb-2">
                                                Pilih Jenis Pemohon (Role) <span class="text-danger">*</span>
                                            </label>
                                            <div class="row g-3">
                                                <!-- Opsi 1: Pegawai (Individu) -->
                                                <div class="col-md-6 col-12">
                                                    <label class="role-radio-card d-block {{ (old('role') == 'pegawai' || !old('role')) ? 'active' : '' }}">
                                                        <div class="d-flex align-items-start gap-2">
                                                            <input type="radio" name="role" value="pegawai" class="mt-1" {{ (old('role') == 'pegawai' || !old('role')) ? 'checked' : '' }} required>
                                                            <div>
                                                                <div class="d-flex align-items-center gap-1.5">
                                                                    <strong class="text-dark">Pegawai</strong>
                                                                    <span class="badge bg-secondary text-white" style="font-size: 0.65rem;">Pemohon Individu</span>
                                                                </div>
                                                                <small class="text-muted d-block mt-1" style="font-size: 0.78rem;">
                                                                    Untuk staf / pegawai perorangan yang mengajukan kebutuhan ATK harian operasional.
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>

                                                <!-- Opsi 2: Subdirektorat (Kolektif) -->
                                                <div class="col-md-6 col-12">
                                                    <label class="role-radio-card d-block {{ old('role') == 'subdit' ? 'active' : '' }}">
                                                        <div class="d-flex align-items-start gap-2">
                                                            <input type="radio" name="role" value="subdit" class="mt-1" {{ old('role') == 'subdit' ? 'checked' : '' }}>
                                                            <div>
                                                                <div class="d-flex align-items-center gap-1.5">
                                                                    <strong class="text-dark">Subdirektorat</strong>
                                                                    <span class="badge bg-info text-white" style="font-size: 0.65rem;">Pemohon Kolektif</span>
                                                                </div>
                                                                <small class="text-muted d-block mt-1" style="font-size: 0.78rem;">
                                                                    Mewakili unit kerja / Subdirektorat untuk mengajukan permohonan ATK kolektif tim.
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="mt-2.5 text-muted small d-flex align-items-center gap-1.5">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-primary flex-shrink-0">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                                </svg>
                                                <span class="fst-italic" style="font-size: 0.78rem;">
                                                    Akun struktural <strong>Kasubag TU</strong>, <strong>Arsiparis</strong>, dan <strong>PIC Distribusi</strong> ditambahkan langsung oleh Kasubag TU melalui <strong>Manajemen Pengguna</strong>.
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Opsi Khusus Subdirektorat (Dropdown Muncul Otomatis saat Subdit dipilih) -->
                                        <div class="col-12" id="subdit-choice-container" style="display: {{ old('role') === 'subdit' ? 'block' : 'none' }};">
                                            <div class="form-group mb-2">
                                                <label class="form-label font-weight-bold text-theme small text-uppercase">
                                                    Pilih Subdirektorat / Unit Kerja <span class="text-danger">*</span>
                                                </label>
                                                <select name="subdit_choice" id="subdit_choice" class="form-select">
                                                    <option value="" disabled {{ old('subdit_choice') ? '' : 'selected' }}>-- Pilih Unit Subdirektorat --</option>
                                                    <option value="Subdirektorat SDK" {{ old('subdit_choice') == 'Subdirektorat SDK' ? 'selected' : '' }}>
                                                        Subdirektorat SDK (Sumber Daya Konstruksi Perumahan dan Kawasan Pemukiman)
                                                    </option>
                                                    <option value="Subdirektorat PJF" {{ old('subdit_choice') == 'Subdirektorat PJF' ? 'selected' : '' }}>
                                                        Subdirektorat PJF (Pengelolaan Jabatan Fungsional Perumahan dan Pengembangan Profesi)
                                                    </option>
                                                    <option value="Subdirektorat KBR" {{ old('subdit_choice') == 'Subdirektorat KBR' ? 'selected' : '' }}>
                                                        Subdirektorat KBR (Kendala Bangunan Rumah)
                                                    </option>
                                                    <option value="Subbagian TU" {{ old('subdit_choice') == 'Subbagian TU' ? 'selected' : '' }}>
                                                        Subbagian TU (Tata Usaha)
                                                    </option>
                                                </select>
                                                <small class="text-muted" style="font-size: 0.78rem;">Pilih unit Subdirektorat resmi yang Anda wakili.</small>
                                            </div>
                                        </div>

                                        <!-- Jabatan Khusus Pegawai (Muncul saat Pegawai dipilih) -->
                                        <div class="col-12" id="pegawai-position-container" style="display: {{ (old('role') === 'pegawai' || !old('role')) ? 'block' : 'none' }};">
                                            <div class="form-group mb-2">
                                                <label class="form-label font-weight-bold text-theme small text-uppercase">
                                                    Jabatan / Posisi Kerja <span class="text-muted font-normal">(Opsional)</span>
                                                </label>
                                                <input type="text" name="position" id="position" class="form-control"
                                                    value="{{ old('position') }}" placeholder="Contoh: Staf Keuangan / Analis Kebijakan / Bagian Umum">
                                                <small class="text-muted" style="font-size: 0.78rem;">Isi dengan posisi jabatan dinas Anda.</small>
                                            </div>
                                        </div>

                                        <!-- Password -->
                                        <div class="col-md-6">
                                            <div class="form-group mb-2">
                                                <label class="form-label font-weight-bold text-theme small text-uppercase">
                                                    Password <span class="text-danger">*</span>
                                                </label>
                                                <input type="password" name="password" class="form-control"
                                                    placeholder="Minimal 6 karakter" required>
                                            </div>
                                        </div>

                                        <!-- Konfirmasi Password -->
                                        <div class="col-md-6">
                                            <div class="form-group mb-2">
                                                <label class="form-label font-weight-bold text-theme small text-uppercase">
                                                    Ulangi Password <span class="text-danger">*</span>
                                                </label>
                                                <input type="password" name="password_confirmation" class="form-control"
                                                    placeholder="Ketik ulang password" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-grid mt-4 mb-3">
                                        <button type="submit" class="btn btn-primary btn-block shadow-sm">
                                            Daftarkan Akun
                                        </button>
                                    </div>

                                    <div class="text-center pt-2">
                                        <span class="text-muted small">Sudah memiliki akun terdaftar?</span>
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
            const roleInputs = document.querySelectorAll('input[name="role"]');
            const roleCards = document.querySelectorAll('.role-radio-card');
            const subditContainer = document.getElementById('subdit-choice-container');
            const pegawaiContainer = document.getElementById('pegawai-position-container');
            const subditSelect = document.getElementById('subdit_choice');

            function updateRoleUI(selectedRole) {
                if (selectedRole === 'subdit') {
                    subditContainer.style.display = 'block';
                    pegawaiContainer.style.display = 'none';
                    if (subditSelect) subditSelect.setAttribute('required', 'required');
                } else {
                    subditContainer.style.display = 'none';
                    pegawaiContainer.style.display = 'block';
                    if (subditSelect) subditSelect.removeAttribute('required');
                }
            }

            roleInputs.forEach(input => {
                input.addEventListener('change', function() {
                    roleCards.forEach(c => c.classList.remove('active'));
                    if (this.checked) {
                        this.closest('.role-radio-card').classList.add('active');
                        updateRoleUI(this.value);
                    }
                });
            });

            // Jalankan inisialisasi awal sesuai radio yang aktif
            const activeRadio = document.querySelector('input[name="role"]:checked');
            if (activeRadio) {
                updateRoleUI(activeRadio.value);
            }
        });
    </script>
</body>
</html>
