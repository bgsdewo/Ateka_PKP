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

        $query = RequestItem::with(['user', 'details.product'])->latest();

        if ($statusFilter && $statusFilter !== 'all') {
            if ($statusFilter === 'approved') {
                $query->whereIn('status', ['approved', 'distributed']);
            } else {
                $query->where('status', $statusFilter);
            }
        }

        $requests = $query->get();

        $pendingCount = RequestItem::where('status', 'pending')->count();
        $approvedCount = RequestItem::whereIn('status', ['approved', 'distributed'])->count();
        $rejectedCount = RequestItem::where('status', 'rejected')->count();
        $totalCount = RequestItem::count();

        return view('persetujuan.index', compact(
            'requests',
            'statusFilter',
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
