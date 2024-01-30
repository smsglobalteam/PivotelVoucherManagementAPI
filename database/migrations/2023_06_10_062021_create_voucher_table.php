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
        Schema::create('voucher', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_code')->unique();
            $table->string('serial')->nullable();
            $table->string('product_code_reference')->nullable();
            $table->integer('value');
            $table->date('expiry_date')->nullable();
            $table->boolean('available');
            $table->integer('service_reference')->nullable();
            $table->string("IMEI")->nullable();
            $table->string("PCN")->nullable();
            $table->string("sim_number")->nullable();
            $table->string("IMSI")->nullable();
            $table->string("PUK")->nullable();
            $table->bigInteger('created_by')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher');
    }
};
