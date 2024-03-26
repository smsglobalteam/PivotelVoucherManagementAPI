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
        Schema::create('batch_order', function (Blueprint $table) {
            $table->id();
            $table->string('batch_id')->unique();
            $table->bigInteger('product_id');
            $table->integer('batch_count');

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::table('batch_order', function(Blueprint $table) {
            $table->foreign('product_id')->references('product_id')->on('product'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_order');
    }
};
