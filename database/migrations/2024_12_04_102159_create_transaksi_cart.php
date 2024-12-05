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
        Schema::create('transaksi_cart', function (Blueprint $table) {
            $table->id();
            $table->string('cart_id', 20);
            $table->string('user_id', 5);
            $table->string('trans_pelanggan', 150)->nullable();
            $table->string('trans_total', 20);
            $table->string('trans_bayar', 20);
            $table->string('trans_kembalian', 20)->nullable();
            $table->dateTime('trans_date');
            $table->timestamps();
            // foreign key
            $table->foreign('cart_id')->references('cart_id')->on('cart')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_cart');
    }
};
