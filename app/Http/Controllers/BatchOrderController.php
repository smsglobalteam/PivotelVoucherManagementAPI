<?php

namespace App\Http\Controllers;

use App\Models\BatchOrderModel;
use App\Models\HistoryLogsModel;
use App\Models\ProductModel;
use App\Models\VoucherModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class BatchOrderController extends Controller
{
    //
    public function getAllBatchOrder()
    {
        $batchOrder = BatchOrderModel::with('voucher')->get();

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
            $newRow[2] = isset($row[0]) ? $row[0] : null;

            // Check and assign the second column to the 'PUK' column (index 7)
            $newRow[7] = isset($row[1]) ? $row[1] : null;

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


    public function createBatchOrder(Request $request)
    {
        $validationErrors = [];
        $errors = [];
        $validData = [];

        try {
            $request->validate([
                'batch_id' => 'required|string|unique:batch_order,batch_id',
                'batch_count' => 'required|integer|min:1',
                'product_id' => 'required|exists:product,product_id',
                'file' => 'required|file|mimes:csv,txt',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Get validation errors
            $validationErrors = $e->errors();

            foreach ($validationErrors as $field => $messages) {
                if (!isset($errors[$field])) {
                    $errors[$field] = [];
                }

                foreach ($messages as $message) {
                    $errors[$field][] = $message;
                }
            }
        }

        $product = ProductModel::where('product_id', $request->product_id)
            ->first();

        if ($product === null) {
            $product = new \stdClass();
            $product->product_code = null;
            $product->product_id = null;
        }

        $file = $request->file('file');
        $filePath = $file->getPathname();
        $fileResource = fopen($filePath, 'r');

        $csvContent = [];
        while (($row = fgetcsv($fileResource)) !== false) {
            $csvContent[] = $row;
        }
        fclose($fileResource);

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
        $customErrors = [];

        //Clean NULL rows
        $transformedContent = $this->cleanDataArray($transformedContent);

        foreach ($transformedContent as $index => $row) {
            if ($index === 0) {
                continue;
            }

            $rowCount++;
            $expireDate = $row[0];
            $value = $row[1];
            $serialNumber = $row[2];
            $IMEI = $row[3];
            $SIMNarrative = $row[4];
            $PCN = $row[5];
            $SIMNo = $row[6];
            $PUK = $row[7];
            $IMSI = $row[8];
            $serviceReference = $row[9];
            $businessUnit = $row[10];

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
                $errors['rows'][$rowCount]['serial'][] = "The serial number is a duplicate.";
                // Track the row where the duplicate was found
                if (!isset($duplicateDetails['serial'][$serialNumber])) {
                    $duplicateDetails['serial'][$serialNumber] = [];
                }
                $duplicateDetails['serial'][$serialNumber][] = $index + 1; // Adjust index to match row number
            } else {
                $uniqueCheck[$serialKey] = $rowCount;
            }

            if (isset($uniqueCheck[$pukKey])) {
                $errors['rows'][$rowCount]['PUK'][] = "The PUK is a duplicate.";
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
                'PUK' => $PUK,
                'value' => $value,
                'expire_date' => $expireDate,
                'IMEI' => $IMEI,
                'SIMNarrative' => $SIMNarrative,
                'PCN' => $PCN,
                'SIMNo' => $SIMNo,
                'IMSI' => $IMSI,
                'service_reference' => $serviceReference,
                'business_unit' => $businessUnit,
            ], [
                'serial' => 'required|string|unique:voucher_main,serial',
                'PUK' => 'required|string|unique:voucher_main,PUK',
                'value' => 'nullable|integer',
                'expire_date' => 'nullable|date_format:Y-m-d|after:today',
                'IMEI' => 'nullable|string',
                'SIMNarrative' => 'nullable|string',
                'PCN' => 'nullable|string',
                'SIMNo' => 'nullable|string',
                'IMSI' => 'nullable|string',
                'service_reference' => 'nullable|string',
                'business_unit' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                $errors['rows']["$rowCount"] = $validator->errors()->messages();
                continue;
            }

            // Store valid data in an array
            $validData[] = [
                'serial' => $serialNumber,
                'PUK' => $PUK,
                'value' => $value,
                'expire_date' => $expireDate,
                'IMEI' => $IMEI,
                'SIMNarrative' => $SIMNarrative,
                'PCN' => $PCN,
                'SIMNo' => $SIMNo,
                'IMSI' => $IMSI,
                'service_reference' => $serviceReference,
                'business_unit' => $businessUnit,

                'product_code' => $product->product_code,
                'product_id' => $product->product_id,

                'created_by' => $request->attributes->get('preferred_username'),

            ];
        }

        if ($request->batch_count != $rowCount) {
            $customErrors['batch_count'][] = "Batch count does not match the number of vouchers uploaded.";
        }

        foreach ($customErrors as $key => $value) {
            if (!isset($errors[$key])) {
                $errors[$key] = $value;
            } else {
                foreach ($value as $errorMsg) {
                    if (!in_array($errorMsg, $errors[$key])) {
                        $errors[$key][] = $errorMsg;
                    }
                }
            }
        }

        foreach ($validationErrors as $key => $value) {
            if (!isset($errors[$key])) {
                $errors[$key] = $value;
            } else {
                foreach ($value as $errorMsg) {
                    if (!in_array($errorMsg, $errors[$key])) {
                        $errors[$key][] = $errorMsg;
                    }
                }
            }
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

        if (!empty($errors)) {
            return response([
                'message' => 'Errors found in the uploaded file.',
                'return_code' => '-206',
                'errors' => $errors,
                'duplicated_rows' => $duplicatedRows,
                'csv' => $csv,
            ], 422);
        }

        $batchOrder = BatchOrderModel::create([
            'batch_id' => $request->batch_id,
            'product_id' => $request->product_id,
            'batch_count' => $rowCount,
            'created_by' => $request->attributes->get('preferred_username'),
        ]);

        $vouchers = [];
        foreach ($validData as &$data) {
            if (!isset($data['value']) || $data['value'] === '') {
                $data['value'] = 0;
            }

            if (!isset($data['expire_date']) || $data['expire_date'] === '') {
                $data['expire_date'] = null;
            }

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
            'product_id' => 'required|exists:product,product_id',
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
