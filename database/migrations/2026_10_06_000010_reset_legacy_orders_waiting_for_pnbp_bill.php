<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Pada alur lama, pesanan berpindah ke 'menunggu_pembayaran' setelah tagihan PNBP
 * terbit. Karena tagihan PNBP kini dihapus, pesanan tersebut tidak memiliki billing
 * dan tidak dapat dibatalkan maupun diverifikasi.
 *
 * Pesanan ini dikembalikan ke 'diproses' supaya Petugas Layanan dapat mengunggah
 * billing yang sesungguhnya. Pembayaran yang sudah terlanjur masuk tetap
 * tertangani lewat arsip tagihan lama.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('orders')
            ->where('status', 'menunggu_pembayaran')
            ->whereNotIn('id', DB::table('billings')->select('order_id'))
            ->update(['status' => 'diproses']);
    }

    public function down(): void
    {
        // Tidak dikembalikan otomatis: status akhir pesanan tidak dapat ditebak.
    }
};