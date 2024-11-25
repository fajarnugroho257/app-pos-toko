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
        Schema::create('toko_pusat', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 5);
            $table->string('pusat_nama', 150);
            $table->string('pusat_slug', 255);
            $table->string('pusat_pemilik', 150);
            $table->text('pusat_alamat');
            $table->timestamps();
            // foreign key
            $table->foreign('user_id')->references('user_id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('toko_pusat');
    }
};
