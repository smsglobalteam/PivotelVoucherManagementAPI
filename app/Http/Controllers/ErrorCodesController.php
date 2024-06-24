<?php

namespace App\Http\Controllers;

use App\Models\ErrorCodesModel;
use App\Models\ProductHistoryModel;
use Illuminate\Http\Request;

class ErrorCodesController extends Controller
{
    //
    public function getAllErorrCodes()
    {
        $errorCodes = ErrorCodesModel::get();

        return response([
            'message' => "All error codes displayed successfully",
            'return_code' => '0',
            'results' => $errorCodes
        ], 200);
    }


    public function getErrorCodeByID($id)
    {
        $errorCodes = ErrorCodesModel::where('error_code', $id)->first();

        if (!$errorCodes) {
            return response([
                'message' => "Error codes not found",
                'return_code' => '-501',
            ], 404);
        }

        return response([
            'message' => "Error code displayed successfully",
            'results' => $errorCodes
        ], 200);
    }

    public function getErrorMessages(Request $request)
    {
        $request->validate([
            'error_codes' => 'nullable',
        ]);

        $errorCodesArray = explode(',', $request->error_codes);

        $errorMessages = ErrorCodesModel::whereIn('error_code', $errorCodesArray)
            ->get(['error_code', 'error_message']);

        if ($errorMessages->isEmpty()) {
            return response()->json([
                'message' => "No error messages found for the provided codes.",
                'results' => []
            ], 404);
        }

        return response()->json([
            'message' => "Error codes processed successfully.",
            'return_code' => '0',
            'results' => $errorMessages
        ], 200);
    }

    public function createNewErrorCode(Request $request)
    {
        $request->validate([
            'error_code' => 'required|integer|unique:error_codes_reference,error_code',
            'error_message' => 'required|string',
            'error_description' => 'nullable|string',
        ]);

        $errorCodes = ErrorCodesModel::create([
            'error_code' => $request->error_code,
            'error_message' => $request->error_message,
            'error_description' => $request->error_description,
            'created_by' => $request->attributes->get('preferred_username'),
        ]);

        return response([
            'message' => "Error codes created successfully",
            'return_code' => '0',
            'results' => $errorCodes
        ], 201);
    }

    public function editErrorByCode($id, Request $request)
    {
        $errorCodesReference = ErrorCodesModel::where('id', $id)->first();

        if (!$errorCodesReference) {
            return response([
                'message' => "Error code not found",
                'return_code' => '-101',

            ], 404);
        }

        $errorCodesReference_old = clone $errorCodesReference;

        $request->validate([
            'error_code' => 'required|integer|unique:error_codes_reference,error_code,' . $errorCodesReference->id,
            'error_message' => 'required|string',
            'error_description' => 'nullable|string',
        ]);

        $errorCodesReference->update([
            'error_code' => $request->error_code,
            'error_message' => $request->error_message,
            'error_description' => $request->error_description,
            'updated_by' => $request->attributes->get('preferred_username'),
        ]);

        $errorCodesReference->refresh();

        return response([
            'message' => "Error code updated successfully",
            'return_code' => '0',
            'results' => $errorCodesReference
        ], 201);
    }

    public function deleteErrorCodeByID($id)
    {
        $errorCodesReference = ErrorCodesModel::where('error_code', $id)->first();

        if (!$errorCodesReference) {
            return response([
                'message' => "Error code not found",
                'return_code' => '-101',
            ], 404);
        }

        $errorCodesReference->delete();

        return response([
            'message' => "Error code deleted successfully",
            'return_code' => '0',
            'results' => $errorCodesReference
        ], 200);
    }

