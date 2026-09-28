<?php

namespace App\Http\Controllers;

use App\Models\RequestItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DistributionController extends Controller
{
    /**
     * Tampilkan halaman pengelolaan dan koordinasi distribusi barang (PIC Distribusi)
     */
    public function index(Request $request)
    {
        $filterType = $request->get('type'); // individu, subdit, or null
        $filterStatus = $request->get('status', 'all'); // default to 'all' so handed over items remain visible
        $search = $request->get('search');
        $monthInput = $request->get('month');
        $yearInput = $request->get('year');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $isAllMonths = ($monthInput === 'all');
        $selectedMonth = ($isAllMonths || $monthInput === null || $monthInput === '') ? null : (int) $monthInput;
        $selectedYear = ($yearInput === 'all' || $yearInput === null || $yearInput === '') ? null : (int) $yearInput;

        $baseQuery = RequestItem::with(['user', 'details.product', 'distributor'])
            ->whereIn('status', ['approved', 'distributed'])
            ->when($filterType, function ($q) use ($filterType) {
                $q->where('type', $filterType);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('request_code', 'like', "%{$search}%")
                        ->orWhere('receiver_name', 'like', "%{$search}%")
                        ->orWhere('distribution_notes', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($uq) use ($search) {
                            $uq->where('name', 'like', "%{$search}%")
                               ->orWhere('position', 'like', "%{$search}%");
                        })
                        ->orWhereHas('details.product', function ($pq) use ($search) {
                            $pq->where('name', 'like', "%{$search}%")
                               ->orWhere('code', 'like', "%{$search}%");
                        });
                });
            })
            ->when($selectedYear, function ($q) use ($selectedYear) {
                $q->where(function ($sub) use ($selectedYear) {
                    $sub->whereYear('distributed_at', $selectedYear)
                        ->orWhere(function ($s2) use ($selectedYear) {
                            $s2->whereNull('distributed_at')
                               ->whereYear('created_at', $selectedYear);
                        });
                });
            })
            ->when($selectedMonth, function ($q) use ($selectedMonth) {
                $q->where(function ($sub) use ($selectedMonth) {
                    $sub->whereMonth('distributed_at', $selectedMonth)
                        ->orWhere(function ($s2) use ($selectedMonth) {
                            $s2->whereNull('distributed_at')
                               ->whereMonth('created_at', $selectedMonth);
                        });
                });
            })
            ->when($startDate, function ($q) use ($startDate) {
                $q->where(function ($sub) use ($startDate) {
                    $sub->whereDate('distributed_at', '>=', $startDate)
                        ->orWhere(function ($s2) use ($startDate) {
                            $s2->whereNull('distributed_at')
                               ->whereDate('created_at', '>=', $startDate);
                        });
                });
            })
            ->when($endDate, function ($q) use ($endDate) {
                $q->where(function ($sub) use ($endDate) {
                    $sub->whereDate('distributed_at', '<=', $endDate)
                        ->orWhere(function ($s2) use ($endDate) {
                            $s2->whereNull('distributed_at')
                               ->whereDate('created_at', '<=', $endDate);
                        });
                });
            });

        // Rekap statistik untuk PIC Distribusi
        $totalReady = (clone $baseQuery)->where('status', 'approved')->count();
        $totalDistributed = (clone $baseQuery)->where('status', 'distributed')->count();
        $totalSubditReady = (clone $baseQuery)->where('status', 'approved')->where('type', 'subdit')->count();
        $totalIndividuReady = (clone $baseQuery)->where('status', 'approved')->where('type', 'individu')->count();

        // Rekapitulasi kolektif per Subdirektorat
        $subditGroups = (clone $baseQuery)
            ->where('status', 'approved')
            ->where('type', 'subdit')
            ->get()
            ->groupBy(function ($item) {
                return $item->user->position ?? ($item->user->name ?? 'Subdit Umum');
            });

        $query = (clone $baseQuery)->latest('updated_at')->latest('id');

        if ($filterStatus === 'ready') {
            $query->where('status', 'approved');
        } elseif ($filterStatus === 'distributed') {
            $query->where('status', 'distributed');
        }

        $requests = $query->paginate(15)->withQueryString();

        return view('distribusi.index', compact(
            'requests',
            'totalReady',
            'totalDistributed',
            'totalSubditReady',
            'totalIndividuReady',
            'subditGroups',
            'filterType',
            'filterStatus',
            'search',
            'selectedMonth',
            'selectedYear',
            'startDate',
            'endDate',
            'isAllMonths'
        ));
    }

    /**
     * Konfirmasi penyerahan barang / selesaikan distribusi
     */
    public function confirmHandover(Request $request, $id)
    {
        $requestItem = RequestItem::with(['user', 'details.product'])->findOrFail($id);

        $request->validate([
            'receiver_name' => 'required|string|max:255',
            'distribution_notes' => 'nullable|string',
        ]);

        $requestItem->update([
            'status' => 'distributed',
            'distributed_at' => now(),
            'distributed_by' => Auth::id(),
            'receiver_name' => $request->receiver_name,
            'distribution_notes' => $request->distribution_notes,
        ]);

        return redirect()->back()
            ->with('success', 'Barang berhasil diserahterimakan ke ' . $request->receiver_name . ' (' . $requestItem->request_code . ').')
            ->with('print_id', $requestItem->id);
    }

    /**
     * Cetak Bukti Serah Terima Barang / Surat Jalan Distribusi ATK
     */
    public function printBukti($id)
    {
        $requestItem = RequestItem::with(['user', 'details.product', 'distributor'])->findOrFail($id);

        return view('distribusi.bukti', compact('requestItem'));
    }
}
