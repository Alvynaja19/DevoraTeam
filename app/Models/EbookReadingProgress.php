<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EbookReadingProgress extends Model
{
    public $timestamps = false;
    protected $fillable = ['member_id', 'source', 'external_id', 'progress_data', 'updated_at'];
    protected $casts = ['progress_data' => 'array', 'updated_at' => 'datetime'];

    public function member() { return $this->belongsTo(Member::class); }
}
