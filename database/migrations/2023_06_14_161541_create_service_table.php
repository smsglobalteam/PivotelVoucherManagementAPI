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

            $table->boolean('is_for_existing_account');
            $table->string('account_number');

            //Personal Information
            $table->string('title');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('birthdate');
            $table->string('company_name')->nullable();
            $table->string('trading_name')->nullable();
            $table->string('email');
            $table->string('telephone_number');
            $table->string('mobile');
            $table->boolean('sign_up_marketing')->nullable();

            //Billing
            $table->string('billing_email');
            $table->string('billing_street_address');
            $table->string('billing_city');
            $table->string('billing_zip_code');
            $table->string('billing_country');
            $table->string('billing_state');
           
            //Shipping
            $table->boolean('shipping_same_as_primary');
            $table->string('shipping_street_address')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_zip_code')->nullable();
            $table->string('shipping_country')->nullable();
            $table->string('shipping_state')->nullable();

            //Credit Card
            $table->string('card_type');
            $table->string('card_holder_name');
            $table->string('card_number');
            $table->string('card_expiry_date');
            $table->integer('card_ccv');

            //Plan Type
            $table->string('satellite_network');
            $table->string('hardware_type')->nullable();
            $table->string('plan_family')->nullable();
            $table->string('sim_number');
            $table->string('imei_esn');
            $table->string('vessel_narrative');
            $table->string('requested_activation_date');
            $table->boolean('is_for_maritime')->nullable();

            //Vessel Information
            $table->string('vessel_name');
            $table->string('fleet_id');
            $table->string('country_of_registry');
            $table->integer('number_of_persons_onboard');
            $table->string('home_port');
            $table->string('port_of_registry');
            $table->string('vessel_type');
            $table->string('sea_going_flag');
            $table->string('self_propelled_flag');
            $table->string('over_100_gt_flag');
            $table->string('tonnage_of_vessel');
            $table->string('year_of_build');
            $table->string('imo_number');
            $table->string('call_sign');
            $table->string('aaic');
            $table->string('mmsi');

            //Vessel Emergency
            $table->string('vessel_emergency_contact_name');
            $table->string('vessel_emergency_contact_address');
            $table->string('vessel_emergency_street_address');
            $table->string('vessel_emergency_city');
            $table->string('vessel_emergency_zip_code');
            $table->string('vessel_emergency_country');
            $table->string('vessel_emergency_state');
            $table->string('vessel_emergency_contact_mobile');
            $table->string('vessel_emergency_contact_email');

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
