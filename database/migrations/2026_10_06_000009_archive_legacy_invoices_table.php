<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Invoice otomatis tidak lagi dibuat saat pembayaran diverifikasi.
 * Billing yang diunggah Petugas Layanan dan faktur selesai dari Petugas Gudang
 * menggantikan peran invoice tersebut, sehingga tabelnya diarsipkan.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('invoices') && ! Schema::hasTable('archived_invoices')) {
            Schema::rename('invoices', 'archived_invoices');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('archived_invoices') && ! Schema::hasTable('invoices')) {
            Schema::rename('archived_invoices', 'invoices');
        }
    }
};