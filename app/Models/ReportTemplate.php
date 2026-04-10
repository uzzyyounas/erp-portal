<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ReportTemplate extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'layout',
        'config', 'is_system', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'config'    => 'array',
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function reports()
    {
        return $this->hasMany(Report::class, 'template_id');
    }

    // ── FULL CONFIG SCHEMA ────────────────────────────────────────────────────
    public static function defaultConfig(): array
    {
        return [
            // ══ COLOURS ══════════════════════════════════════════════════════
            'color_header_bg'     => '#1a3a5c',
            'color_header_text'   => '#ffffff',
            'color_accent'        => '#e8a020',
            'color_row_even'      => '#f5f8ff',
            'color_row_odd'       => '#ffffff',
            'color_subtotal_bg'   => '#dce9f7',
            'color_total_bg'      => '#1a3a5c',
            'color_total_text'    => '#ffffff',
            'color_page_header'   => '#1a3a5c',
            'color_border'        => '#dee2e6',

            // ══ TYPOGRAPHY ════════════════════════════════════════════════════
            'font_size'           => 'medium',   // small | medium | large

            // ══ PAPER ═════════════════════════════════════════════════════════
            'paper_size'          => 'A4',
            'orientation'         => 'landscape',

            // ══ PAGE HEADER LAYOUT ════════════════════════════════════════════
            // header_style: none | simple | full | centered | logo-only
            'header_style'        => 'full',

            // Logo settings
            'logo_position'       => 'left',      // left | right | center | none
            'logo_height'         => 55,           // px in PDF
            'logo_url'            => '',           // override; blank = use company setting

            // Company info shown in header
            'header_show_company_name' => true,
            'header_show_tagline'      => false,
            'header_show_address'      => false,
            'header_show_phone'        => false,
            'header_show_email'        => false,
            'header_show_ntn'          => false,
            'header_company_name_size' => 'large',  // small | medium | large

            // Report title block (inside header)
            'header_show_report_title'    => true,
            'header_title_align'          => 'left',     // left | center | right
            'header_title_size'           => 'large',    // small | medium | large
            'header_show_category'        => true,
            'header_show_print_date'      => true,
            'header_show_generated_by'    => true,
            'header_date_format'          => 'd M Y',    // PHP date format
            'header_show_report_number'   => false,      // auto-incrementing number

            // Header divider
            'header_divider'           => 'thick',  // none | thin | thick | double | colored
            'header_divider_color'     => '',        // blank = color_accent

            // ══ PARAMETERS BAR ════════════════════════════════════════════════
            'show_params_bar'          => true,
            'params_bar_style'         => 'banner',  // banner | inline | table | none
            'params_bar_position'      => 'below-header', // below-header | above-table

            // ══ DATA TABLE ════════════════════════════════════════════════════
            'show_row_numbers'         => true,
            'show_totals_row'          => true,
            'show_subtotals'           => true,
            'show_record_count'        => true,
            'show_column_borders'      => false,
            'zebra_rows'               => true,
            'group_col_index'          => 0,

            // Column header style
            'col_header_style'         => 'filled',  // filled | outline | minimal | gradient

            // Master-detail specific
            'master_style'             => 'card',    // card | banner | grid

            // ══ FOOTER ════════════════════════════════════════════════════════
            'show_page_footer'         => true,
            'footer_left'              => 'company',   // company | report_name | custom | blank
            'footer_center'            => 'report_name', // company | report_name | custom | blank
            'footer_right'             => 'page',     // page | datetime | custom | blank
            'footer_custom_left'       => '',
            'footer_custom_center'     => '',
            'footer_custom_right'      => '',
            'footer_confidential'      => true,
            'footer_show_divider'      => true,

            // ══ EXTRAS ════════════════════════════════════════════════════════
            'watermark_text'           => '',        // e.g. DRAFT, CONFIDENTIAL
            'watermark_opacity'        => 8,         // percent (1–30)
        ];
    }

    public function getEffectiveConfig(): array
    {
        return array_merge(self::defaultConfig(), $this->config ?? []);
    }

    public function getFontPt(): string
    {
        return match($this->getEffectiveConfig()['font_size']) {
            'small'  => '7pt',
            'large'  => '9.5pt',
            default  => '8pt',
        };
    }

    protected static function booted(): void
    {
        static::creating(function ($t) {
            if (empty($t->slug)) $t->slug = Str::slug($t->name);
        });
    }

    public static function layoutOptions(): array
    {
        return [
            'tabular'       => ['label'=>'Tabular',         'icon'=>'bi-table',               'desc'=>'Simple rows with column totals'],
            'grouped'       => ['label'=>'Grouped Summary',  'icon'=>'bi-collection',           'desc'=>'Rows grouped by first column with subtotals'],
            'master-detail' => ['label'=>'Master-Detail',    'icon'=>'bi-layout-text-sidebar',  'desc'=>'Document header + line items table'],
            'statement'     => ['label'=>'Statement',        'icon'=>'bi-file-earmark-ruled',   'desc'=>'Account statement with running balance'],
            'aged'          => ['label'=>'Aged Analysis',    'icon'=>'bi-bar-chart-steps',      'desc'=>'Heat-map aging bucket columns'],
        ];
    }

    public function getLayoutLabel(): string
    {
        return self::layoutOptions()[$this->layout]['label'] ?? ucfirst($this->layout);
    }

    public function getLayoutIcon(): string
    {
        return self::layoutOptions()[$this->layout]['icon'] ?? 'bi-file-earmark';
    }
}
