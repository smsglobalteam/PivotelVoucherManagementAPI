<?php

namespace App\Http\Controllers;

use App\Models\HistoryLogsModel;
use App\Models\VoucherMainModel;
use App\Models\VoucherModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\ErrorCodesController;
use App\Models\VoucherTypeModel;

class VoucherActivationController extends Controller
{
    //
    public function consumeVoucher(Request $request, ErrorCodesController $errorCodesController)
    {
        // Initialize error collections
        $customErrorCodes = [];
        $customErrors = [];

        // Validator setup
        $validator = Validator::make($request->all(), [
            'serial' => 'required',
            'product_id' => 'required',
            'voucher_code' => 'required|exists:voucher_type,voucher_code',
            'business_unit' => 'required',
            'service_reference' => 'required',
            'SIM' => 'nullable',
            'IMSI' => 'nullable',
            'MSISDN' => 'nullable',
        ]);

        if ($validator->fails()) {
            // Map validation errors to custom codes
            $customErrorCodes = $errorCodesController->mapValidationErrorsToCustomCodes($validator);
        }

        $voucher = VoucherModel::where('serial', $request->serial)->first();
        
        if (!$voucher) {
            $customErrors[] = [
                "error_code" => "-7102",
                "error_field" => "serial"
            ];
        } else {
            // Clone voucher for history before modification
            $voucher_old = clone $voucher;

            $voucherType = VoucherTypeModel::where('voucher_code', $request->voucher_code)->first();

            if ($voucher->deplete_date != null) {
                $customErrors[] = [
                    "error_code" => "-7103",
                    "error_field" => "deplete_date"
                ];
            }

            if ($voucher->available == false) {
                $customErrors[] = [
                    "error_code" => "-7105",
                    "error_field" => "available"
                ];
            }

            if ($voucher->product_id != $request->product_id) {
                $customErrors[] = [
                    "error_code" => "-7106",
                    "error_field" => "product_id"
                ];
            }

            // Disable this check as we are now using this function to directly add voucher_code to the voucher_main table
            // if ($voucherType->voucher_code != $request->voucher_code) {
            //     $customErrors[] = [
            //         "error_code" => "-8009",
            //         "error_field" => "voucher_code"
            //     ];
            // }
        }

        if (!empty($customErrorCodes) || !empty($customErrors)) {
            // Merge all errors together
            $errors = array_merge($customErrorCodes, $customErrors);

            // Fetch custom error messages from the database
            $errorMessages = $errorCodesController->getErrorMessagesFromCodes($errors);

            return response()->json([
                'message' => 'Validation failed',
                'errors' => $errorMessages,
            ], 422);
        }

        if ($voucher) {
            $voucher->update(array_filter([
                'deplete_date' => now(),
                'available' => false,
                'updated_by' => $request->attributes->get('preferred_username'),
            
                'voucher_type_id' => $voucherType->id,
                'service_reference' => $request->service_reference,
                'business_unit' => $request->business_unit,
                'SIM' => $request->SIM,
                'IMSI' => $request->IMSI,
                'MSISDN' => $request->MSISDN,
            ], function ($value) {
                return !is_null($value);
            }));

            $history = new HistoryLogsModel();
            $history->username = $request->attributes->get('preferred_username');
            $history->transaction = "Consumed Voucher";
            $history->database_table = "voucher_main";
            $history->old_data = json_encode($voucher_old);
            $history->new_data = json_encode($voucher);
            $history->save();
        }

        return response([
            'message' => "Voucher consumed successfully.",
            'return_code' => '0',
            'results' => $voucher,
        ], 200);
    }




    // OLD CODE --------------------------------
    // public function consumeVoucher(Request $request)
    // {
    //     $request->validate([
    //         'serial' => 'required',
    //         // 'PUK' => 'required',
    //         'product_id' => 'required',
    //         'business_unit' => 'required',
    //         'service_reference' => 'required',
    //         'IMEI' => 'required',
    //         'SIMNarrative' => 'required',
    //         'PCN' => 'required',
    //         'SIMNo' => 'required',
    //         'IMSI' => 'required',
    //     ]);

    //     $voucher = VoucherModel::where('serial', $request->serial)->first();

    //     if (!$voucher) {
    //         return response([
    //             'message' => "Voucher not found.",
    //             'return_code' => '-201',
    //         ], 404);
    //     }

    //      $voucher_old = clone $voucher;

    //     if ($voucher->deplete_date != null) {
    //         return response([
    //             'message' => "This voucher has already been consumed.",
    //             'return_code' => '-204',
    //         ], 401);
    //     }

    //     if ($voucher->expire_date && $voucher->expire_date < date('Y-m-d')) {
    //         return response([
    //             'message' => "Voucher has expired.",
    //             'return_code' => '-203',
    //         ], 401);
    //     }

    //     if ($voucher->available == false) {
    //         return response([
    //             'message' => "Voucher is not active.",
    //             'return_code' => '-202',
    //         ], 401);
    //     }

    //     $mismatches = [];

    //     if ($voucher->product_id != $request->product_id) {
    //         $mismatches[] = "Product ID does not match the voucher's product.";
    //     }

    //     if (!empty($mismatches)) {
    //         return response([
    //             'message' => "Validation errors: " . implode(' ', $mismatches),
    //             'return_code' => '-209',
    //         ], 404);
    //     }

    //     $voucher->deplete_date = now();
    //     $voucher->available = false;
    //     $voucher->service_reference = $request->service_reference;
    //     $voucher->business_unit = $request->business_unit;
    //     $voucher->save();

    //     $history = new HistoryLogsModel();
    //     $history->username = $request->attributes->get('preferred_username');
    //     $history->transaction = "Consumed voucher";
    //     $history->database_table = "voucher_main";
    //     $history->old_data = json_encode($voucher_old);
    //     $history->new_data = json_encode($voucher);
    //     $history->save();

    //     return response([
    //         'message' => "Voucher consumed successfully.",
    //         'return_code' => '0',
    //         'results' => $voucher,
    //     ], 200);
    // }
}
