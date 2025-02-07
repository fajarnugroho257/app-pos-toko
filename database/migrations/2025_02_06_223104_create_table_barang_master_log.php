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
        Schema::create('barang_master_log', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 5);
            $table->unsignedBigInteger('pusat_id');
            $table->unsignedBigInteger('barang_master_id');
            $table->string('barang_master_awal', 15)->nullable();
            $table->string('barang_master_perubahan', 15)->nullable();
            $table->string('barang_master_akhir', 15)->nullable();
            $table->enum('barang_st', ['pengiriman', 'pengurangan', 'penambahan'])->nullable();
            $table->timestamps();
            // foreign key
            $table->foreign('user_id')->references('user_id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('pusat_id')->references('id')->on('toko_pusat')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('barang_master_id')->references('id')->on('barang_master')->onDelete('cascade')->onUpdate('cascade');
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
