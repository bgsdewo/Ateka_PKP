@extends('layouts.app')

@section('page-header')
    <style>
        .iq-navbar-header {
            position: relative;
        }

        .iq-navbar-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 165px;
            /* sesuaikan */
            background: rgba(0, 70, 80, 0.35);
            z-index: 1;
        }

        .iq-navbar-header .iq-container {
            position: relative;
            z-index: 2;
        }
    </style>
    <div class="iq-navbar-header" style="height: 215px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="flex-wrap d-flex justify-content-between align-items-center">
                        <div>
                            <h1>Profil Pengguna</h1>
                            <p>Kelola informasi akun pengguna aplikasi pencatatan stok.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="iq-header-img">
            <img src="{{ asset('hope-ui/assets/images/dashboard/10.png') }}" alt="header"
                class="theme-color-default-img img-fluid w-90 h-90 animated-scaleX">
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">

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
                'kasubag_tu', 'admin' => 'Kasubag TU',
                'pic_distribusi' => 'PIC Distribusi',
                'pegawai_gudang' => 'Pegawai Gudang',
                default => 'Pegawai',
            };
            $roleSubtitle = match ($role) {
                'kasubag_tu', 'admin' => $user->position ?: 'Kasubag TU (Approver)',
                'pic_distribusi' => $user->position ?: ('PIC Distribusi' . ($user->subdit_name ? ' - ' . $user->subdit_name : '')),
                'pegawai_gudang' => $user->position ?: 'Pegawai Gudang',
                default => $user->position ?: 'Pegawai',
            };
        @endphp

        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-inline-flex align-items-center justify-content-center border border-primary-subtle mb-3"
                            style="width: 90px; height: 90px; font-size: 2rem;">
                            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                        </div>
                        <h4 class="mb-1">{{ $user->name }}</h4>
                        <p class="text-muted mb-2">{{ $roleSubtitle }}</p>
                        <span class="badge rounded-pill bg-primary">Aktif</span>
                        <hr>
                        <div class="text-start">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Email</span>
                                <span class="fw-semibold text-end">{{ $user->email }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Role</span>
                                <span class="fw-semibold text-end">{{ $roleLabel }}</span>
                            </div>
                            @if ($user->position)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Jabatan</span>
                                    <span class="fw-semibold text-end">{{ $user->position }}</span>
                                </div>
                            @endif
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Status Akun</span>
                                <span class="fw-semibold">Aktif</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Terdaftar</span>
                                <span
                                    class="fw-semibold">{{ $user->created_at ? $user->created_at->format('d/m/Y') : '-' }}</span>
                            </div>
                        </div>
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
