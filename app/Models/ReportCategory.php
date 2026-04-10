<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportCategory extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'description', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'category_id');
    }

    public function activeReports(): HasMany
    {
        return $this->hasMany(Report::class, 'category_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }
}
