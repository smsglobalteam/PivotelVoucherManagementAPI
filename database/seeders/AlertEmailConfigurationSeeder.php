<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AlertEmailConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $errorCodes = [
            ['configuration_name' => 'alert_email_toggle', 'configuration_value' => 1, 'configuration_description' => '1 to enable, 0 to disable email alert'],
            ['configuration_name' => 'alert_email_interval', 'configuration_value' => 180, 'configuration_description' => 'Sets interval hours per email alert'],
            ['configuration_name' => 'batch_expiry_interval', 'configuration_value' => 180, 'configuration_description' => 'Sets interval hours per batch expiry alert'],
            ['configuration_name' => 'batch_expiry_days_from_now', 'configuration_value' => 48, 'configuration_description' => 'Set the days from now to trigger batch expiry alert'],
            ['configuration_name' => 'batch_expiry_email_toggle', 'configuration_value' => 1, 'configuration_description' => '1 to enable, 0 to disable batch expiry alert'],
        ];

        foreach ($errorCodes as $code) {
            $exists = DB::table('alert_email_configuration')->where('configuration_name', $code['configuration_name'])->exists();

            if (!$exists) {
                DB::table('alert_email_configuration')->insert([
                    'configuration_name' => $code['configuration_name'],
                    'configuration_value' => $code['configuration_value'],
                    'configuration_description' => $code['configuration_description'],
                    'created_by' => 'System-generated',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}
