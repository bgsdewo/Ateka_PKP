<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isKasubagTU()) {
            return redirect()->route('dashboard')->with('error', 'Halaman Manajemen Pengguna hanya dapat diakses oleh Kasubag TU.');
        }

        $users = User::orderBy('name', 'asc')->get();

        return view('master_data.users', compact('users'));
    }

    public function store(Request $request)
    {
        if (!auth()->user() || !auth()->user()->isKasubagTU()) {
            abort(403, 'Akses ditolak: Hanya Kasubag TU yang memiliki hak akses untuk menambah pengguna.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:50'],
            'gelar' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'nip' => [$request->role === 'pegawai_gudang' ? 'nullable' : 'required', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role' => ['required', 'string', 'in:kasubag_tu,pic_distribusi,pegawai,pegawai_gudang'],
            'position' => ['nullable', 'string', 'max:255'],
            'pic_unit_choice' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'nip.required' => 'Nomor Induk Pegawai (NIP) wajib diisi untuk verifikasi identitas dan keamanan lupa password.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
            'role.required' => 'Role pengguna wajib dipilih.',
            'role.in' => 'Pilihan role tidak valid.',
        ]);

        $position = $request->position;
        $subditName = null;

        if ($request->role === 'pic_distribusi') {
            $unit = $request->pic_unit_choice;
            if (str_contains(strtolower($unit ?? ''), 'sdk') || str_contains(strtolower($unit ?? ''), 'sumber daya')) {
                $position = $position ?: 'PIC Distribusi Subdirektorat SDK';
                $subditName = 'Subdirektorat Sumber Daya Konstruksi Perumahan dan Kawasan Permukiman';
            } elseif (str_contains(strtolower($unit ?? ''), 'pjf') || str_contains(strtolower($unit ?? ''), 'fungsional')) {
                $position = $position ?: 'PIC Distribusi Subdirektorat PJF';
                $subditName = 'Subdirektorat Pengelolaan Jabatan Fungsional Perumahan dan Pengembangan Profesi';
            } elseif (str_contains(strtolower($unit ?? ''), 'kbr') || str_contains(strtolower($unit ?? ''), 'kendala')) {
                $position = $position ?: 'PIC Distribusi Subdirektorat KBR';
                $subditName = 'Subdirektorat Kendala Bangunan Rumah';
            } elseif (str_contains(strtolower($unit ?? ''), 'tu') || str_contains(strtolower($unit ?? ''), 'tata usaha')) {
                $position = $position ?: 'PIC Distribusi Subbagian Tata Usaha';
                $subditName = 'Subbagian Tata Usaha';
            } else {
                $position = $position ?: 'PIC Distribusi & Gudang';
                $subditName = 'Subbagian Tata Usaha';
            }
        } elseif ($request->role === 'kasubag_tu') {
            $position = $position ?: 'Kasubag TU';
            $subditName = 'Subbagian Tata Usaha';
        } elseif ($request->role === 'pegawai_gudang') {
            $isDistributor = $request->boolean('is_distributor');
            $position = $position ?: ($isDistributor ? 'Petugas Distribusi Gudang ATK' : 'Pegawai Gudang');
            $subditName = 'Gudang Persediaan ATK';
        } elseif ($request->role === 'pegawai') {
            $subditName = 'Pegawai Perorangan';
        }

        $title = $request->title ?: $request->gelar;

        User::create([
            'name' => $request->name,
            'title' => $title ? trim($title) : null,
            'email' => $request->email,
            'nip' => $request->nip,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_distributor' => ($request->role === 'pegawai_gudang') ? $request->boolean('is_distributor') : false,
            'is_report_examiner' => ($request->role === 'pegawai_gudang') ? $request->boolean('is_report_examiner') : false,
            'position' => $position,
            'subdit_name' => $subditName,
        ]);

        return redirect()
            ->route('master-data.manajemen-pengguna.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, User $manajemen_pengguna)
    {
        if (!auth()->user() || !auth()->user()->isKasubagTU()) {
            abort(403, 'Akses ditolak: Hanya Kasubag TU yang memiliki hak akses untuk mengubah data pengguna.');
        }

        // Mencegah Kasubag TU menurunkan role akunnya sendiri yang dapat menyebabkan terkunci dari sistem
        if (Auth::id() === $manajemen_pengguna->id && $request->role !== $manajemen_pengguna->role) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat mengubah role akun Anda sendiri untuk mencegah kehilangan hak akses administrasi.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:50'],
            'gelar' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($manajemen_pengguna->id)],
            'nip' => [$request->role === 'pegawai_gudang' ? 'nullable' : 'required', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'role' => ['required', 'string', 'in:kasubag_tu,pic_distribusi,pegawai,admin,pegawai_gudang'],
            'is_distributor' => ['nullable'],
            'is_report_examiner' => ['nullable'],
            'position' => ['nullable', 'string', 'max:255'],
            'pic_unit_choice' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'nip.required' => 'Nomor Induk Pegawai (NIP) wajib diisi untuk verifikasi identitas dan keamanan lupa password.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
            'role.required' => 'Role pengguna wajib dipilih.',
            'role.in' => 'Pilihan role tidak valid.',
        ]);

        $position = $request->position;
        $subditName = $manajemen_pengguna->subdit_name;

        if ($request->role === 'pic_distribusi') {
            $unit = $request->pic_unit_choice;
            if (str_contains(strtolower($unit ?? ''), 'sdk') || str_contains(strtolower($unit ?? ''), 'sumber daya')) {
                $position = $position ?: 'PIC Distribusi Subdirektorat SDK';
                $subditName = 'Subdirektorat Sumber Daya Konstruksi Perumahan dan Kawasan Permukiman';
            } elseif (str_contains(strtolower($unit ?? ''), 'pjf') || str_contains(strtolower($unit ?? ''), 'fungsional')) {
                $position = $position ?: 'PIC Distribusi Subdirektorat PJF';
                $subditName = 'Subdirektorat Pengelolaan Jabatan Fungsional Perumahan dan Pengembangan Profesi';
            } elseif (str_contains(strtolower($unit ?? ''), 'kbr') || str_contains(strtolower($unit ?? ''), 'kendala')) {
                $position = $position ?: 'PIC Distribusi Subdirektorat KBR';
                $subditName = 'Subdirektorat Kendala Bangunan Rumah';
            } elseif (str_contains(strtolower($unit ?? ''), 'tu') || str_contains(strtolower($unit ?? ''), 'tata usaha')) {
                $position = $position ?: 'PIC Distribusi Subbagian Tata Usaha';
                $subditName = 'Subbagian Tata Usaha';
            }
        } elseif ($request->role === 'pegawai_gudang') {
            $isDist = $request->boolean('is_distributor');
            $position = $position ?: ($isDist ? 'Petugas Distribusi Gudang ATK' : 'Pegawai Gudang');
            $subditName = $subditName ?: 'Gudang Persediaan ATK';
        } elseif ($request->role === 'pegawai') {
            $subditName = 'Pegawai Perorangan';
        }

        $title = $request->title ?: $request->gelar;

        $data = [
            'name' => $request->name,
            'title' => $title ? trim($title) : null,
            'email' => $request->email,
            'nip' => $request->nip,
            'role' => $request->role,
            'is_distributor' => ($request->role === 'pegawai_gudang') ? $request->boolean('is_distributor') : false,
            'is_report_examiner' => ($request->role === 'pegawai_gudang') ? $request->boolean('is_report_examiner') : false,
            'position' => $position,
            'subdit_name' => $subditName,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $manajemen_pengguna->update($data);

        return redirect()
            ->route('master-data.manajemen-pengguna.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $manajemen_pengguna)
    {
        if (!auth()->user() || !auth()->user()->isKasubagTU()) {
            abort(403, 'Akses ditolak: Hanya Kasubag TU yang memiliki hak akses untuk menghapus pengguna.');
        }

        if (Auth::id() === $manajemen_pengguna->id) {
            return redirect()
                ->route('master-data.manajemen-pengguna.index')
                ->with('error', 'Akun yang sedang digunakan tidak dapat dihapus.');
        }

        $manajemen_pengguna->delete();

        return redirect()
            ->route('master-data.manajemen-pengguna.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}
