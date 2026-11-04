<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\RequestDetail;
use App\Models\RequestItem;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->isKasubagTU() && !auth()->user()->isPegawaiGudang()) {
            return redirect()->route('dashboard')->with('error', 'Halaman Master Data hanya dapat diakses oleh Kasubbag TU dan Pegawai Gudang.');
        }

        $month = $request->get('month');
        $year = $request->get('year');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $categoryId = $request->get('category_id');
        $stockStatus = $request->get('stock_status');

        $query = Product::with(['category', 'stockTransactions' => function ($q) {
                $q->orderBy('transaction_date', 'asc')->orderBy('id', 'asc');
            }])
            ->withCount('stockTransactions');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($stockStatus === 'available') {
            $query->where('stock', '>', 0);
        } elseif ($stockStatus === 'empty') {
            $query->where('stock', '<=', 0);
        }

        if ($startDate && $endDate) {
            $minDate = min($startDate, $endDate);
            $maxDate = max($startDate, $endDate);
            $query->where(function ($q) use ($minDate, $maxDate) {
                $q->whereHas('stockTransactions', function ($sq) use ($minDate, $maxDate) {
                    $sq->where('type', 'masuk')
                       ->whereDate('transaction_date', '>=', $minDate)
                       ->whereDate('transaction_date', '<=', $maxDate);
                })->orWhere(function ($sq) use ($minDate, $maxDate) {
                    $sq->whereDoesntHave('stockTransactions', function ($sub) {
                        $sub->where('type', 'masuk');
                    })->whereDate('created_at', '>=', $minDate)
                      ->whereDate('created_at', '<=', $maxDate);
                });
            });
        } elseif ($startDate) {
            // Filter tepat pada tanggal tersebut (filter harian)
            $query->where(function ($q) use ($startDate) {
                $q->whereHas('stockTransactions', function ($sq) use ($startDate) {
                    $sq->where('type', 'masuk')
                       ->whereDate('transaction_date', $startDate);
                })->orWhere(function ($sq) use ($startDate) {
                    $sq->whereDoesntHave('stockTransactions', function ($sub) {
                        $sub->where('type', 'masuk');
                    })->whereDate('created_at', $startDate);
                });
            });
        } elseif ($endDate) {
            $query->where(function ($q) use ($endDate) {
                $q->whereHas('stockTransactions', function ($sq) use ($endDate) {
                    $sq->where('type', 'masuk')
                       ->whereDate('transaction_date', $endDate);
                })->orWhere(function ($sq) use ($endDate) {
                    $sq->whereDoesntHave('stockTransactions', function ($sub) {
                        $sub->where('type', 'masuk');
                    })->whereDate('created_at', $endDate);
                });
            });
        } else {
            // Filter tahun dan bulan
            if ($year && $month) {
                $query->where(function ($q) use ($year, $month) {
                    $q->whereHas('stockTransactions', function ($sq) use ($year, $month) {
                        $sq->where('type', 'masuk')
                           ->whereYear('transaction_date', $year)
                           ->whereMonth('transaction_date', $month);
                    })->orWhere(function ($sq) use ($year, $month) {
                        $sq->whereDoesntHave('stockTransactions', function ($sub) {
                            $sub->where('type', 'masuk');
                        })->whereYear('created_at', $year)
                          ->whereMonth('created_at', $month);
                    });
                });
            } elseif ($year) {
                $query->where(function ($q) use ($year) {
                    $q->whereHas('stockTransactions', function ($sq) use ($year) {
                        $sq->where('type', 'masuk')
                           ->whereYear('transaction_date', $year);
                    })->orWhere(function ($sq) use ($year) {
                        $sq->whereDoesntHave('stockTransactions', function ($sub) {
                            $sub->where('type', 'masuk');
                        })->whereYear('created_at', $year);
                    });
                });
            } elseif ($month) {
                $query->where(function ($q) use ($month) {
                    $q->whereHas('stockTransactions', function ($sq) use ($month) {
                        $sq->where('type', 'masuk')
                           ->whereMonth('transaction_date', $month);
                    })->orWhere(function ($sq) use ($month) {
                        $sq->whereDoesntHave('stockTransactions', function ($sub) {
                            $sub->where('type', 'masuk');
                        })->whereMonth('created_at', $month);
                    });
                });
            }
        }

        $products = $query->orderBy('name', 'asc')->get();
        $categories = Category::orderBy('name', 'asc')->get();

        // Ambil daftar tahun dinamis dari data transaksi dan produk untuk jangka panjang
        $dbYears = StockTransaction::where('type', 'masuk')
            ->whereNotNull('transaction_date')
            ->selectRaw('YEAR(transaction_date) as year')
            ->distinct()
            ->pluck('year')
            ->toArray();

        $productYears = Product::whereNotNull('created_at')
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->pluck('year')
            ->toArray();

        $currentYear = (int) date('Y');
        $defaultYears = range($currentYear - 3, $currentYear + 3);
        $availableYears = array_unique(array_merge($dbYears, $productYears, $defaultYears));
        rsort($availableYears);

        $totalProducts = $products->count();

        // Ambil usulan pengadaan yang disetujui / pending dan belum dibeli / ditambahkan stoknya di master data (3 item per halaman)
        $unfulfilledProcurements = RequestDetail::whereHas('requestItem', function ($q) {
            $q->where('is_procurement', true)
              ->whereIn('status', ['pending', 'approved']);
        })
        ->where('is_fulfilled', false)
        ->with(['requestItem.user', 'product.category'])
        ->latest('id')
        ->paginate(3, ['*'], 'pengadaan_page')
        ->withQueryString();

        $unfulfilledProcurements->fragment('pengadaan-section');

        return view('master_data.products', compact(
            'products',
            'categories',
            'month',
            'year',
            'startDate',
            'endDate',
            'categoryId',
            'stockStatus',
            'availableYears',
            'totalProducts',
            'unfulfilledProcurements'
        ));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isKasubagTU() && !auth()->user()->isPegawaiGudang()) {
            return redirect()->back()->with('error', 'Hanya Kasubbag TU dan Pegawai Gudang yang memiliki hak akses untuk menambah atau mengubah data master barang.');
        }

        // Auto-detect category & code jika tidak diisi secara manual saat isi stok
        if (empty($request->category_id) && !empty($request->name)) {
            $matched = Product::whereRaw('LOWER(name) = ?', [mb_strtolower(trim($request->name))])->first();
            if ($matched) {
                $request->merge(['category_id' => $matched->category_id]);
                if (empty($request->code)) {
                    $request->merge(['code' => $matched->code]);
                }
            } else {
                foreach (Category::all() as $cat) {
                    if (mb_stripos($request->name, $cat->name) !== false) {
                        $request->merge(['category_id' => $cat->id]);
                        if (empty($request->code) && $cat->kd_rincian) {
                            $request->merge(['code' => $cat->kd_rincian]);
                        }
                        break;
                    }
                }
            }
        }

        // Cek apakah barang dengan nama yang sama sudah ada di master data
        $existingProduct = Product::whereRaw('LOWER(name) = ?', [mb_strtolower(trim((string) $request->name))])
            ->first();

        $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'code' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:255'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['nullable', 'integer', 'min:0'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'no_faktur' => ['nullable', 'string', 'max:255'],
            'transaction_date' => ['nullable', 'date'],
            'uraian' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
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
            'purchase_price.required' => 'Harga beli/perolehan wajib diisi.',
            'purchase_price.numeric' => 'Harga beli harus berupa angka.',
            'purchase_price.min' => 'Harga beli tidak boleh kurang dari 0.',
            'transaction_date.date' => 'Format tanggal faktur tidak valid.',
            'image.image' => 'File yang diunggah harus berupa gambar.',
            'image.mimes' => 'Format gambar harus JPEG, PNG, JPG, atau WEBP.',
            'image.max' => 'Ukuran gambar tidak boleh melebihi 2 MB.',
        ]);

        DB::beginTransaction();
        try {
            $addedQty = (int) $request->stock;

            $imagePath = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = 'product_' . Str::slug($request->code ?: 'item') . '_' . time() . '.' . $file->getClientOriginalExtension();
                $stored = $file->storeAs('products', $filename, 'public');
                $imagePath = 'storage/' . $stored;
            }

            if ($existingProduct) {
                $newTotalStock = $addedQty > 0 ? ((int) $existingProduct->stock + $addedQty) : (int) $existingProduct->stock;
                $updateData = [
                    'category_id' => $request->category_id ?: $existingProduct->category_id,
                    'unit' => $request->unit ?: $existingProduct->unit,
                    'stock' => $newTotalStock,
                    'minimum_stock' => $request->minimum_stock ?? $existingProduct->minimum_stock ?? 0,
                    'purchase_price' => (float) $request->purchase_price > 0 ? $request->purchase_price : $existingProduct->purchase_price,
                    'description' => $request->description ?: $existingProduct->description,
                ];
                if ($imagePath) {
                    if ($existingProduct->image && str_starts_with($existingProduct->image, 'storage/products/')) {
                        $oldRelative = str_replace('storage/', '', $existingProduct->image);
                        if (Storage::disk('public')->exists($oldRelative)) {
                            Storage::disk('public')->delete($oldRelative);
                        }
                    }
                    $updateData['image'] = $imagePath;
                }
                $existingProduct->update($updateData);
                $product = $existingProduct;
            } else {
                // 1. Simpan data produk baru
                $product = Product::create([
                    'category_id' => $request->category_id,
                    'code' => $request->code,
                    'name' => $request->name,
                    'unit' => $request->unit,
                    'stock' => $addedQty,
                    'minimum_stock' => $request->minimum_stock ?? 0,
                    'purchase_price' => $request->purchase_price,
                    'description' => $request->description,
                    'image' => $imagePath,
                ]);
            }

            // 2. Jika stok yang ditambahkan > 0, otomatis catat ke transaksi Barang Masuk beserta No Faktur, Tgl Faktur & Uraiannya
            if ($addedQty > 0) {
                StockTransaction::create([
                    'product_id'       => $product->id,
                    'user_id'          => Auth::id() ?? 1,
                    'type'             => 'masuk',
                    'quantity'         => $addedQty,
                    'stock_after'      => $product->stock,
                    'harga_perolehan'  => $product->purchase_price,
                    'no_faktur'        => $request->no_faktur ?: '-',
                    'uraian'           => $request->uraian ?: 'Pencatatan Stok Master Barang',
                    'transaction_date' => $request->transaction_date ?: now(),
                    'note'             => 'Pembelian ' . $product->name
                ]);
            }

            // 3. Hubungkan dengan usulan pengadaan yang cocok & kirim notifikasi real-time ke pemohon (jika stok > 0)
            $notifiedNames = [];
            if ($product->stock > 0) {
                $notifiedNames = $this->fulfillMatchingProcurements(
                    $product,
                    $request->no_faktur,
                    $request->transaction_date,
                    $request->uraian,
                    $request->filled('request_detail_id') ? (int) $request->request_detail_id : null
                );
            }

            DB::commit();

            $successMsg = 'Barang berhasil ditambahkan dan tercatat di Barang Masuk.';
            if (!empty($notifiedNames)) {
                $successMsg .= ' Barang pengadaan siap diserahterimakan dan notifikasi telah dikirimkan ke Petugas Gudang Distribusi (Pemohon: ' . implode(', ', array_unique($notifiedNames)) . ').';
            }

            return redirect()
                ->route('master-data.daftar-barang.index')
                ->with('success', $successMsg);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, Product $daftar_barang)
    {
        if (!auth()->user()->isKasubagTU() && !auth()->user()->isPegawaiGudang()) {
            return redirect()->back()->with('error', 'Hanya Kasubbag TU dan Pegawai Gudang yang memiliki hak akses untuk menambah atau mengubah data master barang.');
        }

        // Auto-fill category & code jika tidak dikirimkan saat isi stok barang eksisting
        if (empty($request->category_id)) {
            $request->merge(['category_id' => $daftar_barang->category_id]);
        }
        if (empty($request->code)) {
            $request->merge(['code' => $daftar_barang->code]);
        }

        $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'code' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:255'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['nullable', 'integer', 'min:0'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'no_faktur' => ['nullable', 'string', 'max:255'],
            'transaction_date' => ['nullable', 'date'],
            'uraian' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
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
            'purchase_price.required' => 'Harga beli/perolehan wajib diisi.',
            'purchase_price.numeric' => 'Harga beli harus berupa angka.',
            'purchase_price.min' => 'Harga beli tidak boleh kurang dari 0.',
            'transaction_date.date' => 'Format tanggal faktur tidak valid.',
            'image.image' => 'File yang diunggah harus berupa gambar.',
            'image.mimes' => 'Format gambar harus JPEG, PNG, JPG, atau WEBP.',
            'image.max' => 'Ukuran gambar tidak boleh melebihi 2 MB.',
        ]);

        DB::beginTransaction();
        try {
            $updateData = [
                'category_id'    => $request->category_id,
                'code'           => $request->code,
                'name'           => $request->name,
                'unit'           => $request->unit,
                'stock'          => $request->stock,
                'minimum_stock'  => $request->minimum_stock ?? ($daftar_barang->minimum_stock ?? 0),
                'purchase_price' => $request->purchase_price,
                'description'    => $request->description,
            ];

            if ($request->hasFile('image')) {
                // Hapus foto lama jika tersimpan di disk public storage
                if ($daftar_barang->image && str_starts_with($daftar_barang->image, 'storage/products/')) {
                    $oldRelative = str_replace('storage/', '', $daftar_barang->image);
                    if (Storage::disk('public')->exists($oldRelative)) {
                        Storage::disk('public')->delete($oldRelative);
                    }
                }
                $file = $request->file('image');
                $filename = 'product_' . Str::slug($request->code ?: 'item') . '_' . time() . '.' . $file->getClientOriginalExtension();
                $stored = $file->storeAs('products', $filename, 'public');
                $updateData['image'] = 'storage/' . $stored;
            }

            // 1. Perbarui data master barang
            $daftar_barang->update($updateData);

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
                if ($request->filled('transaction_date')) {
                    $masukTx->transaction_date = $request->transaction_date;
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
                    'transaction_date' => $request->transaction_date ?: now(),
                    'note'             => 'Pembelian ' . $daftar_barang->name
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

            // 4. Sinkronkan dengan usulan pengadaan yang cocok (jika stok > 0 dan ada usulan yang belum terpenuhi)
            $notifiedNames = [];
            if ($daftar_barang->stock > 0) {
                $notifiedNames = $this->fulfillMatchingProcurements(
                    $daftar_barang,
                    $request->no_faktur,
                    $request->transaction_date,
                    $request->uraian,
                    $request->filled('request_detail_id') ? (int) $request->request_detail_id : null
                );
            }

            DB::commit();

            $successMsg = 'Barang berhasil diperbarui dan riwayat di Barang Masuk tersinkronisasi secara real-time.';
            if (!empty($notifiedNames)) {
                $successMsg .= ' Barang pengadaan siap diserahterimakan dan notifikasi telah dikirimkan ke Petugas Gudang Distribusi (Pemohon: ' . implode(', ', array_unique($notifiedNames)) . ').';
            }

            return redirect()
                ->route('master-data.daftar-barang.index')
                ->with('success', $successMsg);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Product $daftar_barang)
    {
        if (!auth()->user()->isKasubagTU() && !auth()->user()->isPegawaiGudang()) {
            return redirect()->back()->with('error', 'Hanya Kasubbag TU dan Pegawai Gudang yang memiliki hak akses untuk menghapus data master barang.');
        }

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
        $stockStatus = $request->stock_status;

        $query = Product::with('category');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($stockStatus === 'available') {
            $query->where('stock', '>', 0);
        } elseif ($stockStatus === 'empty') {
            $query->where('stock', '<=', 0);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name', 'asc')->paginate(8)->withQueryString();
        $categories = Category::orderBy('name', 'asc')->get();

                $availableStockCount = Product::where('stock', '>', 0)->count();
        $emptyStockCount = Product::where('stock', '<=', 0)->count();
        $totalAllProducts = $availableStockCount + $emptyStockCount;

        return view('katalog.index', compact('products', 'categories', 'categoryId', 'search', 'stockStatus', 'availableStockCount', 'emptyStockCount', 'totalAllProducts'));
    }

    /**
     * Cek kesesuaian antara produk master dengan usulan pengadaan pegawai/subdit
     */
    public static function isProcurementMatch(Product $product, RequestDetail $detail): bool
    {
        $pName   = mb_strtolower(trim($product->name ?? ''));
        $cName   = mb_strtolower(trim($detail->custom_product_name ?? ''));
        $spec    = mb_strtolower(trim($detail->specification ?? ''));
        $cFull   = trim($cName . ' ' . $spec);
        $catName = mb_strtolower(trim($product->category->name ?? ''));

        if (empty($cName) && empty($cFull)) {
            return false;
        }

        // 1. Exact match nama usulan atau nama + spesifikasi dengan nama produk
        if ($cName === $pName || $cFull === $pName) {
            return true;
        }

        // 2. Exact match antara kategori + spesifikasi dengan nama produk (contoh: Kategori 'Map' + 'Merah' == 'Map Merah')
        if ($catName !== '' && trim($catName . ' ' . $spec) === $pName) {
            return true;
        }

        $cWords = array_filter(preg_split('/[\s,\.\-_]+/', $cName), fn($w) => mb_strlen($w) >= 2);
        $pWords = array_filter(preg_split('/[\s,\.\-_]+/', $pName), fn($w) => mb_strlen($w) >= 2);
        $specWords = array_filter(preg_split('/[\s,\.\-_]+/', $spec), fn($w) => mb_strlen($w) >= 2);

        // 3. Substring matching (mendukung kata pendek ATK seperti map, lem, pen, A4, dsb.)
        if (mb_strlen($cName) >= 3 && str_contains($pName, $cName)) {
            if ($spec !== '') {
                if (str_contains($pName, $spec) || str_contains($cFull, $pName)) {
                    return true;
                }
                // Jika ada kata spesifikasi yang termuat dalam nama barang
                if (!empty($specWords)) {
                    foreach ($specWords as $sw) {
                        if (str_contains($pName, $sw)) {
                            return true;
                        }
                    }
                }
                // Jika nama barang pemohon sudah sangat spesifik (>= 2 kata, e.g. "Kertas A4")
                if (count($cWords) >= 2) {
                    return true;
                }
            } else {
                return true;
            }
        }

        // 4. Keseluruhan usulan (nama + spec) terdapat dalam nama barang atau sebaliknya
        if (mb_strlen($cFull) >= 3) {
            if (str_contains($pName, $cFull) || str_contains($cFull, $pName)) {
                return true;
            }
        }

        // 5. Pencocokan kata (token matching)
        if (!empty($cWords)) {
            $cWordsInP = count(array_intersect($cWords, $pWords));
            if ($cWordsInP === count($cWords)) {
                if ($spec !== '') {
                    if (empty($specWords) || count(array_intersect($specWords, $pWords)) > 0 || count($cWords) >= 2) {
                        return true;
                    }
                } else {
                    return true;
                }
            }
        }

        // 6. Typo / kemiripan karakter
        if (mb_strlen($cName) >= 5 && mb_strlen($pName) >= 5 && levenshtein($pName, $cName) <= 1) {
            return true;
        }

        return false;
    }

    /**
     * Hubungkan produk master dengan usulan pengadaan yang cocok, update status, dan kirim notifikasi ke pemohon
     */
    public function fulfillMatchingProcurements(Product $product, ?string $noFaktur = null, ?string $transactionDate = null, ?string $uraian = null, ?int $specificDetailId = null): array
    {
        if ($product->stock <= 0) {
            return [];
        }

        $fulfilledReqDetails = collect();

        if ($specificDetailId) {
            $chosenDetail = RequestDetail::with('requestItem.user')->find($specificDetailId);
            if ($chosenDetail) {
                $fulfilledReqDetails->push($chosenDetail);
            }
        } else {
            $openDetails = RequestDetail::whereHas('requestItem', function ($q) {
                $q->where('is_procurement', true)
                  ->whereIn('status', ['pending', 'approved']);
            })
            ->where('is_fulfilled', false)
            ->where(function ($subQ) use ($product) {
                $subQ->whereNull('product_id')
                     ->orWhere('product_id', $product->id);
            })
            ->with(['requestItem.user'])
            ->get();

            foreach ($openDetails as $od) {
                if ($od->product_id == $product->id || self::isProcurementMatch($product, $od)) {
                    $fulfilledReqDetails->push($od);
                }
            }
        }

        $notifiedNames = [];
        $notifiedUserIds = [];
        foreach ($fulfilledReqDetails as $det) {
            $det->update([
                'product_id'   => $product->id,
                'is_fulfilled' => true,
                'fulfilled_at' => now(),
            ]);

            $reqItem = $det->requestItem;
            if ($reqItem) {
                $requestedQty = (int) $det->quantity;
                $approvedQty = $det->approved_quantity ? (int) $det->approved_quantity : $requestedQty;
                $itemTotalCost = ($product->purchase_price ?? 0) * $approvedQty;
                $newTotalPrice = ((float) $reqItem->purchase_price) + $itemTotalCost;

                // Set approved quantity pada detail barang pengadaan
                $det->update(['approved_quantity' => $approvedQty]);
                // Catatan: Pemotongan stok dan pencatatan mutasi Barang Keluar akan dicatat
                // ketika Petugas Gudang Distribusi melakukan serah terima fisik barang kepada pemohon.

                // Cek apakah SELURUH item dalam pengadaan ini telah diisi stoknya
                $hasUnfulfilled = $reqItem->details()->where('is_fulfilled', false)->exists();

                $updateData = [
                    'purchase_price'     => $newTotalPrice,
                    'status'             => 'approved',
                    'verified_at'        => $reqItem->verified_at ?: now(),
                    'verified_by'        => $reqItem->verified_by ?: (Auth::id() ?? 1),
                ];

                if (!$hasUnfulfilled) {
                    // Semua item di tiket pengadaan ini sudah lengkap dibeli & diisi stoknya
                    $updateData['purchase_invoice']   = $noFaktur ?: ($reqItem->purchase_invoice ?: '-');
                    $updateData['purchase_date']      = $transactionDate ?: ($reqItem->purchase_date ?: now());
                    $updateData['purchase_notes']     = $uraian ?: ($reqItem->purchase_notes ?: 'Barang telah diisi stoknya oleh Petugas Gudang');
                    $updateData['verification_notes'] = 'Barang telah tersedia di gudang dan dialokasikan untuk pemohon. Siap diserahterimakan oleh Petugas Distribusi.';
                } else {
                    if ($noFaktur && !$reqItem->purchase_notes) {
                        $updateData['purchase_notes'] = 'Sebagian barang telah ditambahkan (' . $product->name . ')';
                    }
                }

                $reqItem->update($updateData);

                UserNotification::sendProcurementNotification($reqItem, $product, $noFaktur);
                if ($reqItem->user) {
                    $notifiedNames[] = $reqItem->user->name_with_title ?: $reqItem->user->name;
                    $notifiedUserIds[] = $reqItem->user_id;
                }
            }
        }

        return $notifiedNames;
    }
}