    public function mapValidationErrorsToCustomCodes($validator)
{
    $failedRules = $validator->failed();
    $errorMappings = [];

    foreach ($failedRules as $field => $rules) {
        foreach (array_keys($rules) as $rule) {
            // Directly determine the error code within the loop
            $key = strtolower($field . '_' . $rule);
            $errorCode = null;

            switch ($key) {
                case 'product_code_required':
                    $errorCode = '-5002';
                    break;
                case 'product_code_regex':
                    $errorCode = '-5003';
                    break;
                case 'product_code_unique':
                    $errorCode = '-5004';
                    break;
                case 'product_id_required':
                    $errorCode = '-5005';
                    break;
                case 'product_id_integer':
                    $errorCode = '-5006';
                    break;
                case 'product_id_unique':
                    $errorCode = '-5007';
                    break;
                case 'product_name_required':
                    $errorCode = '-5008';
                    break;
                case 'product_name_unique':
                    $errorCode = '-5009';
                    break;
                case 'supplier_required':
                    $errorCode = '-5010';
                    break;
                case 'status_required':
                    $errorCode = '-5011';
                    break;
                case 'status_boolean':
                    $errorCode = '-5012';
                    break;

                // Batch order processing errors
                case 'batch_id_required':
                    $errorCode = '-6002';
                    break;
                case 'batch_id_string':
                    $errorCode = '-6003';
                    break;
                case 'batch_id_unique':
                    $errorCode = '-6004';
                    break;
                case 'batch_count_required':
                    $errorCode = '-6005';
                    break;
                case 'batch_count_integer':
                    $errorCode = '-6006';
                    break;
                case 'batch_count_min':
                    $errorCode = '-6007';
                    break;
                case 'product_id_exists':
                    $errorCode = '-6009';
                    break;
                case 'file_required':
                    $errorCode = '-6010';
                    break;
                case 'file_mimes':
                    $errorCode = '-6011';
                    break;
                //-6012, -6013, -6014 are not present here as they are not validation rules but custom checks

                // Voucher processing errors
                case 'serial_required':
                    $errorCode = '-7002';
                    break;
                case 'serial_unique':
                    $errorCode = '-7003';
                    break;
                case 'puk_required':
                    $errorCode = '-7004';
                    break;
                case 'puk_unique':
                    $errorCode = '-7005';
                    break;
                case 'value_integer':
                    $errorCode = '-7006';
                    break;
                case 'expiry_date_date_format':
                    $errorCode = '-7007';
                    break;
                case 'expiry_date_after':
                    $errorCode = '-7008';
                    break;
                case 'imei_string':
                    $errorCode = '-7009';
                    break;
                case 'simnarrative_string':
                    $errorCode = '-7010';
                    break;
                case 'msisdn_string':
                    $errorCode = '-7011';
                    break;
                case 'simno_string':
                    $errorCode = '-7012';
                    break;
                case 'imsi_string':
                    $errorCode = '-7013';
                    break;
                case 'service_reference_string':
                    $errorCode = '-7014';
                    break;
                case 'business_unit_string':
                    $errorCode = '-7015';
                    break;
                case 'service_reference_required':
                    $errorCode = '-7016';
                    break;
                case 'business_unit_required':
                    $errorCode = '-7017';
                    break;
                case 'expiry_date_required':
                    $errorCode = '-7018';
                    break;
                // Add other cases as needed

                // Voucher type processing errors
                case 'voucher_code_regex':
                    $errorCode = '-8002';
                    break;
                case 'voucher_code_required':
                    $errorCode = '-8003';
                    break;
                case 'voucher_name_required':
                    $errorCode = '-8004';
                    break;
                case 'voucher_type_id_required':
                    $errorCode = '-8005';
                    break;
                case 'voucher_type_id_exists':
                    $errorCode = '-8006';
                    break;
                case 'voucher_code_exists':
                    $errorCode = '-8007';
                    break;
                case 'voucher_code_unique':
                    $errorCode = '-8008';
                    break;
                case 'voucher_code_reserved':
                    $errorCode = '-8009';
                    break;

                // Alert email group processing errors
                case 'name_required':
                    $errorCode = '-9002';
                    break;
                case 'email_required':
                    $errorCode = '-9003';
                    break;
                case 'email_email':
                    $errorCode = '-9004';
                    break;
                case 'email_unique':
                    $errorCode = '-9005';
                    break;
                case 'threshold_alert_integer':
                    $errorCode = '-9006';
                    break;
                case 'configuration_value_required':
                    $errorCode = '-9007';
                    break;
                case 'configuration_value_integer':
                    $errorCode = '-9008';
                    break;

                default:
                    // Handle unmapped errors with a default code
                    $errorCode = '-9999'; // Default error code for unmapped errors
                    // Optionally, log the unmapped error for later review
                    // error_log("Unmapped validation error: $key");
                    break;
            }

            // Only add to the array if an error code was found
            if ($errorCode !== null) {
                $errorMappings[] = [
                    'error_code' => $errorCode,
                    'error_field' => $field,
                ];
            }
        }
    }

    return $errorMappings;
}



    public function getErrorMessagesFromCodes(array $errorMappings)
    {
        // Extract just the error codes for the database query
        $errorCodes = array_column($errorMappings, 'error_code');

        // Fetch error messages based on extracted codes
        $errors = ErrorCodesModel::whereIn('error_code', $errorCodes)
            ->get(['error_code', 'error_message']);

        // Map back the error messages to their codes and include the column information
        $result = collect($errorMappings)->map(function ($mapping) use ($errors) {
            $error = $errors->firstWhere('error_code', $mapping['error_code']);

            // Prepare the return structure with column information
            if (!$error) {
                // Return a default message if the error code is not recognized
                return [
                    'error_code' => $mapping['error_code'],
                    'error_message' => $mapping['error_code'] == '-9999' ? 'An unrecognized error occurred.' : 'Error message not found for this code.',
                    'error_field' => $mapping['error_field'],
                ];
            }

            return [
                'error_code' => $error->error_code,
                'error_message' => $error->error_message,
                'error_field' => $mapping['error_field'],
            ];
        })->toArray();

        return $result;
    }
}
