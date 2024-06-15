<?php

namespace App\Http\Controllers\Api\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tenant\Payroll\PayrollSummeryController;
use App\Http\Resources\Payday\Payroll\PayrunDetailsResource;
use App\Http\Resources\Payday\Payroll\PayslipResource;
use App\Models\Tenant\Payroll\Payslip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayslipController extends Controller
{
    public function index()
    {
        $payslips = resolve(PayrollSummeryController::class)->index(auth()->user());
        try {
            return success_response('Payslip List', new PayslipResource($payslips));
        } catch (\Exception $ex) {
            return error_response('Server Error!', [], 500);
        }
    }

    public function summary()
    {

        try {
            $response = resolve(PayrollSummeryController::class)->summery(Auth::user());
            $summary = [
                'total' => 0.00,
                'sent' => 0.00,
                'conflicted' => 0.00,
            ];

            if (isset($response['card_summaries'])) {
                $payslipSummary = $response['card_summaries'];
                $summary['total'] = intval($payslipSummary['total']);
                $summary['sent'] = intval($payslipSummary['sent']);
                $summary['conflicted'] = intval($payslipSummary['conflicted']);
            }

            return success_response('Payslip Summary', ['summary' => $summary]);
        } catch (\Exception $ex) {
            return error_response('Server Error!', [], 500);
        }
    }

    public function show(Payslip $payslip)
    {

        try {
            $payslip = $payslip->load('payrun',
                'payrun.beneficiaries',
                'payrun.beneficiaries.beneficiary',
                'status',
                'beneficiaries',
                'beneficiaries.beneficiary');

            return success_response('Payslip', new PayrunDetailsResource($payslip));
        } catch (\Exception $ex) {
            return error_response($ex->getMessage(), [], 500);
        }
    }
}
