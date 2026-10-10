<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Status pesanan disederhanakan mengikuti alur terbaru:
 *
 * dipesan -> diproses -> menunggu_pembayaran -> menunggu_verifikasi
 *          -> siap_diambil -> selesai   (dibatalkan hanya sebelum billing)
 *
 * Status lama 'lunas' dan 'faktur_terbit' dilebur menjadi 'siap_diambil'
 * karena invoice otomatis sudah dihapus (billing diunggah Petugas Layanan).
 */
return new class extends Migration
{
    private const LEGACY = "'dipesan', 'diproses', 'menunggu_pembayaran', 'menunggu_verifikasi', 'pembayaran_ditolak', 'lunas', 'faktur_terbit', 'siap_diambil', 'selesai', 'dibatalkan'";

    private const TARGET = "'dipesan', 'diproses', 'menunggu_pembayaran', 'menunggu_verifikasi', 'pembayaran_ditolak', 'siap_diambil', 'selesai', 'dibatalkan'";

    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            $this->mapLegacyStatuses();

            return;
        }

        // Lebarkan enum dulu supaya nilai baru bisa dipakai.
        DB::statement('ALTER TABLE orders MODIFY status ENUM(' . self::LEGACY . ") NOT NULL DEFAULT 'dipesan'");

        $this->mapLegacyStatuses();

        DB::statement('ALTER TABLE orders MODIFY status ENUM(' . self::TARGET . ") NOT NULL DEFAULT 'dipesan'");
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            DB::table('orders')->where('status', 'siap_diambil')->update(['status' => 'faktur_terbit']);

            return;
        }

        DB::statement('ALTER TABLE orders MODIFY status ENUM(' . self::LEGACY . ") NOT NULL DEFAULT 'dipesan'");

        DB::table('orders')->where('status', 'siap_diambil')->update(['status' => 'faktur_terbit']);
    }

    private function mapLegacyStatuses(): void
    {
        DB::table('orders')->where('status', 'lunas')->update(['status' => 'siap_diambil']);
        DB::table('orders')->where('status', 'faktur_terbit')->update(['status' => 'siap_diambil']);
    }
};