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
    Schema::create('service', function (Blueprint $table) {
        $table->id();
        $table->string('dealer_code');
        $table->string('tax_id');
        $table->string('full_name');
        $table->string('birthdate');
        $table->string('company_name')->nullable();
        $table->string('trading_name')->nullable();
        $table->string('industry');
        $table->string('telephone_number');
        $table->string('mobile');
        $table->boolean('subscribe_to_news');
        $table->string('primary_street_address');
        $table->string('primary_city');
        $table->integer('primary_zip_code');
        $table->string('primary_email');
        $table->string('billing_street_address');
        $table->string('billing_city');
        $table->integer('billing_zip_code');
        $table->string('billing_email');
        $table->string('emergency_contact');
        $table->string('emergency_telephone');
        $table->string('emergency_mobile');
        $table->string('emergency_email');
        $table->string('emergency_address');
        $table->string('emergency_relationship');
        $table->string('id_type');
        $table->string('id_expiry');
        $table->string('social_security_no');
        $table->string('inquiry_password');
        $table->string('card_type');
        $table->string('card_holder_name');
        $table->string('card_number');
        $table->string('card_expiry_date');
        $table->integer('card_ccv');
        $table->string('plan_type');
        $table->string('satellite_network');
        $table->string('service_type');
        $table->string('service_plan');
        $table->string('plan_term');
        $table->string('sim_number');
        $table->string('equipment_provider');
        $table->string('hardware_model');
        $table->string('imei_esn');
        $table->string('vessel_narrative');
        $table->string('requested_activation_date');
        $table->string('cost_center')->nullable();
        $table->string('tracertrak_full_name')->nullable();
        $table->string('tracertrak_mobile')->nullable();
        $table->string('tracertrak_email')->nullable();
        $table->boolean('tracertrak_geos')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service');
    }
};
