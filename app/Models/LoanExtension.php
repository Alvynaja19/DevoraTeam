<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanExtension extends Model
{
    public $timestamps = false;
    protected $fillable = ['loan_id', 'old_due_date', 'new_due_date', 'extended_by', 'notes', 'created_at'];
    protected $casts = ['old_due_date' => 'date', 'new_due_date' => 'date', 'created_at' => 'datetime'];

    public function loan()       { return $this->belongsTo(Loan::class); }
    public function extendedBy() { return $this->belongsTo(User::class, 'extended_by'); }
}
