<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Book;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateUmumCategory extends Command
{
    protected $signature = 'app:migrate-umum-category';
    protected $description = 'Pindahkan buku dari kategori Umum ke Non Fiksi, lalu hapus kategori Umum';

    public function handle()
    {
        $umum = Category::where('name', 'Umum')->first();

        if (!$umum) {
            $this->info('Kategori Umum tidak ditemukan di database. Tidak ada yang perlu dilakukan.');
            return;
        }

        $this->info("Kategori Umum ditemukan (ID: {$umum->id}).");

        $totalBukuUmum = Book::where('category_id', $umum->id)->count();
        $this->info("Total buku dengan kategori Umum: {$totalBukuUmum}");

        if ($totalBukuUmum > 0) {
            // Cari atau buat kategori Non Fiksi sebagai target
            $nonFiksi = Category::where('name', 'Non Fiksi')->first();

            if (!$nonFiksi) {
                $this->error('Kategori Non Fiksi tidak ditemukan! Proses dibatalkan.');
                return;
            }

            $this->info("Memindahkan {$totalBukuUmum} buku ke kategori '{$nonFiksi->name}' (ID: {$nonFiksi->id})...");
            Book::where('category_id', $umum->id)->update(['category_id' => $nonFiksi->id]);
            $this->info("Berhasil memindahkan semua buku.");
        }

        // Hapus kategori Umum
        $umum->delete();
        $this->info("Kategori 'Umum' berhasil dihapus dari database.");
        $this->info("Selesai!");
    }
}
