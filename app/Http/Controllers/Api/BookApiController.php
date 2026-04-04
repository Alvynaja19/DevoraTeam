<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookApiController extends Controller
{
    public function index(Request $request)
    {
        $books = \App\Models\Book::with('category', 'copies')
            ->latest()
            ->get()
            ->map(function($book) {
                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author,
                    'cover_image' => $book->cover_image ? (str_starts_with($book->cover_image, 'http') ? $book->cover_image : asset($book->cover_image)) : null,
                    'publisher' => $book->publisher,
                    'category' => ['name' => $book->category?->name ?? 'Uncategorized'],
                    'synopsis' => $book->description,
                    'avg_rating' => $book->avg_rating ?? 4.5,
                    'pages' => $book->pages ?? 0,
                    'stock' => $book->availableCopies()->count(),
                    'loan_count' => $book->total_loans ?? 0,
                ];
            });

        return response()->json([
            'data' => $books
        ]);
    }

    public function show($id)
    {
        $book = \App\Models\Book::with('category', 'copies')->findOrFail($id);
        
        return response()->json([
            'data' => [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'cover_image' => $book->cover_image ? (str_starts_with($book->cover_image, 'http') ? $book->cover_image : asset($book->cover_image)) : null,
                'publisher' => $book->publisher,
                'category' => ['name' => $book->category?->name ?? 'Uncategorized'],
                'synopsis' => $book->description,
                'avg_rating' => $book->avg_rating ?? 4.5,
                'pages' => $book->pages ?? 0,
                'stock' => $book->availableCopies()->count(),
                'loan_count' => $book->total_loans ?? 0,
            ]
        ]);
    }
}
