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
        $users = User::orderBy('name', 'asc')->get();

        return view('master_data.users', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role' => ['required', 'string', 'in:kasubag_tu,arsiparis,pic_distribusi,pegawai,subdit'],
            'position' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
            'role.required' => 'Role pengguna wajib dipilih.',
            'role.in' => 'Pilihan role tidak valid.',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'position' => $request->position,
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
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'role' => ['required', 'string', 'in:kasubag_tu,arsiparis,pic_distribusi,pegawai,subdit,admin'],
            'position' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
            'role.required' => 'Role pengguna wajib dipilih.',
            'role.in' => 'Pilihan role tidak valid.',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'position' => $request->position,
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
