<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'loan_code', 'member_id', 'loan_date', 'due_date', 'loan_type',
        'competition_end_date', 'extended_count', 'extended_due_date',
        'status', 'created_by', 'notes',
    ];

    protected $casts = [
        'loan_date'            => 'date',
        'due_date'             => 'date',
        'competition_end_date' => 'date',
        'extended_due_date'    => 'date',
    ];

    public function member()     { return $this->belongsTo(Member::class); }
    public function createdBy()  { return $this->belongsTo(User::class, 'created_by'); }
    public function items()      { return $this->hasMany(LoanItem::class); }
    public function extensions() { return $this->hasMany(LoanExtension::class); }

    public function effectiveDueDate()
    {
        return $this->extended_due_date ?? $this->due_date;
    }

    public function scopeAktif($query)
    {
        return $query->whereIn('status', ['aktif', 'diperpanjang', 'terlambat']);
    }
}
