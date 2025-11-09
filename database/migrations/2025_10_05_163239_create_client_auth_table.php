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
        Schema::create('client_auth', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cabang_id');
            $table->date('token_date');
            $table->string('token_value', 6);
            $table->enum('token_usage', ['yes', 'no'])->default('no');
            $table->enum('token_active', ['yes', 'no'])->default('no');
            $table->timestamps();
            // foreign key
            $table->foreign('cabang_id')->references('id')->on('toko_cabang')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_auth');
    }
};
