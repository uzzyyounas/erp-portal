<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general'); // general | branding | pdf
            $table->string('label')->nullable();
            $table->string('type')->default('text'); // text | textarea | image_url | color | boolean
            $table->timestamps();
        });

        // Seed default settings
        $defaults = [
            ['key'=>'company_name',     'value'=>'Your Company Name',  'group'=>'branding', 'label'=>'Company Name',       'type'=>'text'],
            ['key'=>'company_tagline',  'value'=>'',                   'group'=>'branding', 'label'=>'Tagline / Slogan',   'type'=>'text'],
            ['key'=>'company_address',  'value'=>'',                   'group'=>'branding', 'label'=>'Address Line 1',     'type'=>'text'],
            ['key'=>'company_address2', 'value'=>'',                   'group'=>'branding', 'label'=>'Address Line 2',     'type'=>'text'],
            ['key'=>'company_city',     'value'=>'',                   'group'=>'branding', 'label'=>'City / Province',    'type'=>'text'],
            ['key'=>'company_phone',    'value'=>'',                   'group'=>'branding', 'label'=>'Phone',              'type'=>'text'],
            ['key'=>'company_email',    'value'=>'',                   'group'=>'branding', 'label'=>'Email',              'type'=>'text'],
            ['key'=>'company_website',  'value'=>'',                   'group'=>'branding', 'label'=>'Website',            'type'=>'text'],
            ['key'=>'company_ntn',      'value'=>'',                   'group'=>'branding', 'label'=>'NTN / Tax Number',   'type'=>'text'],
            ['key'=>'company_logo_url', 'value'=>'',                   'group'=>'branding', 'label'=>'Logo URL',           'type'=>'image_url'],
        ];

        foreach ($defaults as $row) {
            DB::table('company_settings')->insert(array_merge($row, [
                'created_at' => now(), 'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
