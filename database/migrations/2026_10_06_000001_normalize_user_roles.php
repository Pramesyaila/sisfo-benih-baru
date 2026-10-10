<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Sistem kini hanya memiliki tiga aktor:
 * konsumen, petugas_layanan (super admin), dan petugas_gudang.
 *
 * Role lama dipetakan ke role baru:
 * - manager_gudang  -> petugas_gudang
 * - petugas_pnbp   -> petugas_layanan (modul PNBP dihapus)
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            DB::table('users')->where('role', 'manager_gudang')->update(['role' => 'petugas_gudang']);
            DB::table('users')->where('role', 'petugas_pnbp')->update(['role' => 'petugas_layanan']);

            return;
        }

        // 1. Lebarkan enum dulu agar nilai baru bisa dipakai.
        DB::statement(
            "ALTER TABLE users MODIFY role ENUM('konsumen', 'petugas_layanan', 'petugas_pnbp', 'manager_gudang', 'petugas_gudang') NOT NULL DEFAULT 'konsumen'"
        );

        // 2. Petakan role lama ke role baru.
        DB::table('users')->where('role', 'manager_gudang')->update(['role' => 'petugas_gudang']);
        DB::table('users')->where('role', 'petugas_pnbp')->update(['role' => 'petugas_layanan']);

        // 3. Persempit kembali enum ke tiga role final.
        DB::statement(
            "ALTER TABLE users MODIFY role ENUM('konsumen', 'petugas_layanan', 'petugas_gudang') NOT NULL DEFAULT 'konsumen'"
        );
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            DB::table('users')->where('role', 'petugas_gudang')->update(['role' => 'manager_gudang']);

            return;
        }

        DB::statement(
            "ALTER TABLE users MODIFY role ENUM('konsumen', 'petugas_layanan', 'petugas_pnbp', 'manager_gudang', 'petugas_gudang') NOT NULL DEFAULT 'konsumen'"
        );

        DB::table('users')->where('role', 'petugas_gudang')->update(['role' => 'manager_gudang']);

        DB::statement(
            "ALTER TABLE users MODIFY role ENUM('konsumen', 'petugas_layanan', 'petugas_pnbp', 'manager_gudang') NOT NULL DEFAULT 'konsumen'"
        );
    }
};