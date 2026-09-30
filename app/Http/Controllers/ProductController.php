<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'stockTransactions' => function ($q) {
                $q->orderBy('transaction_date', 'asc')->orderBy('id', 'asc');
            }])
            ->withCount('stockTransactions')
            ->orderBy('name', 'asc')
            ->get();

        $categories = Category::orderBy('name', 'asc')->get();

        return view('master_data.products', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'code' => ['required', 'string', 'max:255', 'unique:products,code'],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:255'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'no_faktur' => ['nullable', 'string', 'max:255'],
            'uraian' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ], [
            'category_id.required' => 'Kategori barang wajib dipilih.',
            'category_id.exists' => 'Kategori barang tidak valid.',
            'code.required' => 'Kode barang wajib diisi.',
            'code.unique' => 'Kode barang sudah digunakan.',
            'name.required' => 'Nama barang wajib diisi.',
            'unit.required' => 'Satuan wajib diisi.',
            'stock.required' => 'Stok awal wajib diisi.',
            'stock.integer' => 'Stok awal harus berupa angka.',
            'stock.min' => 'Stok awal tidak boleh kurang dari 0.',
            'minimum_stock.required' => 'Stok minimum wajib diisi.',
            'minimum_stock.integer' => 'Stok minimum harus berupa angka.',
            'minimum_stock.min' => 'Stok minimum tidak boleh kurang dari 0.',
            'purchase_price.required' => 'Harga beli/perolehan wajib diisi.',
            'purchase_price.numeric' => 'Harga beli harus berupa angka.',
            'purchase_price.min' => 'Harga beli tidak boleh kurang dari 0.',
        ]);

        DB::beginTransaction();
        try {
            // 1. Simpan data produk baru
            $product = Product::create([
                'category_id' => $request->category_id,
                'code' => $request->code,
                'name' => $request->name,
                'unit' => $request->unit,
                'stock' => $request->stock,
                'minimum_stock' => $request->minimum_stock,
                'purchase_price' => $request->purchase_price,
                'description' => $request->description,
            ]);

            // 2. Jika stok awal > 0, otomatis catat ke transaksi Barang Masuk beserta No Faktur & Uraiannya
            if ($product->stock > 0) {
                StockTransaction::create([
                    'product_id'       => $product->id,
                    'user_id'          => Auth::id() ?? 1,
                    'type'             => 'masuk',
                    'quantity'         => $product->stock,
                    'stock_after'      => $product->stock,
                    'harga_perolehan'  => $product->purchase_price,
                    'no_faktur'        => $request->no_faktur ?? '-',
                    'uraian'           => $request->uraian ?? 'Pencatatan Stok Awal Master Barang',
                    'transaction_date' => now(),
                    'note'             => 'Pencatatan Stok Awal / Penambahan Master Barang Baru (' . $product->code . ')'
                ]);
            }

            DB::commit();

            return redirect()
                ->route('master-data.daftar-barang.index')
                ->with('success', 'Barang berhasil ditambahkan dan tercatat di Barang Masuk.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, Product $daftar_barang)
    {
        $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'code' => ['required', 'string', 'max:255', Rule::unique('products', 'code')->ignore($daftar_barang->id)],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:255'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'no_faktur' => ['nullable', 'string', 'max:255'],
            'uraian' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ], [
            'category_id.required' => 'Kategori barang wajib dipilih.',
            'category_id.exists' => 'Kategori barang tidak valid.',
            'code.required' => 'Kode barang wajib diisi.',
            'code.unique' => 'Kode barang sudah digunakan.',
            'name.required' => 'Nama barang wajib diisi.',
            'unit.required' => 'Satuan wajib diisi.',
            'stock.required' => 'Stok awal wajib diisi.',
            'stock.integer' => 'Stok awal harus berupa angka.',
            'stock.min' => 'Stok awal tidak boleh kurang dari 0.',
            'minimum_stock.required' => 'Stok minimum wajib diisi.',
            'minimum_stock.integer' => 'Stok minimum harus berupa angka.',
            'minimum_stock.min' => 'Stok minimum tidak boleh kurang dari 0.',
            'purchase_price.required' => 'Harga beli/perolehan wajib diisi.',
            'purchase_price.numeric' => 'Harga beli harus berupa angka.',
            'purchase_price.min' => 'Harga beli tidak boleh kurang dari 0.',
        ]);

        DB::beginTransaction();
        try {
            // 1. Perbarui data master barang
            $daftar_barang->update([
                'category_id'    => $request->category_id,
                'code'           => $request->code,
                'name'           => $request->name,
                'unit'           => $request->unit,
                'stock'          => $request->stock,
                'minimum_stock'  => $request->minimum_stock,
                'purchase_price' => $request->purchase_price,
                'description'    => $request->description,
            ]);

            // 2. Sinkronisasi real-time ke riwayat Barang Masuk (StockTransaction)
            $masukTx = StockTransaction::where('product_id', $daftar_barang->id)
                ->where('type', 'masuk')
                ->orderBy('id', 'asc')
                ->first();

            if ($masukTx) {
                // Hitung total keluar dan transaksi masuk lain untuk rekalkulasi quantity awal yang pas
                $totalKeluar = (int) StockTransaction::where('product_id', $daftar_barang->id)
                    ->where('type', 'keluar')
                    ->sum('quantity');

                $otherMasuk = (int) StockTransaction::where('product_id', $daftar_barang->id)
                    ->where('type', 'masuk')
                    ->where('id', '!=', $masukTx->id)
                    ->sum('quantity');

                $newQty = max(0, (int)$request->stock + $totalKeluar - $otherMasuk);

                $masukTx->quantity = $newQty;
                $masukTx->harga_perolehan = $request->purchase_price;
                if ($request->has('no_faktur')) {
                    $masukTx->no_faktur = $request->no_faktur ?: '-';
                }
                if ($request->has('uraian')) {
                    $masukTx->uraian = $request->uraian ?: 'Pencatatan Stok Master Barang';
                }
                $masukTx->save();
            } elseif ($request->stock > 0) {
                // Jika sebelumnya tidak ada transaksi masuk (stok awal 0) dan sekarang diisi > 0
                StockTransaction::create([
                    'product_id'       => $daftar_barang->id,
                    'user_id'          => Auth::id() ?? 1,
                    'type'             => 'masuk',
                    'quantity'         => $request->stock,
                    'stock_after'      => $request->stock,
                    'harga_perolehan'  => $request->purchase_price,
                    'no_faktur'        => $request->no_faktur ?: '-',
                    'uraian'           => $request->uraian ?: 'Pencatatan Stok Master Barang',
                    'transaction_date' => now(),
                    'note'             => 'Pencatatan Stok / Pembaruan Master Barang (' . $daftar_barang->code . ')'
                ]);
            }

            // 3. Rekalkulasi berurutan stock_after untuk seluruh transaksi produk ini agar akurat
            $runningStock = 0;
            $allTransactions = StockTransaction::where('product_id', $daftar_barang->id)
                ->orderBy('transaction_date', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            foreach ($allTransactions as $tx) {
                if ($tx->type === 'masuk') {
                    $runningStock += (int) $tx->quantity;
                } else {
                    $runningStock -= (int) $tx->quantity;
                }
                $tx->stock_after = max(0, $runningStock);
                $tx->save();
            }

            DB::commit();

            return redirect()
                ->route('master-data.daftar-barang.index')
                ->with('success', 'Barang berhasil diperbarui dan riwayat di Barang Masuk tersinkronisasi secara real-time.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Product $daftar_barang)
    {
        DB::beginTransaction();
        try {
            // 1. Hapus riwayat transaksi stok terkait (Barang Masuk & Keluar) secara real-time
            $daftar_barang->stockTransactions()->delete();

            // 2. Hapus detail permohonan yang merujuk barang ini jika ada
            $daftar_barang->requestDetails()->delete();

            // 3. Hapus data barang di Master Data
            $daftar_barang->delete();

            DB::commit();

            return redirect()
                ->route('master-data.daftar-barang.index')
                ->with('success', 'Barang dan seluruh riwayat transaksinya di Barang Masuk berhasil dihapus secara real-time.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('master-data.daftar-barang.index')
                ->with('error', 'Gagal menghapus barang: ' . $e->getMessage());
        }
    }

    public function catalog(Request $request)
    {
        $categoryId = $request->category_id;
        $search = $request->search;

        $query = Product::with('category');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name', 'asc')->get();
        $categories = Category::orderBy('name', 'asc')->get();

        return view('katalog.index', compact('products', 'categories', 'categoryId', 'search'));
    }
}
