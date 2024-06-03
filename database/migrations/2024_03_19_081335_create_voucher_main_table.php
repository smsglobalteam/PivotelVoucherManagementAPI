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
    
            $table->date('deplete_date')->nullable();
            
            $table->string('serial')->unique();

            $table->bigInteger('product_id')->unsigned();
            $table->bigInteger('voucher_type_id')->unsigned()->nullable();

            $table->string('SIM')->nullable();
            $table->string('PUK')->unique();
            $table->string('IMSI')->nullable();
            $table->string('MSISDN')->nullable();
            
            $table->boolean('available')->default(true);

            $table->string('service_reference')->nullable();
            $table->string('business_unit')->nullable();

            $table->string('batch_id');
            $table->string('note')->nullable();
           
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::table('voucher_main', function(Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('product'); 
            $table->foreign('batch_id')->references('batch_id')->on('batch_order');
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