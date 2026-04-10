<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Report Categories (for nav menu grouping)
        Schema::create('report_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->default('bi-bar-chart-fill'); // Bootstrap icon class
            $table->string('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Reports
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('report_categories')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->default('bi-bar-chart-fill');
            $table->string('route')->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Report access by role
        Schema::create('report_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->unique(['report_id', 'role_id']);
        });

        // Report run audit log
        Schema::create('report_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->json('parameters')->nullable();
            $table->string('ip_address')->nullable();
            $table->integer('generation_time_ms')->nullable(); // how long PDF took
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_logs');
        Schema::dropIfExists('report_role');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('report_categories');
    }
};
