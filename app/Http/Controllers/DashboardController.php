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



        $currentUser = auth()->user();

        $role = $currentUser->role ?? 'pegawai';

        $isKasubagTU = in_array($role, ['kasubag_tu', 'admin']);

        $isPegawaiGudang = $role === 'pegawai_gudang';

        $isRequester = in_array($role, ['pegawai', 'pic_distribusi', 'subdit', 'member']) || empty($role);



        // Hanya hitung statistik gudang & grafik jika user adalah Kasubbag TU atau Pegawai Gudang

        if ($isKasubagTU || $isPegawaiGudang) {

            // 1. Statistik Produk (Cached 60s untuk TTFB & LCP kilat)
            $productStats = Cache::store('file')->remember('dashboard_product_stats', 60, function () {                 return DB::table('products')
                    ->selectRaw('
                        COUNT(*) as total_products,
                        COALESCE(SUM(stock), 0) as total_stock,
                        COALESCE(SUM(stock * COALESCE(purchase_price, 0)), 0) as total_inventory_value,
                        COALESCE(SUM(CASE WHEN stock > 0 THEN 1 ELSE 0 END), 0) as available_products_count,
                        COALESCE(SUM(CASE WHEN stock <= 0 THEN 1 ELSE 0 END), 0) as out_of_stock_count,
                        COALESCE(SUM(CASE WHEN stock > 0 AND stock < 10 THEN 1 ELSE 0 END), 0) as low_stock_count
                    ')
                    ->first();
            });



            $totalProducts = (int) ($productStats->total_products ?? 0);

            $totalStock = (int) ($productStats->total_stock ?? 0);

            $totalInventoryValue = (float) ($productStats->total_inventory_value ?? 0);

            $availableProductsCount = (int) ($productStats->available_products_count ?? 0);

            $outOfStockCount = (int) ($productStats->out_of_stock_count ?? 0);

            $lowStockCount = (int) ($productStats->low_stock_count ?? 0);



            // 2. Transaksi Persediaan berdasarkan Periode (Cached 60s)
            $cacheKeyTxStats = "dashboard_tx_stats_{$selectedYear}_{$selectedMonth}";
            $txStats = Cache::store('file')->remember($cacheKeyTxStats, 60, function () use ($selectedYear, $selectedMonth) {
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
                    COALESCE(SUM(CASE WHEN type = 'keluar' AND (recipient_type = 'subdit' OR (recipient_type IS NULL AND (note LIKE '%Subdit%' OR uraian LIKE '%Subdit%'))) THEN quantity ELSE 0 END), 0) as total_stock_out_subdit,
                    COALESCE(SUM(CASE WHEN type = 'keluar' AND (recipient_type = 'individu' OR (recipient_type IS NULL AND (note IS NULL OR note NOT LIKE '%Subdit%') AND (uraian IS NULL OR uraian NOT LIKE '%Subdit%'))) THEN quantity ELSE 0 END), 0) as total_stock_out_individu
                ")->first();
            });



            $totalStockIn = (int) ($txStats->total_stock_in ?? 0);

            $totalStockOut = (int) ($txStats->total_stock_out ?? 0);

            $totalStockOutSubdit = (int) ($txStats->total_stock_out_subdit ?? 0);

            $totalStockOutIndividu = (int) ($txStats->total_stock_out_individu ?? 0);

            // Distribusi per permohonan (tiket) yang telah diserahterimakan pada periode terpilih.
            // Dihitung dari tabel requests agar permohonan perorangan (pegawai) & kolektif (subdit)
            // terhitung benar, tidak bergantung pada catatan teks mutasi stok.
            $cacheKeyDistrib = "dashboard_distrib_tickets_{$selectedYear}_{$selectedMonth}";
            $distribStats = Cache::store('file')->remember($cacheKeyDistrib, 60, function () use ($selectedYear, $selectedMonth) {
                $dq = DB::table('requests')->where('status', 'distributed');
                if ($selectedYear) {
                    $dq->whereYear('distributed_at', $selectedYear);
                }
                if ($selectedMonth) {
                    $dq->whereMonth('distributed_at', $selectedMonth);
                }
                return $dq->selectRaw("
                    COUNT(*) as total_tickets,
                    COALESCE(SUM(CASE WHEN type = 'subdit' THEN 1 ELSE 0 END), 0) as subdit_tickets
                ")->first();
            });
            $distribTotalTickets = (int) ($distribStats->total_tickets ?? 0);
            $distribSubditTickets = (int) ($distribStats->subdit_tickets ?? 0);
            $distribIndividuTickets = $distribTotalTickets - $distribSubditTickets;

            $totalStockInValue = 0;

            $totalStockOutValue = 0;



            // 2b. Daftar Barang yang Memiliki Stok Fisik Tersedia di Gudang (Eager load efisien)
            $availableProductsList = Product::select('id', 'name', 'code', 'bmn_code', 'stock', 'unit', 'purchase_price', 'category_id', 'image')
                ->with('category:id,name')
                ->where('stock', '>', 0)
                ->orderBy('stock', 'desc')
                ->paginate(5)
                ->withQueryString();

            // 3. Stok Kritis & Top Products (Cached 60s)
            $cacheKeyLowStock = 'dashboard_low_stock_products';
            $lowStockProducts = Cache::store('file')->remember($cacheKeyLowStock, 60, function () {
                $items = Product::select('id', 'name', 'code', 'bmn_code', 'stock', 'unit', 'category_id', 'image')
                    ->with('category:id,name')
                    ->where('stock', '>', 0)
                    ->where('stock', '<', 10)
                    ->orderBy('stock', 'asc')
                    ->limit(5)
                    ->get();
                if ($items->isEmpty()) {
                    $items = Product::select('id', 'name', 'code', 'bmn_code', 'stock', 'unit', 'category_id', 'image')
                        ->with('category:id,name')
                        ->where('stock', '<=', 0)
                        ->orderBy('id', 'asc')
                        ->limit(5)
                        ->get();
                }
                return $items;
            });



            $topMinProducts = $lowStockProducts;

            $topMaxProducts = collect();



            // 4. Transaksi Terakhir

            $cacheKeyLatestTx = "dashboard_latest_tx_{$selectedYear}_{$selectedMonth}";

            $latestTransactions = Cache::store('file')->remember($cacheKeyLatestTx, 300, function () use ($selectedYear, $selectedMonth) {

                return StockTransaction::with(['product.category', 'user'])

                    ->when($selectedYear, fn($q) => $q->whereYear('transaction_date', $selectedYear))

                    ->when($selectedMonth, fn($q) => $q->whereMonth('transaction_date', $selectedMonth))

                    ->latest('transaction_date')

                    ->latest('id')

                    ->limit(6)

                    ->get();

            });



            // 5. Grafik Transaksi

            $cacheKeyMonthly = "dashboard_monthly_tx_{$selectedYear}_{$selectedMonth}";

            $monthlyTransactions = Cache::store('file')->remember($cacheKeyMonthly, 300, function () use ($selectedYear, $selectedMonth) {

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



            // 6. Ringkasan Antrean & Distribusi (Cached 30s)
            $pendingSummary = Cache::store('file')->remember('dashboard_pending_summary', 30, function () {
                return RequestItem::getPendingSummary();
            });

            $pendingRequestsCount = $pendingSummary['total'];
            $kasubagPendingProcurementCount = $pendingSummary['procurement'];
            $kasubagPendingStockCount = $pendingSummary['stock'];

            $isDistributorGudang = $currentUser && $currentUser->isPegawaiGudang() && $currentUser->isDistributor();

            $readyDistribusiCount = ($isPegawaiGudang && !$isDistributorGudang) 
                ? 0 
                : Cache::store('file')->remember('dashboard_ready_distribusi_count', 30, function () {
                    return RequestItem::getReadyDistribusiCount();
                });

            $totalDistributedCount = 0;

            // Hitung pengadaan disetujui TU yang menunggu stok masuk ke gudang (Cached 30s)
            $pendingPengadaanGudangCount = Cache::store('file')->remember('dashboard_pending_pengadaan_gudang_count', 30, function () {
                return DB::table('requests')
                    ->where('status', 'approved')
                    ->where('is_procurement', true)
                    ->whereExists(function ($q) {
                        $q->select(DB::raw(1))
                            ->from('request_details')
                            ->whereColumn('request_details.request_id', 'requests.id')
                            ->where('request_details.is_fulfilled', false);
                    })
                    ->count();
            });

            $pendingProcurementsExaminerCount = $pendingPengadaanGudangCount;



            // Antrean Tugas Operasional Terkini untuk Pegawai Gudang

            $gudangTasksList = collect();

            if ($isPegawaiGudang) {

                $gudangTasksQuery = RequestItem::with(['user', 'details.product'])

                    ->where('status', 'approved');



                // Jika pegawai gudang BUKAN distributor (pemeriksa barang fisik), hanya tampilkan pengadaan yang belum diinput stoknya

                if (!$isDistributorGudang) {

                    $gudangTasksQuery->where('is_procurement', true)

                        ->whereHas('details', function ($q) {

                            $q->where('is_fulfilled', false);

                        });

                } else {

                    // Pegawai Gudang Distribusi: Hanya tampilkan permohonan yang barangnya SIAP diserahkan

                    // (Permintaan stok reguler ATAU pengadaan yang seluruh itemnya sudah masuk gudang / fulfilled)

                    $gudangTasksQuery->where(function ($q) {

                        $q->where(function ($sub) {

                            $sub->where('is_procurement', false)

                                ->orWhere('is_procurement', 0)

                                ->orWhereNull('is_procurement');

                        })->orWhere(function ($sub) {

                            $sub->where(function ($p) {

                                $p->where('is_procurement', true)->orWhere('is_procurement', 1);

                            })->whereDoesntHave('details', function ($dq) {

                                $dq->where('is_fulfilled', false)

                                   ->orWhere('is_fulfilled', 0)

                                   ->orWhereNull('is_fulfilled');

                            });

                        });

                    });

                }



                $gudangTasksList = $gudangTasksQuery->latest('updated_at')

                    ->limit(8)

                    ->get();

            }



            // Hitung total disetujui periode ini untuk Kasubbag TU

            $cacheKeyApproved = "dashboard_kasubag_approved_{$selectedYear}_{$selectedMonth}";

            $kasubagApprovedCount = (int) Cache::store('file')->remember($cacheKeyApproved, 300, function () use ($selectedYear, $selectedMonth) {

                return DB::table('requests')

                    ->where('status', 'approved')

                    ->when($selectedYear, fn($q) => $q->whereYear('created_at', $selectedYear))

                    ->when($selectedMonth, fn($q) => $q->whereMonth('created_at', $selectedMonth))

                    ->count();

            });



            // Antrean verifikasi permohonan terkini untuk Kasubag TU

            $pendingVerifikasiList = collect();

            if ($isKasubagTU && $pendingRequestsCount > 0) {

                $pendingVerifikasiList = RequestItem::with(['user', 'details.product'])

                    ->where('status', 'pending')

                    ->latest()

                    ->limit(5)

                    ->get();

            }

        } else {

            // Defaults instan untuk Pegawai / Requester (0 overhead)

            $totalProducts = 0;

            $totalStock = 0;

            $totalInventoryValue = 0;

            $availableProductsCount = 0;

            $availableProductsList = collect();

            $outOfStockCount = 0;

            $lowStockCount = 0;

            $totalStockIn = 0;

            $totalStockOut = 0;

            $totalStockOutSubdit = 0;

            $totalStockOutIndividu = 0;
            $distribTotalTickets = 0;
            $distribSubditTickets = 0;
            $distribIndividuTickets = 0;

            $totalStockInValue = 0;

            $totalStockOutValue = 0;

            $lowStockProducts = collect();

            $topMinProducts = collect();

            $topMaxProducts = collect();

            $latestTransactions = collect();

            $chartLabels = collect();

            $chartIn = collect();

            $chartOut = collect();

            $isDistributorGudang = false;

            $pendingRequestsCount = 0;

            $kasubagApprovedCount = 0;

            $kasubagPendingProcurementCount = 0;

            $kasubagPendingStockCount = 0;

            $pendingVerifikasiList = collect();

            $readyDistribusiCount = 0;

            $pendingProcurementsExaminerCount = 0;

            $pendingPengadaanGudangCount = 0;

            $gudangTasksList = collect();

            $totalDistributedCount = 0;

        }



        // 7. Data Khusus Pengguna / Requester (Hanya dijalankan jika user login memang berstatus requester)



        $userRequests = collect();

        $trackedRequests = collect();

        $userTotalCount = 0;

        $userPendingCount = 0;

        $userApprovedCount = 0;

        $userDistributedCount = 0;

        $userRejectedCount = 0;

        $userProcurementBoughtCount = 0;

        $recentProcurementNotifications = collect();



        if ($currentUser && $isRequester && !$isKasubagTU) {

            $baseUserQuery = function ($q) use ($currentUser) {

                if ($currentUser->isPicDistribusi()) {

                    $q->where(function ($sub) use ($currentUser) {

                        $sub->where('user_id', $currentUser->id)

                            ->orWhere(function ($forPic) use ($currentUser) {

                                $forPic->forPic($currentUser);

                            });

                    });

                } else {

                    $q->where('user_id', $currentUser->id);

                }

            };



            $userRequests = RequestItem::with('details.product')

                ->where($baseUserQuery)

                ->when($selectedYear, fn($q) => $q->whereYear('created_at', $selectedYear))

                ->when($selectedMonth, fn($q) => $q->whereMonth('created_at', $selectedMonth))

                ->latest()

                ->limit(10)

                ->get();



            // Pelacakan menampilkan pengajuan yang MASIH AKTIF, tidak dibatasi bulan/tahun dashboard
            $trackedRequests = RequestItem::with('details.product')

                ->where($baseUserQuery)

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



            // Optimasi penghitungan status requester dalam 1 query agregat

            $userCounts = RequestItem::where($baseUserQuery)

                ->when($selectedYear, fn($q) => $q->whereYear('created_at', $selectedYear))

                ->when($selectedMonth, fn($q) => $q->whereMonth('created_at', $selectedMonth))

                ->selectRaw("

                    COUNT(*) as total_c,

                    COALESCE(SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END), 0) as pending_c,

                    COALESCE(SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END), 0) as approved_c,

                    COALESCE(SUM(CASE WHEN status = 'distributed' THEN 1 ELSE 0 END), 0) as distributed_c,

                    COALESCE(SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END), 0) as rejected_c,

                    COALESCE(SUM(CASE WHEN is_procurement = 1 AND status = 'approved' AND (purchase_invoice IS NOT NULL OR purchase_date IS NOT NULL) THEN 1 ELSE 0 END), 0) as procurement_bought_c

                ")

                ->first();



            $userTotalCount = (int) ($userCounts->total_c ?? 0);

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

            'availableProductsCount',

            'availableProductsList',

            'outOfStockCount',

            'kasubagApprovedCount',

            'kasubagPendingStockCount',

            'kasubagPendingProcurementCount',

            'pendingVerifikasiList',

            'totalStockIn',

            'totalStockInValue',

            'totalStockOut',

            'totalStockOutValue',

            'totalStockOutSubdit',

            'totalStockOutIndividu',
            'distribTotalTickets',
            'distribSubditTickets',
            'distribIndividuTickets',

            'lowStockCount',

            'lowStockProducts',

            'topMinProducts',

            'topMaxProducts',

            'latestTransactions',

            'chartLabels',

            'chartIn',

            'chartOut',

            'isDistributorGudang',

            'pendingRequestsCount',

            'readyDistribusiCount',

            'pendingProcurementsExaminerCount',

            'pendingPengadaanGudangCount',

            'gudangTasksList',

            'totalDistributedCount',

            'userRequests',

            'trackedRequests',

            'userTotalCount',

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

