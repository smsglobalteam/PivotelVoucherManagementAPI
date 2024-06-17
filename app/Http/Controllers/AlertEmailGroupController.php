<?php

namespace App\Http\Controllers;

use App\Models\AlertEmailGroupModel;
use App\Models\HistoryLogsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\ErrorCodesController;

class AlertEmailGroupController extends Controller
{
    //
    public function getAllAlertEmailGroup()
    {
        $alertEmailGroup = AlertEmailGroupModel::get();

        return response([
            'message' => "All error codes displayed successfully",
            'return_code' => '0',
            'results' => $alertEmailGroup
        ], 200);
    }

    public function getAlertEmailGroup($id)
    {
        $alertEmailGroup = AlertEmailGroupModel::where('id', $id)->first();

        return response([
            'message' => "Error code displayed successfully",
            'return_code' => '0',
            'results' => $alertEmailGroup
        ], 200);
    }


    public function createNewAlertEmailGroup(Request $request, ErrorCodesController $errorCodesController)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:alert_email_group,email',
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

        $alertEmailGroup = AlertEmailGroupModel::create([
            'name' => $request->name,
            'email' => $request->email,
            'created_by' => $request->attributes->get('preferred_username'),
        ]);

        $history = new HistoryLogsModel();
        $history->username = $request->attributes->get('preferred_username');
        $history->transaction = "Created Alert Email Group";
        $history->database_table = "alert_email_group";
        $history->new_data = json_encode($alertEmailGroup->toArray());
        $history->save();

        return response([
            'message' => "Alert email group entry created successfully",
            'return_code' => '0',
            'results' => $alertEmailGroup
        ], 201);
    }

    public function updateAlertEmailGroup(Request $request, $id, ErrorCodesController $errorCodesController)
    {
        $alertEmailGroup = AlertEmailGroupModel::where('id', $id)->first();

        if (!$alertEmailGroup) {
            return response([
                'message' => "Alert email group member not found",
                'return_code' => '-101',

            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:alert_email_group,email,' . $id,
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

        $alertEmailGroup->name = $request->name;
        $alertEmailGroup->email = $request->email;
        $alertEmailGroup->updated_by = $request->attributes->get('preferred_username');
        $alertEmailGroup->save();

        $history = new HistoryLogsModel();
        $history->username = $request->attributes->get('preferred_username');
        $history->transaction = "Updated Alert Email Group";
        $history->database_table = "alert_email_group";
        $history->old_data = json_encode($alertEmailGroup->toArray());
        $history->new_data = json_encode($request->all());
        $history->save();

        return response([
            'message' => "Alert email group entry updated successfully",
            'return_code' => '0',
            'results' => $alertEmailGroup
        ], 200);
    }

    public function deleteAlertEmailGroup(Request $request, $id)
    {
        $alertEmailGroup = AlertEmailGroupModel::where('id', $id)->first();

        if (!$alertEmailGroup) {
            return response([
                'message' => "Alert email group member not found",
                'return_code' => '-101',

            ], 404);
        }

        $alertEmailGroup->delete();

        $history = new HistoryLogsModel();
        $history->username = $request->attributes->get('preferred_username');
        $history->transaction = "Deleted Alert Email Group";
        $history->database_table = "alert_email_group";
        $history->old_data = json_encode($alertEmailGroup->toArray());
        $history->save();

        return response([
            'message' => "Alert email group entry deleted successfully",
            'return_code' => '0',
            'results' => $alertEmailGroup
        ], 200);
    }

}
