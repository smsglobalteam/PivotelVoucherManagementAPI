<?php

namespace App\Http\Controllers;

use App\Models\BatchOrderModel;
use App\Models\ErrorCodesModel;
use App\Models\HistoryLogsModel;
use App\Models\ProductModel;
use App\Models\VoucherModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BatchOrderController extends Controller
{
    //
    public function getAllBatchOrder()
    {
        $batchOrder = BatchOrderModel::with('voucher')
            ->leftJoin('product', 'batch_order.product_id', '=', 'product.id')
            ->orderBy('created_at', 'desc')
            ->leftJoin('voucher_main', function ($join) {
                $join->on('batch_order.product_id', '=', 'voucher_main.product_id')
                    ->where('voucher_main.available', true)
                    ->whereNull('voucher_main.deplete_date')
                    ->where(function ($query) {
                        $query->whereNull('voucher_main.expiry_date')
                            ->orWhere('voucher_main.expiry_date', '>', now());
                    });
            })
            ->select('batch_order.*', 'product.threshold_alert', DB::raw('COUNT(voucher_main.id) as available_voucher_count'))
            ->groupBy(
                'batch_order.id',
                'product.product_name',
                'product.threshold_alert'
            )
            ->get();

        return response([
            'message' => "All batch order successfully",
            'return_code' => '0',
            'results' => $batchOrder,
        ], 200);
    }

    public function getBatchOrderByVoucherID($batch_id)
    {
        $batchOrder = BatchOrderModel::where('batch_id', $batch_id)
            ->with('voucher')
            ->first();

        if (!$batchOrder) {
            return response([
                'message' => "Batch Order not found",
                'return_code' => '-201',
            ], 404);
        }

        return response([
            'message' => "Batch order displayed successfully",
            'return_code' => '0',
            'results' => $batchOrder,
        ], 200);
    }

    private function transformCSVData($csvContent)
    {
        $transformedContent = [];
        // Define the header for the new CSV format
        $header = [
            'expire_date',
            'value',
            'serial',
            'IMEI',
            'SIMNarrative',
            'PCN',
            'SIMNo',
            'PUK',
            'IMSI',
            'service_reference',
            'business_unit'
        ];

        $transformedContent[] = $header;

        // Loop through each row of the original CSV content
        foreach ($csvContent as $row) {
            // Create a new row for the destination format with default/null values
            $newRow = array_fill(0, count($header), null);

            // Check and assign the first column to the 'serial' column (index 2)
            $newRow[0] = isset($row[0]) ? $row[0] : null;

            // Check and assign the second column to the 'PUK' column (index 7)
            $newRow[2] = isset($row[1]) ? $row[1] : null;

            // Add the new row to the transformed content
            $transformedContent[] = $newRow;
        }

        return $transformedContent;
    }

    function cleanDataArray(array $data): array
    {
        return array_filter($data, function ($row) {
            // Check if all values in the row are null
            return !empty(array_filter($row, function ($value) {
                return !is_null($value);
            }));
        });
    }


    public function testReq(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        $file = $request->file('file');

        $extension = $file->getClientOriginalExtension();
        if ($extension !== 'csv') {
            return response([
                'message' => 'Invalid file format. Only CSV files are supported.',
                'return_code' => '-205',
            ], 422);
        }

        $filePath = $file->getPathname();
        $fileResource = fopen($filePath, 'r');

        $firstRow = fgetcsv($fileResource);
        $isPivotelFormat = count($firstRow) == 2;
        $csvContent = [];

        if ($isPivotelFormat) {

            rewind($fileResource);

            while (($row = fgetcsv($fileResource)) !== FALSE) {
                $csvContent[] = $row;
            }
            fclose($fileResource);

            // Transform the CSV data
            $csvContent = $this->transformCSVData($csvContent);
        } else {
            rewind($fileResource);
            while (($row = fgetcsv($fileResource)) !== FALSE) {
                $csvContent[] = $row;
            }
            fclose($fileResource);
        }

        return response([
            'message' => 'CSV content',
            'filePath' => $filePath,
            'csvContent' => $csvContent,
        ], 200);
    }

    public function transformErrorRows($validator, $rowNumber)
    {
        $errors = [];
        foreach ($validator->errors()->getMessages() as $field => $messages) {
            foreach ($messages as $message) {
                $errorCode = $this->getCustomErrorCode($message);
                $errors[$rowNumber][] = [
                    'error_code' => $errorCode,
                    'error_message' => $message,
                    'error_field' => $field
                ];
            }
        }
        return $errors;
    }


    public function createBatchOrder(Request $request, ErrorCodesController $errorCodesController)
    {

        //Get error message and code for duplicates
        $errorCodeSerialDuplicate = "-6013"; // "The serial is a duplicate in the CSV."
        $errorCodePUKDuplicate = "-6014"; // "The PUK is a duplicate in the CSV."

        $errorFieldSerial = "serial";
        $errorFieldPUK = "PUK";

        $errorMessageSerialDuplicate = ErrorCodesModel::where('error_code', $errorCodeSerialDuplicate)->first();
        $errorMessagePUKDuplicate = ErrorCodesModel::where('error_code', $errorCodePUKDuplicate)->first();

        // Check if the error messages were found, assign default messages if not
        if (!$errorMessageSerialDuplicate) {
            $errorMessageSerialDuplicate = [
                'error_code' => $errorCodeSerialDuplicate,
                'error_message' => "The serial is a duplicate.",
                'error_field' => $errorFieldSerial
            ];
        } else {
            // Create custom response with only error_code and error_message
            $errorMessageSerialDuplicate = [
                'error_code' => $errorMessageSerialDuplicate->error_code,
                'error_message' => $errorMessageSerialDuplicate->error_message,
                'error_field' => $errorFieldSerial
            ];
        }

        if (!$errorMessagePUKDuplicate) {
            $errorMessagePUKDuplicate = [
                'error_code' => $errorCodePUKDuplicate,
                'error_message' => "The PUK is a duplicate.",
                'error_field' => $errorFieldPUK
            ];
        } else {
            // Create custom response with only error_code and error_message
            $errorMessagePUKDuplicate = [
                'error_code' => $errorMessagePUKDuplicate->error_code,
                'error_message' => $errorMessagePUKDuplicate->error_message,
                'error_field' => $errorFieldPUK
            ];
        }


        $csvDuplicates = [];
        $errors = [];
        $validData = [];
        $customErrors = []; // Array to collect custom error codes

        try {
            $request->validate([
                'batch_id' => 'required|string|unique:batch_order,batch_id',
                'batch_count' => 'required|integer|min:1',
                'product_id' => 'required|exists:product,id',
                'expiry_date' => 'nullable|date_format:Y-m-d',
                // 'voucher_type_id' => 'required|exists:voucher_type,id',
                'file' => 'required|file|mimes:csv,txt',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Map each validation error to a custom code
            $customErrors = array_merge($customErrors, $errorCodesController->mapValidationErrorsToCustomCodes($e->validator));
        }

        $product = ProductModel::where('id', $request->product_id)
            ->first();

        if ($product === null) {
            $product = new \stdClass();
            $product->product_code = null;
            $product->id = null;
        }

        $file = $request->file('file');
        $filePath = $file->getPathname();
        $fileResource = fopen($filePath, 'r');

        $csvContent = [];
        while (($row = fgetcsv($fileResource)) !== false) {
            $csvContent[] = $row;
        }
        fclose($fileResource);

        $csvContent = $this->cleanDataArray($csvContent);

        $isPivotelFormat = count($csvContent[0]) == 2;
        if ($isPivotelFormat) {
            $transformedContent = $this->transformCSVData($csvContent);
        } else {
            $transformedContent = $csvContent;
        }

        $vouchers = [];
        $duplicates = [];
        $uniqueCheck = [];
        $rowCount = 0;
        $serialArray = [];
        $PUKArray = [];
        $appearanceDetails = []; // Ensure this is initialized
        $duplicateDetails = []; // Ensure this is initialized


        foreach ($transformedContent as $index => $row) {
            if ($index === 0) {
                continue;
            }

            $rowCount++;
            $serialNumber = $row[0];
            $SIM = $row[1];
            $PUK = $row[2];
            $IMSI = $row[3];
            $MSISDN = $row[4];
            $serviceReference = $row[5];
            $businessUnit = $row[6];
            $note = $row[7];

            // Record every appearance of serials and PUKs
            $appearanceDetails['serial'][$serialNumber][] = $rowCount;
            $appearanceDetails['PUK'][$PUK][] = $rowCount;

            // Construct unique keys for fields that need to be unique
            $serialKey = $serialNumber;
            $pukKey = $PUK;

            $serialArray[] = $serialNumber;
            $PUKArray[] = $PUK;

            $csv['serial'] = $serialArray;
            $csv['PUK'] = $PUKArray;

            // Check for duplicates and add error messages
            if (isset($uniqueCheck[$serialKey])) {
                $csvDuplicates[$rowCount][] = $errorMessageSerialDuplicate;
                // Track the row where the duplicate was found
                if (!isset($duplicateDetails['serial'][$serialNumber])) {
                    $duplicateDetails['serial'][$serialNumber] = [];
                }
                $duplicateDetails['serial'][$serialNumber][] = $index + 1; // Adjust index to match row number
            } else {
                $uniqueCheck[$serialKey] = $rowCount;
            }

            if (isset($uniqueCheck[$pukKey])) {
                $csvDuplicates[$rowCount][] = $errorMessagePUKDuplicate;
                // Track the row where the duplicate was found
                if (!isset($duplicateDetails['PUK'][$PUK])) {
                    $duplicateDetails['PUK'][$PUK] = [];
                }
                $duplicateDetails['PUK'][$PUK][] = $index + 1; // Adjust index to match row number
            } else {
                $uniqueCheck[$pukKey] = $rowCount;
            }

            // Perform validation for the current row
            $validator = Validator::make([
                'serial' => $serialNumber,
                'SIM' => $SIM,
                'PUK' => $PUK,
                'IMSI' => $IMSI,
                'MSISDN' => $MSISDN,
                'service_reference' => $serviceReference,
                'business_unit' => $businessUnit,
                'note' => $note,
            ], [
                'serial' => 'required|string|unique:voucher_main,serial',
                'SIM' => 'nullable|string',
                'PUK' => 'required|string|unique:voucher_main,PUK',
                'IMSI' => 'nullable|string',
                'MSISDN' => 'nullable|string',
                'service_reference' => 'nullable|string',
                'business_unit' => 'nullable|string',
                'note' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                $rowErrorCurrent = $errorCodesController->mapValidationErrorsToCustomCodes($validator);

                // Get the current row's error messages
                $currentRowErrors = $errorCodesController->getErrorMessagesFromCodes($rowErrorCurrent);

                // Store the current row's errors under the row counter key
                $csvDuplicates[$rowCount] = $currentRowErrors;

                continue; // Skip further processing for this row
            }


            // Store valid data in an array
            $validData[] = [
                'serial' => $serialNumber,
                'PUK' => $PUK,
                'SIM' => $SIM,
                'IMSI' => $IMSI,
                'MSISDN' => $MSISDN,
                'service_reference' => $serviceReference,
                'business_unit' => $businessUnit,
                'note' => $note,

                // 'product_code' => $product->product_code,
                'product_id' => $product->id,
                // 'voucher_type_id' => $request->voucher_type_id,
                'expiry_date' => $request->expiry_date,

                'created_by' => $request->attributes->get('preferred_username'),

            ];
        }


        if ($request->batch_count != count($transformedContent) - 1) {
            $customErrors[] = [
                "error_code" => "-6012",
                "error_field" => "batch_count" // Assuming you want to specify which field the error relates to
            ];
        }


        $duplicatedRows = [];

        foreach ($appearanceDetails as $type => $details) {
            foreach ($details as $value => $rows) {
                // Report as duplicate only if appears more than once
                if (count($rows) > 1) {
                    $duplicatedRows[] = [
                        $type => $value,
                        'rows' => $rows
                    ];
                }
            }
        }

        $errorMessages = $errorCodesController->getErrorMessagesFromCodes($customErrors);

        if (!empty($errorMessages) || !empty($csvDuplicates)) {
            return response([
                'message' => 'Errors found in the uploaded file.',
                'return_code' => '-206',
                'errors' => $errorMessages,
                'csvDuplicates' => $csvDuplicates,
                'duplicated_rows' => $duplicatedRows,
                'csv' => $csv,
            ], 422);
        }

        $batchOrder = BatchOrderModel::create([
            'batch_id' => $request->batch_id,
            'product_id' => $request->product_id,
            // 'voucher_type_id' => $request->voucher_type_id,
            'expiry_date' => $request->expiry_date,
            'batch_count' => $rowCount,
            'created_by' => $request->attributes->get('preferred_username'),
        ]);

        $vouchers = [];
        foreach ($validData as &$data) {
            // if (!isset($data['value']) || $data['value'] === '') {
            //     $data['value'] = 0;
            // }

            // if (!isset($data['expire_date']) || $data['expire_date'] === '') {
            //     $data['expire_date'] = null;
            // }

            $data['batch_id'] = $batchOrder->batch_id;
            $voucher = VoucherModel::create($data);
            $vouchers[] = $voucher;
        }

        $batchOrderHistory = new HistoryLogsModel();
        $batchOrderHistory->username = $request->attributes->get('preferred_username');
        $batchOrderHistory->transaction = "Created Batch Order";
        $batchOrderHistory->database_table = "batch_order";
        $batchOrderHistory->new_data = json_encode($batchOrder);
        $batchOrderHistory->save();

        $history = new HistoryLogsModel();
        $history->username = $request->attributes->get('preferred_username');
        $history->transaction = "Created Batch Order Vouchers";
        $history->database_table = "voucher_main";
        $history->new_data = json_encode($vouchers);
        $history->save();


        return response([
            'message' => "Batch order created successfully",
            'return_code' => '0',
            'results' => $batchOrder,
            'vouchers' => $vouchers,
        ], 201);
    }

    public function editBatchOrderByID($id, Request $request)
    {
        $batchOrder = BatchOrderModel::where('batch_id', $id)->first();

        if (!$batchOrder) {
            return response([
                'message' => "Batch order not found",
                'return_code' => '-101',
            ], 404);
        }

        $batchOrder_old = clone $batchOrder;

        $request->validate([
            'product_id' => 'required|exists:product,id',
        ]);

        $batchOrder->update([
            'product_id' => $request->product_id,
            'updated_by' => $request->attributes->get('preferred_username'),
        ]);

        $batchOrder->refresh();

        if ($batchOrder->wasChanged()) {
            $batchOrderHistory = new HistoryLogsModel();
            $batchOrderHistory->username = $request->attributes->get('preferred_username');
            $batchOrderHistory->transaction = "Edited Batch Order";
            $batchOrderHistory->database_table = "batch_order";
            $batchOrderHistory->old_data = json_encode($batchOrder_old->toArray());
            $batchOrderHistory->new_data = json_encode($batchOrder->toArray());
            $batchOrderHistory->save();
        }

        return response([
            'message' => "Batch order updated successfully",
            'return_code' => '0',
            'results' => $batchOrder
        ], 201);
    }

    public function deleteBatchOrderByID($batch_id)
    {
        $batchOrder = BatchOrderModel::where('batch_id', $batch_id)->first();

        if (!$batchOrder) {
            return response([
                'message' => "Batch order not found",
                'return_code' => '-101',
            ], 404);
        }

        $batchOrder->delete();

        return response([
            'message' => "Batch order deleted successfully",
            'return_code' => '0',
            'results' => $batchOrder
        ], 200);
    }
}
