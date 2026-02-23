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
        Schema::create('tagihan_cicilan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cart_draft_id');
            $table->string('cicilan', 12);
            $table->date('cicilan_date');
            $table->timestamps();
            // foreign key
            $table->foreign('cart_draft_id')->references('id')->on('cart_draft')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan_cicilan');
    }
};
