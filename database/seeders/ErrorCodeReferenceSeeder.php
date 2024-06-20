<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ErrorCodeReferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $errorCodes = [
            //Product Errors
            ['error_code' => '-5001', 'error_message' => 'Product not found in the database.', 'error_description' => 'No record for the product in the database.'],
            ['error_code' => '-5002', 'error_message' => 'The product code is required.', 'error_description' => 'Missing product code in the data provided.'],
            ['error_code' => '-5003', 'error_message' => 'The product code can not have spaces.', 'error_description' => 'Product code contains invalid spaces.'],
            ['error_code' => '-5004', 'error_message' => 'The product code must be unique.', 'error_description' => 'Duplicate product code entry detected.'],
            ['error_code' => '-5005', 'error_message' => 'The product ID is required.', 'error_description' => 'Missing product ID in the data provided.'],
            ['error_code' => '-5006', 'error_message' => 'The product ID must be an integer.', 'error_description' => 'Invalid format for product ID, integer required.'],
            ['error_code' => '-5007', 'error_message' => 'The product ID must be unique.', 'error_description' => 'Duplicate product ID entry detected.'],
            ['error_code' => '-5008', 'error_message' => 'The product name is required.', 'error_description' => 'Missing product name in the data provided.'],
            ['error_code' => '-5009', 'error_message' => 'The product name must be unique.', 'error_description' => 'Duplicate product name entry detected.'],
            ['error_code' => '-5010', 'error_message' => 'The product supplier is required.', 'error_description' => 'Missing product supplier information.'],
            ['error_code' => '-5011', 'error_message' => 'The status is required.', 'error_description' => 'Missing product status information.'],
            ['error_code' => '-5012', 'error_message' => 'The status must be true(1) or false(0).', 'error_description' => 'Non boolean product status value.'],
            
            //Batch Order Errors
            ['error_code' => '-6002', 'error_message' => 'The batch ID is required.', 'error_description' => 'Missing batch ID in the request.'],
            ['error_code' => '-6003', 'error_message' => 'The batch ID must be a string.', 'error_description' => 'Batch ID must be alphanumeric and cannot contain special characters.'],
            ['error_code' => '-6004', 'error_message' => 'The batch ID must be unique.', 'error_description' => 'The batch ID provided already exists in the system.'],
            ['error_code' => '-6005', 'error_message' => 'The batch count is required.', 'error_description' => 'Batch count is necessary to process the batch.'],
            ['error_code' => '-6006', 'error_message' => 'The batch count must be an integer.', 'error_description' => 'Batch count should be a whole number.'],
            ['error_code' => '-6007', 'error_message' => 'The batch count must be at least 1.', 'error_description' => 'Batch count cannot be zero or negative.'],
            ['error_code' => '-6008', 'error_message' => 'The product ID is required.', 'error_description' => 'Product ID is necessary for linking to the correct product records.'],
            ['error_code' => '-6009', 'error_message' => 'The product ID must exist in the database.', 'error_description' => 'The provided product ID does not match any existing records.'],
            ['error_code' => '-6010', 'error_message' => 'The file is required.', 'error_description' => 'A file must be uploaded to proceed with the batch operation.'],
            ['error_code' => '-6011', 'error_message' => 'The file must be of type csv or txt.', 'error_description' => 'Invalid file format; only CSV or TXT files are accepted.'],
            ['error_code' => '-6012', 'error_message' => 'The batch count does not match the number of rows in the CSV', 'error_description' => 'Mismatch between batch count and the contents of the file.'],
            ['error_code' => '-6013', 'error_message' => 'The serial is a duplicate in the CSV.', 'error_description' => 'Repeated serial number found in the uploaded CSV file.'],
            ['error_code' => '-6014', 'error_message' => 'The PUK is a duplicate in the CSV.', 'error_description' => 'Repeated PUK number detected in the file.'],

            //Voucher Errors
            ['error_code' => '-7002', 'error_message' => 'The serial is required.', 'error_description' => 'Serial number must be provided for registration.'],
            ['error_code' => '-7003', 'error_message' => 'The serial must be unique.', 'error_description' => 'Each serial number in the system must be unique to avoid conflicts.'],
            ['error_code' => '-7004', 'error_message' => 'The PUK is required.', 'error_description' => 'PUK is necessary for SIM card activation.'],
            ['error_code' => '-7005', 'error_message' => 'The PUK must be unique.', 'error_description' => 'PUK must be unique to ensure secure access.'],
            ['error_code' => '-7006', 'error_message' => 'The value must be an integer.', 'error_description' => 'Numerical input expected to be an integer.'],
            ['error_code' => '-7007', 'error_message' => 'The expire date format is invalid.', 'error_description' => 'Date must be in a recognized format, typically YYYY-MM-DD.'],
            ['error_code' => '-7008', 'error_message' => 'The expire date must be after today.', 'error_description' => 'Provided expire date must be a future date.'],
            ['error_code' => '-7009', 'error_message' => 'The IMEI must be a string.', 'error_description' => 'IMEI should be entered as a string of digits.'],
            ['error_code' => '-7010', 'error_message' => 'The SIMNarrative must be a string.', 'error_description' => 'Narrative input must be textual.'],
            ['error_code' => '-7011', 'error_message' => 'The MSISDN must be a string.', 'error_description' => 'MSISDN values are expected to be strings.'],
            ['error_code' => '-7012', 'error_message' => 'The SIMNo must be a string.', 'error_description' => 'SIM number input should be alphanumeric.'],
            ['error_code' => '-7013', 'error_message' => 'The IMSI must be a string.', 'error_description' => 'IMSI should consist of numeric characters only.'],
            ['error_code' => '-7014', 'error_message' => 'The service reference must be a string.', 'error_description' => 'Service reference should be textual.'],
            ['error_code' => '-7015', 'error_message' => 'The business unit must be a string.', 'error_description' => 'Business unit descriptions must be provided in text format.'],
            ['error_code' => '-7016', 'error_message' => 'The service reference is required.', 'error_description' => 'Missing service reference in the request.'],
            ['error_code' => '-7017', 'error_message' => 'The business unit required.', 'error_description' => 'Missing business unit in the request.'],

            //Voucher Activation
            ['error_code' => '-7102', 'error_message' => 'Voucher under that serial not found.', 'error_description' => 'No matching serial for any voucher.'],
            ['error_code' => '-7103', 'error_message' => 'This voucher has already been consumed.', 'error_description' => 'Voucher has been redeemed already.'],
            ['error_code' => '-7104', 'error_message' => 'This voucher has already expired.', 'error_description' => 'Voucher is past its expiration date.'],
            ['error_code' => '-7105', 'error_message' => 'This voucher is not active.', 'error_description' => 'Voucher is not currently active.'],
            ['error_code' => '-7106', 'error_message' => 'Product ID does not match the voucher product.', 'error_description' => 'Mismatch between voucher and product ID.'],

            //Voucher Type
            ['error_code' => '-8002', 'error_message' => 'Voucher code format is invalid.', 'error_description' => ''],
            ['error_code' => '-8003', 'error_message' => 'The voucher code is required.', 'error_description' => ''],
            ['error_code' => '-8004', 'error_message' => 'The voucher name is required.', 'error_description' => ''],
            ['error_code' => '-8005', 'error_message' => 'The voucher type ID is required.', 'error_description' => ''],
            ['error_code' => '-8006', 'error_message' => 'The voucher type ID must exist in the database.', 'error_description' => ''],
            ['error_code' => '-8007', 'error_message' => 'The voucher code must exist in the database.', 'error_description' => ''],
            ['error_code' => '-8008', 'error_message' => 'The voucher code must be unique.', 'error_description' => ''],
            ['error_code' => '-8009', 'error_message' => 'The voucher code entered does not match the encoded voucher code.', 'error_description' => ''],

            //Alert Email Group
            ['error_code' => '-9002', 'error_message' => 'Name is required.', 'error_description' => ''],
            ['error_code' => '-9003', 'error_message' => 'Email is required.', 'error_description' => ''],
            ['error_code' => '-9004', 'error_message' => 'Email format is invalid.', 'error_description' => ''],
            ['error_code' => '-9005', 'error_message' => 'Email must be unique.', 'error_description' => ''],
            ['error_code' => '-9006', 'error_message' => 'Threshold must be an integer.', 'error_description' => ''],
        ];

        foreach ($errorCodes as $code) {
            // Check if the error code already exists
            $exists = DB::table('error_codes_reference')->where('error_code', $code['error_code'])->exists();

            // Insert the error code if it does not exist
            if (!$exists) {
                DB::table('error_codes_reference')->insert([
                    'error_code' => $code['error_code'],
                    'error_message' => $code['error_message'],
                    'error_description' => $code['error_description'],
                    'created_by' => 'System-generated',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}
