<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportParameter extends Model
{
    protected $fillable = [
        'report_id', 'name', 'label', 'type',
        'options', 'options_source', 'depends_on', 'default_value',
        'is_required', 'placeholder', 'sort_order',
    ];

    protected $casts = ['is_required' => 'boolean'];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Return options as an array.
     * For 'static' source: decode JSON.
     * For 'sql' source: run the query.
     */
    public function resolveOptions(?string $companyPrefix = '0'): array
    {
        if ($this->type === 'company') {
            return $this->getCompanyOptions();
        }

        if ($this->options_source === 'sql' && $this->options) {
            try {
                $sql = str_replace('{company_prefix}', $companyPrefix, $this->options);
                $rows = \Illuminate\Support\Facades\DB::select($sql);
                return array_map(fn($r) => ['value' => $r->value, 'label' => $r->label], $rows);
            } catch (\Exception $e) {
                return [];
            }
        }

        if ($this->options) {
            $decoded = json_decode($this->options, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    private function getCompanyOptions(): array
    {
        // Detect available company prefixes from your ERP database
        $tables = \Illuminate\Support\Facades\DB::select("SHOW TABLES LIKE '%_fiscal_year'");
        $companies = [];
        foreach ($tables as $t) {
            $name = array_values((array)$t)[0];
            $prefix = str_replace('_fiscal_year', '', $name);
            $companies[] = ['value' => $prefix, 'label' => "Company {$prefix}"];
        }
        return $companies ?: [
            ['value' => '0', 'label' => 'Company 0 (Default)'],
            ['value' => '1', 'label' => 'Company 1'],
        ];
    }
}
