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
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->isKasubagTU() && !auth()->user()->isPicDistribusi() && !auth()->user()->isArsiparis()) {
            return redirect()->route('dashboard')->with('error', 'Halaman Master Data hanya dapat diakses oleh Kasubag TU, PIC Distribusi, dan Arsiparis.');
        }

        $month = $request->get('month');
        $year = $request->get('year');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = Product::with(['category', 'stockTransactions' => function ($q) {
                $q->orderBy('transaction_date', 'asc')->orderBy('id', 'asc');
            }])
            ->withCount('stockTransactions');

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

        // Ambil usulan pengadaan yang disetujui / pending dan belum terpenuhi master barangnya
        $unfulfilledProcurements = RequestDetail::whereHas('requestItem', function ($q) {
            $q->where('is_procurement', true)
              ->whereIn('status', ['pending', 'approved']);
        })
        ->whereNull('product_id')
        ->with(['requestItem.user'])
        ->get();

        return view('master_data.products', compact(
            'products',
            'categories',
            'month',
            'year',
            'startDate',
            'endDate',
            'availableYears',
            'totalProducts',
            'unfulfilledProcurements'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'code' => ['required', 'string', 'max:255', 'unique:products,code'],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:255'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['nullable', 'integer', 'min:0'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'no_faktur' => ['nullable', 'string', 'max:255'],
            'transaction_date' => ['nullable', 'date'],
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
            'purchase_price.required' => 'Harga beli/perolehan wajib diisi.',
            'purchase_price.numeric' => 'Harga beli harus berupa angka.',
            'purchase_price.min' => 'Harga beli tidak boleh kurang dari 0.',
            'transaction_date.date' => 'Format tanggal faktur tidak valid.',
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
                'minimum_stock' => $request->minimum_stock ?? 0,
                'purchase_price' => $request->purchase_price,
                'description' => $request->description,
            ]);

            // 2. Jika stok awal > 0, otomatis catat ke transaksi Barang Masuk beserta No Faktur, Tgl Faktur & Uraiannya
            if ($product->stock > 0) {
                StockTransaction::create([
                    'product_id'       => $product->id,
                    'user_id'          => Auth::id() ?? 1,
                    'type'             => 'masuk',
                    'quantity'         => $product->stock,
                    'stock_after'      => $product->stock,
                    'harga_perolehan'  => $product->purchase_price,
                    'no_faktur'        => $request->no_faktur ?: '-',
                    'uraian'           => $request->uraian ?: 'Pencatatan Stok Awal Master Barang',
                    'transaction_date' => $request->transaction_date ?: now(),
                    'note'             => 'Pencatatan Stok Awal / Penambahan Master Barang Baru (' . $product->code . ')'
                ]);
            }

            // 3. Hubungkan dengan usulan pengadaan yang cocok & kirim notifikasi real-time ke pemohon
            $notifiedNames = $this->fulfillMatchingProcurements(
                $product,
                $request->no_faktur,
                $request->transaction_date,
                $request->uraian,
                $request->filled('request_detail_id') ? (int) $request->request_detail_id : null
            );

            DB::commit();

            $successMsg = 'Barang berhasil ditambahkan dan tercatat di Barang Masuk.';
            if (!empty($notifiedNames)) {
                $successMsg .= ' Usulan pengadaan terpenuhi dan notifikasi telah dikirimkan ke pemohon (' . implode(', ', array_unique($notifiedNames)) . ').';
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
        $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'code' => ['required', 'string', 'max:255', Rule::unique('products', 'code')->ignore($daftar_barang->id)],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:255'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['nullable', 'integer', 'min:0'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'no_faktur' => ['nullable', 'string', 'max:255'],
            'transaction_date' => ['nullable', 'date'],
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
            'purchase_price.required' => 'Harga beli/perolehan wajib diisi.',
            'purchase_price.numeric' => 'Harga beli harus berupa angka.',
            'purchase_price.min' => 'Harga beli tidak boleh kurang dari 0.',
            'transaction_date.date' => 'Format tanggal faktur tidak valid.',
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
                'minimum_stock'  => $request->minimum_stock ?? ($daftar_barang->minimum_stock ?? 0),
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

            // 4. Sinkronkan dengan usulan pengadaan yang cocok (jika ada usulan yang belum terpenuhi)
            $notifiedNames = $this->fulfillMatchingProcurements(
                $daftar_barang,
                $request->no_faktur,
                $request->transaction_date,
                $request->uraian
            );

            DB::commit();

            $successMsg = 'Barang berhasil diperbarui dan riwayat di Barang Masuk tersinkronisasi secara real-time.';
            if (!empty($notifiedNames)) {
                $successMsg .= ' Usulan pengadaan terpenuhi dan notifikasi telah dikirimkan ke pemohon (' . implode(', ', array_unique($notifiedNames)) . ').';
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
            ->whereNull('product_id')
            ->with(['requestItem.user'])
            ->get();

            foreach ($openDetails as $od) {
                if (self::isProcurementMatch($product, $od)) {
                    $fulfilledReqDetails->push($od);
                }
            }
        }

        $notifiedNames = [];
        foreach ($fulfilledReqDetails as $det) {
            $det->update(['product_id' => $product->id]);
            $reqItem = $det->requestItem;
            if ($reqItem) {
                $reqItem->update([
                    'purchase_invoice'   => $noFaktur ?: ($reqItem->purchase_invoice ?: '-'),
                    'purchase_date'      => $transactionDate ?: ($reqItem->purchase_date ?: now()),
                    'purchase_price'     => ($product->purchase_price ?? 0) * ($det->approved_quantity ?: $det->quantity),
                    'purchase_notes'     => $uraian ?: ($reqItem->purchase_notes ?: 'Barang dibeli dan dimasukkan ke Daftar Barang'),
                    'verification_notes' => $reqItem->verification_notes ?: 'Barang telah dibeli oleh Subbag TU dan kini tersedia di daftar barang/katalog.',
                    'status'             => 'approved',
                    'verified_at'        => $reqItem->verified_at ?: now(),
                    'verified_by'        => $reqItem->verified_by ?: (Auth::id() ?? 1),
                ]);

                if ($reqItem->user) {
                    UserNotification::sendProcurementNotification($reqItem, $product, $noFaktur);
                    $notifiedNames[] = $reqItem->user->name_with_title ?: $reqItem->user->name;
                }
            }
        }

        return $notifiedNames;
    }
}
