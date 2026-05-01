<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchBookDescriptions extends Command
{
    protected $signature = 'books:fetch-descriptions
                            {--limit=50 : Jumlah buku yang diproses sekali jalan}
                            {--all : Timpa semua deskripsi, termasuk yang sudah ada}
                            {--id= : Hanya proses buku dengan ID tertentu}
                            {--delay=1000 : Jeda antar request dalam milidetik}';

    protected $description = 'Auto-fetch deskripsi buku dari Google Books API berdasarkan ISBN atau judul+penulis';

    private const GOOGLE_BOOKS_API = 'https://www.googleapis.com/books/v1/volumes';

    public function handle(): int
    {
        $limit   = (int) $this->option('limit');
        $all     = $this->option('all');
        $id      = $this->option('id');
        $delayMs = (int) $this->option('delay');

        // Build query
        $query = Book::query();

        if ($id) {
            $query->where('id', $id);
        } elseif (! $all) {
            $query->whereNull('description')->orWhere('description', '');
        }

        $total = $query->count();

        if ($total === 0) {
            $this->info('✅ Semua buku sudah memiliki deskripsi.');
            return self::SUCCESS;
        }

        $this->info("📚 Ditemukan {$total} buku yang akan diproses.");
        $this->info("⚙️  Limit: {$limit} buku | Jeda: {$delayMs}ms per request");
        $this->newLine();

        $books   = $query->limit($limit)->get();
        $updated = 0;
        $failed  = 0;
        $skipped = 0;

        $bar = $this->output->createProgressBar($books->count());
        $bar->start();

        foreach ($books as $book) {
            $description = $this->fetchDescription($book);

            if ($description) {
                $book->update(['description' => $description]);
                $updated++;
            } elseif ($description === false) {
                $failed++;
            } else {
                $skipped++;
            }

            $bar->advance();
            usleep($delayMs * 1000); // jeda antar request
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Status', 'Jumlah'],
            [
                ['✅ Berhasil diisi', $updated],
                ['⚠️  Tidak ditemukan', $skipped],
                ['❌ Error', $failed],
            ]
        );

        if ($updated < $total && $total > $limit) {
            $remaining = $total - $books->count();
            $this->newLine();
            $this->warn("Masih ada {$remaining} buku belum diproses. Jalankan ulang perintah ini untuk melanjutkan.");
        }

        return self::SUCCESS;
    }

    /**
     * Cari deskripsi buku di Google Books API.
     * Return: string jika berhasil, null jika tidak ditemukan, false jika error.
     */
    private function fetchDescription(Book $book): string|null|false
    {
        try {
            // Prioritas 1: cari berdasarkan ISBN (paling akurat)
            if ($book->isbn) {
                $isbn = preg_replace('/[^0-9X]/', '', $book->isbn);
                $result = $this->queryGoogleBooks("isbn:{$isbn}");
                if ($result) return $result;
            }

            // Prioritas 2: cari berdasarkan judul + penulis
            $title  = urlencode($book->title);
            $author = $book->author ? '+inauthor:' . urlencode($book->author) : '';
            $result = $this->queryGoogleBooks("intitle:{$title}{$author}");
            if ($result) return $result;

            // Tidak ditemukan
            return null;

        } catch (\Exception $e) {
            Log::error("FetchBookDescriptions error for book #{$book->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Query ke Google Books API dan ambil description dari hasil pertama.
     */
    private function queryGoogleBooks(string $q): ?string
    {
        $response = Http::timeout(10)->get(self::GOOGLE_BOOKS_API, [
            'q'          => $q,
            'maxResults' => 3,
            'langRestrict' => 'id', // prioritaskan buku Indonesia
            'fields'     => 'items(volumeInfo(title,description))',
        ]);

        if (! $response->ok()) return null;

        $items = $response->json('items', []);

        // Coba ambil deskripsi dari hasil yang ada
        foreach ($items as $item) {
            $desc = $item['volumeInfo']['description'] ?? null;
            if ($desc && strlen($desc) > 30) {
                return $this->cleanDescription($desc);
            }
        }

        // Jika tidak ada hasil berbahasa Indonesia, coba tanpa batasan bahasa
        $response2 = Http::timeout(10)->get(self::GOOGLE_BOOKS_API, [
            'q'          => $q,
            'maxResults' => 3,
            'fields'     => 'items(volumeInfo(title,description))',
        ]);

        if ($response2->ok()) {
            foreach ($response2->json('items', []) as $item) {
                $desc = $item['volumeInfo']['description'] ?? null;
                if ($desc && strlen($desc) > 30) {
                    return $this->cleanDescription($desc);
                }
            }
        }

        return null;
    }

    /**
     * Bersihkan deskripsi dari HTML tags.
     */
    private function cleanDescription(string $description): string
    {
        // Strip HTML tags
        $clean = strip_tags($description);
        // Normalize whitespace
        $clean = preg_replace('/\s+/', ' ', $clean);
        return trim($clean);
    }
}
