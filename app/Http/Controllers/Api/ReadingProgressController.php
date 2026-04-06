<?php

namespace App\Http\Controllers\Api;
use App\Models\ReadingProgress;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReadingProgressController extends Controller
{
    public function get($ebookId, Request $request)
    {
        $user = $request->user();

        $progress = ReadingProgress::where('user_id', $user->id)
            ->where('ebook_id', $ebookId)
            ->first();

        return response()->json([
            'progress' => $progress?->progress ?? 0
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        ReadingProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'ebook_id' => $request->ebook_id
            ],
            [
                'progress' => $request->progress
            ]
        );

        return response()->json(['status' => 'ok']);
    }
}
