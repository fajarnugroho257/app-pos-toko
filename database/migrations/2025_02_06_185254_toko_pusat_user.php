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
        Schema::create('toko_pusat_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pusat_id');
            $table->string('user_id', 5);
            $table->timestamps();
            // foreign key
            $table->foreign('pusat_id')->references('id')->on('toko_pusat')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('user_id')->references('user_id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
