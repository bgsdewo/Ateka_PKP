<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        if (!(auth()->user()->isKasubagTU() || auth()->user()->isPegawaiGudang())) {
            return redirect()->route('dashboard')->with('error', 'Halaman Master Data Kategori hanya dapat diakses oleh Kasubbag TU dan Pegawai Gudang.');
        }

        $categories = Category::orderBy('name', 'asc')->get();

        return view('master_data.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        if (!(auth()->user()->isKasubagTU() || auth()->user()->isPegawaiGudang())) {
            return redirect()->route('dashboard')->with('error', 'Halaman Master Data Kategori hanya dapat diakses oleh Kasubbag TU dan Pegawai Gudang.');
        }
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'kode_barang' => ['nullable', 'string', 'max:50'],
            'kd_rincian' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori sudah digunakan.',
            'name.max' => 'Nama kategori maksimal 255 karakter.',
        ]);

        Category::create([
            'name' => $request->name,
            'kode_barang' => $request->kode_barang,
            'kd_rincian' => $request->kd_rincian,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('master-data.kategori-barang.index')
            ->with('success', 'Kategori barang berhasil ditambahkan.');
    }

    public function update(Request $request, Category $kategori_barang)
    {
        if (!auth()->user() || !(auth()->user()->isKasubagTU() || auth()->user()->isPegawaiGudang())) {
            return redirect()->route('dashboard')->with('error', 'Halaman Master Data Kategori hanya dapat diakses oleh Kasubbag TU dan Pegawai Gudang.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($kategori_barang->id)],
            'kode_barang' => ['nullable', 'string', 'max:50'],
            'kd_rincian' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori sudah digunakan.',
            'name.max' => 'Nama kategori maksimal 255 karakter.',
        ]);

        $kategori_barang->update([
            'name' => $request->name,
            'kode_barang' => $request->kode_barang,
            'kd_rincian' => $request->kd_rincian,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('master-data.kategori-barang.index')
            ->with('success', 'Kategori barang berhasil diperbarui.');
    }

    public function destroy(Category $kategori_barang)
    {
        if (!auth()->user() || !auth()->user()->isKasubagTU()) {
            return redirect()->route('dashboard')->with('error', 'Hanya Kasubbag TU yang dapat menghapus kategori.');
        }

        if ($kategori_barang->products()->exists()) {
            return redirect()
                ->route('master-data.kategori-barang.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh data produk.');
        }

        $kategori_barang->delete();

        return redirect()
            ->route('master-data.kategori-barang.index')
            ->with('success', 'Kategori barang berhasil dihapus.');
    }
}
