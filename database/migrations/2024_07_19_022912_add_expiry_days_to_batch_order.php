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
        Schema::table('batch_order', function (Blueprint $table) {
            $table->integer('expiry_days')->nullable()->after('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batch_order', function (Blueprint $table) {
            $table->dropColumn('expiry_days');
        });
    }
};
