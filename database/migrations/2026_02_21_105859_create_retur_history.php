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
        Schema::create('retur_history', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 5);
            $table->string('cart_id', 20);
            $table->unsignedBigInteger('barang_cabang_id');
            $table->string('retur_qty', 5)->nullable();
            $table->string('retur_harga', 15)->nullable();
            $table->timestamps();
            // foreign key
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('cart_id')->references('cart_id')->on('cart')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('barang_cabang_id')->references('id')->on('barang_cabang')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retur_history');
    }
};
