<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambahkan penanda super admin.
 *
 * Hanya satu akun yang boleh menjadi super admin. Akun ini dibuat otomatis
 * oleh seeder sebagai akun bawaan sistem, dan menjadi satu-satunya akun yang
 * dapat mengelola fitur Kelola Admin.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'is_super_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_super_admin')->default(false)->after('role');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'is_super_admin')) {
            Schema::table('users', fn (Blueprint $table) => $table->dropColumn('is_super_admin'));
        }
    }
};