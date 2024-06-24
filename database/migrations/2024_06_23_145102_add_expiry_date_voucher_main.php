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
        //
        Schema::table('voucher_main', function (Blueprint $table) {
            $table->date('expiry_date')->nullable()->after('available');
        });

        Schema::table('batch_order', function (Blueprint $table) {
            $table->date('expiry_date')->nullable()->after('batch_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
