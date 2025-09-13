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
        Schema::create('peserta', function (Blueprint $table) {
            $table->id(); // int, primary key, auto-increment

            // Foreign key ke tabel kegiatan
            // Tipe data di diagram adalah varchar, namun seharusnya integer agar sesuai dengan Primary Key
            $table->unsignedBigInteger('id_kegiatan'); 
            $table->foreign('id_kegiatan')->references('id')->on('kegiatan')->onDelete('cascade');

            $table->string('nama', 250);
            $table->string('email', 250)->nullable();
            $table->string('no_hp', 250)->nullable();
            $table->string('prodi', 250)->nullable();
            $table->string('nim', 250)->nullable();
            $table->string('kelompok', 250)->nullable();
            $table->string('qrcode', 250)->nullable();
            $table->string('token', 250)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peserta');
    }
};
