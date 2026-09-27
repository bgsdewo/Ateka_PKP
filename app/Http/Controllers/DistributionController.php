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
        $filterStatus = $request->get('status', 'ready'); // 'ready' (approved), 'distributed', 'all'
        $search = $request->get('search');

        $query = RequestItem::with(['user', 'details.product', 'distributor'])
            ->whereIn('status', ['approved', 'distributed']);

        if ($filterStatus === 'ready') {
            $query->where('status', 'approved');
        } elseif ($filterStatus === 'distributed') {
            $query->where('status', 'distributed');
        }

        if ($filterType) {
            $query->where('type', $filterType);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('request_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('position', 'like', "%{$search}%");
                  });
            });
        }

        $requests = $query->latest('updated_at')->get();

        // Rekap statistik untuk PIC Distribusi
        $totalReady = RequestItem::where('status', 'approved')->count();
        $totalDistributed = RequestItem::where('status', 'distributed')->count();
        $totalSubditReady = RequestItem::where('status', 'approved')->where('type', 'subdit')->count();
        $totalIndividuReady = RequestItem::where('status', 'approved')->where('type', 'individu')->count();

        // Rekapitulasi kolektif per Subdirektorat
        $subditGroups = RequestItem::with(['user', 'details.product'])
            ->where('status', 'approved')
            ->where('type', 'subdit')
            ->get()
            ->groupBy(function ($item) {
                return $item->user->position ?? ($item->user->name ?? 'Subdit Umum');
            });

        return view('distribusi.index', compact(
            'requests',
            'totalReady',
            'totalDistributed',
            'totalSubditReady',
            'totalIndividuReady',
            'subditGroups',
            'filterType',
            'filterStatus',
            'search'
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

        return redirect()->back()->with('success', 'Barang berhasil diserahterimakan ke ' . $request->receiver_name . ' (' . $requestItem->request_code . ').');
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
