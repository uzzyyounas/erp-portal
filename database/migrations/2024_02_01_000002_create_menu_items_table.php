<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
            $table->string('name');
            $table->string('icon')->default('bi-file-text');

            // type: report | form | link | divider
            $table->enum('type', ['report', 'form', 'link', 'divider'])->default('report');

            // Named Laravel route (e.g. reports.sales.summary)
            $table->string('route_name')->nullable();

            // Optional JSON route parameters e.g. {"slug": "monthly-sales"}
            $table->json('route_params')->nullable();

            $table->string('description')->nullable();
            $table->boolean('open_in_new_tab')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Item-level role access
        // If an item has NO rows here → inherits from module (visible to all who can see module)
        // If it has rows → only those roles (+ admin) can see it
        Schema::create('menu_item_role', function (Blueprint $table) {
            $table->foreignId('menu_item_id')->constrained('menu_items')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->primary(['menu_item_id', 'role_id']);
        });

        // ── Seed sample menu items for Sales module ───────────────────────
        $salesModuleId = DB::table('modules')->where('slug', 'sales')->value('id');
        $now = now();

        if ($salesModuleId) {
            DB::table('menu_items')->insert([
                [
                    'module_id'   => $salesModuleId,
                    'name'        => 'Sales Summary Report',
                    'icon'        => 'bi-bar-chart-fill',
                    'type'        => 'report',
                    'route_name'  => 'reports.sales.summary',
                    'route_params'=> null,
                    'description' => 'Monthly sales summary by salesman',
                    'sort_order'  => 1,
                    'is_active'   => true,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ],
                [
                    'module_id'   => $salesModuleId,
                    'name'        => 'Sales Order Entry',
                    'icon'        => 'bi-pencil-square',
                    'type'        => 'form',
                    'route_name'  => 'forms.sales.order',
                    'route_params'=> null,
                    'description' => 'Create a new sales order',
                    'sort_order'  => 2,
                    'is_active'   => true,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_item_role');
        Schema::dropIfExists('menu_items');
    }
};
