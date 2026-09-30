<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->orderBy('name', 'asc')
            ->get();

        $transactions = StockTransaction::with(['product.category', 'user'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $totalProducts = Product::count();
        $totalStock = Product::sum('stock');

        $lowStockCount = Product::whereColumn('stock', '<=', 'minimum_stock')
            ->where('stock', '>', 0)
            ->count();

        $emptyStockCount = Product::where('stock', 0)->count();

        return view('inventory.index', compact(
            'products',
            'transactions',
            'totalProducts',
            'totalStock',
            'lowStockCount',
            'emptyStockCount'
        ));
    }

    public function storeTransaction(Request $request)
    {
        if (!$request->has('tanggal_faktur') && $request->has('transaction_date')) {
            $request->merge(['tanggal_faktur' => $request->transaction_date]);
        }
        if (!$request->has('keterangan') && $request->has('description')) {
            $request->merge(['keterangan' => $request->description]);
        }

        // 1. Validasi menyesuaikan nama input dari form Blade Anda
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'type' => ['required', 'in:masuk,keluar'],
            'quantity' => ['required', 'integer', 'min:1'],
            'satuan_beli' => ['nullable', 'string', 'max:50'],
            'jumlah_beli' => ['nullable', 'integer', 'min:1'],
            'isi_per_kemasan' => ['nullable', 'integer', 'min:1'],
            'tanggal_faktur' => ['required', 'date'], // Sesuai payload Anda
            'no_faktur' => ['nullable', 'string', 'max:255'],
            'uraian' => ['nullable', 'string', 'max:255'],
            'kondisi' => ['nullable', 'string', 'max:255'],
            'harga_perolehan' => ['nullable', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'string'], // Sesuai payload Anda
        ], [
            'product_id.required' => 'Barang wajib dipilih.',
            'product_id.exists' => 'Barang tidak valid.',
            'type.required' => 'Jenis transaksi wajib dipilih.',
            'type.in' => 'Jenis transaksi tidak valid.',
            'quantity.required' => 'Jumlah transaksi wajib diisi.',
            'quantity.integer' => 'Jumlah transaksi harus berupa angka.',
            'quantity.min' => 'Jumlah transaksi minimal 1.',
            'tanggal_faktur.required' => 'Tanggal transaksi/faktur wajib diisi.',
            'tanggal_faktur.date' => 'Format tanggal tidak valid.',
        ]);

        DB::transaction(function () use ($request) {
            $product = Product::lockForUpdate()->findOrFail($request->product_id);
            $quantity = (int) $request->quantity;

            // Jika transaksi masuk dan ada jumlah kemasan serta isi per kemasan, pastikan quantity stok sesuai konversi
            if ($request->type === 'masuk' && $request->filled('jumlah_beli') && $request->filled('isi_per_kemasan')) {
                $calcQty = (int) $request->jumlah_beli * (int) $request->isi_per_kemasan;
                if ($calcQty > 0) {
                    $quantity = $calcQty;
                }
            }

            $currentStock = (int) $product->stock;

            if ($request->type === 'keluar' && $quantity > $currentStock) {
                throw ValidationException::withMessages([
                    'quantity' => 'Jumlah barang keluar tidak boleh melebihi stok tersedia.',
                ]);
            }

            if ($request->type === 'masuk') {
                $stockAfter = $currentStock + $quantity;
            } else {
                $stockAfter = $currentStock - $quantity;
            }

            $hargaPerolehan = ($request->filled('harga_perolehan') && (float) $request->harga_perolehan > 0)
                ? (float) $request->harga_perolehan
                : (float) ($product->purchase_price ?? 0);

            StockTransaction::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'type' => $request->type,
                'quantity' => $quantity,
                'satuan_beli' => $request->satuan_beli,
                'jumlah_beli' => $request->filled('jumlah_beli') ? (int) $request->jumlah_beli : null,
                'isi_per_kemasan' => $request->filled('isi_per_kemasan') ? (int) $request->isi_per_kemasan : null,
                'stock_after' => $stockAfter,

                // 2. Mapping data dari request form ke kolom database
                'transaction_date' => $request->tanggal_faktur,
                'no_faktur' => $request->no_faktur,
                'uraian' => $request->uraian,
                'kondisi' => $request->kondisi,
                'harga_perolehan' => $hargaPerolehan,
                'note' => $request->keterangan,
            ]);

            $productUpdateData = ['stock' => $stockAfter];
            if ($request->type === 'masuk' && $hargaPerolehan > 0) {
                $productUpdateData['purchase_price'] = $hargaPerolehan;
            }
            $product->update($productUpdateData);
        });

        if ($request->type == 'masuk') {
            return redirect()
                ->route('barang-masuk')
                ->with('success', 'Barang masuk berhasil disimpan.');
        }

        return redirect()
            ->route('barang-keluar')
            ->with('success', 'Barang keluar berhasil disimpan.');
    }
}
