<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pnbp_bills', function (Blueprint $table) {
            $table->longText('content')->nullable()->after('due_date');
            $table->text('notes')->nullable()->after('content');
        });
    }

    public function down(): void
    {
        Schema::table('pnbp_bills', function (Blueprint $table) {
            $table->dropColumn(['content', 'notes']);
        });
    }
};