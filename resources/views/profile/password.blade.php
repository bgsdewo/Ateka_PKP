@extends('layouts.app')

@section('page-header')
    <div class="iq-navbar-header" style="height: 220px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="flex-wrap d-flex justify-content-between align-items-center text-white">
                        <div>
                            <h1 class="text-white fw-bold mb-1">Ubah Password</h1>
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

        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="header-title">
                            <h4 class="card-title mb-1">Form Ubah Password</h4>
                            <p class="mb-0 text-muted">Gunakan password baru yang kuat untuk menjaga keamanan akun.</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('profile.password.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label">Password Lama <span class="text-danger">*</span></label>
                                <input type="password" name="current_password"
                                    class="form-control @error('current_password') is-invalid @enderror"
                                    placeholder="Masukkan password lama" required>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password Baru <span class="text-danger">*</span></label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Masukkan password baru" required>
                                <small class="text-muted">Minimal 8 karakter, gunakan kombinasi huruf besar, huruf kecil,
                                    angka, dan simbol.</small>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Konfirmasi Password Baru <span
                                        class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control"
                                    placeholder="Ulangi password baru" required>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <button type="reset" class="btn btn-soft-secondary">Reset</button>
                                <button type="submit" class="btn btn-primary">Update Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
