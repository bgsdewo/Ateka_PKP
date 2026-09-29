<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequestItem;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $statusFilter = $request->get('status');
        $typeFilter = $request->get('type');
        $search = $request->get('search');
        $monthInput = $request->get('month');
        $yearInput = $request->get('year');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $isAllMonths = ($monthInput === 'all');
        $selectedMonth = ($isAllMonths || $monthInput === null || $monthInput === '') ? null : (int) $monthInput;
        $selectedYear = ($yearInput === 'all' || $yearInput === null || $yearInput === '') ? null : (int) $yearInput;

        $baseQuery = RequestItem::query()
            ->when($typeFilter, function ($q) use ($typeFilter) {
                $q->where('type', $typeFilter);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('request_code', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%")
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
            ->when($selectedYear, fn($q) => $q->whereYear('created_at', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->whereMonth('created_at', $selectedMonth))
            ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate));

        $pendingCount = (clone $baseQuery)->where('status', 'pending')->count();
        $approvedCount = (clone $baseQuery)->whereIn('status', ['approved', 'distributed'])->count();
        $rejectedCount = (clone $baseQuery)->where('status', 'rejected')->count();
        $totalCount = (clone $baseQuery)->count();

        $query = (clone $baseQuery)->with(['user', 'details.product'])->latest('created_at')->latest('id');

        if ($statusFilter && $statusFilter !== 'all') {
            if ($statusFilter === 'approved') {
                $query->whereIn('status', ['approved', 'distributed']);
            } else {
                $query->where('status', $statusFilter);
            }
        }

        $requests = $query->paginate(15)->withQueryString();

        return view('persetujuan.index', compact(
            'requests',
            'statusFilter',
            'typeFilter',
            'search',
            'selectedMonth',
            'selectedYear',
            'startDate',
            'endDate',
            'isAllMonths',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'totalCount'
        ));
    }

    public function updateStatus(Request $request, $id)
    {
        $item = RequestItem::with('details.product', 'user')->findOrFail($id);

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'nullable|string|max:255'
        ]);

        DB::beginTransaction();
        try {
            if ($request->status == 'approved') {
                // Cek ketersediaan stok terlebih dahulu
                foreach ($item->details as $detail) {
                    $product = $detail->product;
                    if ($product && $product->stock < $detail->quantity) {
                        return redirect()->back()->with('error', 'Gagal disetujui! Stok untuk barang "' . $product->name . '" tidak mencukupi (Sisa stok: ' . $product->stock . ').');
                    }
                }

                // Kurangi stok dan catat ke riwayat barang keluar
                foreach ($item->details as $detail) {
                    $product = $detail->product;
                    if ($product) {
                        // Kurangi stok master barang
                        $product->stock -= $detail->quantity;
                        $product->save();

                        // Catat otomatis ke riwayat Barang Keluar beserta nama pemohon sebagai Uraian/Penerima
                        StockTransaction::create([
                            'product_id'       => $product->id,
                            'user_id'          => Auth::id(),
                            'type'             => 'keluar', // Sesuai pilihan ENUM database ('masuk', 'keluar')
                            'recipient_type'   => $item->type ?? 'individu',
                            'quantity'         => $detail->quantity,
                            'stock_after'      => $product->stock, // Sisa stok terbaru
                            'uraian'           => $item->user->name ?? 'Pegawai', // <-- Nama pemohon otomatis masuk ke Uraian/Penerima
                            'kondisi'          => 'Baik',
                            'harga_perolehan'  => $product->purchase_price ?? 0,
                            'transaction_date' => now(),
                            'note'             => 'Pengeluaran Permintaan ' . ucfirst($item->type) . ' oleh ' . ($item->user->name ?? 'Pegawai') . ' (' . $item->request_code . ')'
                        ]);
                    }
                }
            }

            // Update status pengajuan
            $item->update([
                'status' => $request->status,
                'rejection_reason' => $request->status == 'rejected' ? $request->rejection_reason : null
            ]);

            DB::commit();

            $pesan = $request->status == 'approved'
                ? 'Status pengajuan berhasil disetujui! Stok otomatis berkurang dan tercatat di Barang Keluar.'
                : 'Permintaan berhasil ditolak.';

            return redirect()->route('persetujuan.index')->with('success', $pesan);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}
