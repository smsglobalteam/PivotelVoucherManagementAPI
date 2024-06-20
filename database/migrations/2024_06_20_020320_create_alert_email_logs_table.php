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
        Schema::create('alert_email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('call_method');
            $table->string('call_by')->nullable();
            $table->text('email');
            $table->mediumText('alerted_products');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alert_email_logs');
    }
};
