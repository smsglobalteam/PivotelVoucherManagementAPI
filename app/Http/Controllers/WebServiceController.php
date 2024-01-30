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

        if (!$service) {
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
            'is_for_existing_account' => 'required|boolean',
            'account_number' => 'required|string',

            //Personal Information
            'title' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'birthdate' => 'required|date_format:Y-m-d',
            'company_name' => 'nullable|string',
            'trading_name' => 'nullable|string',
            'email' => 'required|email',
            'telephone_number' => 'required|string',
            'mobile' => 'required|string',
            'sign_up_marketing' => 'nullable|boolean',

            //Billing
            'billing_email' => 'required|email',
            'billing_street_address' => 'required|string',
            'billing_city' => 'required|string',
            'billing_zip_code' => 'required|numeric',
            'billing_country' => 'required|string',
            'billing_state' => 'required|string',

            //Shipping
            'shipping_same_as_primary' => 'required|boolean',
            'shipping_street_address' => 'nullable|string',
            'shipping_city' => 'nullable|string',
            'shipping_zip_code' => 'nullable|numeric',
            'shipping_country' => 'nullable|string',
            'shipping_state' => 'nullable|string',

            //Credit Card
            'card_type' => 'required|string',
            'card_holder_name' => 'required|string',
            'card_number' => 'required|string',
            'card_expiry_date' => 'required',
            'card_ccv' => 'required|numeric',

            //Plan Type
            'satellite_network' => 'required|string',
            'hardware_type' => 'nullable|string',
            'plan_family' => 'nullable|string',
            'sim_number' => 'required|string',
            'imei_esn' => 'required|string',
            'vessel_narrative' => 'required|string',
            'requested_activation_date' => 'required|date_format:Y-m-d',
            'is_for_maritime' => 'nullable|boolean',

            //Vessel Information
            'vessel_name' => 'required|string',
            'fleet_id' => 'required|string',
            'country_of_registry' => 'required|string',
            'number_of_persons_onboard' => 'required|numeric',
            'home_port' => 'required|string',
            'port_of_registry' => 'required|string',
            'vessel_type' => 'required|string',
            'sea_going_flag' => 'required|string',
            'self_propelled_flag' => 'required|string',
            'over_100_gt_flag' => 'required|string',
            'tonnage_of_vessel' => 'required|string',
            'year_of_build' => 'required|string',
            'imo_number' => 'required|string',
            'call_sign' => 'required|string',
            'aaic' => 'required|string',
            'mmsi' => 'required|string',

            //Vessel Information
            'vessel_emergency_contact_name' => 'required|string',
            'vessel_emergency_contact_address' => 'required|string',
            'vessel_emergency_street_address' => 'required|string',
            'vessel_emergency_city' => 'required|string',
            'vessel_emergency_zip_code' => 'required|string',
            'vessel_emergency_country' => 'required|string',
            'vessel_emergency_state' => 'required|string',
            'vessel_emergency_contact_mobile' => 'required|string',
            'vessel_emergency_contact_email' => 'required|email',
        ]);


        try {
            Mail::to($request->email)->send(new ApplicationSubmitted($request));
        } catch (\Exception $e) {
            return response([
                'message' => 'Email was not sent. An error occured.',
                'catch' => $e->getMessage()
            ], 400);
        }


        $service = ServiceModel::create([
            'is_for_existing_account' => $request->is_for_existing_account,
            'account_number' => $request->account_number,

            // Personal Information
            'title' => $request->title,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'birthdate' => $request->birthdate,
            'company_name' => $request->company_name,
            'trading_name' => $request->trading_name,
            'email' => $request->email,
            'telephone_number' => $request->telephone_number,
            'mobile' => $request->mobile,
            'sign_up_marketing' => $request->sign_up_marketing,

            // Billing
            'billing_email' => $request->billing_email,
            'billing_street_address' => $request->billing_street_address,
            'billing_city' => $request->billing_city,
            'billing_zip_code' => $request->billing_zip_code,
            'billing_country' => $request->billing_country,
            'billing_state' => $request->billing_state,

            // Shipping
            'shipping_same_as_primary' => $request->shipping_same_as_primary,
            'shipping_street_address' => $request->shipping_street_address,
            'shipping_city' => $request->shipping_city,
            'shipping_zip_code' => $request->shipping_zip_code,
            'shipping_country' => $request->shipping_country,
            'shipping_state' => $request->shipping_state,

            // Credit Card
            'card_type' => $request->card_type,
            'card_holder_name' => $request->card_holder_name,
            'card_number' => $request->card_number,
            'card_expiry_date' => $request->card_expiry_date,
            'card_ccv' => $request->card_ccv,

            // Plan Type
            'satellite_network' => $request->satellite_network,
            'hardware_type' => $request->hardware_type,
            'plan_family' => $request->plan_family,
            'sim_number' => $request->sim_number,
            'imei_esn' => $request->imei_esn,
            'vessel_narrative' => $request->vessel_narrative,
            'requested_activation_date' => $request->requested_activation_date,
            'is_for_maritime' => $request->is_for_maritime,

            // Vessel Information
            'vessel_name' => $request->vessel_name,
            'fleet_id' => $request->fleet_id,
            'country_of_registry' => $request->country_of_registry,
            'number_of_persons_onboard' => $request->number_of_persons_onboard,
            'home_port' => $request->home_port,
            'port_of_registry' => $request->port_of_registry,
            'vessel_type' => $request->vessel_type,
            'sea_going_flag' => $request->sea_going_flag,
            'self_propelled_flag' => $request->self_propelled_flag,
            'over_100_gt_flag' => $request->over_100_gt_flag,
            'tonnage_of_vessel' => $request->tonnage_of_vessel,
            'year_of_build' => $request->year_of_build,
            'imo_number' => $request->imo_number,
            'call_sign' => $request->call_sign,
            'aaic' => $request->aaic,
            'mmsi' => $request->mmsi,

            // Vessel Information
            'vessel_emergency_contact_name' => $request->vessel_emergency_contact_name,
            'vessel_emergency_contact_address' => $request->vessel_emergency_contact_address,
            'vessel_emergency_street_address' => $request->vessel_emergency_street_address,
            'vessel_emergency_city' => $request->vessel_emergency_city,
            'vessel_emergency_zip_code' => $request->vessel_emergency_zip_code,
            'vessel_emergency_country' => $request->vessel_emergency_country,
            'vessel_emergency_state' => $request->vessel_emergency_state,
            'vessel_emergency_contact_mobile' => $request->vessel_emergency_contact_mobile,
            'vessel_emergency_contact_email' => $request->vessel_emergency_contact_email,

        ]);


        return response([
            'message' => "Application submitted successfully",
            'results' => $service
        ], 200);
    }
}