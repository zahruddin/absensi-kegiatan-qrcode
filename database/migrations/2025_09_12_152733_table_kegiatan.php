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
        Schema::create('kegiatan', function (Blueprint $table) {
            $table->id(); // int, primary key, auto-increment
            
            // Foreign key ke tabel users
            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            
            $table->string('nama', 250);
            $table->string('qrcode', 250)->nullable();
            
            // Kolom 'date' menggunakan varchar sesuai diagram,
            // namun sangat disarankan menggunakan tipe data 'date' atau 'datetime'
            $table->string('date', 250); 
            // Contoh jika menggunakan tipe data date: $table->date('date');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatan');
    }
};
