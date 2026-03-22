<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinePayment extends Model
{
    public $timestamps = false;
    protected $fillable = ['fine_id', 'amount_paid', 'payment_date', 'receipt_number', 'received_by', 'notes', 'created_at'];
    protected $casts = ['payment_date' => 'date', 'created_at' => 'datetime'];

    public function fine()       { return $this->belongsTo(Fine::class); }
    public function receivedBy() { return $this->belongsTo(User::class, 'received_by'); }
}
