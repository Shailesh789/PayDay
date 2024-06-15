<?php

namespace App\Http\Controllers\Api\Leave;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tenant\Employee\EmployeeLeaveAllowanceController;
use App\Services\Api\Leave\LeaveAllowanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveAllowanceController extends Controller
{
    public function __construct(LeaveAllowanceService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        try {
            $response = resolve(EmployeeLeaveAllowanceController::class)->index(Auth::user());
            $data = $this->service->allowance($response);

            return success_response('Leave Allowance', $data);
        } catch (\Exception $ex) {
            return error_response('Server Error!', [], 500);
        }
    }

    public function allowanceDetails()
    {
        try {
            $response = resolve(EmployeeLeaveAllowanceController::class)->index(Auth::user());
            $data = $this->service->allowanceDetails($response['allowances']);

            return success_response('Leave Allowance', $data);
        } catch (\Exception $ex) {
            return error_response('Server Error!', [], 500);
        }
    }

    public function availability()
    {
        try {
            $response = resolve(EmployeeLeaveAllowanceController::class)->index(Auth::user());
            $data = $this->service->availability($response);

            return success_response('Leave Availability', $data);
        } catch (\Exception $ex) {
            return error_response('Server Error!', [], 500);
        }
    }

    public function type()
    {
        try {
            $response = resolve(EmployeeLeaveAllowanceController::class)->index(Auth::user());
            $data = $this->service->type($response);

            return success_response('Leave Types', $data);
        } catch (\Exception $ex) {
            return error_response('Server Error!', [], 500);
        }
    }
}
