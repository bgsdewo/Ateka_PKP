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
        $loginInput = $request->input('email');
        $isEmail = filter_var($loginInput, FILTER_VALIDATE_EMAIL);

        $rules = [
            'email' => ['required'],
            'password' => ['required'],
        ];
        if ($isEmail) {
            $rules['email'][] = 'email';
        }

        $request->validate($rules, [
            'email.required' => 'Email atau NIP wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $credentials = [
            $isEmail ? 'email' : 'nip' => $loginInput,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))->with('success', 'Login berhasil.');
        }

        return back()->withInput($request->only('email'))->with('error', 'Email/NIP atau password salah.');
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
            'title' => ['required', 'string', 'max:50'],
            'gelar' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'nip' => ['required', 'string', 'max:50', 'unique:users,nip'],
            'role' => ['nullable', 'string', 'in:pegawai'],
            'position' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email dinas/kantor wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Alamat email ini sudah terdaftar. Silakan masuk (login) dengan akun tersebut atau gunakan email dinas lain.',
            'title.required' => 'Gelar akademik wajib diisi.',
            'nip.required' => 'Nomor Induk Pegawai (NIP) wajib diisi untuk keamanan akun.',
            'nip.unique' => 'Nomor Induk Pegawai (NIP) ini sudah terdaftar dalam sistem.',
            'role.in' => 'Pilihan role pendaftaran hanya untuk Pegawai.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $titleInput = $validated['title'] ?? ($validated['gelar'] ?? null);
        $position = !empty($validated['position']) ? $validated['position'] : 'Pegawai Staf';
        $subditName = 'Pegawai Perorangan';

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'title' => $titleInput ? trim($titleInput) : null,
            'email' => $validated['email'],
            'nip' => $validated['nip'],
            'role' => 'pegawai',
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
            'nip' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'nip.required' => 'Nomor Induk Pegawai (NIP) wajib diisi.',
            'email.required' => 'Alamat email dinas terdaftar wajib diisi.',
            'email.email' => 'Format email dinas tidak valid.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $cleanInputNip = preg_replace('/[^a-zA-Z0-9]/', '', $request->nip);
        $inputEmail = strtolower(trim($request->email));

        // Cari akun yang cocok dengan kombinasi Email dan NIP secara bersamaan
        $user = \App\Models\User::whereRaw('LOWER(email) = ?', [$inputEmail])
            ->where(function ($q) use ($request, $cleanInputNip) {
                $q->where('nip', $request->nip)
                  ->orWhereRaw("REPLACE(REPLACE(nip, ' ', ''), '-', '') = ?", [$cleanInputNip]);
            })
            ->first();

        // Fallback pencarian fleksibel jika format spasi/simbol NIP di DB berbeda
        if (!$user) {
            $user = \App\Models\User::whereRaw('LOWER(email) = ?', [$inputEmail])->get()->first(function ($u) use ($cleanInputNip, $request) {
                if (empty($u->nip)) {
                    return false;
                }
                $cleanUserNip = preg_replace('/[^a-zA-Z0-9]/', '', $u->nip);
                return $cleanUserNip === $cleanInputNip || $u->nip === $request->nip;
            });
        }

        if (!$user) {
            return back()->withInput($request->only('nip', 'email'))
                ->with('error', 'Kombinasi NIP dan Email dinas tidak sesuai dengan data pengguna manapun.');
        }

        // KEAMANAN TINGKAT TINGGI: Akun Administrator / Kasubbag TU dilarang reset mandiri melalui formulir publik
        if ($user->isKasubagTU() || $user->isAdmin()) {
            return back()->withInput($request->only('nip', 'email'))
                ->with('error', 'Demi perlindungan keamanan sistem instansi, akun Administrator / Kasubbag TU tidak dapat direset melalui formulir publik. Silakan hubungi admin server internal.');
        }

        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        return redirect()->route('login')
            ->with('success', 'Password berhasil diperbarui untuk akun ' . $user->name . '! Silakan masuk dengan kata sandi baru.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Logout berhasil.');
    }
}
