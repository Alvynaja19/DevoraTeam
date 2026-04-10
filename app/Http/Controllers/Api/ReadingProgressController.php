<?php

namespace App\Http\Controllers\Api;

use App\Models\ReadingProgress;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class ReadingProgressController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $data = ReadingProgress::where('user_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json($data);
    }
    public function get($ebookId, Request $request)
    {
        $user = $request->user();

        $progress = ReadingProgress::where('user_id', $user->id)
            ->where('ebook_id', $ebookId)
            ->first();

        return response()->json([
            'progress' => $progress?->progress ?? 0,
            'current_page' => $progress?->current_page ?? 0,
            'total_page' => $progress?->total_page ?? 0,
        ]);
    }

    public function update(Request $request)
    {
        Log::info($request->all());

        $user = $request->user();

        ReadingProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'ebook_id' => $request->ebook_id
            ],
            [
                'progress' => $request->progress,
                'current_page' => $request->current_page,
                'total_page' => $request->total_page
            ]
        );

        return response()->json(['status' => 'ok']);
    }
    
}
