<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EbookApiController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->query('q', 'literature'); // Default search
        $apiKey = env('GOOGLE_BOOKS_API_KEY');
        
        $response = \Illuminate\Support\Facades\Http::get('https://www.googleapis.com/books/v1/volumes', [
            'q' => $query,
            'key' => $apiKey,
            'maxResults' => 20,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $items = $data['items'] ?? [];
            
            $formatted = collect($items)->map(function ($item) {
                $info = $item['volumeInfo'];
                return [
                    'id' => $item['id'],
                    'title' => $info['title'] ?? 'Tanpa Judul',
                    'author' => implode(', ', $info['authors'] ?? ['-']),
                    'cover_image' => isset($info['imageLinks']['thumbnail']) 
                        ? str_replace('http:', 'https:', $info['imageLinks']['thumbnail']) 
                        : null,
                    'publisher' => $info['publisher'] ?? '-',
                    'category' => ['name' => implode(', ', $info['categories'] ?? ['E-Book'])],
                    'synopsis' => $info['description'] ?? 'Tidak ada sinopsis untuk buku digital ini.',
                    'avg_rating' => $info['averageRating'] ?? 4.5,
                    'pages' => $info['pageCount'] ?? 0,
                    'stock' => 999, // Infinite for e-books
                    'web_reader' => $item['accessInfo']['webReaderLink'] ?? null,
                    'pdf_link' => $item['accessInfo']['pdf']['acsTokenLink'] ?? null,
                ];
            });

            return response()->json(['data' => $formatted]);
        }

        return response()->json(['data' => []]);
    }

    public function show($source, $externalId)
    {
        $apiKey = env('GOOGLE_BOOKS_API_KEY');
        $response = \Illuminate\Support\Facades\Http::get("https://www.googleapis.com/books/v1/volumes/{$externalId}", [
            'key' => $apiKey,
        ]);

        if ($response->successful()) {
            $item = $response->json();
            $info = $item['volumeInfo'];
            return response()->json([
                'data' => [
                    'id' => $item['id'],
                    'title' => $info['title'] ?? 'Tanpa Judul',
                    'author' => implode(', ', $info['authors'] ?? ['-']),
                    'cover_image' => isset($info['imageLinks']['thumbnail']) 
                        ? str_replace('http:', 'https:', $info['imageLinks']['thumbnail']) 
                        : null,
                    'publisher' => $info['publisher'] ?? '-',
                    'category' => ['name' => implode(', ', $info['categories'] ?? ['E-Book'])],
                    'synopsis' => $info['description'] ?? 'Tidak ada sinopsis untuk buku digital ini.',
                    'avg_rating' => $info['averageRating'] ?? 4.5,
                    'pages' => $info['pageCount'] ?? 0,
                    'stock' => 999,
                    'pdf_link' => $item['accessInfo']['pdf']['acsTokenLink'] ?? null,
                    'web_reader' => $item['accessInfo']['webReaderLink'] ?? null,
                ]
            ]);
        }

        return response()->json(['data' => null], 404);
    }
}
