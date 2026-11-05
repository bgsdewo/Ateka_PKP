@extends('layouts.auth')

@section('title', 'Masuk')
@section('icon', 'au-t-login')
@section('heading', 'Masuk')
@section('subheading', 'Gunakan akun ATEKA Anda untuk melanjutkan.')
@section('error_title', 'Tidak dapat masuk:')

@section('content')
    <form action="{{ route('login.process') }}" method="POST" data-loading-text="Memproses...">
        @csrf
        <div class="au-field">
            <label for="email" class="au-label">Email atau NIP</label>
            <div class="au-inwrap au-i-user"><input type="text" id="email" name="email" class="au-input" value="{{ old('email') }}"
                placeholder="nama@pkp.go.id atau NIP" required autocomplete="username" autofocus></div>
        </div>

        <div class="au-field">
            <label for="password" class="au-label">Kata sandi</label>
            <div class="au-pass au-inwrap au-i-lock">
                <input type="password" id="password" name="password" class="au-input"
                    placeholder="Masukkan kata sandi" required autocomplete="current-password">
                <button type="button" class="au-eye" data-toggle-password="password" aria-label="Tampilkan atau sembunyikan kata sandi" tabindex="-1"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path><circle cx="12" cy="12" r="3"></circle></svg></button>
            </div>
        </div>

        <div class="au-row">
            <a href="{{ route('password.forgot') }}" class="au-link">Lupa kata sandi?</a>
        </div>

        <button type="submit" class="au-btn">Masuk</button>

        <p class="au-alt">Belum punya akun? <a href="{{ route('register') }}" class="au-link">Daftar sebagai pegawai</a></p>
    </form>
@endsection
