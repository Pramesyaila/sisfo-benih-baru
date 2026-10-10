<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->date('pickup_date')->nullable()->after('notes');
            $table->enum('pickup_location', ['brmp_penerapan', 'ip2mp_cipaku', 'dikirim'])->nullable()->after('pickup_date');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['pickup_date', 'pickup_location']);
        });
    }
};