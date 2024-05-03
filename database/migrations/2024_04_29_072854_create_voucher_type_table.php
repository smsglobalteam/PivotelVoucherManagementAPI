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
        Schema::create('voucher_type', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id')->unsigned();
            $table->string('voucher_code')->unique();
            $table->string('voucher_name');
            $table->boolean('status')->default(true);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::table('voucher_type', function(Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('product'); 
        });

        // Foreign key for voucher_type_id
        Schema::table('voucher_main', function(Blueprint $table) {
            $table->foreign('voucher_type_id')->references('id')->on('voucher_type');
        });

        Schema::table('batch_order', function(Blueprint $table) {
            $table->foreign('voucher_type_id')->references('id')->on('voucher_type');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_type');
    }
};
