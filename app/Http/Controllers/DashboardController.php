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
        $selectedYear = (int) $request->get('year', date('Y'));
        
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
        $periodTransactionsQuery = StockTransaction::whereYear('transaction_date', $selectedYear);
        if ($selectedMonth) {
            $periodTransactionsQuery->whereMonth('transaction_date', $selectedMonth);
        }

        $totalStockIn = (clone $periodTransactionsQuery)->where('type', 'masuk')->sum('quantity');
        $totalStockOut = (clone $periodTransactionsQuery)->where('type', 'keluar')->sum('quantity');

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

        // Jika transaksi pada periode kosong, ambil mutasi terkini agar widget tetap terisi informasi
        if ($latestTransactions->isEmpty()) {
            $latestTransactions = StockTransaction::with(['product.category', 'user'])
                ->latest('transaction_date')
                ->latest('id')
                ->limit(6)
                ->get();
        }

        // Grafik Transaksi: Jika bulan dipilih -> per tanggal. Jika 'all' -> per bulan.
        if ($selectedMonth) {
            $monthlyTransactions = StockTransaction::select(
                DB::raw('DATE(transaction_date) as date'),
                DB::raw("SUM(CASE WHEN type = 'masuk' THEN quantity ELSE 0 END) as total_in"),
                DB::raw("SUM(CASE WHEN type = 'keluar' THEN quantity ELSE 0 END) as total_out")
            )
                ->whereMonth('transaction_date', $selectedMonth)
                ->whereYear('transaction_date', $selectedYear)
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
                ->whereYear('transaction_date', $selectedYear)
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
        $readyDistribusiCount = RequestItem::where('status', 'approved')->count();
        $totalDistributedCount = RequestItem::where('status', 'distributed')->count();

        // Data khusus user requester yang sedang login
        $userId = auth()->id();
        $userRequests = RequestItem::with('details.product')
            ->where('user_id', $userId)
            ->latest()
            ->limit(5)
            ->get();
        $userPendingCount = RequestItem::where('user_id', $userId)->where('status', 'pending')->count();
        $userApprovedCount = RequestItem::where('user_id', $userId)->where('status', 'approved')->count();
        $userDistributedCount = RequestItem::where('user_id', $userId)->where('status', 'distributed')->count();
        $userRejectedCount = RequestItem::where('user_id', $userId)->where('status', 'rejected')->count();

        // Periode text label
        $monthNamesFull = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $selectedPeriodLabel = $selectedMonth 
            ? ($monthNamesFull[$selectedMonth] ?? "Bulan $selectedMonth") . " {$selectedYear}"
            : "Tahun {$selectedYear} (Semua Bulan)";

        return view('dashboard.index', compact(
            'totalProducts',
            'totalStock',
            'totalInventoryValue',
            'totalStockIn',
            'totalStockOut',
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
            'userPendingCount',
            'userApprovedCount',
            'userDistributedCount',
            'userRejectedCount',
            'selectedMonth',
            'selectedYear',
            'selectedPeriodLabel',
            'isAllMonths'
        ));
    }
}
