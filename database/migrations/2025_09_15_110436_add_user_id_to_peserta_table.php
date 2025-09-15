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
            // Tambahkan kolom foreign key 'id_user' setelah 'id_kegiatan'
            $table->foreignId('id_user')
                ->nullable() // Wajib nullable agar peserta yang ditambah manual oleh operator tidak error
                ->after('id_kegiatan')
                ->constrained('users') // Terhubung ke tabel 'users'
                ->onDelete('set null'); // Jika user dihapus, data peserta tetap ada (id_user menjadi NULL)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peserta', function (Blueprint $table) {
            //
        });
    }
};
