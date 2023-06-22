<?php

namespace App\Http\Controllers;

use App\Mail\ApplicationSubmitted;
use App\Models\ServiceModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class WebServiceController extends Controller
{
    public function getAllApplication()
    {
        $service = ServiceModel::get();

        return response([
            'message' => "All applications displayed successfully",
            'results' => $service
        ], 200);
    }

    public function getApplicationByID($id)
    {
        $service = ServiceModel::where('id', $id)->first();

        if(!$service)
        {
            return response([
                'message' => "Application not found",
            ], 404);
        }

        return response([
            'message' => "Application displayed successfully",
            'results' => $service
        ], 200);
    }

    public function submitApplication(Request $request)
    {
        $request->validate([
            'dealer_code' => 'required|string',
            'tax_id' => 'required|string',
            'full_name' => 'required|string',
            'birthdate' => 'required|date_format:Y-m-d',
            'company_name' => 'nullable|string',
            'trading_name' => 'nullable|string',
            'industry' => 'required|string',
            'telephone_number' => 'required|string',
            'mobile' => 'required|string',
            'subscribe_to_news' => 'required|boolean',

            'primary_street_address' => 'required|string',
            'primary_city' => 'required|string',
            'primary_state' => 'required|string',
            'primary_zip_code' => 'required|numeric',
            'primary_country' => 'required|string',
            'primary_email' => 'required|email',

            'shipping_same_as_primary' => 'required|boolean',
            'shipping_street_address' => 'required|string',
            'shipping_city' => 'required|string',
            'shipping_state' => 'required|string',
            'shipping_zip_code' => 'required|numeric',
            'shipping_country' => 'required|string',
            'shipping_email' => 'required|email',

            'emergency_contact' => 'required|string',
            'emergency_telephone' => 'required|string',
            'emergency_mobile' => 'required|string',
            'emergency_email' => 'required|email',
            'emergency_address' => 'required|string',
            'emergency_relationship' => 'required|string',
            'id_type' => 'required|string',
            'id_expiry' => 'required|date_format:Y-m-d',
            'social_security_no' => 'required|string',
            'inquiry_password' => 'required|string',
            'card_type' => 'required|string',
            'card_holder_name' => 'required|string',
            'card_number' => 'required|string',
            'card_expiry_date' => 'required|date_format:Y-m-d',
            'card_ccv' => 'required|numeric',
            'plan_type' => 'required|string',
            'satellite_network' => 'required|string',
            'service_type' => 'required|string',
            'service_plan' => 'required|string',
            'plan_term' => 'required|string',
            'sim_number' => 'required|string',
            'equipment_provider' => 'required|string',
            'hardware_model' => 'required|string',
            'imei_esn' => 'required|string',
            'vessel_narrative' => 'required|string',
            'requested_activation_date' => 'required|date_format:Y-m-d',
            'cost_center' => 'nullable|string',
            'tracertrak_full_name' => 'nullable|string',
            'tracertrak_mobile' => 'nullable|string',
            'tracertrak_email' => 'nullable|string',
            'tracertrak_geos' => 'nullable|boolean'
        ]);


        try {
            Mail::to($request->primary_email)->send(new ApplicationSubmitted($request->primary_email, $request->full_name));
        } catch (\Exception $e) {
            return response([
                'message' => 'Email was not sent. An error occured.',

            ], 400);
        }


        $service = ServiceModel::create([
            'dealer_code' => $request->dealer_code,
            'tax_id' => $request->tax_id,
            'full_name' => $request->full_name,
            'birthdate' => $request->birthdate,
            'company_name' => $request->company_name,
            'trading_name' => $request->trading_name,
            'industry' => $request->industry,
            'telephone_number' => $request->telephone_number,
            'mobile' => $request->mobile,
            'subscribe_to_news' => $request->subscribe_to_news,
            'primary_street_address' => $request->primary_street_address,
            'primary_city' => $request->primary_city,
            'primary_zip_code' => $request->primary_zip_code,
            'primary_email' => $request->primary_email,
            'billing_street_address' => $request->billing_street_address,
            'billing_city' => $request->billing_city,
            'billing_zip_code' => $request->billing_zip_code,
            'billing_email' => $request->billing_email,
            'emergency_contact' => $request->emergency_contact,
            'emergency_telephone' => $request->emergency_telephone,
            'emergency_mobile' => $request->emergency_mobile,
            'emergency_email' => $request->emergency_email,
            'emergency_address' => $request->emergency_address,
            'emergency_relationship' => $request->emergency_relationship,
            'id_type' => $request->id_type,
            'id_expiry' => $request->id_expiry,
            'social_security_no' => $request->social_security_no,
            'inquiry_password' => $request->inquiry_password,
            'card_type' => $request->card_type,
            'card_holder_name' => $request->card_holder_name,
            'card_number' => $request->card_number,
            'card_expiry_date' => $request->card_expiry_date,
            'card_ccv' => $request->card_ccv,
            'plan_type' => $request->plan_type,
            'satellite_network' => $request->satellite_network,
            'service_type' => $request->service_type,
            'service_plan' => $request->service_plan,
            'plan_term' => $request->plan_term,
            'sim_number' => $request->sim_number,
            'equipment_provider' => $request->equipment_provider,
            'hardware_model' => $request->hardware_model,
            'imei_esn' => $request->imei_esn,
            'vessel_narrative' => $request->vessel_narrative,
            'requested_activation_date' => $request->requested_activation_date,
            'cost_center' => $request->cost_center,
            'tracertrak_full_name' => $request->tracertrak_full_name,
            'tracertrak_mobile' => $request->tracertrak_mobile,
            'tracertrak_email' => $request->tracertrak_email,
            'tracertrak_geos' => $request->tracertrak_geos
        ]);


        return response([
            'message' => "Application submitted successfully",
            'results' => $service
        ], 200);
    }
}