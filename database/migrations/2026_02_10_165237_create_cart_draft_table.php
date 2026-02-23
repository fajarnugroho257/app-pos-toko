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
        Schema::create('cart_draft', function (Blueprint $table) {
            $table->id();
            $table->string('cart_id', 20);
            $table->string('draft_uang_muka', 12)->nullable();
            $table->string('draft_uang_sisa', 12)->nullable();
            $table->date('draft_start')->nullable();
            $table->date('draft_end')->nullable();
            $table->text('draft_note')->nullable();
            $table->enum('draft_st',['yes', 'no'] )->default('no');
            $table->timestamps();
            // foreign key
            $table->foreign('cart_id')->references('cart_id')->on('cart')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_draft', function (Blueprint $table) {
            //
        });
    }
};
