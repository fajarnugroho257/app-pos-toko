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
        Schema::table('cart', function (Blueprint $table) {
            $table->string('user_id', 5)->after('cabang_id');
        });

        // INNER JOIN transaksi_cart b ON a.cart_id = b.cart_id SET a.user_id = b.user_id WHERE a.user_id IS NULL;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};
