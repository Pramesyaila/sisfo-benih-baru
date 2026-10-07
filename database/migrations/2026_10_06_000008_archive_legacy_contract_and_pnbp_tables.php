<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modul kontrak/tagihan PNBP dihapus dari aplikasi.
 *
 * Data historis tidak dihapus: tabel lama diarsipkan dengan nama *_archived
 * agar tetap dapat ditelusuri namun tidak lagi digunakan alur mana pun.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('contracts') && ! Schema::hasTable('archived_contracts')) {
            Schema::rename('contracts', 'archived_contracts');
        }

        if (Schema::hasTable('pnbp_bills') && ! Schema::hasTable('archived_pnbp_bills')) {
            Schema::rename('pnbp_bills', 'archived_pnbp_bills');
        }

        if (Schema::hasColumn('archived_pnbp_bills', 'order_id')
            && Schema::hasColumn('payment_proofs', 'pnbp_bill_id')) {
            Schema::table('payment_proofs', function (Blueprint $table) {
                $table->dropForeign(['pnbp_bill_id']);
                $table->dropColumn('pnbp_bill_id');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('payment_proofs', 'pnbp_bill_id')) {
            Schema::table('payment_proofs', function (Blueprint $table) {
                $table->unsignedBigInteger('pnbp_bill_id')->nullable()->after('order_id');
            });
        }

        if (Schema::hasTable('archived_pnbp_bills') && ! Schema::hasTable('pnbp_bills')) {
            Schema::rename('archived_pnbp_bills', 'pnbp_bills');
        }

        if (Schema::hasTable('archived_contracts') && ! Schema::hasTable('contracts')) {
            Schema::rename('archived_contracts', 'contracts');
        }
    }
};