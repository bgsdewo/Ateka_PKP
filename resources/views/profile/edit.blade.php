@extends('layouts.app')

@section('page-header')
    <div class="iq-navbar-header" style="height: 220px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="flex-wrap d-flex justify-content-between align-items-center text-white">
                        <div>
                            <h1 class="text-white fw-bold mb-1">Profil Pengguna</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <style>
        .ateka-profile .card { border: 1px solid #dfe8e3; border-radius: 18px; box-shadow: 0 1px 2px rgba(12, 43, 39, .05), 0 14px 30px -18px rgba(12, 43, 39, .22); }
        .ateka-profile .card-header { background: transparent; border-bottom: 1px solid #e6eeea; padding: 1.1rem 1.5rem; }
        .ateka-profile .card-body { padding: 1.4rem 1.5rem; }
        .ateka-profile .card-title { font-weight: 800; color: #0c2b27; }
        .ateka-profile .form-label { font-weight: 700; font-size: .8rem; color: #2b3d34; letter-spacing: .01em; }
        .ateka-profile .form-control[readonly] { background: #f2f6f4; color: #4b5f56; }
        .ateka-profile small.text-muted { font-size: .76rem; }
        .ateka-avatar { width: 92px; height: 92px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 2.2rem; font-weight: 800; color: #fff; background: var(--brand-gradient, linear-gradient(135deg, #0c2b27, #1e5a4f)); border: 4px solid #eaf3f0; box-shadow: 0 10px 24px -10px rgba(12, 43, 39, .65); }
        .ateka-pill { display: inline-block; padding: .28rem .8rem; border-radius: 999px; font-size: .74rem; font-weight: 700; background: #eaf3f0; color: #0c2b27; border: 1px solid #cfe3d8; }
        .ateka-pill.is-active::before { content: ""; display: inline-block; width: 7px; height: 7px; margin-right: 6px; border-radius: 50%; background: #10b981; vertical-align: 1px; }
        .ateka-info-row { display: flex; justify-content: space-between; align-items: baseline; gap: 14px; padding: .7rem 0; border-bottom: 1px solid #e9f0ec; font-size: .88rem; }
        .ateka-info-row:last-child { border-bottom: 0; padding-bottom: 0; }
        .ateka-info-row span:first-child { color: #6a7d74; flex: 0 0 auto; }
        .ateka-info-row span:last-child { font-weight: 700; color: #14231c; text-align: right; word-break: break-word; }
        .ateka-profile .btn-outline-primary { color: #0c2b27; border-color: #bcd3c8; background: #fff; font-weight: 700; }
        .ateka-profile .btn-outline-primary:hover { background: #eaf3f0; border-color: #0c2b27; color: #0c2b27; }
        .ateka-profile .btn-soft-secondary { background: #eef3f0; color: #2b3d34; border-color: transparent; font-weight: 600; }
        .ateka-profile .btn-soft-secondary:hover { background: #e0e9e4; color: #14231c; }
    </style>
    <div class="container-fluid content-inner mt-n5 py-0 ateka-profile">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @php
            $role = $user->role ?? 'pegawai';
            $roleLabel = match ($role) {
                'kasubag_tu', 'admin' => 'Kasubbag TU',
                'pic_distribusi' => $user->pic_role_title ?? 'PIC ( SDK )',
                'pegawai_gudang' => 'Pegawai Gudang',
                default => 'Pegawai',
            };
            $roleSubtitle = match ($role) {
                'kasubag_tu', 'admin' => $user->position ?: 'Kasubbag TU (Approver)',
                'pic_distribusi' => $user->position ?: ('PIC Distribusi' . ($user->subdit_name ? ' - ' . $user->subdit_name : '')),
                'pegawai_gudang' => $user->position ?: 'Pegawai Gudang',
                default => $user->position ?: 'Pegawai',
            };
        @endphp

        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="ateka-avatar mb-3">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</div>
                        <h4 class="mb-1 fw-bold">{{ $user->name }}</h4>
                        <p class="text-muted mb-2">{{ $roleSubtitle }}</p>
                        <span class="ateka-pill is-active">Akun Aktif</span>
                        <div class="text-start mt-4">
                            <div class="ateka-info-row"><span>Email</span><span>{{ $user->email }}</span></div>
                            <div class="ateka-info-row"><span>Role</span><span>{{ $roleLabel }}</span></div>
                            @if ($user->position)
                                <div class="ateka-info-row"><span>Jabatan</span><span>{{ $user->position }}</span></div>
                            @endif
                            <div class="ateka-info-row"><span>Terdaftar</span><span>{{ $user->created_at ? $user->created_at->format('d/m/Y') : '-' }}</span></div>
                        </div>
                        <a href="{{ route('profile.password') }}" class="btn btn-outline-primary w-100 mt-4">Ubah Password</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="header-title">
                            <h4 class="card-title mb-1">Informasi Profil</h4>
                            <p class="mb-0 text-muted">Perbarui informasi akun pengguna yang sedang login.</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $user->name) }}" placeholder="Masukkan nama lengkap" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', $user->email) }}" placeholder="Masukkan email" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Role</label>
                                    <input type="text" class="form-control bg-light" value="{{ $roleSubtitle }}" readonly>
                                    <small class="text-muted">Peran dan hak akses disesuaikan dengan akun pengguna yang aktif.</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status Akun</label>
                                    <input type="text" class="form-control bg-light" value="Aktif" readonly>
                                    <small class="text-muted">Status ditampilkan sebagai informasi akun aktif.</small>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <button type="reset" class="btn btn-soft-secondary">Reset</button>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
