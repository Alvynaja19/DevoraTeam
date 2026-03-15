<?php

namespace App\Imports;

use App\Models\Book;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Carbon\Carbon;

class BookImport implements ToModel, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    private int $imported = 0;
    private int $skipped  = 0;

    /**
     * Mapping kolom Excel perpustakaan → Model Book
     *
     * Kolom: No | No Klasifikasi | Jml Buku | Judul Buku | Penerbit |
     *        Kota | Tahun Terbit | ISBN | Penulis | Perolehan |
     *        Diterima Tanggal | Tahun
     */
    public function model(array $row): ?Book
    {
        $judul = trim($row['judul_buku'] ?? $row['judul'] ?? '');
        if (empty($judul)) {
            $this->skipped++;
            return null;
        }

        $isbn = $this->cleanValue($row['isbn'] ?? null);

        // Jika ISBN ada dan sudah ada di DB → update data, skip insert
        if ($isbn) {
            $existing = Book::where('isbn', $isbn)->first();
            if ($existing) {
                $existing->update($this->buildAttributes($row, $judul, $isbn));
                $this->skipped++;
                return null;
            }
        }

        $this->imported++;
        return new Book($this->buildAttributes($row, $judul, $isbn));
    }

    private function buildAttributes(array $row, string $judul, ?string $isbn): array
    {
        $jumlah = max(1, (int) ($row['jml_buku'] ?? $row['jumlah_buku'] ?? 1));

        return [
            'judul'            => $judul,
            'pengarang'        => trim($row['penulis'] ?? $row['pengarang'] ?? '-'),
            'penerbit'         => $this->cleanValue($row['penerbit'] ?? null),
            'kota'             => $this->cleanValue($row['kota'] ?? null),
            'tahun_terbit'     => $this->cleanValue($row['tahun_terbit'] ?? $row['tahun'] ?? null),
            'isbn'             => $isbn,
            'jumlah_buku'      => $jumlah,
            'stok'             => $jumlah,
            'no_klasifikasi'   => $this->cleanValue($row['no_klasifikasi'] ?? null),
            'perolehan'        => $this->cleanValue($row['perolehan'] ?? null),
            'tanggal_diterima' => $this->parseDate($row['diterima_tanggal'] ?? null),
        ];
    }

    private function cleanValue($value): ?string
    {
        $str = trim((string) ($value ?? ''));
        return $str !== '' ? $str : null;
    }

    private function parseDate($value): ?string
    {
        if (!$value) return null;
        try {
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            }
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getImportedCount(): int { return $this->imported; }
    public function getSkippedCount(): int  { return $this->skipped; }
}
