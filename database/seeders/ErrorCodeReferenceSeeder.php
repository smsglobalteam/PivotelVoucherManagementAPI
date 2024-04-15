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
            ['error_code' => '-5001', 'error_message' => 'Product not found in the database.'],
            ['error_code' => '-5002', 'error_message' => 'The Product Code is required.'],
            ['error_code' => '-5003', 'error_message' => 'The Product Code can not have spaces.'],
            ['error_code' => '-5004', 'error_message' => 'The Product Code must be unique.'],
            ['error_code' => '-5005', 'error_message' => 'The Product ID is required.'],
            ['error_code' => '-5006', 'error_message' => 'The Product ID must be an integer.'],
            ['error_code' => '-5007', 'error_message' => 'The Product ID must be unique.'],
            ['error_code' => '-5008', 'error_message' => 'The Product Name is required.'],
            ['error_code' => '-5009', 'error_message' => 'The Product Name must be unique.'],
            ['error_code' => '-5010', 'error_message' => 'The Supplier is required.'],

            ['error_code' => '-6002', 'error_message' => 'The batch ID is required.'],
            ['error_code' => '-6003', 'error_message' => 'The batch ID must be a string.'],
            ['error_code' => '-6004', 'error_message' => 'The batch ID must be unique.'],
            ['error_code' => '-6005', 'error_message' => 'The batch count is required.'],
            ['error_code' => '-6006', 'error_message' => 'The batch count must be an integer.'],
            ['error_code' => '-6007', 'error_message' => 'The batch count must be at least 1.'],
            ['error_code' => '-6008', 'error_message' => 'The product ID is required.'],
            ['error_code' => '-6009', 'error_message' => 'The product ID must exist in the database.'],
            ['error_code' => '-6010', 'error_message' => 'The file is required.'],
            ['error_code' => '-6011', 'error_message' => 'The file must be of type csv or txt.'],
            ['error_code' => '-6012', 'error_message' => 'The batch count does not match the number of rows in the CSV'],
            ['error_code' => '-6013', 'error_message' => 'The serial is a duplicate in the CSV.'],
            ['error_code' => '-6014', 'error_message' => 'The PUK is a duplicate in the CSV.'],

            ['error_code' => '-7002', 'error_message' => 'The serial is required.'],
            ['error_code' => '-7003', 'error_message' => 'The serial must be unique.'],
            ['error_code' => '-7004', 'error_message' => 'The PUK is required.'],
            ['error_code' => '-7005', 'error_message' => 'The PUK must be unique.'],
            ['error_code' => '-7006', 'error_message' => 'The value must be an integer.'],
            ['error_code' => '-7007', 'error_message' => 'The expire date format is invalid.'],
            ['error_code' => '-7008', 'error_message' => 'The expire date must be after today.'],
            ['error_code' => '-7009', 'error_message' => 'The IMEI must be a string.'],
            ['error_code' => '-7010', 'error_message' => 'The SIMNarrative must be a string.'],
            ['error_code' => '-7011', 'error_message' => 'The PCN must be a string.'],
            ['error_code' => '-7012', 'error_message' => 'The SIMNo must be a string.'],
            ['error_code' => '-7013', 'error_message' => 'The IMSI must be a string.'],
            ['error_code' => '-7014', 'error_message' => 'The service reference must be a string.'],
            ['error_code' => '-7015', 'error_message' => 'The business unit must be a string.'],
        ];

        foreach ($errorCodes as $code) {
            DB::table('error_codes_reference')->insert([
                'error_code' => $code['error_code'],
                'error_message' => $code['error_message'],
                'created_by' => 'system',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
