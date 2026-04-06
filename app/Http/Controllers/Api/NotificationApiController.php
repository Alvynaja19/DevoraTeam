<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MemberNotification;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    /**
     * GET /api/v1/notifications
     * Ambil daftar notifikasi milik user yang login.
     */
    public function index(Request $request)
    {
        $member = $request->user()->member;

        if (!$member) {
            return response()->json(['data' => [], 'unread_count' => 0]);
        }

        $notifications = MemberNotification::where('member_id', $member->id)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(fn($n) => [
                'id'         => $n->id,
                'type'       => $n->type,
                'title'      => $n->title,
                'body'       => $n->body,
                'data'       => $n->data,
                'is_read'    => $n->is_read,
                'created_at' => $n->created_at?->diffForHumans() ?? '',
                'sent_at'    => $n->sent_at?->translatedFormat('d M Y, H:i') ?? null,
            ]);

        $unreadCount = MemberNotification::where('member_id', $member->id)
            ->unread()
            ->count();

        return response()->json([
            'data'         => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * POST /api/v1/notifications/{id}/read
     * Tandai satu notifikasi sudah dibaca.
     */
    public function markRead(Request $request, int $id)
    {
        $member = $request->user()->member;

        if (!$member) {
            return response()->json(['message' => 'Member not found'], 404);
        }

        $notification = MemberNotification::where('id', $id)
            ->where('member_id', $member->id)
            ->first();

        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        if (!$notification->is_read) {
            $notification->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        return response()->json(['message' => 'Marked as read']);
    }

    /**
     * POST /api/v1/notifications/read-all
     * Tandai semua notifikasi user sudah dibaca.
     */
    public function markAllRead(Request $request)
    {
        $member = $request->user()->member;

        if (!$member) {
            return response()->json(['message' => 'Member not found'], 404);
        }

        MemberNotification::where('member_id', $member->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['message' => 'All notifications marked as read']);
    }
}
