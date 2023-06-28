<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationSubmitted extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * Create a new message instance.
     */

    public $dealer_code;
    public $tax_id;
    public $full_name;
    public $birthdate;
    public $company_name;
    public $trading_name;
    public $industry;
    public $telephone_number;
    public $mobile;
    public $subscribe_to_news;
    public $primary_street_address;
    public $primary_city;
    public $primary_state;
    public $primary_zip_code;
    public $primary_country;
    public $primary_email;
    public $shipping_same_as_primary;
    public $shipping_street_address;
    public $shipping_city;
    public $shipping_state;
    public $shipping_zip_code;
    public $shipping_country;
    public $shipping_email;
    public $emergency_contact;
    public $emergency_telephone;
    public $emergency_mobile;
    public $emergency_email;
    public $emergency_address;
    public $emergency_relationship;
    public $id_type;
    public $id_expiry;
    public $social_security_no;
    public $inquiry_password;
    public $card_type;
    public $card_holder_name;
    public $card_number;
    public $card_expiry_date;
    public $card_ccv;
    public $plan_type;
    public $satellite_network;
    public $service_type;
    public $service_plan;
    public $plan_term;
    public $sim_number;
    public $equipment_provider;
    public $hardware_model;
    public $imei_esn;
    public $vessel_narrative;
    public $requested_activation_date;
    public $cost_center;
    public $tracertrak_full_name;
    public $tracertrak_mobile;
    public $tracertrak_email;
    public $tracertrak_geos;



    public function __construct($request)
    {
        //
        $this->dealer_code = $request->dealer_code;
        $this->tax_id = $request->tax_id;
        $this->full_name = $request->full_name;
        $this->birthdate = $request->birthdate;
        $this->company_name = $request->company_name;
        $this->trading_name = $request->trading_name;
        $this->industry = $request->industry;
        $this->telephone_number = $request->telephone_number;
        $this->mobile = $request->mobile;
        $this->subscribe_to_news = $request->subscribe_to_news;
        $this->primary_street_address = $request->primary_street_address;
        $this->primary_city = $request->primary_city;
        $this->primary_state = $request->primary_state;
        $this->primary_zip_code = $request->primary_zip_code;
        $this->primary_country = $request->primary_country;
        $this->primary_email = $request->primary_email;
        $this->shipping_same_as_primary = $request->shipping_same_as_primary;
        $this->shipping_street_address = $request->shipping_street_address;
        $this->shipping_city = $request->shipping_city;
        $this->shipping_state = $request->shipping_state;
        $this->shipping_zip_code = $request->shipping_zip_code;
        $this->shipping_country = $request->shipping_country;
        $this->shipping_email = $request->shipping_email;
        $this->emergency_contact = $request->emergency_contact;
        $this->emergency_telephone = $request->emergency_telephone;
        $this->emergency_mobile = $request->emergency_mobile;
        $this->emergency_email = $request->emergency_email;
        $this->emergency_address = $request->emergency_address;
        $this->emergency_relationship = $request->emergency_relationship;
        $this->id_type = $request->id_type;
        $this->id_expiry = $request->id_expiry;
        $this->social_security_no = $request->social_security_no;
        $this->inquiry_password = $request->inquiry_password;
        $this->card_type = $request->card_type;
        $this->card_holder_name = $request->card_holder_name;
        $this->card_number = $request->card_number;
        $this->card_expiry_date = $request->card_expiry_date;
        $this->card_ccv = $request->card_ccv;
        $this->plan_type = $request->plan_type;
        $this->satellite_network = $request->satellite_network;
        $this->service_type = $request->service_type;
        $this->service_plan = $request->service_plan;
        $this->plan_term = $request->plan_term;
        $this->sim_number = $request->sim_number;
        $this->equipment_provider = $request->equipment_provider;
        $this->hardware_model = $request->hardware_model;
        $this->imei_esn = $request->imei_esn;
        $this->vessel_narrative = $request->vessel_narrative;
        $this->requested_activation_date = $request->requested_activation_date;
        $this->cost_center = $request->cost_center;
        $this->tracertrak_full_name = $request->tracertrak_full_name;
        $this->tracertrak_mobile = $request->tracertrak_mobile;
        $this->tracertrak_email = $request->tracertrak_email;
        $this->tracertrak_geos = $request->tracertrak_geos;

    }

    public function build()
    {
        return $this->subject('Application Submitted - '.$this->full_name)
            ->markdown('emails.application-submitted-mail', [
                'dealer_code' => $this->dealer_code,
                'tax_id' => $this->tax_id,
                'full_name' => $this->full_name,
                'birthdate' => $this->birthdate,
                'company_name' => $this->company_name,
                'trading_name' => $this->trading_name,
                'industry' => $this->industry,
                'telephone_number' => $this->telephone_number,
                'mobile' => $this->mobile,
                'subscribe_to_news' => $this->subscribe_to_news,
                'primary_street_address' => $this->primary_street_address,
                'primary_city' => $this->primary_city,
                'primary_state' => $this->primary_state,
                'primary_zip_code' => $this->primary_zip_code,
                'primary_country' => $this->primary_country,
                'primary_email' => $this->primary_email,
                'shipping_same_as_primary' => $this->shipping_same_as_primary,
                'shipping_street_address' => $this->shipping_street_address,
                'shipping_city' => $this->shipping_city,
                'shipping_state' => $this->shipping_state,
                'shipping_zip_code' => $this->shipping_zip_code,
                'shipping_country' => $this->shipping_country,
                'shipping_email' => $this->shipping_email,
                'emergency_contact' => $this->emergency_contact,
                'emergency_telephone' => $this->emergency_telephone,
                'emergency_mobile' => $this->emergency_mobile,
                'emergency_email' => $this->emergency_email,
                'emergency_address' => $this->emergency_address,
                'emergency_relationship' => $this->emergency_relationship,
                'id_type' => $this->id_type,
                'id_expiry' => $this->id_expiry,
                'social_security_no' => $this->social_security_no,
                'inquiry_password' => $this->inquiry_password,
                'card_type' => $this->card_type,
                'card_holder_name' => $this->card_holder_name,
                'card_number' => $this->card_number,
                'card_expiry_date' => $this->card_expiry_date,
                'card_ccv' => $this->card_ccv,
                'plan_type' => $this->plan_type,
                'satellite_network' => $this->satellite_network,
                'service_type' => $this->service_type,
                'service_plan' => $this->service_plan,
                'plan_term' => $this->plan_term,
                'sim_number' => $this->sim_number,
                'equipment_provider' => $this->equipment_provider,
                'hardware_model' => $this->hardware_model,
                'imei_esn' => $this->imei_esn,
                'vessel_narrative' => $this->vessel_narrative,
                'requested_activation_date' => $this->requested_activation_date,
                'cost_center' => $this->cost_center,
                'tracertrak_full_name' => $this->tracertrak_full_name,
                'tracertrak_mobile' => $this->tracertrak_mobile,
                'tracertrak_email' => $this->tracertrak_email,
                'tracertrak_geos' => $this->tracertrak_geos
            ]);
    }
}
