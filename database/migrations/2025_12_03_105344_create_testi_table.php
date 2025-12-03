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
        Schema::create('testi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pusat_id');
            $table->string('person', 100)->nullable();
            $table->string('desc')->nullable();
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
        Schema::dropIfExists('testi');
    }
};
