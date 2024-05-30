<?php

namespace App\Http\Controllers;

use App\Models\ServicePlansModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WebServicePlansController extends Controller
{
    //
    public function getAllServicePlans()
    {
        $servicePlans = Cache::remember('servicePlans', 60 * 60, function () {
            return ServicePlansModel::all();
        });

        return response([
            'message' => "All service plans displayed successfully",
            'return_code' => '0',
            'results' => $servicePlans
        ], 200);
    }

    public function getAllServicePlansByCode($id)
    {
        $servicePlans = ServicePlansModel::where('code', $id)->first();

        if (!$servicePlans) {
            return response([
                'message' => "Service plan not found",
            ], 404);
        }

        return response([
            'message' => "Service plan displayed successfully",
            'results' => $servicePlans
        ], 200);
    }

    public function createNewServicePlan(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'code' => 'required|integer',
            //unique:service_plans,code
            'description' => 'required|string',
            'display_description' => 'required|string',
            'group' => 'required|string',
            'contract' => 'required|string',
            'plan' => 'required|string',
            'start_date' => 'required|date_format:n/j/Y',
            'end_date' => 'required|date_format:n/j/Y',
        ]);

        $startDate = Carbon::createFromFormat('n/j/Y', $request->input('start_date'))->format('Y-m-d');
        $endDate = Carbon::createFromFormat('n/j/Y', $request->input('end_date'))->format('Y-m-d');

        $servicePlan = ServicePlansModel::create([
            'type' => $request->type,
            'code' => $request->code,
            'description' => $request->description,
            'display_description' => $request->display_description,
            'group' => $request->group,
            'contract' => $request->contract,
            'plan' => $request->plan,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'created_by' => $request->attributes->get('preferred_username'),
        ]);

        return response([
            'message' => "Service plan created successfully",
            'return_code' => '0',
            'results' => $servicePlan,
            'test' => $startDate
        ], 201);
    }

    public function editServicePlanByCode($code, Request $request)
    {
        $servicePlan = ServicePlansModel::where('code', $code)->first();

        $request->validate([
            'type' => 'required|string',
            // 'code' => 'required|integer',
            //unique:service_plans,code
            'description' => 'required|string',
            'display_description' => 'required|string',
            'group' => 'required|string',
            'contract' => 'required|string',
            'plan' => 'required|string',
            'start_date' => 'required|date_format:n/j/Y',
            'end_date' => 'required|date_format:n/j/Y',
        ]);

        // Convert dates to MySQL format

        $startDate = Carbon::createFromFormat('n/j/Y', $request->input('start_date'))->format('Y-m-d');
        $endDate = Carbon::createFromFormat('n/j/Y', $request->input('end_date'))->format('Y-m-d');

        $servicePlan->update([
            'type' => $request->type,
            'code' => $request->code,
            'description' => $request->description,
            'display_description' => $request->display_description,
            'group' => $request->group,
            'contract' => $request->contract,
            'plan' => $request->plan,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'updated_by' => $request->attributes->get('preferred_username'),
        ]);

        $servicePlan->refresh();

        return response([
            'message' => "Service plan created successfully",
            'return_code' => '0',
            'results' => $servicePlan,
            'test' => $startDate
        ], 201);
    }
}
