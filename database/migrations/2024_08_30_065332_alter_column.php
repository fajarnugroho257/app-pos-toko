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
        Schema::table('app_role_menu', function (Blueprint $table) {
            $table->string('role_menu_id', 10)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_role_menu', function (Blueprint $table) {
            $table->string('role_menu_id', 5)->change();
        });
    }
};
