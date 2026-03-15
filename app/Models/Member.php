<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Member extends Model
{
    protected $fillable = [
        'user_id',
        'nama',
        'status',
        'nis',
        'kelas',
        'alamat',
        'telepon',
        'qr_code',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot(): void
    {
        parent::boot();

        // Auto-generate QR code unik setelah anggota dibuat
        // Format: MB-0001-XXXX
        static::created(function (Member $member) {
            $member->qr_code = 'MB-' . str_pad($member->id, 4, '0', STR_PAD_LEFT) . '-' . strtoupper(Str::random(4));
            $member->saveQuietly();
        });
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }
}
