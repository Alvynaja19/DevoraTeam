<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EbookCache extends Model
{
    public $timestamps = false;
    protected $fillable = ['source', 'external_id', 'data', 'cached_at', 'expires_at'];
    protected $casts = [
        'data'       => 'array',
        'cached_at'  => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now());
    }
}
