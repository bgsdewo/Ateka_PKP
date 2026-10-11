<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $notifications = $user->notifications()
            ->take(20)
            ->get()
            ->map(function ($notif) {
                return [
                    'id' => $notif->id,
                    'title' => $notif->title,
                    'message' => $notif->message,
                    'type' => $notif->type,
                    'icon' => $notif->icon,
                    'link' => $notif->link,
                    'is_read' => (bool) $notif->is_read,
                    'time_ago' => $notif->created_at->diffForHumans(),
                    'created_at' => $notif->created_at->format('d/m/Y H:i'),
                ];
            });

        $unreadCount = $user->unreadNotificationsCount;

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'unread_count' => $unreadCount,
                'notifications' => $notifications,
            ]);
        }

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Tandai notifikasi spesifik sebagai sudah dibaca
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = UserNotification::where('user_id', $user->id)->findOrFail($id);
        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

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
