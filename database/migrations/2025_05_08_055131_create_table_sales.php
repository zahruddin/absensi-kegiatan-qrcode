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
        Schema::create('sales', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->foreignId('id_user')->nullable()->constrained('users')->onDelete('set null'); // Kasir yang menangani
            $table->decimal('total_harga', 12, 2); // Total harga saat transaksi dilakukan
            $table->decimal('total_diskon', 12, 2)->default(0); // Total diskon saat transaksi
            $table->decimal('total_bayar', 12, 2); // Total yang dibayarkan customer
            $table->enum('metode_bayar', ['cash', 'qris'])->default('qris'); // Metode pembayaran
            $table->enum('status_bayar', ['pending', 'lunas', 'gagal'])->default('pending'); // Status pembayaran
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
