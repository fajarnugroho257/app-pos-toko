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
        Schema::table('cart_draft', function (Blueprint $table) {
            $table->string('draft_uang_tagihan', 12)->after('draft_uang_sisa')->nullable();
            $table->string('draft_pelanggan', 50)->after('draft_end')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_draft', function (Blueprint $table) {
            $table->removeColumn('draft_uang_tagihan');
            $table->removeColumn('draft_pelanggan');
        });
    }
};
