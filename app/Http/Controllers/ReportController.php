<?php

namespace App\Http\Controllers;

use App\Models\StockTransaction;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Halaman Utama Laporan Mutasi Persediaan (Filter, Excel, PDF, Cetak)
     */
    public function index(Request $request)
    {
        if (!auth()->user()->isKasubagTU() && !auth()->user()->isPicDistribusi() && !auth()->user()->isArsiparis()) {
            return redirect()->route('dashboard')->with('error', 'Halaman Laporan & Arsip hanya dapat diakses oleh Kasubag TU, PIC Distribusi, dan Arsiparis.');
        }

        $type = $request->type;
        $recipientType = $request->recipient_type; // 'subdit' or 'individu'
        $month = $request->month;
        $year = $request->year;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $categoryId = $request->category_id;

        $query = StockTransaction::with(['product.category', 'user'])
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
     * Rekapitulasi Data Mutasi Barang (Khusus Arsiparis / Kantor)
     */
    public function rekapitulasi(Request $request)
    {
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

        return view('reports.rekapitulasi', compact(
            'categoriesRekap',
            'year',
            'month',
            'type',
            'recipientType',
            'totalAllProducts',
            'totalAllStock',
            'totalAllValue'
        ));
    }

    /**
     * Cetak Dokumen Resmi Berita Acara Rekapitulasi Mutasi Barang (Arsip Administrasi)
     */
    public function beritaAcara(Request $request)
    {
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
}
