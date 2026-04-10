<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── CLEAN DATABASE (Overwrite) ───────────────────────────────────
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('module_role')->truncate();
        DB::table('menu_item_role')->truncate(); // if exists
        DB::table('menu_items')->truncate();
        DB::table('modules')->truncate();
        DB::table('users')->truncate();
        DB::table('roles')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ── Roles ───────────────────────────────────────────────────────
        $admin = Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
            'description' => 'Full system access',
            'is_active' => true,
        ]);

        $manager = Role::create([
            'name' => 'Manager',
            'slug' => 'manager',
            'description' => 'View all reports',
            'is_active' => true,
        ]);

        $accountant = Role::create([
            'name' => 'Accountant',
            'slug' => 'accountant',
            'description' => 'Finance & accounting reports',
            'is_active' => true,
        ]);

        $sales = Role::create([
            'name' => 'Sales Staff',
            'slug' => 'sales',
            'description' => 'Sales reports only',
            'is_active' => true,
        ]);

        $viewer = Role::create([
            'name' => 'Viewer',
            'slug' => 'viewer',
            'description' => 'Read-only access',
            'is_active' => true,
        ]);

        // ── Users ───────────────────────────────────────────────────────
        User::create([
            'role_id' => $admin->id,
            'name' => 'System Administrator',
            'email' => 'admin@admin.com',
            'username' => 'admin',
            'password' => Hash::make('Admin@123'),
            'is_active' => true,
        ]);

        User::create([
            'role_id' => $manager->id,
            'name' => 'Demo Manager',
            'email' => 'manager@admin.com',
            'username' => 'manager',
            'password' => Hash::make('Manager@123'),
            'is_active' => true,
        ]);

        // ── Modules ─────────────────────────────────────────────────────
        $salesModule = Module::create([
            'name' => 'Sales',
            'slug' => 'sales',
            'icon' => 'bi-cart-check-fill',
            'color' => '#1a3a5c',
            'description' => 'Sales reports',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $financeModule = Module::create([
            'name' => 'Finance',
            'slug' => 'finance',
            'icon' => 'bi-bank',
            'color' => '#198754',
            'description' => 'Finance reports',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $inventoryModule = Module::create([
            'name' => 'Inventory',
            'slug' => 'inventory',
            'icon' => 'bi-boxes',
            'color' => '#e8a020',
            'description' => 'Inventory reports',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        $hrModule = Module::create([
            'name' => 'HR',
            'slug' => 'hr',
            'icon' => 'bi-people-fill',
            'color' => '#6f42c1',
            'description' => 'HR & Payroll',
            'sort_order' => 4,
            'is_active' => true,
        ]);

        // ── Module Role Mapping ─────────────────────────────────────────
        $financeModule->roles()->attach([$admin->id]);
        $hrModule->roles()->attach([$admin->id]);

        $salesModule->roles()->attach([$admin->id, $sales->id]);

        // Inventory = open (no attach)

        // ── Menu Items ──────────────────────────────────────────────────
        $monthlyReport = MenuItem::create([
            'module_id' => $salesModule->id,
            'name' => 'Monthly Sales Report',
            'slug' => 'sales-monthly-report',
            'icon' => 'bi-bar-chart-line',
            'type' => 'report',
            'route_name' => 'reports.sales.monthly',
            'description' => 'Sales summary',
            'sort_order' => 1,
            'is_active' => true,
            'show_in_menu' => true,
        ]);

        $orderEntry = MenuItem::create([
            'module_id' => $salesModule->id,
            'name' => 'Sales Order Entry',
            'slug' => 'sales-order-entry',
            'icon' => 'bi-pencil-square',
            'type' => 'form',
            'route_name' => 'forms.sales.order-entry',
            'description' => 'Create sales orders',
            'sort_order' => 2,
            'is_active' => true,
            'show_in_menu' => true,
        ]);

        // ── Menu Role Mapping ───────────────────────────────────────────
        $monthlyReport->roles()->attach([$admin->id, $sales->id]);

        // ── Future Modules (inactive) ───────────────────────────────────
        MenuItem::create([
            'module_id' => $financeModule->id,
            'name' => 'Trial Balance',
            'slug' => 'finance-trial-balance',
            'icon' => 'bi-journal-text',
            'type' => 'report',
            'route_name' => 'reports.finance.trial-balance',
            'description' => 'Coming soon',
            'sort_order' => 1,
            'is_active' => false,
            'show_in_menu' => false,
        ]);

        MenuItem::create([
            'module_id' => $inventoryModule->id,
            'name' => 'Stock Ledger',
            'slug' => 'inventory-stock-ledger',
            'icon' => 'bi-clipboard-data',
            'type' => 'report',
            'route_name' => 'reports.inventory.stock-ledger',
            'description' => 'Coming soon',
            'sort_order' => 1,
            'is_active' => false,
            'show_in_menu' => false,
        ]);
    }
}
