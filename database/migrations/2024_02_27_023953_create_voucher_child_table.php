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
        Schema::create('voucher_child', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_code_reference');
            $table->boolean('depleted')->default(false);
            $table->date('depleted_date')->nullable();
            $table->string('serviceID')->nullable();
            $table->string('business_unit')->nullable();
            $table->string('serial_number')->nullable();
            
            $table->timestamps();
        });

        Schema::table('voucher_child', function(Blueprint $table) {
            $table->foreign('voucher_code_reference')->references('voucher_code')->on('voucher_main')->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_child');
    }
};
