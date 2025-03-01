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
        Schema::table('barang_master_log', function (Blueprint $table) {
            $table->unsignedBigInteger('cabang_id')->after('pusat_id')->nullable();
            // foreignkey
            $table->foreign('cabang_id')->references('id')->on('toko_cabang')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_master_log', function (Blueprint $table) {
            //
        });
    }
};
