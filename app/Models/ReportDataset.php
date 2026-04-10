<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportDataset extends Model
{
    protected $fillable = [
        'report_id', 'title', 'show_title', 'date_label', 'date_value',
        'sql_query', 'group_column', 'column_config', 'sort_order',
    ];

    protected $casts = [
        'show_title'    => 'boolean',
        'column_config' => 'array',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
