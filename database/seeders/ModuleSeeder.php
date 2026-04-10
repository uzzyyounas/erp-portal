<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Role;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        // ── Fetch Roles ────────────────────────────────────────────────
        $admin = Role::where('slug', 'admin')->first();
        $salesManager = Role::where('slug', 'sales-manager')->first();

        // ── Modules ───────────────────────────────────────────────────
        $salesModule = Module::firstOrCreate(['slug' => 'sales'], [
            'name'        => 'Sales',
            'icon'        => 'bi-cart-check-fill',
            'color'       => '#1a3a5c',
            'description' => 'Sales reports and order entry forms',
            'sort_order'  => 1,
            'is_active'   => true,
        ]);

        $financeModule = Module::firstOrCreate(['slug' => 'finance'], [
            'name'        => 'Finance',
            'icon'        => 'bi-bank',
            'color'       => '#198754',
            'description' => 'Financial reports and ledgers',
            'sort_order'  => 2,
            'is_active'   => true,
        ]);

        $inventoryModule = Module::firstOrCreate(['slug' => 'inventory'], [
            'name'        => 'Inventory',
            'icon'        => 'bi-boxes',
            'color'       => '#e8a020',
            'description' => 'Stock and inventory reports',
            'sort_order'  => 3,
            'is_active'   => true,
        ]);

        $hrModule = Module::firstOrCreate(['slug' => 'hr'], [
            'name'        => 'HR',
            'icon'        => 'bi-people-fill',
            'color'       => '#6f42c1',
            'description' => 'Human resources and payroll',
            'sort_order'  => 4,
            'is_active'   => true,
        ]);

        // ── Module Role Assignments ────────────────────────────────────
        // Finance & HR → Admin only
        if ($admin) {
            $financeModule->roles()->syncWithoutDetaching([$admin->id]);
            $hrModule->roles()->syncWithoutDetaching([$admin->id]);
        }

        // Sales → Admin + Sales Manager (optional if you want restriction)
        if ($admin && $salesManager) {
            $salesModule->roles()->syncWithoutDetaching([
                $admin->id,
                $salesManager->id
            ]);
        }

        // Inventory → Open (no restriction)
    }
}
