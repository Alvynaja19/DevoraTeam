<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group', 'description', 'updated_by'];

    public function updatedBy() { return $this->belongsTo(User::class, 'updated_by'); }

    /**
     * Ambil nilai setting berdasarkan key, dengan cast otomatis sesuai type.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $cached = Cache::remember("setting_{$key}", 3600, function () use ($key) {
            $setting = static::where('key', $key)->first();
            if (!$setting) return null;
            return ['value' => $setting->value, 'type' => $setting->type];
        });

        if (!$cached) return $default;

        return match ($cached['type'] ?? 'string') {
            'integer' => (int) $cached['value'],
            'boolean' => $cached['value'] === 'true',
            'json'    => json_decode($cached['value'], true),
            default   => $cached['value'],
        };
    }

    /**
     * Update setting dan bust cache.
     */
    public static function set(string $key, mixed $value): void
    {
        static::where('key', $key)->update(['value' => (string) $value]);
        Cache::forget("setting_{$key}");
    }
}
