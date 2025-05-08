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
        Schema::create('sales_details', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->foreignId('id_sale')->constrained('sales')->onDelete('cascade'); // Relasi ke tabel sales
            $table->foreignId('id_produk')->constrained('products')->onDelete('cascade'); // Relasi ke produk
            $table->string('nama_produk'); // Nama produk saat transaksi (agar tidak berubah saat harga berubah)
            $table->decimal('harga_produk', 12, 2); // Harga produk saat transaksi (bukan harga saat ini)
            $table->integer('jumlah'); // Jumlah item yang dibeli
            $table->decimal('subtotal', 12, 2); // Total harga sebelum diskon
            $table->decimal('diskon', 12, 2)->default(0); // Diskon per item
            $table->decimal('total', 12, 2); // Harga total setelah diskon
            $table->timestamps(); // created_at & updated_at
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_detail');
    }
};
