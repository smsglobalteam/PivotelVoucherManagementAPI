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
        Schema::create('voucher_main', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_code')->unique();
            $table->string('product_code_reference')->nullable();
            $table->date('expiry_date')->nullable();

            $table->integer('value');
            $table->boolean('available')->default(true);

            $table->date('depleted_date')->nullable();

            $table->string('serviceID');
            $table->string('business_unit');
            $table->string('serial_number')->unique();

            $table->string('IMEI')->nullable();
            $table->string('SIMNarrative')->nullable();
            $table->string('SIMNo')->nullable();
            $table->string('IMSI')->nullable();
            $table->string('PUK')->nullable();

            $table->bigInteger('created_by')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_main');
    }
};