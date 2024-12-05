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
        Schema::create('barang_cabang_log', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 5);
            $table->unsignedBigInteger('pusat_id');
            $table->unsignedBigInteger('cabang_id');
            $table->unsignedBigInteger('barang_cabang_id');
            $table->string('barang_awal', 15)->nullable();
            $table->string('barang_perubahan', 15)->nullable();
            $table->string('barang_transaksi', 15)->nullable();
            $table->string('barang_transaksi_id', 15)->nullable();
            $table->string('barang_akhir', 15)->nullable();
            $table->enum('barang_st', ['perubahan', 'transaksi'])->nullable();
            $table->timestamps();
            // foreign key
            $table->foreign('user_id')->references('user_id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('pusat_id')->references('id')->on('toko_pusat')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('cabang_id')->references('id')->on('toko_cabang')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('barang_cabang_id')->references('id')->on('barang_cabang')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_cabang_log');
    }
};
