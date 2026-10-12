<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockTransaction;
use Illuminate\Http\Request;

class BarangKeluarController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->isKasubagTU() && !auth()->user()->isPicDistribusi()) {
            return redirect()->route('dashboard')->with('error', 'Halaman Gudang hanya dapat diakses oleh Kasubag TU dan PIC Distribusi.');
        }

        $month = $request->get('month');
        $year = $request->get('year');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = StockTransaction::with(['product.category', 'user'])
            ->where('type', 'keluar');

        if ($startDate && $endDate) {
            $minDate = min($startDate, $endDate);
            $maxDate = max($startDate, $endDate);
            $query->whereDate('transaction_date', '>=', $minDate)
                  ->whereDate('transaction_date', '<=', $maxDate);
        } elseif ($startDate) {
            // Jika hanya tanggal awal yang diisi, filter tepat pada tanggal tersebut (filter harian)
            $query->whereDate('transaction_date', $startDate);
        } elseif ($endDate) {
            // Jika hanya tanggal akhir yang diisi, filter tepat pada tanggal tersebut
            $query->whereDate('transaction_date', $endDate);
        } else {
            // Jika tanggal spesifik tidak diisi, gunakan filter Tahun dan Bulan
            if ($year) {
                $query->whereYear('transaction_date', $year);
            }

            if ($month) {
                $query->whereMonth('transaction_date', $month);
            }
        }

        $transactions = $query->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // Ambil daftar tahun dinamis dari data transaksi untuk penggunaan jangka panjang
        $dbYears = StockTransaction::where('type', 'keluar')
            ->whereNotNull('transaction_date')
            ->selectRaw('YEAR(transaction_date) as year')
            ->distinct()
            ->pluck('year')
            ->toArray();

        $currentYear = (int) date('Y');
        $defaultYears = range($currentYear - 3, $currentYear + 3);
        $availableYears = array_unique(array_merge($dbYears, $defaultYears));
        rsort($availableYears);

        $totalQuantity = $transactions->sum('quantity');
        $totalTransactions = $transactions->count();
        $totalNominal = $transactions->sum(function ($tx) {
            $price = $tx->harga_perolehan > 0 ? $tx->harga_perolehan : ($tx->product->purchase_price ?? 0);
            return (float) ($tx->quantity * $price);
        });

        return view('inventory.barang-keluar', compact(
            'transactions',
            'month',
            'year',
            'startDate',
            'endDate',
            'availableYears',
            'totalQuantity',
            'totalTransactions',
            'totalNominal'
        ));
    }
}
