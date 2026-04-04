<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'category_id', 'isbn', 'title', 'author', 'publisher',
        'year', 'edition', 'language', 'pages', 'description',
        'cover_image', 'rack_number', 'total_copies', 'total_loans',
        'classification_number', 'city', 'acquisition_type', 'received_date', 'inventory_year'
    ];

    protected $casts = [
        'total_copies' => 'integer',
        'total_loans'  => 'integer',
    ];

    public function category()     { return $this->belongsTo(Category::class); }
    public function copies()       { return $this->hasMany(BookCopy::class); }

    public function availableCopies()
    {
        return $this->copies()->where('status', 'tersedia')->where('condition', '!=', 'hilang');
    }

    public function scopeSearch($query, string $term)
    {
        return $query->whereFullText(['title', 'author'], $term);
    }

    public function scopePopular($query)
    {
        return $query->orderByDesc('total_loans');
    }
}
