<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingProgress extends Model
{
    protected $fillable = [
    'user_id',
    'ebook_id',
    'progress',
    'current_page',
    'total_page',
];
}
