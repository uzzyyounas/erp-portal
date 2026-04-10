<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->default('bi-grid');
            $table->string('color')->default('#1a3a5c');
            $table->string('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Module-level role access
        // If a module has NO rows here → accessible by all authenticated users
        // If it has rows → only those roles (+ admin) can see it
        Schema::create('module_role', function (Blueprint $table) {
            $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->primary(['module_id', 'role_id']);
        });

        // ── Seed default modules ──────────────────────────────────────────
        $now = now();
        $modules = [
            ['name' => 'Sales',     'slug' => 'sales',     'icon' => 'bi-cart-check-fill',        'color' => '#1a3a5c', 'sort_order' => 1],
            ['name' => 'Finance',   'slug' => 'finance',   'icon' => 'bi-bank',                   'color' => '#2d6a9f', 'sort_order' => 2],
            ['name' => 'Inventory', 'slug' => 'inventory', 'icon' => 'bi-boxes',                  'color' => '#198754', 'sort_order' => 3],
            ['name' => 'HR',        'slug' => 'hr',        'icon' => 'bi-people-fill',            'color' => '#6f42c1', 'sort_order' => 4],
        ];

        foreach ($modules as $m) {
            DB::table('modules')->insert(array_merge($m, [
                'description' => null,
                'is_active'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('module_role');
        Schema::dropIfExists('modules');
    }
};
