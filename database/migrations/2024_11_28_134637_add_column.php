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
            $table->string('barang_barcode')->after('pusat_id');
            $table->string('barang_harga_jual', 15)->after('barang_harga')->default(0);
            $table->renameColumn('barang_harga', 'barang_harga_beli');
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
