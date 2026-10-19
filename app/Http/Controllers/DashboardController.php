<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\RequestItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $yearInput = $request->get('year');
        $isAllYears = ($yearInput === 'all');
        if ($isAllYears) {
            $selectedYear = null;
        } elseif ($yearInput !== null && $yearInput !== '') {
            $selectedYear = (int) $yearInput;
        } else {
            $selectedYear = (int) date('Y');
        }
        
        $monthInput = $request->get('month');
        $isAllMonths = ($monthInput === 'all');

        if ($isAllMonths) {
            $selectedMonth = null;
        } elseif ($monthInput !== null && $monthInput !== '') {
            $selectedMonth = (int) $monthInput;
        } else {
            $selectedMonth = (int) date('n'); // default current month (1-12)
        }

        $totalProducts = Product::count();
        $totalStock = Product::sum('stock');

        // Nilai saldo seluruh persediaan (Stok * Harga Beli)
        $totalInventoryValue = Product::all()->sum(function ($product) {
            return $product->stock * ($product->purchase_price ?? 0);
        });

        // Transaksi Persediaan berdasarkan Periode yang dipilih
        $periodTransactionsQuery = StockTransaction::query();
        if ($selectedYear) {
            $periodTransactionsQuery->whereYear('transaction_date', $selectedYear);
        }
        if ($selectedMonth) {
            $periodTransactionsQuery->whereMonth('transaction_date', $selectedMonth);
        }

        $totalStockIn = (clone $periodTransactionsQuery)->where('type', 'masuk')->sum('quantity');
        $totalStockOut = (clone $periodTransactionsQuery)->where('type', 'keluar')->sum('quantity');

        $totalStockInValue = (clone $periodTransactionsQuery)
            ->where('type', 'masuk')
            ->with('product')
            ->get()
            ->sum(function ($tx) {
                $price = $tx->harga_perolehan > 0 ? $tx->harga_perolehan : ($tx->product->purchase_price ?? 0);
                return (float) ($tx->quantity * $price);
            });

        $totalStockOutValue = (clone $periodTransactionsQuery)
            ->where('type', 'keluar')
            ->with('product')
            ->get()
            ->sum(function ($tx) {
                $price = $tx->harga_perolehan > 0 ? $tx->harga_perolehan : ($tx->product->purchase_price ?? 0);
                return (float) ($tx->quantity * $price);
            });

        $totalStockOutSubdit = (clone $periodTransactionsQuery)
            ->where('type', 'keluar')
            ->where(function ($q) {
                $q->where('recipient_type', 'subdit')
                  ->orWhere('note', 'like', '%Subdit%');
            })
            ->sum('quantity');

        $totalStockOutIndividu = (clone $periodTransactionsQuery)
            ->where('type', 'keluar')
            ->where(function ($q) {
                $q->where('recipient_type', 'individu')
                  ->orWhere('note', 'not like', '%Subdit%');
            })
            ->sum('quantity');

        $lowStockCount = Product::where('stock', '<', 10)->count();

        $lowStockProducts = Product::with('category')
            ->where('stock', '<', 10)
            ->orderBy('stock', 'asc')
            ->limit(5)
            ->get();

        $topMinProducts = Product::with('category')
            ->where('stock', '<', 10)
            ->orderBy('stock', 'asc')
            ->limit(5)
            ->get();

        $topMaxProducts = Product::with('category')
            ->where('stock', '>=', 10)
            ->orderBy('stock', 'desc')
            ->limit(5)
            ->get();

        $latestTransactions = StockTransaction::with(['product.category', 'user'])
            ->when($selectedYear, fn($q) => $q->whereYear('transaction_date', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->whereMonth('transaction_date', $selectedMonth))
            ->latest('transaction_date')
            ->latest('id')
            ->limit(6)
            ->get();

        // Grafik Transaksi: Jika bulan dipilih -> per tanggal. Jika 'all' -> per bulan.
        if ($selectedMonth) {
            $monthlyTransactions = StockTransaction::select(
                DB::raw('DATE(transaction_date) as date'),
                DB::raw("SUM(CASE WHEN type = 'masuk' THEN quantity ELSE 0 END) as total_in"),
                DB::raw("SUM(CASE WHEN type = 'keluar' THEN quantity ELSE 0 END) as total_out")
            )
                ->whereMonth('transaction_date', $selectedMonth)
                ->when($selectedYear, fn($q) => $q->whereYear('transaction_date', $selectedYear))
                ->groupBy(DB::raw('DATE(transaction_date)'))
                ->orderBy('date')
                ->get();

            $chartLabels = $monthlyTransactions->pluck('date')->map(function ($date) {
                return date('d M', strtotime($date));
            })->values();
        } else {
            $monthlyTransactions = StockTransaction::select(
                DB::raw('MONTH(transaction_date) as month_num'),
                DB::raw("SUM(CASE WHEN type = 'masuk' THEN quantity ELSE 0 END) as total_in"),
                DB::raw("SUM(CASE WHEN type = 'keluar' THEN quantity ELSE 0 END) as total_out")
            )
                ->when($selectedYear, fn($q) => $q->whereYear('transaction_date', $selectedYear))
                ->groupBy(DB::raw('MONTH(transaction_date)'))
                ->orderBy('month_num')
                ->get();

            $monthNames = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
                7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
            ];

            $chartLabels = $monthlyTransactions->pluck('month_num')->map(function ($m) use ($monthNames) {
                return $monthNames[(int)$m] ?? "Bulan $m";
            })->values();
        }

        $chartIn = $monthlyTransactions->pluck('total_in')->map(function ($value) {
            return (int) $value;
        })->values();

        $chartOut = $monthlyTransactions->pluck('total_out')->map(function ($value) {
            return (int) $value;
        })->values();

        $pendingRequestsCount = RequestItem::where('status', 'pending')->count();

        $readyDistribusiQuery = RequestItem::where('status', 'approved')
            ->where(function ($q) {
                $q->where('is_procurement', false)->orWhereNull('is_procurement');
            });

        $totalDistributedQuery = RequestItem::where('status', 'distributed')
            ->where(function ($q) {
                $q->where('is_procurement', false)->orWhereNull('is_procurement');
            });

        $readyDistribusiCount = $readyDistribusiQuery->count();
        $totalDistributedCount = $totalDistributedQuery->count();

        // Data khusus user requester yang sedang login (Subdit / Pegawai)
        $userId = auth()->id();
        $userRequestsQuery = RequestItem::with('details.product')
            ->where('user_id', $userId);

        if ($selectedYear) {
            $userRequestsQuery->whereYear('created_at', $selectedYear);
        }

        if ($selectedMonth) {
            $userRequestsQuery->whereMonth('created_at', $selectedMonth);
        }

        $userRequests = (clone $userRequestsQuery)
            ->latest()
            ->limit(10)
            ->get();

        // Daftar pengajuan untuk widget Pelacakan Status:
        // Hanya tampilkan pengajuan yang sedang berjalan (pending, approved),
        // atau yang baru diserahterimakan (distributed) namun belum mengunggah berkas fisik tanda tangan (maksimal 24 jam).
        // Begitu berkas tanda tangan diunggah atau telah lewat 24 jam, otomatis disembunyikan agar tidak menumpuk.
        $trackedRequests = RequestItem::with('details.product')
            ->where('user_id', $userId)
            ->where(function ($q) {
                $q->whereIn('status', ['pending', 'approved'])
                  ->orWhere(function ($sub) {
                      $sub->where('status', 'distributed')
                          ->whereNull('signed_proof')
                          ->where(function ($timeQ) {
                              $timeQ->where('distributed_at', '>=', now()->subHours(24))
                                    ->orWhere(function ($fallbackQ) {
                                        $fallbackQ->whereNull('distributed_at')
                                                  ->where('updated_at', '>=', now()->subHours(24));
                                    });
                          });
                  });
            })
            ->orderByRaw("CASE WHEN status IN ('pending', 'approved') THEN 0 ELSE 1 END")
            ->latest()
            ->limit(5)
            ->get();

        $userPendingCount = (clone $userRequestsQuery)->where('status', 'pending')->count();
        $userApprovedCount = (clone $userRequestsQuery)->where('status', 'approved')->count();
        $userDistributedCount = (clone $userRequestsQuery)->where('status', 'distributed')->count();
        $userRejectedCount = (clone $userRequestsQuery)->where('status', 'rejected')->count();

        $userProcurementBoughtCount = (clone $userRequestsQuery)
            ->where('is_procurement', true)
            ->where('status', 'approved')
            ->where(function ($q) {
                $q->whereNotNull('purchase_invoice')
                  ->orWhereNotNull('purchase_date');
            })
            ->count();

        $recentProcurementNotifications = auth()->user()->notifications()
            ->where('type', 'procurement_available')
            ->where('is_read', false)
            ->with('product')
            ->latest()
            ->take(15)
            ->get();

        // Periode text label
        $monthNamesFull = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        if ($selectedMonth && $selectedYear) {
            $selectedPeriodLabel = ($monthNamesFull[$selectedMonth] ?? "Bulan $selectedMonth") . " {$selectedYear}";
        } elseif ($selectedMonth && !$selectedYear) {
            $selectedPeriodLabel = ($monthNamesFull[$selectedMonth] ?? "Bulan $selectedMonth") . " (Semua Tahun)";
        } elseif (!$selectedMonth && $selectedYear) {
            $selectedPeriodLabel = "Tahun {$selectedYear} (Semua Bulan)";
        } else {
            $selectedPeriodLabel = "Semua Periode";
        }

        return view('dashboard.index', compact(
            'totalProducts',
            'totalStock',
            'totalInventoryValue',
            'totalStockIn',
            'totalStockInValue',
            'totalStockOut',
            'totalStockOutValue',
            'totalStockOutSubdit',
            'totalStockOutIndividu',
            'lowStockCount',
            'lowStockProducts',
            'topMinProducts',
            'topMaxProducts',
            'latestTransactions',
            'chartLabels',
            'chartIn',
            'chartOut',
            'pendingRequestsCount',
            'readyDistribusiCount',
            'totalDistributedCount',
            'userRequests',
            'trackedRequests',
            'userPendingCount',
            'userApprovedCount',
            'userDistributedCount',
            'userRejectedCount',
            'userProcurementBoughtCount',
            'recentProcurementNotifications',
            'selectedMonth',
            'selectedYear',
            'selectedPeriodLabel',
            'isAllMonths',
            'isAllYears'
        ));
    }
}
