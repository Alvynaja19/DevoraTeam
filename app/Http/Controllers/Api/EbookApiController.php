<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EbookApiController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->query('q', 'indonesia'); // Default search
        $iaQuery = "({$query}) AND mediatype:texts AND NOT collection:inlibrary AND NOT collection:printdisabled";
        
        $response = \Illuminate\Support\Facades\Http::get('https://archive.org/advancedsearch.php', [
            'q' => $iaQuery,
            'output' => 'json',
            'rows' => 20,
            'page' => 1,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $items = $data['response']['docs'] ?? [];
            
            $formatted = collect($items)->map(function ($item) {
                $identifier = $item['identifier'] ?? '';
                
                $author = 'Tanpa Penulis';
                if (!empty($item['creator'])) {
                    $author = is_array($item['creator']) ? implode(', ', $item['creator']) : $item['creator'];
                }

                $description = 'Tidak ada sinopsis untuk buku digital ini.';
                if (!empty($item['description'])) {
                    $descStr = is_array($item['description']) ? implode(' ', $item['description']) : $item['description'];
                    $description = strip_tags($descStr);
                }

                return [
                    'id' => $identifier,
                    'title' => $item['title'] ?? 'Tanpa Judul',
                    'author' => $author,
                    'cover_image' => "https://archive.org/services/img/{$identifier}",
                    'publisher' => $item['publisher'] ?? '-',
                    'category' => ['name' => 'E-Book (Internet Archive)'],
                    'synopsis' => $description,
                    'avg_rating' => 4.5,
                    'pages' => $item['imagecount'] ?? 0,
                    'stock' => 999, // Infinite for e-books
                    'web_reader' => "https://archive.org/embed/{$identifier}?ui=embed",
                    'pdf_link' => null,
                ];
            });

            return response()->json(['data' => $formatted]);
        }

        return response()->json(['data' => []]);
    }

    public function show($source, $externalId)
    {
        $response = \Illuminate\Support\Facades\Http::get("https://archive.org/metadata/{$externalId}");

        if ($response->successful()) {
            $data = $response->json();
            $files = $data['files'] ?? [];
            $pdfFile = null;
            
            foreach ($files as $file) {
                if (isset($file['format']) && (strpos(strtoupper($file['format']), 'PDF') !== false)) {
                    $pdfFile = $file['name'];
                    break;
                }
            }
            
            $pdfLink = $pdfFile ? "https://archive.org/download/{$externalId}/" . ltrim($pdfFile, '/') : null;
            $webReader = "https://archive.org/embed/{$externalId}?ui=embed";

            $metadata = $data['metadata'] ?? [];
            $title = $metadata['title'] ?? 'Tanpa Judul';

            return response()->json([
                'status' => 200,
                'data' => [
                    'id' => $externalId,
                    'title' => is_array($title) ? implode(', ', $title) : $title,
                    'pdf_link' => $pdfLink,
                    'web_reader' => $webReader,
                    'cover_image' => "https://archive.org/services/img/{$externalId}",
                ]
            ]);
        }

        return response()->json(['status' => 404, 'data' => null], 404);
    }
}
