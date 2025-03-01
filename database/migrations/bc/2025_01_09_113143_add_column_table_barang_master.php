<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('barang_master', function (Blueprint $table) {
            // satuan
            $table->string('barang_persentase', 50)->default('0')->nullable()->after('barang_harga_jual');
            $table->string('barang_keuntungan', 50)->default('0')->nullable()->after('barang_harga_jual');
            // grosir
            $table->string('barang_grosir_pembelian', 50)->default('0')->nullable()->after('barang_harga_jual');
            $table->string('barang_grosir_persentase', 50)->default('0')->nullable()->after('barang_harga_jual');
            $table->string('barang_grosir_keuntungan', 50)->default('0')->nullable()->after('barang_harga_jual');
            $table->string('barang_grosir_harga_jual', 50)->default('0')->nullable()->after('barang_harga_jual');
            // perubahan
            $table->string('barang_stok_perubahan', 50)->default('0')->nullable()->after('barang_harga_jual');
            $table->string('barang_master_stok_hasil', 50)->default('0')->nullable()->after('barang_harga_jual');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_master', function (Blueprint $table) {
            //
        });
    }
};
