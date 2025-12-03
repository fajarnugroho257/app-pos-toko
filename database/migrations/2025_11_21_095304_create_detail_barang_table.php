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
        Schema::create('detail_barang', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('barang_id');
            $table->unsignedBigInteger('pusat_id');
            $table->string('detail_image_name');
            $table->string('detail_image_path');
            $table->enum('detail_st', ['no', 'yes'])->default('no');
            $table->timestamps();
            // foreign key
            $table->foreign('barang_id')->references('id')->on('barang_master')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('pusat_id')->references('id')->on('toko_pusat')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_barang');
    }
};
