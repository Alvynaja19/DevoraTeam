<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Book extends Model
{
    protected $fillable = [
        'judul',
        'pengarang',
        'penerbit',
        'tahun_terbit',
        'isbn',
        'jumlah_buku',
        'stok',
        'kategori',
        'cover_image',
        'barcode',
        'no_klasifikasi',
        'kota',
        'perolehan',
        'tanggal_diterima',
    ];

    protected $casts = [
        'tanggal_diterima' => 'date',
    ];

    protected static function boot(): void
    {
        parent::boot();

        // Auto-generate barcode unik setelah buku dibuat
        // Format: BK-0001-XXXX
        static::created(function (Book $book) {
            $book->barcode = 'BK-' . str_pad($book->id, 4, '0', STR_PAD_LEFT) . '-' . strtoupper(Str::random(4));
            $book->saveQuietly();
        });
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }
}
