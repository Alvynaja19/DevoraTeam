<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = ['member_id', 'book_id', 'queue_number', 'status', 'notified_at', 'expires_at', 'notes'];
    protected $casts = ['notified_at' => 'datetime', 'expires_at' => 'datetime'];

    public function member() { return $this->belongsTo(Member::class); }
    public function book()   { return $this->belongsTo(Book::class); }

    public function scopeMenunggu($query) { return $query->where('status', 'menunggu'); }
}
