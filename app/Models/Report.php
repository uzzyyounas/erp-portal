<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Report extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'description',
        'sql_query', 'blade_view', 'template_id', 'column_config',
        'designer_config',
        'pdf_paper_size', 'pdf_orientation', 'show_in_menu', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active'       => 'boolean',
        'show_in_menu'    => 'boolean',
        'column_config'   => 'array',
        'designer_config' => 'array',
    ];

    public function category(): BelongsTo { return $this->belongsTo(ReportCategory::class); }
    public function template()            { return $this->belongsTo(ReportTemplate::class, 'template_id'); }
    public function parameters(): HasMany { return $this->hasMany(ReportParameter::class)->orderBy('sort_order'); }
    public function datasets(): HasMany   { return $this->hasMany(ReportDataset::class)->orderBy('sort_order'); }
    public function roles(): BelongsToMany{ return $this->belongsToMany(Role::class, 'report_role'); }
    public function logs(): HasMany       { return $this->hasMany(ReportLog::class); }

    /** Has a visual designer config saved */
    public function hasDesignerConfig(): bool
    {
        return !empty($this->designer_config);
    }

    public function hasDatasets(): bool
    {
        return $this->datasets()->exists();
    }
}
