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
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'nip' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role' => ['required', 'string', 'in:kasubag_tu,arsiparis,pic_distribusi,pegawai,subdit'],
            'position' => ['nullable', 'string', 'max:255'],
            'pic_unit_choice' => ['nullable', 'string', 'in:TU,KBR,SDK,PJF'],
            'subdit_unit_choice' => ['nullable', 'string'],
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
            if ($unit === 'TU') {
                $position = $position ?: 'PIC Distribusi Subbagian Tata Usaha';
                $subditName = 'Subbagian Tata Usaha';
            } elseif ($unit === 'KBR') {
                $position = $position ?: 'PIC Distribusi Subdirektorat KBR';
                $subditName = 'Subdirektorat Kendala Bangunan Rumah';
            } elseif ($unit === 'SDK') {
                $position = $position ?: 'PIC Distribusi Subdirektorat SDK';
                $subditName = 'Subdirektorat Sumber Daya Konstruksi Perumahan dan Kawasan Permukiman';
            } elseif ($unit === 'PJF') {
                $position = $position ?: 'PIC Distribusi Subdirektorat PJF';
                $subditName = 'Subdirektorat Pengelolaan Jabatan Fungsional Perumahan dan Pengembangan Profesi';
            } else {
                $posLower = strtolower($position ?? '');
                if (str_contains($posLower, 'tu') || str_contains($posLower, 'tata usaha')) {
                    $subditName = 'Subbagian Tata Usaha';
                } elseif (str_contains($posLower, 'kbr') || str_contains($posLower, 'kendala')) {
                    $subditName = 'Subdirektorat Kendala Bangunan Rumah';
                } elseif (str_contains($posLower, 'sdk') || str_contains($posLower, 'sumber daya')) {
                    $subditName = 'Subdirektorat Sumber Daya Konstruksi Perumahan dan Kawasan Permukiman';
                } elseif (str_contains($posLower, 'pjf') || str_contains($posLower, 'fungsional')) {
                    $subditName = 'Subdirektorat Pengelolaan Jabatan Fungsional Perumahan dan Pengembangan Profesi';
                }
            }
        } elseif ($request->role === 'subdit') {
            $choice = $request->subdit_unit_choice ?: $request->position;
            if (str_contains(strtolower($choice ?? ''), 'sdk')) {
                $position = 'Subdirektorat SDK';
                $subditName = 'Subdirektorat Sumber Daya Konstruksi Perumahan dan Kawasan Permukiman';
            } elseif (str_contains(strtolower($choice ?? ''), 'pjf')) {
                $position = 'Subdirektorat PJF';
                $subditName = 'Subdirektorat Pengelolaan Jabatan Fungsional Perumahan dan Pengembangan Profesi';
            } elseif (str_contains(strtolower($choice ?? ''), 'kbr')) {
                $position = 'Subdirektorat KBR';
                $subditName = 'Subdirektorat Kendala Bangunan Rumah';
            } elseif (str_contains(strtolower($choice ?? ''), 'tu')) {
                $position = 'Subbagian Tata Usaha';
                $subditName = 'Subbagian Tata Usaha';
            } else {
                $subditName = $choice;
            }
        } elseif ($request->role === 'arsiparis') {
            $position = $position ?: 'Arsiparis Ahli Pertama';
            $subditName = 'Subbagian Tata Usaha';
        } elseif ($request->role === 'kasubag_tu') {
            $position = $position ?: 'Kasubag TU';
            $subditName = 'Subbagian Tata Usaha';
        } elseif ($request->role === 'pegawai') {
            $subditName = 'Pegawai Perorangan';
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nip' => $request->nip,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'position' => $position,
            'subdit_name' => $subditName,
        ]);

        return redirect()
            ->route('master-data.manajemen-pengguna.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, User $manajemen_pengguna)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($manajemen_pengguna->id)],
            'nip' => ['required', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'role' => ['required', 'string', 'in:kasubag_tu,arsiparis,pic_distribusi,pegawai,subdit,admin'],
            'position' => ['nullable', 'string', 'max:255'],
            'pic_unit_choice' => ['nullable', 'string', 'in:TU,KBR,SDK,PJF'],
            'subdit_unit_choice' => ['nullable', 'string'],
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
            if ($unit === 'TU') {
                $position = $position ?: 'PIC Distribusi Subbagian Tata Usaha';
                $subditName = 'Subbagian Tata Usaha';
            } elseif ($unit === 'KBR') {
                $position = $position ?: 'PIC Distribusi Subdirektorat KBR';
                $subditName = 'Subdirektorat Kendala Bangunan Rumah';
            } elseif ($unit === 'SDK') {
                $position = $position ?: 'PIC Distribusi Subdirektorat SDK';
                $subditName = 'Subdirektorat Sumber Daya Konstruksi Perumahan dan Kawasan Permukiman';
            } elseif ($unit === 'PJF') {
                $position = $position ?: 'PIC Distribusi Subdirektorat PJF';
                $subditName = 'Subdirektorat Pengelolaan Jabatan Fungsional Perumahan dan Pengembangan Profesi';
            }
        } elseif ($request->role === 'subdit') {
            $choice = $request->subdit_unit_choice ?: $request->position;
            if (str_contains(strtolower($choice ?? ''), 'sdk')) {
                $position = 'Subdirektorat SDK';
                $subditName = 'Subdirektorat Sumber Daya Konstruksi Perumahan dan Kawasan Permukiman';
            } elseif (str_contains(strtolower($choice ?? ''), 'pjf')) {
                $position = 'Subdirektorat PJF';
                $subditName = 'Subdirektorat Pengelolaan Jabatan Fungsional Perumahan dan Pengembangan Profesi';
            } elseif (str_contains(strtolower($choice ?? ''), 'kbr')) {
                $position = 'Subdirektorat KBR';
                $subditName = 'Subdirektorat Kendala Bangunan Rumah';
            } elseif (str_contains(strtolower($choice ?? ''), 'tu')) {
                $position = 'Subbagian Tata Usaha';
                $subditName = 'Subbagian Tata Usaha';
            }
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'nip' => $request->nip,
            'role' => $request->role,
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
