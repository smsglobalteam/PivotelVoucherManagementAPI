<?php

namespace App\Http\Controllers;

use App\Models\AlertEmailGroupModel;
use App\Models\HistoryLogsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\ErrorCodesController;
use App\Models\ProductModel;
use Illuminate\Support\Facades\DB;
use App\Mail\ThresholdAlertMail;
use App\Models\AlertEmailLogsModel;
use Illuminate\Support\Facades\Mail;
use PhpParser\Node\Stmt\Else_;

class AlertEmailGroupController extends Controller
{

    public function triggerAlert(Request $request)
    {
        $products = ProductModel::orderBy('created_at', 'desc')
            ->leftJoin('voucher_main', function ($join) {
                $join->on('product.id', '=', 'voucher_main.product_id')
                    ->where('voucher_main.available', true)
                    ->whereNull('voucher_main.deplete_date');
            })
            ->select('product.*', DB::raw('COUNT(voucher_main.id) as available_voucher_count'))
            ->groupBy('product.id')
            ->get();

        $alertEmailGroup = AlertEmailGroupModel::get();
        $alertProducts = [];

        foreach ($products as $product) {
            if ($product->threshold_alert > $product->available_voucher_count) {
                $alertProducts[] = $product;
            }
        }

        if (!empty($alertProducts)) {
            foreach ($alertEmailGroup as $recipient) {
                try {
                    Mail::to($recipient->email)->send(new ThresholdAlertMail($alertProducts));
                } catch (\Exception $e) {
                    return response([
                        'message' => 'Email was not sent. An error occurred.',
                        'error' => $e->getMessage()
                    ], 400);
                }
            }
        }

        if (!empty($alertProducts)) {
            $alertEmailLog = new AlertEmailLogsModel();
            $alertEmailLog->call_method = "manual";
            $alertEmailLog->call_by = $request->attributes->get('preferred_username');
            $alertEmailLog->email = json_encode($alertEmailGroup->pluck('email'));
            $alertEmailLog->alerted_products = json_encode($alertProducts);
            $alertEmailLog->save();

            return response([
                'message' => "Alert emails sent successfully",
                'return_code' => '0',
                'alerted_vouchers' => $alertProducts
            ], 200);
        } else {
            return response([
                'message' => "All vouchers are above threshold",
                'return_code' => '0',
            ], 200);
        }
    }

    public function automatedAlert($key)
    {
        if ($key != env('ALERT_PUBLIC_KEY')) {
            return response([
                'message' => "Unauthorized access",
                'return_code' => '-101',
            ], 401);
        }

        $products = ProductModel::orderBy('created_at', 'desc')
            ->leftJoin('voucher_main', function ($join) {
                $join->on('product.id', '=', 'voucher_main.product_id')
                    ->where('voucher_main.available', true)
                    ->whereNull('voucher_main.deplete_date');
            })
            ->select('product.*', DB::raw('COUNT(voucher_main.id) as available_voucher_count'))
            ->groupBy('product.id')
            ->get();

        $alertEmailGroup = AlertEmailGroupModel::get();
        $alertProducts = [];

        foreach ($products as $product) {
            if ($product->threshold_alert > $product->available_voucher_count) {
                $alertProducts[] = $product;
            }
        }

        if (!empty($alertProducts)) {
            foreach ($alertEmailGroup as $recipient) {
                try {
                    Mail::to($recipient->email)->send(new ThresholdAlertMail($alertProducts));
                } catch (\Exception $e) {
                    return response([
                        'message' => 'Email was not sent. An error occurred.',
                        'error' => $e->getMessage()
                    ], 400);
                }
            }
        }

        if (!empty($alertProducts)) {
            $alertEmailLog = new AlertEmailLogsModel();
            $alertEmailLog->call_method = "automated";
            $alertEmailLog->call_by = "automated_system";
            $alertEmailLog->email = json_encode($alertEmailGroup->pluck('email'));
            $alertEmailLog->alerted_products = json_encode($alertProducts);
            $alertEmailLog->save();

            return response([
                'message' => "Alert emails sent successfully",
                'return_code' => '0',
                'alerted_vouchers' => $alertProducts
            ], 200);
        } else {
            return response([
                'message' => "All vouchers are above threshold",
                'return_code' => '0',
            ], 200);
        }
    }

    public function alertNotification()
    {
        $products = ProductModel::orderBy('created_at', 'desc')
            ->leftJoin('voucher_main', function ($join) {
                $join->on('product.id', '=', 'voucher_main.product_id')
                    ->where('voucher_main.available', true)
                    ->whereNull('voucher_main.deplete_date');
            })
            ->select('product.*', DB::raw('COUNT(voucher_main.id) as available_voucher_count'))
            ->groupBy('product.id')
            ->get();

        $alertEmailGroup = AlertEmailGroupModel::get();
        $alertProducts = [];

        foreach ($products as $product) {
            if ($product->threshold_alert > $product->available_voucher_count) {
                $alertProducts[] = $product;
            }
        }

        if (!empty($alertProducts)) {

            return response([
                'message' => "Vouchers below threshold",
                'return_code' => '0',
                'alerted_vouchers' => $alertProducts
            ], 200);
        } else {
            return response([
                'message' => "All vouchers are above threshold",
                'return_code' => '0',
                'alerted_vouchers' => $alertProducts
            ], 200);
        }
    }

    public function getAllAlertEmailLogs()
    {
        $alertEmailLog = AlertEmailLogsModel::get();

        return response([
            'message' => "All alert email logs displayed successfully",
            'return_code' => '0',
            'results' => $alertEmailLog
        ], 200);
    }

    public function getAllAlertEmailGroup()
    {
        $alertEmailGroup = AlertEmailGroupModel::get();

        return response([
            'message' => "All alert email group members displayed successfully",
            'return_code' => '0',
            'results' => $alertEmailGroup
        ], 200);
    }

    public function getAlertEmailGroup($id)
    {
        $alertEmailGroup = AlertEmailGroupModel::where('id', $id)->first();

        return response([
            'message' => "Alert email group member displayed successfully",
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
