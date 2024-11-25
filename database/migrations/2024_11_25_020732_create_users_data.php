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
        Schema::create('users_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cabang_id');
            $table->string('user_id', 5);
            $table->string('user_nama_lengkap', 200)->nullable();
            $table->string('user_alamat', 200)->nullable();
            $table->enum('user_jk', ['L', 'P']);
            $table->enum('user_st', ['yes', 'no'])->default('yes');
            $table->timestamps();
            // foreign key
            $table->foreign('cabang_id')->references('id')->on('toko_cabang')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_data');
    }
};
