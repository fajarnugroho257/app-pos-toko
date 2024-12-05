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
        Schema::create('cart_data', function (Blueprint $table) {
            $table->id();
            $table->string('cart_id', 20);
            $table->unsignedBigInteger('barang_cabang_id');
            $table->string('cart_barcode');
            $table->string('cart_harga_jual', 15);
            $table->string('cart_nama');
            $table->string('cart_qty', 10);
            $table->string('cart_subtotal', 20);
            $table->timestamps();
            // foreign key
            $table->foreign('cart_id')->references('cart_id')->on('cart')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('barang_cabang_id')->references('id')->on('barang_cabang')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_data');
    }
};
