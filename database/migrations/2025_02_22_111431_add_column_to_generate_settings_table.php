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
        Schema::table('generate_settings', function (Blueprint $table) {
            $table->float('default_delivery_charge')->default(0);
            $table->boolean('cash_on_delivery')->default(1);
            $table->boolean('online_payment')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('generate_settings', function (Blueprint $table) {
            $table->dropColumn(['default_delivery_charge', 'cash_on_delivery', 'online_payment']);
        });
    }
};
