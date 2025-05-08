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
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Primary Key (id otomatis di Laravel)
            $table->string('nama_produk')->index(); // Nama produk dengan index untuk pencarian cepat
            $table->string('gambar', 255)->nullable(); // Path gambar produk
            $table->decimal('harga_produk', 12, 2); // Harga produk
            $table->integer('stok_produk')->default(0); // Stok produk dengan default 0
            $table->text('deskripsi')->nullable(); // Deskripsi produk (opsional)
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif'); // Status produk
            $table->softDeletes(); // Fitur arsip (deleted_at)
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
