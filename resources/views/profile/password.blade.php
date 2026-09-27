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
                            <h1>Ubah Password</h1>
                            <p>Perbarui password akun untuk menjaga keamanan akses sistem.</p>
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

        <div class="row">
            <div class="col-lg-6">
                <div class="card" data-aos="fade-up" data-aos-delay="500">
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
