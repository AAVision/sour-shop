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

        Schema::table('flash_sales', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
            $table->date('start_date')->nullable()->after('start_time');
            $table->date('end_date')->nullable()->after('end_time');
            $table->time('start_time')->change();
            $table->time('end_time')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flash_sales', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
        });
    }
};
