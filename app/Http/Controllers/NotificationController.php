<?php

namespace App\Http\Controllers;

use App\Models\RequestItem;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    /**
     * Dapatkan daftar notifikasi pengguna
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['unread_count' => 0, 'notifications' => []], 401);
        }

        $isKasubagTU = $user->isKasubagTU();
        $isDistributorGudang = $user->isPegawaiGudang() && $user->isDistributor();

        $notifications = collect();
        $stats = [
            'total_pending' => 0,
            'pending_procurement' => 0,
            'pending_stock' => 0,
            'history_count' => 0,
        ];

        if ($isKasubagTU) {
            // 1. Permintaan Pending yang Memerlukan Verifikasi Kasubbag TU
            $pendingQuery = RequestItem::where('status', 'pending')
                ->with(['user', 'details.product']);

            $pendingProcurementCount = (clone $pendingQuery)->where('is_procurement', true)->count();
            $pendingStockCount = (clone $pendingQuery)->where(function ($q) {
                $q->where('is_procurement', false)->orWhereNull('is_procurement');
            })->count();
            $totalPending = $pendingProcurementCount + $pendingStockCount;

            $stats['total_pending'] = $totalPending;
            $stats['pending_procurement'] = $pendingProcurementCount;
            $stats['pending_stock'] = $pendingStockCount;

            $pendingRequests = (clone $pendingQuery)->latest()->get();

            foreach ($pendingRequests as $req) {
                $pemohonName = $req->user ? ($req->user->name_with_title ?: $req->user->name) : 'Pegawai';
                $unitKerja = $req->user ? ($req->user->position ?? ($req->type === 'subdit' ? 'Subdirektorat' : 'Pegawai')) : 'Pegawai';
                $detailCount = $req->details->count();

                if ($req->is_procurement) {
                    $itemNames = $req->details->pluck('custom_product_name')->filter()->values();
                } else {
                    $itemNames = $req->details->map(function ($d) {
                        return $d->product ? $d->product->name : $d->custom_product_name;
                    })->filter()->values();
                }

                $itemSummary = $itemNames->take(3)->join(', ');
                if ($detailCount > 3) {
                    $itemSummary .= ' (+' . ($detailCount - 3) . ' lainnya)';
                } elseif (empty($itemSummary)) {
                    $itemSummary = 'Barang ATK';
                }

                $category = $req->is_procurement ? 'pengadaan' : 'persediaan';

                $notifications->push([
                    'id' => 'req-' . $req->id,
                    'request_id' => $req->id,
                    'request_code' => $req->request_code,
                    'title' => $req->is_procurement ? 'Usulan Pengadaan Barang Baru (Beli)' : 'Permintaan Barang Persediaan Gudang',
                    'message' => $pemohonName . ' (' . $unitKerja . ') mengajukan kebutuhan: ' . $itemSummary . ($req->notes ? ' &bull; Alasan: "' . Str::limit($req->notes, 75) . '"' : ''),
                    'type' => $req->is_procurement ? 'pending_procurement' : 'pending_stock',
                    'category' => $category,
                    'badge_label' => $req->is_procurement ? 'Pengadaan (Beli)' : 'Persediaan (Gudang)',
                    'badge_class' => $req->is_procurement ? 'bg-warning-subtle text-dark border border-warning' : 'bg-info-subtle text-primary border border-info',
                    'status_badge' => 'Menunggu Verifikasi',
                    'status_badge_class' => 'bg-danger text-white',
                    'link' => route('persetujuan.index', ['category' => $category]),
                    'action_text' => 'Verifikasi Sekarang',
                    'is_read' => false,
                    'is_pending' => true,
                    'requester' => $pemohonName,
                    'unit_name' => $unitKerja,
                    'item_summary' => $itemSummary,
                    'notes' => $req->notes,
                    'time_ago' => $req->created_at->diffForHumans(),
                    'created_at' => $req->created_at->format('d/m/Y H:i'),
                    'timestamp' => $req->created_at->timestamp,
                ]);
            }

            // 2. Riwayat Keputusan Verifikasi Kasubbag TU (Approved / Rejected)
            $historyRequests = RequestItem::whereIn('status', ['approved', 'rejected'])
                ->with(['user', 'details.product'])
                ->latest('updated_at')
                ->take(20)
                ->get();

            $stats['history_count'] = $historyRequests->count();

            foreach ($historyRequests as $req) {
                $pemohonName = $req->user ? ($req->user->name_with_title ?: $req->user->name) : 'Pegawai';
                $unitKerja = $req->user ? ($req->user->position ?? ($req->type === 'subdit' ? 'Subdirektorat' : 'Pegawai')) : 'Pegawai';
                $isApproved = $req->status === 'approved';
                $detailCount = $req->details->count();
                $firstDetail = $req->details->first();
                $itemName = $firstDetail ? ($firstDetail->item_name ?: 'Barang ATK') : 'Barang ATK';
                if ($detailCount > 1) {
                    $itemName .= ' (+' . ($detailCount - 1) . ' item)';
                }

                $category = $req->is_procurement ? 'pengadaan' : 'persediaan';

                $notifications->push([
                    'id' => 'hist-' . $req->id,
                    'request_id' => $req->id,
                    'request_code' => $req->request_code,
                    'title' => ($req->is_procurement ? 'Pengadaan: ' : 'Persediaan: ') . $itemName . ' (' . ($isApproved ? 'Disetujui' : 'Ditolak') . ')',
                    'message' => 'Permintaan dari ' . $pemohonName . ' (' . $unitKerja . ') telah selesai diverifikasi oleh Kasubbag TU.',
                    'type' => $isApproved ? 'approved' : 'rejected',
                    'category' => 'history',
                    'badge_label' => $req->is_procurement ? 'Pengadaan' : 'Persediaan',
                    'badge_class' => 'bg-secondary-subtle text-secondary border',
                    'status_badge' => $isApproved ? 'Selesai Disetujui' : 'Ditolak',
                    'status_badge_class' => $isApproved ? 'bg-success text-white' : 'bg-secondary text-white',
                    'link' => route('persetujuan.index', ['category' => $category]),
                    'action_text' => 'Lihat Rincian',
                    'is_read' => true,
                    'is_pending' => false,
                    'requester' => $pemohonName,
                    'unit_name' => $unitKerja,
                    'item_summary' => $itemName,
                    'notes' => $req->notes,
                    'time_ago' => ($req->updated_at ?: $req->created_at)->diffForHumans(),
                    'created_at' => ($req->updated_at ?: $req->created_at)->format('d/m/Y H:i'),
                    'timestamp' => ($req->updated_at ?: $req->created_at)->timestamp,
                ]);
            }

            $unreadCount = $totalPending + $user->unreadNotificationsCount;

        } elseif ($isDistributorGudang) {
            // Antrean Distribusi Fisik Barang untuk Petugas Gudang (Distributor)
            $readyDistribusiQuery = RequestItem::where('status', 'approved')
                ->where(function ($q) {
                    $q->where('is_procurement', false)->orWhereNull('is_procurement');
                })
                ->with(['user', 'details.product']);

            $readyItems = (clone $readyDistribusiQuery)->latest('updated_at')->latest('id')->get();
            $stats['total_pending'] = $readyItems->count();

            foreach ($readyItems as $req) {
                $pemohonName = $req->user ? ($req->user->name_with_title ?: $req->user->name) : 'Pemohon';
                $unitKerja = $req->user ? ($req->user->position ?? ($req->type === 'subdit' ? 'Subdirektorat' : 'Pegawai')) : 'Pegawai';
                $detailCount = $req->details->count();
                $firstDetail = $req->details->first();
                $itemName = $firstDetail ? ($firstDetail->item_name ?: 'Barang ATK') : 'Barang ATK';
                if ($detailCount > 1) {
                    $itemName .= ' (+' . ($detailCount - 1) . ' item)';
                }

                $notifications->push([
                    'id' => 'dist-' . $req->id,
                    'request_id' => $req->id,
                    'request_code' => $req->request_code,
                    'title' => 'Barang Siap Diserahkan ke Pemohon: ' . $itemName,
                    'message' => 'Permintaan barang dari ' . $pemohonName . ' (' . $unitKerja . ') telah disetujui Kasubbag TU dan siap diserahterimakan fisiknya.',
                    'type' => 'ready_distribution',
                    'category' => 'ready',
                    'badge_label' => $req->type === 'subdit' ? 'Kolektif Subdit' : 'Perorangan Pegawai',
                    'badge_class' => $req->type === 'subdit' ? 'bg-success-subtle text-success border border-success' : 'bg-primary-subtle text-primary border border-primary',
                    'status_badge' => 'Siap Diserahkan',
                    'status_badge_class' => 'bg-warning text-dark',
                    'link' => route('distribusi.index', ['status' => 'ready']),
                    'action_text' => 'Proses Serah Terima',
                    'is_read' => false,
                    'is_pending' => true,
                    'requester' => $pemohonName,
                    'unit_name' => $unitKerja,
                    'item_summary' => $itemName,
                    'notes' => $req->notes,
                    'time_ago' => ($req->updated_at ?: $req->created_at)->diffForHumans(),
                    'created_at' => ($req->updated_at ?: $req->created_at)->format('d/m/Y H:i'),
                    'timestamp' => ($req->updated_at ?: $req->created_at)->timestamp,
                ]);
            }

            $unreadCount = $stats['total_pending'] + $user->unreadNotificationsCount;

        } else {
            // Pengguna Reguler (Pegawai / Subdit)
            $unreadCount = $user->unreadNotificationsCount;
        }

        // Ambil notifikasi dari tabel user_notifications (milik user saat ini)
        $userNotifs = $user->notifications()->take(30)->get();
        foreach ($userNotifs as $notif) {
            $notifications->push([
                'id' => $notif->id,
                'request_id' => null,
                'request_code' => null,
                'title' => $notif->title,
                'message' => $notif->message,
                'type' => $notif->type,
                'category' => 'system',
                'badge_label' => 'Sistem ATK',
                'badge_class' => 'bg-info-subtle text-info border border-info',
                'status_badge' => $notif->is_read ? 'Sudah Dibaca' : 'Baru',
                'status_badge_class' => $notif->is_read ? 'bg-light text-secondary border' : 'bg-danger text-white',
                'link' => $notif->link,
                'action_text' => 'Buka',
                'is_read' => (bool) $notif->is_read,
                'is_pending' => false,
                'time_ago' => $notif->created_at->diffForHumans(),
                'created_at' => $notif->created_at->format('d/m/Y H:i'),
                'timestamp' => $notif->created_at->timestamp,
            ]);
        }

        // Urutkan notifikasi: item yang pending ditaruh di paling atas, lalu berdasarkan waktu terbaru
        $notifications = $notifications->sortByDesc('timestamp')->sortByDesc('is_pending')->values();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'unread_count' => $unreadCount,
                'notifications' => $notifications,
            ]);
        }

        return redirect()->route('dashboard');
    }

    /**
     * Tandai notifikasi spesifik sebagai sudah dibaca
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        if (is_numeric($id)) {
            $notification = UserNotification::where('user_id', $user->id)->find($id);
            if ($notification) {
                $notification->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);
            }
        }

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'unread_count' => $user->unreadNotificationsCount,
            ]);
        }

        return redirect()->back()->with('success', 'Notifikasi telah ditandai sudah dibaca.');
    }

    /**
     * Tandai banyak notifikasi sekaligus sebagai sudah dibaca (batch dismiss)
     */
    public function markBatchAsRead(Request $request)
    {
        $user = Auth::user();
        $rawIds = $request->input('ids');

        if (is_string($rawIds)) {
            $ids = array_filter(array_map('trim', explode(',', $rawIds)));
        } elseif (is_array($rawIds)) {
            $ids = $rawIds;
        } else {
            $ids = [];
        }

        if (!empty($ids)) {
            UserNotification::where('user_id', $user->id)
                ->whereIn('id', $ids)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'unread_count' => $user->unreadNotificationsCount,
            ]);
        }

        return redirect()->back()->with('success', 'Notifikasi terpilih telah ditandai sudah dibaca.');
    }

    /**
     * Tandai semua notifikasi pengguna sebagai sudah dibaca
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        UserNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
            ]);
        }

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sudah dibaca.');
    }
}
