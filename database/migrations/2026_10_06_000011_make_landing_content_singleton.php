<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Konten landing page adalah data tunggal (singleton).
 *
 * Sebelumnya baris ditentukan lewat id = 1, namun 'id' tidak termasuk $fillable
 * sehingga firstOrCreate(['id' => 1]) mengabaikan nilai tersebut dan membuat
 * baris baru dengan id otomatis. Akibatnya bisa terbentuk lebih dari satu baris
 * dan baris yang terbaca bukan baris yang terbaru.
 *
 * Sekarang baris dijamin unik melalui kolom 'key'.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('landing_contents', 'key')) {
            Schema::table('landing_contents', function (Blueprint $table) {
                $table->string('key', 40)->default('default')->after('id');
            });
        }

        // Sisakan hanya baris terlama bila sebelumnya pernah ada duplikat.
        $keep = DB::table('landing_contents')->orderBy('id')->value('id');

        if ($keep !== null) {
            DB::table('landing_contents')->where('id', '!=', $keep)->delete();
        }

        DB::table('landing_contents')->where('key', '!=', 'default')->update(['key' => 'default']);

        Schema::table('landing_contents', function (Blueprint $table) {
            $table->unique('key');
        });
    }

    public function down(): void
    {
        Schema::table('landing_contents', function (Blueprint $table) {
            $table->dropUnique(['key']);
            $table->dropColumn('key');
        });
    }
};