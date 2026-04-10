<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CompanySetting extends Model
{
    protected $fillable = ['key', 'value', 'group', 'label', 'type'];
    protected $table    = 'company_settings';

    /** Get a single setting value */
    public static function get(string $key, mixed $default = ''): string
    {
        return Cache::remember("company_setting_{$key}", 300, function () use ($key, $default) {
            $row = static::where('key', $key)->first();
            return $row ? ($row->value ?? '') : $default;
        });
    }

    /** Get all settings as key→value array */
    public static function all_settings(): array
    {
        return Cache::remember('company_settings_all', 300, function () {
            return static::all()->pluck('value', 'key')->toArray();
        });
    }

    /** Clear cache after save */
    protected static function booted(): void
    {
        static::saved(function ($setting) {
            Cache::forget("company_setting_{$setting->key}");
            Cache::forget('company_settings_all');
        });
    }
}
