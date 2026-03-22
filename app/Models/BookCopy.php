<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookCopy extends Model
{
    protected $fillable = [
        'book_id', 'copy_code', 'barcode', 'condition', 'status', 'notes',
    ];

    public function book()      { return $this->belongsTo(Book::class); }
    public function loanItems() { return $this->hasMany(LoanItem::class, 'copy_id'); }

    public function scopeTersedia($query)
    {
        return $query->where('status', 'tersedia')->where('condition', '!=', 'hilang');
    }
}
