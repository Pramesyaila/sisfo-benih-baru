<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Data identitas lengkap untuk konsumen (registrasi) dan petugas (Kelola Admin).
 *
 * - instansi        : instansi/kelompok tani
 * - kelurahan/desa, kecamatan, kabupaten/kota, provinsi : rincian alamat
 * - whatsapp        : nomor WhatsApp bila berbeda dari nomor HP
 *
 * Kolom ini juga dipakai untuk mengisi lokasi pada surat permohonan dan faktur.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'instansi')) {
                $table->string('instansi')->nullable()->after('nik');
            }
            if (! Schema::hasColumn('users', 'kelurahan')) {
                $table->string('kelurahan')->nullable()->after('instansi');
            }
            if (! Schema::hasColumn('users', 'kecamatan')) {
                $table->string('kecamatan')->nullable()->after('kelurahan');
            }
            if (! Schema::hasColumn('users', 'kabupaten_kota')) {
                $table->string('kabupaten_kota')->nullable()->after('kecamatan');
            }
            if (! Schema::hasColumn('users', 'provinsi')) {
                $table->string('provinsi')->nullable()->after('kabupaten_kota');
            }
            if (! Schema::hasColumn('users', 'whatsapp')) {
                $table->string('whatsapp')->nullable()->after('phone');
            }
        });
    }

    public function down(): void
    {
        $columns = array_values(array_filter(
            ['instansi', 'kelurahan', 'kecamatan', 'kabupaten_kota', 'provinsi', 'whatsapp'],
            fn ($column) => Schema::hasColumn('users', $column)
        ));

        if ($columns) {
            Schema::table('users', fn (Blueprint $table) => $table->dropColumn($columns));
        }
    }
};