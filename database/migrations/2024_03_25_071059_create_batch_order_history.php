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
        Schema::create('batch_order_history', function (Blueprint $table) {
            $table->id();
            $table->string("user_id");
            $table->string("transaction");
            $table->text("batch_order_old_data")->nullable();
            $table->text("batch_order_new_data")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_order_history');
    }
};
