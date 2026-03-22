<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'classes';
    protected $fillable = ['name', 'grade', 'major', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function members() { return $this->hasMany(Member::class, 'class_id'); }

    public function scopeAktif($query) { return $query->where('is_active', true); }
}
