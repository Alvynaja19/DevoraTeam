<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@perpus.smapa.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Sample books
        $books = [
            ['judul' => 'Laskar Pelangi', 'pengarang' => 'Andrea Hirata', 'penerbit' => 'Bentang Pustaka', 'tahun_terbit' => '2005', 'isbn' => '9789793062792', 'jumlah_buku' => 5, 'stok' => 5, 'kategori' => 'Novel'],
            ['judul' => 'Bumi Manusia', 'pengarang' => 'Pramoedya Ananta Toer', 'penerbit' => 'Hasta Mitra', 'tahun_terbit' => '1980', 'isbn' => '9789799731234', 'jumlah_buku' => 3, 'stok' => 3, 'kategori' => 'Novel'],
            ['judul' => 'Filosofi Teras', 'pengarang' => 'Henry Manampiring', 'penerbit' => 'Kompas', 'tahun_terbit' => '2018', 'isbn' => '9786024125981', 'jumlah_buku' => 4, 'stok' => 4, 'kategori' => 'Non-Fiksi'],
            ['judul' => 'Matematika SMA Kelas 10', 'pengarang' => 'Tim Kemendikbud', 'penerbit' => 'Kemendikbud', 'tahun_terbit' => '2022', 'isbn' => '9786023456789', 'jumlah_buku' => 10, 'stok' => 10, 'kategori' => 'Pelajaran'],
            ['judul' => 'Fisika SMA Kelas 11', 'pengarang' => 'Tim Kemendikbud', 'penerbit' => 'Kemendikbud', 'tahun_terbit' => '2022', 'isbn' => '9786023456790', 'jumlah_buku' => 8, 'stok' => 8, 'kategori' => 'Pelajaran'],
            ['judul' => 'Sejarah Indonesia', 'pengarang' => 'Tim Kemendikbud', 'penerbit' => 'Kemendikbud', 'tahun_terbit' => '2021', 'isbn' => '9786023456791', 'jumlah_buku' => 6, 'stok' => 6, 'kategori' => 'Pelajaran'],
            ['judul' => 'Sang Pemimpi', 'pengarang' => 'Andrea Hirata', 'penerbit' => 'Bentang Pustaka', 'tahun_terbit' => '2006', 'isbn' => '9789793062808', 'jumlah_buku' => 3, 'stok' => 3, 'kategori' => 'Novel'],
            ['judul' => 'Atomic Habits', 'pengarang' => 'James Clear', 'penerbit' => 'Gramedia', 'tahun_terbit' => '2019', 'isbn' => '9786020633176', 'jumlah_buku' => 4, 'stok' => 4, 'kategori' => 'Non-Fiksi'],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }

        // Sample members
        $members = [
            ['nama' => 'Ahmad Rizky', 'nis' => '2024001', 'kelas' => 'X-1', 'alamat' => 'Jl. Merdeka No. 10', 'telepon' => '081234567890'],
            ['nama' => 'Siti Nurhaliza', 'nis' => '2024002', 'kelas' => 'X-2', 'alamat' => 'Jl. Sudirman No. 15', 'telepon' => '081234567891'],
            ['nama' => 'Budi Santoso', 'nis' => '2024003', 'kelas' => 'XI-1', 'alamat' => 'Jl. Gatot Subroto No. 5', 'telepon' => '081234567892'],
            ['nama' => 'Dewi Lestari', 'nis' => '2024004', 'kelas' => 'XI-2', 'alamat' => 'Jl. Ahmad Yani No. 20', 'telepon' => '081234567893'],
            ['nama' => 'Eko Prasetyo', 'nis' => '2024005', 'kelas' => 'XII-1', 'alamat' => 'Jl. Diponegoro No. 8', 'telepon' => '081234567894'],
        ];

        foreach ($members as $member) {
            Member::create($member);
        }
    }
}
