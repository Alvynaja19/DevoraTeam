<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fine extends Model
{
    protected $fillable = [
        'loan_item_id', 'fine_type', 'days_late', 'amount', 'status',
        'paid_at', 'confirmed_by', 'freed_by', 'freed_reason', 'notes',
    ];

    protected $casts = ['paid_at' => 'datetime', 'amount' => 'integer'];

    public function loanItem()   { return $this->belongsTo(LoanItem::class); }
    public function confirmedBy(){ return $this->belongsTo(User::class, 'confirmed_by'); }
    public function freedBy()    { return $this->belongsTo(User::class, 'freed_by'); }
    public function payments()   { return $this->hasMany(FinePayment::class); }

    public function scopeBelumLunas($query) { return $query->where('status', 'belum_lunas'); }
}
