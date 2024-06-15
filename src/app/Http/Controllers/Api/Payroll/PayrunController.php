<?php

namespace App\Http\Controllers\Api\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tenant\Employee\EmployeePayrunController;
use App\Http\Resources\Payday\Payroll\PayrunResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayrunController extends Controller
{
    public function index()
    {

        try {
            $payrun = resolve(EmployeePayrunController::class)->index(Auth::user());
             return success_response('Payrun!', new PayrunResource($payrun));
        } catch (\Exception $ex) {
            return error_response('Internal Server Error!', [], 500);
        }
    }
}
