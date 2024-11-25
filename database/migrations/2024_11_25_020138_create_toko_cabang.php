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
        Schema::create('toko_cabang', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pusat_id');
            $table->string('cabang_nama', 150);
            $table->string('cabang_slug', 255);
            $table->text('cabang_alamat');
            $table->timestamps();
            // foreign key
            $table->foreign('pusat_id')->references('id')->on('toko_pusat')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('toko_cabang');
    }
};
