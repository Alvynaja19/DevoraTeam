<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use Illuminate\Http\Request;

class MemberApiController extends Controller
{
    /**
     * POST /api/v1/member/fcm-token
     * Simpan atau update FCM token device user yang login.
     */
    public function storeFcmToken(Request $request)
    {
        $request->validate([
            'token'  => 'required|string',
            'device' => 'nullable|string|max:50',
        ]);

        $user = $request->user();

        // Upsert: kalau token sudah ada update, kalau belum insert
        FcmToken::updateOrCreate(
            ['token' => $request->token],
            [
                'user_id' => $user->id,
                'device'  => $request->device ?? 'unknown',
            ]
        );

        return response()->json(['message' => 'FCM token saved']);
    }
}
