<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EbookBookmark extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'member_id', 'source', 'external_id', 'title', 'author',
        'cover_url', 'read_url', 'last_read', 'is_favorite', 'created_at',
    ];
    protected $casts = [
        'last_read'   => 'datetime',
        'is_favorite' => 'boolean',
        'created_at'  => 'datetime',
    ];

    public function member() { return $this->belongsTo(Member::class); }

    public function scopeFavorite($query) { return $query->where('is_favorite', true); }
}
