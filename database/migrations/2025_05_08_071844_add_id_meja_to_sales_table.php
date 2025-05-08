<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('id_meja')->nullable()->after('id_user');

            // Relasi ke tabel "tables"
            $table->foreign('id_meja')->references('id')->on('tables')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['id_meja']);
            $table->dropColumn('id_meja');
        });
    }
};
