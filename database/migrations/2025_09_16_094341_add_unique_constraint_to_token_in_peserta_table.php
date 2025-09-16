<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('peserta', function (Blueprint $table) {
            // Tambahkan index unik ke kolom 'token'
            // Menggunakan array memungkinkan Anda menamai index-nya jika perlu
            $table->unique(['token']);
        });
    }

    /**
     * Reverse the migrations.
     * Logika ini akan berjalan jika Anda melakukan rollback.
     */
    public function down(): void
    {
        Schema::table('peserta', function (Blueprint $table) {
            // Hapus index unik dari kolom 'token'
            $table->dropUnique(['token']);
        });
    }
};