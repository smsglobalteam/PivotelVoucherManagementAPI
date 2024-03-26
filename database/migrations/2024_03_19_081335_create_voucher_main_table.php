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
    
            $table->date('expire_date')->nullable();
            $table->integer('value')->nullable();
            $table->date('deplete_date')->nullable();
            
            $table->string('serial')->unique();

            $table->string('product_code')->nullable();
            $table->bigInteger('product_id')->nullable();

            $table->string('IMEI')->nullable();
            $table->string('SIMNarrative')->nullable();
            $table->string('PCN')->nullable();
            $table->string('SIMNo')->nullable();
            $table->string('PUK')->unique();
            $table->string('IMSI')->nullable();
            
            $table->boolean('available')->default(true);

            $table->string('service_reference')->nullable();
            $table->string('business_unit')->nullable();

            $table->string('batch_id');
           
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::table('voucher_main', function(Blueprint $table) {
            $table->foreign('product_code')->references('product_code')->on('product');
            $table->foreign('product_id')->references('product_id')->on('product'); 
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