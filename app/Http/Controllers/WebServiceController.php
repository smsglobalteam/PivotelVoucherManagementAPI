<?php

namespace App\Http\Controllers;

use App\Mail\ApplicationSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class WebServiceController extends Controller
{
    //
    public function submitApplication(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required',
        ]);

        if ($request->email) {
            try {
                Mail::to($request->email)->send(new ApplicationSubmitted($request->email, $request->name));
            } catch (\Exception $e) {
                return response([
                    'message' => 'Email was not sent. An error occured.',

                ], 400);
            }
        }

        // $voucher = VoucherModel::create([
        //     'value' => $request->value,
        //     'expiry_date' => $request->expiry_date,
        //     'status' => 'active',
        //     'service_reference' => $request->service_reference,
        //     'created_by' => 1
        // ]);

        return response([
            'message' => "Application submitted successfully",
            // 'results' => $voucher
        ], 200);
    }


}
