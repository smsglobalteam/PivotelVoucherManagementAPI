<?php

namespace App\Http\Controllers;

use App\Models\BatchOrderHistoryModel;
use App\Models\BatchOrderModel;
use App\Models\ProductModel;
use App\Models\VoucherHistory;
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

    public function createBatchOrder(Request $request)
    {
        $request->validate([
            'batch_id' => 'required|integer|unique:batch_order,batch_id',
            'batch_count' => 'required|integer|min:1',
            'product_id' => 'nullable|exists:product,product_id',
            'file' => 'required|file',
        ]);

        $product = ProductModel::where('product_id', $request->product_id)
            ->first();

        $file = $request->file('file');

        $extension = $file->getClientOriginalExtension();
        if ($extension !== 'csv') {
            return response([
                'message' => 'Invalid file format. Only CSV files are supported.',
                'return_code' => '-205',
            ], 422);
        }

        $filePath = $file->getPathname();
        $file = fopen($filePath, 'r');

        fgetcsv($file);

        $vouchers = [];
        $rowCount = 0;

        while (($row = fgetcsv($file)) !== false) {
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
        
            // Perform validation for the current row
            $validator = Validator::make([
                'expire_date' => $expireDate,
                'value' => $value,
                'serial' => $serialNumber,
                'IMEI' => $IMEI,
                'SIMNarrative' => $SIMNarrative,
                'PCN' => $PCN,
                'SIMNo' => $SIMNo,
                'PUK' => $PUK,
                'IMSI' => $IMSI,
                'service_reference' => $serviceReference,
                'business_unit' => $businessUnit,
            ], [
                'expire_date' => 'nullable|date_format:Y-m-d|after:today',
                'value' => 'nullable|integer',
                'serial' => 'required|string|unique:voucher_main,serial',
                'IMEI' => 'nullable|string',
                'SIMNarrative' => 'nullable|string',
                'PCN' => 'nullable|string',
                'SIMNo' => 'nullable|string',
                'PUK' => 'required|string|unique:voucher_main,PUK',
                'IMSI' => 'nullable|string',
                'service_reference' => 'nullable|string',
                'business_unit' => 'nullable|string',
            ]);
        
            if ($validator->fails()) {
                $errors["$rowCount"] = $validator->errors()->messages();
                continue;
            }
        
            // Store valid data in an array
            $validData[] = [
                'expire_date' => $expireDate,
                'value' => $value,
                'serial' => $serialNumber,
                'IMEI' => $IMEI,
                'SIMNarrative' => $SIMNarrative,
                'PCN' => $PCN,
                'SIMNo' => $SIMNo,
                'PUK' => $PUK,
                'IMSI' => $IMSI,
                'service_reference' => $serviceReference,
                'business_unit' => $businessUnit,
    
                'product_code' => $product->product_code,
                'product_id' => $product->product_id,

            ];
        }
        

        fclose($file);

        if (!empty ($errors)) {
            return response([
                'message' => 'Errors found in the uploaded file.',
                'return_code' => '-206',
                'errors' => $errors,
            ], 422);
        }

        if($request->batch_count != $rowCount) {
            return response([
                'message' => 'Batch count does not match the number of vouchers uploaded.',
                'return_code' => '-211',
            ], 422);
        }

        $batchOrder = BatchOrderModel::create([
            'batch_id' => $request->batch_id,
            'product_id' => $request->product_id,
            'batch_count' => $rowCount,
            'created_by' => $request->attributes->get('preferred_username'),
        ]);

        $vouchers = [];
        foreach ($validData as $data) {
            $data['batch_id'] = $batchOrder->batch_id; 
            $voucher = VoucherModel::create($data);
            $vouchers[] = $voucher;
        }    

        $batchOrderHistory = new BatchOrderHistoryModel();
        $batchOrderHistory->user_id = $request->attributes->get('preferred_username');
        $batchOrderHistory->transaction = "Created Batch Order";
        $batchOrderHistory->batch_order_new_data = json_encode($batchOrder);
        $batchOrderHistory->save();

        $history = new VoucherHistory();
        $history->user_id = $request->attributes->get('preferred_username');
        $history->transaction = "Created Batch Order";
        $history->voucher_new_data = json_encode($vouchers);
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
            $batchOrderHistory = new BatchOrderHistoryModel();
            $batchOrderHistory->user_id = $request->attributes->get('preferred_username');
            $batchOrderHistory->transaction = "Edited Batch Order";
            $batchOrderHistory->batch_order_old_data = json_encode($batchOrder_old->toArray());
            $batchOrderHistory->batch_order_new_data = json_encode($batchOrder->toArray());
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
