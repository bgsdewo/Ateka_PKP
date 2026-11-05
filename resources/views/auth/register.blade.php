@extends('layouts.auth')

@section('title', 'Pendaftaran Akun')
@section('card_class', 'au-wide')
@section('icon', 'au-t-userplus')
@section('heading', 'Pendaftaran akun pegawai')
@section('subheading', 'Isi data berikut sesuai identitas kepegawaian Anda.')
@section('error_title', 'Pendaftaran belum berhasil:')

@section('content')
    <form action="{{ route('register.store') }}" method="POST" data-loading-text="Mendaftarkan...">
        @csrf
        <input type="hidden" name="role" value="pegawai">

        <div class="au-grid">
            <div class="au-field">
                <label for="name" class="au-label">Nama lengkap <span class="au-req">*</span></label>
                <div class="au-inwrap au-i-user"><input type="text" id="name" name="name" class="au-input" value="{{ old('name') }}"
                    placeholder="Sesuai identitas pegawai" required autofocus autocomplete="name"></div>
            </div>
            <div class="au-field">
                <label for="email" class="au-label">Email dinas <span class="au-req">*</span></label>
                <div class="au-inwrap au-i-mail"><input type="email" id="email" name="email" class="au-input" value="{{ old('email') }}"
                    placeholder="nama@pkp.go.id" required autocomplete="email"></div>
            </div>
            <div class="au-field">
                <label for="nip" class="au-label">NIP <span class="au-req">*</span></label>
                <div class="au-inwrap au-i-id"><input type="text" id="nip" name="nip" class="au-input au-mono" value="{{ old('nip') }}"
                    placeholder="18 digit NIP" required inputmode="numeric"></div>
            </div>
            <div class="au-field">
                <label for="title" class="au-label">Gelar <span class="au-opt">(opsional)</span></label>
                <div class="au-inwrap au-i-award"><input type="text" id="title" name="title" class="au-input" value="{{ old('title', old('gelar')) }}"
                    placeholder="mis. S.Kom., M.Si."></div>
            </div>
            <div class="au-field">
                <label for="password" class="au-label">Kata sandi <span class="au-req">*</span></label>
                <div class="au-pass au-inwrap au-i-lock">
                    <input type="password" id="password" name="password" class="au-input"
                        placeholder="Minimal 6 karakter" required autocomplete="new-password">
                    <button type="button" class="au-eye" data-toggle-password="password" aria-label="Tampilkan atau sembunyikan kata sandi" tabindex="-1"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path><circle cx="12" cy="12" r="3"></circle></svg></button>
                </div>
            </div>
            <div class="au-field">
                <label for="password_confirmation" class="au-label">Ulangi kata sandi <span class="au-req">*</span></label>
                <div class="au-pass au-inwrap au-i-lock">
                    <input type="password" id="password_confirmation" name="password_confirmation" class="au-input"
                        placeholder="Ketik ulang kata sandi" required autocomplete="new-password">
                    <button type="button" class="au-eye" data-toggle-password="password_confirmation" aria-label="Tampilkan atau sembunyikan ulang kata sandi" tabindex="-1"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path><circle cx="12" cy="12" r="3"></circle></svg></button>
                </div>
            </div>
        </div>

        <button type="submit" class="au-btn">Daftar</button>

        <p class="au-alt">Sudah punya akun? <a href="{{ route('login') }}" class="au-link">Masuk</a></p>
    </form>
@endsection
