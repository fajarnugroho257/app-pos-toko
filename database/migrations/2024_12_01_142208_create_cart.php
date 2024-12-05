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
        Schema::create('cart', function (Blueprint $table) {
            $table->string('cart_id', 20)->primary();
            $table->unsignedBigInteger('pusat_id');
            $table->unsignedBigInteger('cabang_id');
            $table->enum('cart_st', ['yes', 'no', 'draft'])->default('draft');
            $table->timestamps();
            // foreign key
            $table->foreign('pusat_id')->references('id')->on('toko_pusat')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('cabang_id')->references('id')->on('toko_cabang')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart');
    }
};
