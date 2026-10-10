<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Faktur penjualan dibuat Petugas Layanan setelah bukti pembayaran diverifikasi.
 * Berbeda dari billing (dokumen yang diunggah petugas), faktur ini dibuat
 * otomatis dari data pesanan sehingga tidak perlu ada berkas yang diunggah.
 *
 * Nama index untuk foreign key diberi prefiks sendiri karena MySQL/MySQL menyimpan
 * nama constraint secara global per schema. Tabel lama sudah diarsipkan menjadi
 * archived_invoices, namun nama constraint invoices_order_id_foreign masih
 * tersimpan di tabel tersebut.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->unique('invoices_faktur_order_id_unique')
                ->constrained('orders', indexName: 'invoices_faktur_order_id_foreign')
                ->cascadeOnDelete();
            $table->string('invoice_number')->unique('invoices_faktur_number_unique');
            $table->unsignedBigInteger('total')->default(0);
            $table->date('pickup_date')->nullable();
            $table->string('pickup_location')->nullable();
            $table->text('recipient')->nullable();
            $table->foreignId('issued_by')
                ->nullable()
                ->constrained('users', indexName: 'invoices_faktur_issued_by_foreign')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};