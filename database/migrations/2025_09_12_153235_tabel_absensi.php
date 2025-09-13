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
        Schema::create('absensi', function (Blueprint $table) {
            $table->id(); // int, primary key, auto-increment

            // Foreign key ke tabel peserta
            // Tipe data di diagram adalah varchar, namun seharusnya integer
            $table->unsignedBigInteger('id_peserta');
            $table->foreign('id_peserta')->references('id')->on('peserta')->onDelete('cascade');
            
            // Foreign key ke tabel kategori_absensi
            // Tipe data di diagram adalah varchar, namun seharusnya integer
            $table->unsignedBigInteger('id_kategori');
            $table->foreign('id_kategori')->references('id')->on('kategori_absensi')->onDelete('cascade');

            // Kolom 'datetime' menggunakan varchar sesuai diagram,
            // namun sangat disarankan menggunakan tipe data 'datetime' atau 'timestamp'
            $table->string('datetime', 250);
            // Contoh jika menggunakan tipe data datetime: $table->dateTime('waktu_absen');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
