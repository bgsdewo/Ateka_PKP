<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\RequestItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

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

        // 1. Optimasi Statistik Produk (Cached 20s via local file store untuk akselerasi reload instan)
        $productStats = Cache::store('file')->remember('dashboard_product_stats', 20, function () {
            return DB::table('products')
                ->selectRaw('
                    COUNT(*) as total_products,
                    COALESCE(SUM(stock), 0) as total_stock,
                    COALESCE(SUM(stock * COALESCE(purchase_price, 0)), 0) as total_inventory_value,
                    COALESCE(SUM(CASE WHEN stock < 10 THEN 1 ELSE 0 END), 0) as low_stock_count
                ')
                ->first();
        });

        $totalProducts = (int) ($productStats->total_products ?? 0);
        $totalStock = (int) ($productStats->total_stock ?? 0);
        $totalInventoryValue = (float) ($productStats->total_inventory_value ?? 0);
        $lowStockCount = (int) ($productStats->low_stock_count ?? 0);

        // 2. Optimasi Transaksi Persediaan berdasarkan Periode (Cached 20s via local file store)
        $cacheKeyTx = "dashboard_tx_stats_{$selectedYear}_{$selectedMonth}";
        $txStats = Cache::store('file')->remember($cacheKeyTx, 20, function () use ($selectedYear, $selectedMonth) {
            $txQuery = DB::table('stock_transactions');
            if ($selectedYear) {
                $txQuery->whereYear('transaction_date', $selectedYear);
            }
            if ($selectedMonth) {
                $txQuery->whereMonth('transaction_date', $selectedMonth);
            }

            return $txQuery->selectRaw("
                COALESCE(SUM(CASE WHEN type = 'masuk' THEN quantity ELSE 0 END), 0) as total_stock_in,
                COALESCE(SUM(CASE WHEN type = 'keluar' THEN quantity ELSE 0 END), 0) as total_stock_out,
                COALESCE(SUM(CASE WHEN type = 'keluar' AND (recipient_type = 'subdit' OR note LIKE '%Subdit%') THEN quantity ELSE 0 END), 0) as total_stock_out_subdit,
                COALESCE(SUM(CASE WHEN type = 'keluar' AND (recipient_type = 'individu' OR (recipient_type != 'subdit' AND (note NOT LIKE '%Subdit%' OR note IS NULL))) THEN quantity ELSE 0 END), 0) as total_stock_out_individu
            ")->first();
        });

        $totalStockIn = (int) ($txStats->total_stock_in ?? 0);
        $totalStockOut = (int) ($txStats->total_stock_out ?? 0);
        $totalStockOutSubdit = (int) ($txStats->total_stock_out_subdit ?? 0);
        $totalStockOutIndividu = (int) ($txStats->total_stock_out_individu ?? 0);
        $totalStockInValue = 0;
        $totalStockOutValue = 0;

        // 3. Stok Kritis & Top Products (Cached 30s)
        $lowStockProducts = Cache::store('file')->remember('dashboard_low_stock_products', 30, function () {
            return Product::with('category')
                ->where('stock', '<', 10)
                ->orderBy('stock', 'asc')
                ->limit(5)
                ->get();
        });

        $topMinProducts = $lowStockProducts;
        $topMaxProducts = collect();

        // 4. Transaksi Terakhir (Cached 30s)
        $cacheKeyLatestTx = "dashboard_latest_tx_{$selectedYear}_{$selectedMonth}";
        $latestTransactions = Cache::store('file')->remember($cacheKeyLatestTx, 30, function () use ($selectedYear, $selectedMonth) {
            return StockTransaction::with(['product.category', 'user'])
                ->when($selectedYear, fn($q) => $q->whereYear('transaction_date', $selectedYear))
                ->when($selectedMonth, fn($q) => $q->whereMonth('transaction_date', $selectedMonth))
                ->latest('transaction_date')
                ->latest('id')
                ->limit(6)
                ->get();
        });

        // 5. Grafik Transaksi (Cached 20s via local file store)
        $cacheKeyMonthly = "dashboard_monthly_tx_{$selectedYear}_{$selectedMonth}";
        $monthlyTransactions = Cache::store('file')->remember($cacheKeyMonthly, 20, function () use ($selectedYear, $selectedMonth) {
            if ($selectedMonth) {
                return StockTransaction::select(
                    DB::raw('DATE(transaction_date) as date'),
                    DB::raw("SUM(CASE WHEN type = 'masuk' THEN quantity ELSE 0 END) as total_in"),
                    DB::raw("SUM(CASE WHEN type = 'keluar' THEN quantity ELSE 0 END) as total_out")
                )
                    ->whereMonth('transaction_date', $selectedMonth)
                    ->when($selectedYear, fn($q) => $q->whereYear('transaction_date', $selectedYear))
                    ->groupBy(DB::raw('DATE(transaction_date)'))
                    ->orderBy('date')
                    ->get();
            } else {
                return StockTransaction::select(
                    DB::raw('MONTH(transaction_date) as month_num'),
                    DB::raw("SUM(CASE WHEN type = 'masuk' THEN quantity ELSE 0 END) as total_in"),
                    DB::raw("SUM(CASE WHEN type = 'keluar' THEN quantity ELSE 0 END) as total_out")
                )
                    ->when($selectedYear, fn($q) => $q->whereYear('transaction_date', $selectedYear))
                    ->groupBy(DB::raw('MONTH(transaction_date)'))
                    ->orderBy('month_num')
                    ->get();
            }
        });

        if ($selectedMonth) {
            $chartLabels = $monthlyTransactions->pluck('date')->map(function ($date) {
                return date('d M', strtotime($date));
            })->values();
        } else {
            $monthNames = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
                7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
            ];

            $chartLabels = $monthlyTransactions->pluck('month_num')->map(function ($m) use ($monthNames) {
                return $monthNames[(int)$m] ?? "Bulan $m";
            })->values();
        }

        $chartIn = $monthlyTransactions->pluck('total_in')->map(fn($v) => (int)$v)->values();
        $chartOut = $monthlyTransactions->pluck('total_out')->map(fn($v) => (int)$v)->values();

        // 6. Ringkasan Antrean & Distribusi (Menggunakan Request-Scoped Cache di Model RequestItem)
        $pendingSummary = RequestItem::getPendingSummary();
        $pendingRequestsCount = $pendingSummary['total'];
        $readyDistribusiCount = RequestItem::getReadyDistribusiCount();
        $totalDistributedCount = 0;
        $pendingProcurementsExaminerCount = 0;

        // 7. Data Khusus Pengguna / Requester (Hanya dijalankan jika user login memang berstatus requester)
        $currentUser = auth()->user();
        $role = $currentUser->role ?? 'pegawai';
        $isKasubagTU = in_array($role, ['kasubag_tu', 'admin']);
        $isRequester = in_array($role, ['pegawai', 'pic_distribusi', 'subdit', 'member']) || empty($role);

        $userRequests = collect();
        $trackedRequests = collect();
        $userPendingCount = 0;
        $userApprovedCount = 0;
        $userDistributedCount = 0;
        $userRejectedCount = 0;
        $userProcurementBoughtCount = 0;
        $recentProcurementNotifications = collect();

        if ($currentUser && $isRequester && !$isKasubagTU) {
            $userId = $currentUser->id;

            $userRequests = RequestItem::with('details.product')
                ->where('user_id', $userId)
                ->when($selectedYear, fn($q) => $q->whereYear('created_at', $selectedYear))
                ->when($selectedMonth, fn($q) => $q->whereMonth('created_at', $selectedMonth))
                ->latest()
                ->limit(10)
                ->get();

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
                ->when($selectedYear, fn($q) => $q->whereYear('created_at', $selectedYear))
                ->when($selectedMonth, fn($q) => $q->whereMonth('created_at', $selectedMonth))
                ->orderByRaw("CASE WHEN status IN ('pending', 'approved') THEN 0 ELSE 1 END")
                ->latest()
                ->limit(5)
                ->get();

            // Optimasi penghitungan status requester dalam 1 query agregat
            $userCounts = RequestItem::where('user_id', $userId)
                ->when($selectedYear, fn($q) => $q->whereYear('created_at', $selectedYear))
                ->when($selectedMonth, fn($q) => $q->whereMonth('created_at', $selectedMonth))
                ->selectRaw("
                    COALESCE(SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END), 0) as pending_c,
                    COALESCE(SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END), 0) as approved_c,
                    COALESCE(SUM(CASE WHEN status = 'distributed' THEN 1 ELSE 0 END), 0) as distributed_c,
                    COALESCE(SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END), 0) as rejected_c,
                    COALESCE(SUM(CASE WHEN is_procurement = 1 AND status = 'approved' AND (purchase_invoice IS NOT NULL OR purchase_date IS NOT NULL) THEN 1 ELSE 0 END), 0) as procurement_bought_c
                ")
                ->first();

            $userPendingCount = (int) ($userCounts->pending_c ?? 0);
            $userApprovedCount = (int) ($userCounts->approved_c ?? 0);
            $userDistributedCount = (int) ($userCounts->distributed_c ?? 0);
            $userRejectedCount = (int) ($userCounts->rejected_c ?? 0);
            $userProcurementBoughtCount = (int) ($userCounts->procurement_bought_c ?? 0);

            $recentProcurementNotifications = $currentUser->notifications()
                ->whereIn('type', ['procurement_ready_distribution', 'procurement_available'])
                ->where('is_read', false)
                ->with('product')
                ->latest()
                ->take(15)
                ->get();
        }

        // 8. Label Periode
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
            'pendingProcurementsExaminerCount',
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
