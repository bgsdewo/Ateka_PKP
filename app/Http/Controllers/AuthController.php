<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
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
        return view('auth.register');
    }

    public function storeRegister(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'string', 'in:pegawai,subdit'],
            'position' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email dinas/kantor wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'role.required' => 'Silakan pilih peran (role) Anda.',
            'role.in' => 'Pilihan role pendaftaran hanya untuk Pegawai atau Subdirektorat.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Berikan posisi / jabatan default yang representatif jika dikosongkan
        $position = $validated['position'];
        if (empty($position)) {
            $position = match ($validated['role']) {
                'subdit' => 'Perwakilan Subdirektorat',
                default => 'Pegawai Staf',
            };
        }

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'position' => $position,
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
        ]);

        // Login otomatis dan arahkan ke dashboard
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Pendaftaran akun berhasil! Selamat datang, ' . $user->name . '.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Logout berhasil.');
    }
}
