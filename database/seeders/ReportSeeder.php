<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        // ── Report Categories ─────────────────────────────────────────────
        $categories = [
            [
                'name'        => 'Sales Reports',
                'slug'        => 'sales-reports',
                'icon'        => 'bi-graph-up-arrow',
                'description' => 'Customer receivables, aging, and sales analysis.',
                'sort_order'  => 1,
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            // Add more categories here as needed
        ];

        foreach ($categories as $cat) {
            DB::table('report_categories')->updateOrInsert(
                ['slug' => $cat['slug']],
                $cat
            );
        }

        $salesCategoryId = DB::table('report_categories')
            ->where('slug', 'sales-reports')
            ->value('id');

        // ── Reports ───────────────────────────────────────────────────────
        $reports = [
            [
                'category_id' => $salesCategoryId,
                'name'        => 'Aged Customer Analysis',
                'slug'        => 'aged-customer-analysis',        // ← must match controller slug
                'icon'        => 'bi-bar-chart-steps',
                'route'       => 'aged-customer-analysis.index',
                'description' => 'Customer receivables aging report grouped by 0, 1-30, 31-60, and 60+ days.',
                'sort_order'  => 1,
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            // Add more reports here as needed
        ];

        foreach ($reports as $report) {
            DB::table('reports')->updateOrInsert(
                ['slug' => $report['slug']],
                $report
            );
        }

        $this->command->info('Reports seeded successfully.');
    }
}
