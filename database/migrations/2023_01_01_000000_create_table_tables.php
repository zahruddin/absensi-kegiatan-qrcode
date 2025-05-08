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
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->string('nama_meja')->index(); // Nama produk dengan index untuk pencarian cepat
            $table->string('qr_code', 255)->nullable(); // Path gambar produk
            $table->enum('status', ['ordered', 'ready'])->default('ready'); // Status produk
            $table->softDeletes(); // Fitur arsip (deleted_at)
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
