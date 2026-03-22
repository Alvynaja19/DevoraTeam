<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookRating extends Model
{
    protected $fillable = ['book_id', 'member_id', 'rating', 'review'];

    public function book()   { return $this->belongsTo(Book::class); }
    public function member() { return $this->belongsTo(Member::class); }
}
