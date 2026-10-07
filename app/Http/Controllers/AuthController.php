<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return response()
            ->view('auth.login')
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))->with('success', 'Login berhasil.');
        }
        return back()->withInput($request->only('email'))->with('error', 'Email atau password salah.');
    }
    public function register()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard')->with('info', 'Anda sudah masuk sebagai ' . Auth::user()->name . '.');
        }

        return response()
            ->view('auth.register')
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');
    }

    public function storeRegister(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard')->with('info', 'Anda sudah masuk sebagai ' . Auth::user()->name . '.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:50'],
            'gelar' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'nip' => ['required', 'string', 'max:50'],
            'role' => ['required', 'string', 'in:pegawai,subdit'],
            'subdit_choice' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email dinas/kantor wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Alamat email ini sudah terdaftar. Silakan masuk (login) dengan akun tersebut atau gunakan email dinas lain.',
            'nip.required' => 'Nomor Induk Pegawai (NIP) wajib diisi untuk keamanan akun.',
            'role.required' => 'Silakan pilih peran (role) Anda.',
            'role.in' => 'Pilihan role pendaftaran hanya untuk Pegawai atau Subdirektorat.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $titleInput = $validated['title'] ?? ($validated['gelar'] ?? null);

        if ($validated['role'] === 'subdit') {
            $subditChoice = $request->input('subdit_choice');
            if (empty($subditChoice)) {
                return back()->withInput()->withErrors(['subdit_choice' => 'Silakan pilih unit Subdirektorat Anda.']);
            }
            $position = $subditChoice;
            $subditName = match ($subditChoice) {
                'Subdirektorat SDK' => 'Subdirektorat Sumber Daya Konstruksi Perumahan dan Kawasan Permukiman',
                'Subdirektorat PJF' => 'Subdirektorat Pengelolaan Jabatan Fungsional Perumahan dan Pengembangan Profesi',
                'Subdirektorat KBR' => 'Subdirektorat Kendala Bangunan Rumah',
                'Subbagian TU' => 'Subbagian Tata Usaha',
                default => $subditChoice,
            };
        } else {
            $position = $validated['position'] ?: 'Pegawai Staf';
            $subditName = 'Pegawai Perorangan';
        }

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'title' => $titleInput ? trim($titleInput) : null,
            'email' => $validated['email'],
            'nip' => $validated['nip'],
            'role' => $validated['role'],
            'position' => $position,
            'subdit_name' => $subditName,
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
        ]);

        // Login otomatis dan arahkan ke dashboard
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Pendaftaran akun berhasil! Selamat datang, ' . $user->name . '.');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function resetPasswordWithNip(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'nip' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'nip.required' => 'Nomor Induk Pegawai (NIP) wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withInput($request->only('email', 'nip'))->with('error', 'Akun dengan alamat email tersebut tidak ditemukan.');
        }

        // Bandingkan NIP dengan fleksibilitas pembersihan karakter non-alfanumerik
        $cleanInputNip = preg_replace('/[^a-zA-Z0-9]/', '', $request->nip);
        $cleanUserNip = preg_replace('/[^a-zA-Z0-9]/', '', $user->nip ?? '');

        if (empty($user->nip) || ($cleanInputNip !== $cleanUserNip && $user->nip !== $request->nip)) {
            return back()->withInput($request->only('email', 'nip'))->with('error', 'Nomor Induk Pegawai (NIP) tidak cocok dengan data akun terdaftar.');
        }

        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        return redirect()->route('login')->with('success', 'Password berhasil diubah! Silakan masuk dengan kata sandi baru Anda.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Logout berhasil.');
    }
}
