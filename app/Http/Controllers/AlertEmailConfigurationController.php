<?php

namespace App\Http\Controllers;

use App\Models\AlertEmailConfigurationModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\HistoryLogsModel;

class AlertEmailConfigurationController extends Controller
{
    //
    public function getConfigurationPublic($key)
    {
        if ($key != env('ALERT_PUBLIC_KEY')) {
            return response([
                'message' => "Unauthorized access",
                'return_code' => '-101',
            ], 401);
        }

        $alertEmailConfiguration = AlertEmailConfigurationModel::get();

        return response([
            'message' => "All alert email configuration displayed successfully",
            'return_code' => '0',
            'results' => $alertEmailConfiguration
        ], 200);
    }

    public function getAllEmailAlertConfiguration()
    {
        $alertEmailConfiguration = AlertEmailConfigurationModel::get();

        return response([
            'message' => "All alert email configuration displayed successfully",
            'return_code' => '0',
            'results' => $alertEmailConfiguration
        ], 200);
    }

    public function updateEmailAlertConfiguration(Request $request, $id, ErrorCodesController $errorCodesController)
    {
        $alertEmailConfiguration = AlertEmailConfigurationModel::where('id', $id)->first();
        
        if (!$alertEmailConfiguration) {
            return response([
                'message' => "Alert email configuration not found",
                'return_code' => '-101',

            ], 404);
        }

        $alertEmailConfiguration_old = clone $alertEmailConfiguration;

        $validator = Validator::make($request->all(), [
            'configuration_value' => 'required|integer',
            'configuration_description' => 'nullable'
        ]);

        if ($validator->fails()) {
            // Map validation errors to custom codes
            $customErrorCodes = $errorCodesController->mapValidationErrorsToCustomCodes($validator);

            // Fetch custom error messages from the database
            $errorMessages = $errorCodesController->getErrorMessagesFromCodes($customErrorCodes);

            return response()->json([
                'message' => 'Validation failed',
                'errors' => $errorMessages,
            ], 422);
        }

        $alertEmailConfiguration->configuration_value = $request->configuration_value;
        $alertEmailConfiguration->configuration_description = $request->configuration_description;
        $alertEmailConfiguration->updated_by = $request->attributes->get('preferred_username');
        $alertEmailConfiguration->save();

        $history = new HistoryLogsModel();
        $history->username = $request->attributes->get('preferred_username');
        $history->transaction = "Updated Alert Email Configuration";
        $history->database_table = "alert_email_configuration";
        $history->old_data = json_encode($alertEmailConfiguration->toArray());
        $history->new_data = json_encode($alertEmailConfiguration_old->toArray());
        $history->save();

        return response([
            'message' => "Alert email configuration updated successfully",
            'return_code' => '0',
            'results' => $alertEmailConfiguration
        ], 200);
    }
}
