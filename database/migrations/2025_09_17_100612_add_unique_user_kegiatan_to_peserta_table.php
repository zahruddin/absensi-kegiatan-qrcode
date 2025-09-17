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
            // Tambahkan unique constraint pada kombinasi dua kolom ini
            $table->unique(['id_kegiatan', 'id_user']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peserta', function (Blueprint $table) {
            // Hapus unique constraint jika di-rollback
            $table->dropUnique(['id_kegiatan', 'id_user']);
        });
    }
};