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
     * Hapus notifikasi spesifik saat diklik / dibaca agar tidak menumpuk di riwayat
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = UserNotification::where('user_id', $user->id)->findOrFail($id);
        $notification->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'unread_count' => $user->unreadNotificationsCount,
            ]);
        }

        return redirect()->back()->with('success', 'Notifikasi telah dibersihkan.');
    }

    /**
     * Bersihkan semua notifikasi pengguna agar tidak menumpuk
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        UserNotification::where('user_id', $user->id)->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
            ]);
        }

        return redirect()->back()->with('success', 'Semua notifikasi telah dibersihkan.');
    }
}
