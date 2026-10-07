<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * LogoJF sortable is managed from the Kelola Landing Page page (customer side).
 * Logo ditampilkan pada header katalog konsumen.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('landing_contents', 'logo')) {
            Schema::table('landing_contents', function (Blueprint $table) {
                $table->string('logo')->nullable()->after('key');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('landing_contents', 'logo')) {
            Schema::table('landing_contents', fn (Blueprint $table) => $table->dropColumn('logo'));
        }
    }
};