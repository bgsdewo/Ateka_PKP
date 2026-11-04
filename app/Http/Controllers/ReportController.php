<?php

namespace App\Http\Controllers;

use App\Models\StockTransaction;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Halaman Utama Laporan Mutasi Persediaan (Filter, Excel, PDF, Cetak)
     */
    public function index(Request $request)
    {
        if (!auth()->user()->isKasubagTU() && !auth()->user()->isPegawaiGudang()) {
            return redirect()->route('dashboard')->with('error', 'Halaman Laporan & Arsip hanya dapat diakses oleh Kasubbag TU dan Pegawai Gudang.');
        }

        $type = $request->type;
        $recipientType = $request->recipient_type; // 'subdit' or 'individu'
        $month = $request->month;
        $year = $request->year;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $categoryId = $request->category_id;

        $query = StockTransaction::with(['product.category', 'user', 'requestItem.user'])
            ->when($year, function ($q) use ($year) {
                $q->whereYear('transaction_date', $year);
            })
            ->when($month, function ($q) use ($month) {
                $q->whereMonth('transaction_date', $month);
            })
            ->when($type, function ($q) use ($type) {
                $q->where('type', $type);
            })
            ->when($recipientType, function ($q) use ($recipientType) {
                if ($recipientType === 'subdit') {
                    $q->where('type', 'keluar')
                      ->where(function ($sub) {
                          $sub->where('recipient_type', 'subdit')
                              ->orWhere('note', 'like', '%Subdit%')
                              ->orWhere('uraian', 'like', '%Subdit%');
                      });
                } elseif (in_array($recipientType, ['pegawai', 'individu'])) {
                    $q->where('type', 'keluar')
                      ->where(function ($sub) {
                          $sub->where('recipient_type', 'individu')
                              ->orWhere('note', 'like', '%Individu%')
                              ->orWhere('note', 'like', '%Pegawai%')
                              ->orWhere(function ($fallback) {
                                  $fallback->whereNull('recipient_type')
                                           ->where('note', 'not like', '%Subdit%')
                                           ->where('uraian', 'not like', '%Subdit%');
                              });
                      });
                }
            })
            ->when($startDate, function ($q) use ($startDate) {
                $q->whereDate('transaction_date', '>=', $startDate);
            })
            ->when($endDate, function ($q) use ($endDate) {
                $q->whereDate('transaction_date', '<=', $endDate);
            })
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->whereHas('product', function ($pq) use ($categoryId) {
                    $pq->where('category_id', $categoryId);
                });
            })
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc');

        $transactions = $query->get();

        $totalTransactions = $transactions->count();

        $totalIn = $transactions
            ->where('type', 'masuk')
            ->sum('quantity');

        $totalOut = $transactions
            ->where('type', 'keluar')
            ->sum('quantity');

        $totalSubditOut = $transactions
            ->where('type', 'keluar')
            ->filter(fn($t) => $t->is_subdit)
            ->sum('quantity');

        $totalIndividuOut = $transactions
            ->where('type', 'keluar')
            ->filter(fn($t) => $t->is_individu)
            ->sum('quantity');

        $categories = Category::orderBy('name', 'asc')->get();

        return view('reports.index', compact(
            'transactions',
            'totalTransactions',
            'totalIn',
            'totalOut',
            'totalSubditOut',
            'totalIndividuOut',
            'type',
            'recipientType',
            'month',
            'year',
            'startDate',
            'endDate',
            'categoryId',
            'categories'
        ));
    }

    /**
     * Cetak Dokumen Resmi Laporan Mutasi & Persediaan Barang (Kop Surat & Tanda Tangan)
     */
    public function printReport(Request $request)
    {
        if (!auth()->user()->isKasubagTU() && !auth()->user()->isPegawaiGudang()) {
            return redirect()->route('dashboard')->with('error', 'Halaman Cetak Laporan hanya dapat diakses oleh Kasubbag TU dan Pegawai Gudang.');
        }

        $type = $request->type;
        $recipientType = $request->recipient_type;
        $month = $request->month;
        $year = $request->year;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $categoryId = $request->category_id;

        $query = StockTransaction::with(['product.category', 'user', 'requestItem.user'])
            ->when($year, function ($q) use ($year) {
                $q->whereYear('transaction_date', $year);
            })
            ->when($month, function ($q) use ($month) {
                $q->whereMonth('transaction_date', $month);
            })
            ->when($type, function ($q) use ($type) {
                $q->where('type', $type);
            })
            ->when($recipientType, function ($q) use ($recipientType) {
                if ($recipientType === 'subdit') {
                    $q->where('type', 'keluar')
                      ->where(function ($sub) {
                          $sub->where('recipient_type', 'subdit')
                              ->orWhere('note', 'like', '%Subdit%')
                              ->orWhere('uraian', 'like', '%Subdit%');
                      });
                } elseif (in_array($recipientType, ['pegawai', 'individu'])) {
                    $q->where('type', 'keluar')
                      ->where(function ($sub) {
                          $sub->where('recipient_type', 'individu')
                              ->orWhere('note', 'like', '%Individu%')
                              ->orWhere('note', 'like', '%Pegawai%')
                              ->orWhere(function ($fallback) {
                                  $fallback->whereNull('recipient_type')
                                           ->where('note', 'not like', '%Subdit%')
                                           ->where('uraian', 'not like', '%Subdit%');
                              });
                      });
                }
            })
            ->when($startDate, function ($q) use ($startDate) {
                $q->whereDate('transaction_date', '>=', $startDate);
            })
            ->when($endDate, function ($q) use ($endDate) {
                $q->whereDate('transaction_date', '<=', $endDate);
            })
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->whereHas('product', function ($pq) use ($categoryId) {
                    $pq->where('category_id', $categoryId);
                });
            })
            ->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc');

        $transactions = $query->get();

        $totalTransactions = $transactions->count();
        $totalIn = $transactions->where('type', 'masuk')->sum('quantity');
        $totalOut = $transactions->where('type', 'keluar')->sum('quantity');
        $selectedCategory = $categoryId ? Category::find($categoryId) : null;

        if ($startDate && $endDate) {
            $periodLabel = \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') . ' s/d ' . \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y');
        } elseif ($month && $year) {
            $periodLabel = \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');
        } elseif ($year) {
            $periodLabel = "Tahun {$year}";
        } else {
            $periodLabel = "Semua Periode Transaksi";
        }

        return view('reports.print', compact(
            'transactions',
            'totalTransactions',
            'totalIn',
            'totalOut',
            'type',
            'recipientType',
            'month',
            'year',
            'startDate',
            'endDate',
            'categoryId',
            'selectedCategory',
            'periodLabel'
        ));
    }

    /**
     * Rekapitulasi Data Mutasi Barang
     */
    public function rekapitulasi(Request $request)
    {
        if (!auth()->user()->isKasubagTU() && !auth()->user()->isPegawaiGudang()) {
            return redirect()->route('dashboard')->with('error', 'Halaman Rekapitulasi Laporan hanya dapat diakses oleh Kasubbag TU dan Pegawai Gudang.');
        }

        $year = $request->get('year', date('Y'));
        $month = $request->get('month');
        $type = $request->get('type');
        $recipientType = $request->get('recipient_type');

        // Rekapitulasi per Kategori Barang
        $categoriesRekap = Category::with(['products.transactions' => function ($q) use ($year, $month, $type, $recipientType) {
            $q->whereYear('transaction_date', $year);
            if ($month) {
                $q->whereMonth('transaction_date', $month);
            }
            if ($type) {
                $q->where('type', $type);
            }
            if ($recipientType) {
                if ($recipientType === 'subdit') {
                    $q->where('type', 'keluar')
                      ->where(function ($sub) {
                          $sub->where('recipient_type', 'subdit')
                              ->orWhere('note', 'like', '%Subdit%')
                              ->orWhere('uraian', 'like', '%Subdit%');
                      });
                } elseif (in_array($recipientType, ['pegawai', 'individu'])) {
                    $q->where('type', 'keluar')
                      ->where(function ($sub) {
                          $sub->where('recipient_type', 'individu')
                              ->orWhere('note', 'like', '%Individu%')
                              ->orWhere('note', 'like', '%Pegawai%')
                              ->orWhere(function ($fallback) {
                                  $fallback->whereNull('recipient_type')
                                           ->where('note', 'not like', '%Subdit%')
                                           ->where('uraian', 'not like', '%Subdit%');
                              });
                      });
                }
            }
        }])->get()->map(function ($cat) {
            $totalIn = 0;
            $totalOut = 0;
            $totalNilai = 0;
            $totalStok = 0;

            foreach ($cat->products as $p) {
                $in = $p->transactions->where('type', 'masuk')->sum('quantity');
                $out = $p->transactions->where('type', 'keluar')->sum('quantity');
                $totalIn += $in;
                $totalOut += $out;
                $totalStok += $p->stock;
                $totalNilai += ($p->stock * ($p->purchase_price ?? 0));
            }

            return (object) [
                'id' => $cat->id,
                'name' => $cat->name,
                'product_count' => $cat->products->count(),
                'total_in' => $totalIn,
                'total_out' => $totalOut,
                'current_stock' => $totalStok,
                'total_value' => $totalNilai,
            ];
        })->sort(function ($a, $b) {
            $aHasData = ($a->total_in > 0 || $a->total_out > 0 || $a->current_stock > 0 || $a->total_value > 0);
            $bHasData = ($b->total_in > 0 || $b->total_out > 0 || $b->current_stock > 0 || $b->total_value > 0);

            // Prioritaskan kategori yang memiliki barang masuk, keluar, sisa stok, atau nilai saldo
            if ($aHasData !== $bHasData) {
                return $bHasData <=> $aHasData;
            }

            // 1. Urutkan dari yang memiliki Barang Masuk terbesar
            if ($b->total_in !== $a->total_in) {
                return $b->total_in <=> $a->total_in;
            }

            // 2. Urutkan dari yang memiliki Barang Keluar terbesar
            if ($b->total_out !== $a->total_out) {
                return $b->total_out <=> $a->total_out;
            }

            // 3. Urutkan dari yang memiliki Sisa Stok Fisik terbesar
            if ($b->current_stock !== $a->current_stock) {
                return $b->current_stock <=> $a->current_stock;
            }

            // 4. Urutkan dari yang memiliki Nilai Saldo terbesar
            if ($b->total_value != $a->total_value) {
                return $b->total_value <=> $a->total_value;
            }

            // 5. Fallback berdasarkan abjad nama jika nilainya sama
            return strcasecmp($a->name, $b->name);
        })->values();

        $totalAllProducts = Product::count();
        $totalAllStock = Product::sum('stock');
        $totalAllValue = Product::all()->sum(fn($p) => $p->stock * ($p->purchase_price ?? 0));

        // Grand totals mutasi seluruh kategori (untuk footer tabel)
        $grandTotalMasuk = $categoriesRekap->sum('total_in');
        $grandTotalKeluar = $categoriesRekap->sum('total_out');
        $grandTotalStok = $categoriesRekap->sum('current_stock');
        $grandTotalNilai = $categoriesRekap->sum('total_value');
        $totalCategoriesCount = $categoriesRekap->count();

        // Paginasi: 10 kategori per halaman
        $perPage = (int) $request->get('per_page', 10);
        if ($perPage <= 0) {
            $perPage = 10;
        }

        $currentPage = Paginator::resolveCurrentPage('page') ?: 1;
        $currentPageItems = $categoriesRekap->forPage($currentPage, $perPage)->values();

        $categoriesPaginated = (new LengthAwarePaginator(
            $currentPageItems,
            $totalCategoriesCount,
            $perPage,
            $currentPage,
            [
                'path' => Paginator::resolveCurrentPath(),
            ]
        ))->withQueryString();

        return view('reports.rekapitulasi', [
            'categoriesRekap' => $categoriesPaginated,
            'year' => $year,
            'month' => $month,
            'type' => $type,
            'recipientType' => $recipientType,
            'totalAllProducts' => $totalAllProducts,
            'totalAllStock' => $totalAllStock,
            'totalAllValue' => $totalAllValue,
            'grandTotalMasuk' => $grandTotalMasuk,
            'grandTotalKeluar' => $grandTotalKeluar,
            'grandTotalStok' => $grandTotalStok,
            'grandTotalNilai' => $grandTotalNilai,
        ]);
    }

    /**
     * Cetak Dokumen Resmi Berita Acara Rekapitulasi Mutasi Barang (Arsip Administrasi)
     */
    public function beritaAcara(Request $request)
    {
        if (!auth()->user()->isKasubagTU() && !auth()->user()->isPegawaiGudang()) {
            return redirect()->route('dashboard')->with('error', 'Halaman Berita Acara hanya dapat diakses oleh Kasubbag TU dan Pegawai Gudang.');
        }

        $year = $request->get('year', date('Y'));
        $month = $request->get('month');
        $type = $request->get('type');
        $recipientType = $request->get('recipient_type');

        $query = StockTransaction::with(['product.category', 'user'])
            ->whereYear('transaction_date', $year);

        if ($month) {
            $query->whereMonth('transaction_date', $month);
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($recipientType) {
            if ($recipientType === 'subdit') {
                $query->where('type', 'keluar')
                      ->where(function ($sub) {
                          $sub->where('recipient_type', 'subdit')
                              ->orWhere('note', 'like', '%Subdit%')
                              ->orWhere('uraian', 'like', '%Subdit%');
                      });
            } elseif (in_array($recipientType, ['pegawai', 'individu'])) {
                $query->where('type', 'keluar')
                      ->where(function ($sub) {
                          $sub->where('recipient_type', 'individu')
                              ->orWhere('note', 'like', '%Individu%')
                              ->orWhere('note', 'like', '%Pegawai%')
                              ->orWhere(function ($fallback) {
                                  $fallback->whereNull('recipient_type')
                                           ->where('note', 'not like', '%Subdit%')
                                           ->where('uraian', 'not like', '%Subdit%');
                              });
                      });
            }
        }

        $transactions = $query->orderBy('transaction_date', 'asc')->get();
        $totalIn = $transactions->where('type', 'masuk')->sum('quantity');
        $totalOut = $transactions->where('type', 'keluar')->sum('quantity');
        $totalProducts = Product::count();
        $totalStock = Product::sum('stock');
        $totalValue = Product::all()->sum(fn($p) => $p->stock * ($p->purchase_price ?? 0));

        $periodLabel = $month 
            ? \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y')
            : "Tahun {$year}";

        return view('reports.berita_acara', compact(
            'transactions',
            'totalIn',
            'totalOut',
            'totalProducts',
            'totalStock',
            'totalValue',
            'periodLabel',
            'year',
            'month',
            'type',
            'recipientType'
        ));
    }

    /**
     * Halaman Interaktif Kartu Persediaan Barang (Pilih Barang, Bulan, Tahun & Pratinjau)
     */
    public function kartuPersediaan(Request $request)
    {
        if (!auth()->user()->isKasubagTU() && !auth()->user()->isPegawaiGudang()) {
            return redirect()->route('dashboard')->with('error', 'Halaman Kartu Persediaan hanya dapat diakses oleh Kasubbag TU dan Pegawai Gudang.');
        }

        $productId = $request->get('product_id');
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));
        $onlyActive = $request->get('only_active', '1') === '1';

        // Cari ID barang yang memiliki transaksi pada periode bulan dan tahun ini
        $activeProductIds = StockTransaction::query()
            ->when($year, fn($q) => $q->whereYear('transaction_date', $year))
            ->when($month, fn($q) => $q->whereMonth('transaction_date', $month))
            ->distinct()
            ->pluck('product_id')
            ->toArray();

        // Daftar barang aktif bulan ini (beserta jumlah transaksinya)
        $activeProducts = Product::whereIn('id', $activeProductIds)
            ->withCount(['transactions' => function ($q) use ($year, $month) {
                $q->when($year, fn($sq) => $sq->whereYear('transaction_date', $year))
                  ->when($month, fn($sq) => $sq->whereMonth('transaction_date', $month));
            }])
            ->orderBy('name', 'asc')
            ->get();

        // Ambil semua daftar barang untuk katalog lengkap
        $allProducts = Product::orderBy('name', 'asc')->get();

        // Tentukan barang yang sedang dipilih
        $selectedProduct = null;
        if ($productId) {
            $selectedProduct = Product::find($productId);
        }

        if (!$selectedProduct) {
            // Prioritaskan barang pertama yang memiliki mutasi bulan ini
            $selectedProduct = $activeProducts->first() ?: $allProducts->first();
            if ($selectedProduct) {
                $productId = $selectedProduct->id;
            }
        }

        $transactions = collect();
        $saldoAwal = 0;
        $monthName = '';

        if ($selectedProduct) {
            $queryTransactions = StockTransaction::with(['requestItem.user', 'user', 'requestItem.distributor'])
                ->where('product_id', $selectedProduct->id);

            if ($month && $year) {
                $startDate = \Carbon\Carbon::createFromDate($year, (int)$month, 1)->startOfMonth();
                $monthName = \Carbon\Carbon::createFromDate($year, (int)$month, 1)->translatedFormat('F');

                $inBefore = StockTransaction::where('product_id', $selectedProduct->id)
                    ->whereDate('transaction_date', '<', $startDate)
                    ->where('type', 'masuk')
                    ->sum('quantity');

                $outBefore = StockTransaction::where('product_id', $selectedProduct->id)
                    ->whereDate('transaction_date', '<', $startDate)
                    ->where('type', 'keluar')
                    ->sum('quantity');

                $saldoAwal = max(0, $inBefore - $outBefore);

                $transactions = $queryTransactions
                    ->whereYear('transaction_date', $year)
                    ->whereMonth('transaction_date', (int)$month)
                    ->orderBy('transaction_date', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();
            } elseif ($year) {
                $monthName = 'Semua Bulan';
                $inBefore = StockTransaction::where('product_id', $selectedProduct->id)
                    ->whereYear('transaction_date', '<', $year)
                    ->where('type', 'masuk')
                    ->sum('quantity');

                $outBefore = StockTransaction::where('product_id', $selectedProduct->id)
                    ->whereYear('transaction_date', '<', $year)
                    ->where('type', 'keluar')
                    ->sum('quantity');

                $saldoAwal = max(0, $inBefore - $outBefore);

                $transactions = $queryTransactions
                    ->whereYear('transaction_date', $year)
                    ->orderBy('transaction_date', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();
            } else {
                $monthName = 'Semua Periode';
                $saldoAwal = 0;
                $transactions = $queryTransactions
                    ->orderBy('transaction_date', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();
            }
        }

        // Akun Kasubbag TU (Mengetahui)
        $kasubagUser = \App\Models\User::where('role', 'kasubag_tu')->first()
            ?: \App\Models\User::where('role', 'admin')->first();
        $kasubagName = $kasubagUser ? ($kasubagUser->name_with_title ?: $kasubagUser->name) : 'Utami M.';
        $kasubagNip = $kasubagUser && $kasubagUser->nip ? $kasubagUser->nip : '19850612 201001 2 003';

        // Akun Pegawai Gudang yang bertugas memeriksa laporan (Pembuat Daftar)
        $examinerUser = \App\Models\User::where('role', 'pegawai_gudang')->where('is_report_examiner', true)->latest('id')->first()
            ?: \App\Models\User::where('role', 'pegawai_gudang')->latest('id')->first();
        $examinerName = $examinerUser ? ($examinerUser->name_with_title ?: $examinerUser->name) : 'Petugas Pemeriksa';
        $examinerNip = $examinerUser && $examinerUser->nip ? $examinerUser->nip : '-';

        return view('reports.kartu_persediaan.index', compact(
            'allProducts',
            'activeProducts',
            'onlyActive',
            'selectedProduct',
            'productId',
            'month',
            'year',
            'monthName',
            'saldoAwal',
            'transactions',
            'kasubagName',
            'kasubagNip',
            'examinerName',
            'examinerNip'
        ));
    }

    /**
     * Cetak Lembar Kartu Persediaan Barang Format Resmi Pemerintah (A4 Landscape)
     */
    public function cetakKartuPersediaan(Request $request)
    {
        if (!auth()->user()->isKasubagTU() && !auth()->user()->isPegawaiGudang()) {
            return redirect()->route('dashboard')->with('error', 'Halaman Cetak Kartu Persediaan hanya dapat diakses oleh Kasubbag TU dan Pegawai Gudang.');
        }

        $productId = $request->get('product_id');
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));

        $monthName = ($month && $year) 
            ? \Carbon\Carbon::createFromDate($year, (int)$month, 1)->translatedFormat('F')
            : ($year ? 'Semua Bulan' : 'Semua Periode');

        $printAll = ($productId === 'all');

        if ($printAll) {
            $activeProductIds = StockTransaction::query()
                ->when($year, fn($q) => $q->whereYear('transaction_date', $year))
                ->when($month, fn($q) => $q->whereMonth('transaction_date', $month))
                ->distinct()
                ->pluck('product_id')
                ->toArray();

            $productsList = Product::whereIn('id', $activeProductIds)->orderBy('name', 'asc')->get();
            if ($productsList->isEmpty()) {
                $firstProduct = Product::first();
                $productsList = $firstProduct ? collect([$firstProduct]) : collect();
            }
        } else {
            $selected = $productId ? Product::find($productId) : null;
            if (!$selected) {
                $firstTx = StockTransaction::orderBy('transaction_date', 'desc')->first();
                $selected = $firstTx ? $firstTx->product : Product::first();
            }
            $productsList = $selected ? collect([$selected]) : collect();
        }

        $cards = collect();

        foreach ($productsList as $product) {
            $queryTransactions = StockTransaction::with(['requestItem.user', 'user', 'requestItem.distributor'])
                ->where('product_id', $product->id);

            $saldoAwal = 0;
            if ($month && $year) {
                $startDate = \Carbon\Carbon::createFromDate($year, (int)$month, 1)->startOfMonth();

                $inBefore = StockTransaction::where('product_id', $product->id)
                    ->whereDate('transaction_date', '<', $startDate)
                    ->where('type', 'masuk')
                    ->sum('quantity');

                $outBefore = StockTransaction::where('product_id', $product->id)
                    ->whereDate('transaction_date', '<', $startDate)
                    ->where('type', 'keluar')
                    ->sum('quantity');

                $saldoAwal = max(0, $inBefore - $outBefore);

                $transactions = $queryTransactions
                    ->whereYear('transaction_date', $year)
                    ->whereMonth('transaction_date', (int)$month)
                    ->orderBy('transaction_date', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();
            } elseif ($year) {
                $inBefore = StockTransaction::where('product_id', $product->id)
                    ->whereYear('transaction_date', '<', $year)
                    ->where('type', 'masuk')
                    ->sum('quantity');

                $outBefore = StockTransaction::where('product_id', $product->id)
                    ->whereYear('transaction_date', '<', $year)
                    ->where('type', 'keluar')
                    ->sum('quantity');

                $saldoAwal = max(0, $inBefore - $outBefore);

                $transactions = $queryTransactions
                    ->whereYear('transaction_date', $year)
                    ->orderBy('transaction_date', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();
            } else {
                $transactions = $queryTransactions
                    ->orderBy('transaction_date', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();
            }

            $cards->push((object) [
                'product' => $product,
                'saldoAwal' => $saldoAwal,
                'transactions' => $transactions,
            ]);
        }

        // Akun Kasubbag TU (Mengetahui)
        $kasubagUser = \App\Models\User::where('role', 'kasubag_tu')->first()
            ?: \App\Models\User::where('role', 'admin')->first();
        $kasubagName = $kasubagUser ? ($kasubagUser->name_with_title ?: $kasubagUser->name) : 'Utami M.';
        $kasubagNip = $kasubagUser && $kasubagUser->nip ? $kasubagUser->nip : '19850612 201001 2 003';

        // Akun Pegawai Gudang yang bertugas memeriksa laporan (Pembuat Daftar)
        $examinerUser = \App\Models\User::where('role', 'pegawai_gudang')->where('is_report_examiner', true)->latest('id')->first()
            ?: \App\Models\User::where('role', 'pegawai_gudang')->latest('id')->first();
        $examinerName = $examinerUser ? ($examinerUser->name_with_title ?: $examinerUser->name) : 'Petugas Pemeriksa';
        $examinerNip = $examinerUser && $examinerUser->nip ? $examinerUser->nip : '-';

        return view('reports.kartu_persediaan.cetak', compact(
            'cards',
            'month',
            'year',
            'monthName',
            'kasubagName',
            'kasubagNip',
            'examinerName',
            'examinerNip',
            'printAll'
        ));
    }
}
