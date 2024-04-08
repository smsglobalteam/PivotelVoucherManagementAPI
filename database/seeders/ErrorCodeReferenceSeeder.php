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
