<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('voucher_history', function (Blueprint $table) {
            $table->id();
            $table->string("user_id");
            $table->string("transaction");
            $table->text("voucher_old_data")->nullable();
            $table->text("voucher_new_data")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('voucher_history');
    }
};
