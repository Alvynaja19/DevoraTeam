<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanItem extends Model
{
    protected $fillable = [
        'loan_id', 'copy_id', 'returned_at', 'condition_after', 'returned_by', 'notes',
    ];

    protected $casts = ['returned_at' => 'datetime'];

    public function loan()       { return $this->belongsTo(Loan::class); }
    public function copy()       { return $this->belongsTo(BookCopy::class, 'copy_id'); }
    public function returnedBy() { return $this->belongsTo(User::class, 'returned_by'); }
    public function fines()      { return $this->hasMany(Fine::class); }

    public function isReturned(): bool { return !is_null($this->returned_at); }
    public function isLate(): bool
    {
        $dueDate = $this->loan?->effectiveDueDate();
        return !$this->isReturned() && $dueDate && now()->gt($dueDate);
    }
}
