<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    public $timestamps = false;
    protected $fillable = ['member_id', 'visit_date', 'visit_time', 'category', 'scanned_by', 'notes', 'created_at'];
    protected $casts = ['visit_date' => 'date', 'created_at' => 'datetime'];

    public function member()    { return $this->belongsTo(Member::class); }
    public function scannedBy() { return $this->belongsTo(User::class, 'scanned_by'); }
}
