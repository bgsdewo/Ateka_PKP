@extends('layouts.auth')

@section('title', 'Atur Ulang Kata Sandi')
@section('card_class', 'au-wide')
@section('heading', 'Atur ulang kata sandi')
@section('subheading', 'Verifikasi dengan NIP dan email dinas yang terdaftar, lalu tetapkan kata sandi baru.')
@section('error_title', 'Kata sandi belum dapat diperbarui:')

@section('content')
    <form action="{{ route('password.reset.nip') }}" method="POST" data-loading-text="Memproses...">
        @csrf
        <div class="au-grid">
            <div class="au-field">
                <label for="nip" class="au-label">NIP <span class="au-req">*</span></label>
                <input type="text" name="nip" id="nip" class="au-input au-mono" value="{{ old('nip') }}"
                    placeholder="18 digit NIP" required autofocus inputmode="numeric">
            </div>
            <div class="au-field">
                <label for="email" class="au-label">Email dinas terdaftar <span class="au-req">*</span></label>
                <input type="email" name="email" id="email" class="au-input" value="{{ old('email') }}"
                    placeholder="nama@pkp.go.id" required autocomplete="email">
            </div>
            <div class="au-field">
                <label for="password" class="au-label">Kata sandi baru <span class="au-req">*</span></label>
                <div class="au-pass">
                    <input type="password" name="password" id="password" class="au-input"
                        placeholder="Minimal 6 karakter" required minlength="6" autocomplete="new-password">
                    <button type="button" class="au-eye" data-toggle-password="password" aria-label="Tampilkan atau sembunyikan kata sandi baru" tabindex="-1"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path><circle cx="12" cy="12" r="3"></circle></svg></button>
                </div>
            </div>
            <div class="au-field">
                <label for="password_confirmation" class="au-label">Ulangi kata sandi <span class="au-req">*</span></label>
                <div class="au-pass">
                    <input type="password" name="password_confirmation" id="password_confirmation" class="au-input"
                        placeholder="Ketik ulang kata sandi baru" required minlength="6" autocomplete="new-password">
                    <button type="button" class="au-eye" data-toggle-password="password_confirmation" aria-label="Tampilkan atau sembunyikan ulang kata sandi" tabindex="-1"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path><circle cx="12" cy="12" r="3"></circle></svg></button>
                </div>
            </div>
        </div>

        <button type="submit" class="au-btn">Simpan kata sandi baru</button>

        <p class="au-alt"><a href="{{ route('login') }}" class="au-link">&larr; Kembali ke halaman masuk</a></p>
    </form>
@endsection
