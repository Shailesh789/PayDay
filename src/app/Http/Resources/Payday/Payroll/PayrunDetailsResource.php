<?php

namespace App\Http\Resources\Payday\Payroll;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PayrunDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);
        $payslipInfo = [];
        $payslipInfo['id'] = intval($data['id']);
        $payslipInfo['use_id'] = intval($data['user_id']);
        $payslipInfo['payslip_id'] = @$data['payrun']['uid'];
        $payslipInfo['payrun_id'] = intval($data['payrun_id']);
        $payslipInfo['tenant_id'] = $data['tenant_id'];
        $payslipInfo['start_date'] = Carbon::parse($data['start_date'])->format('d M Y');
        $payslipInfo['end_date'] =  Carbon::parse($data['end_date'])->format('d M Y');
        $payslipInfo['created_at'] =  Carbon::parse($data['created_at'])->format('d M Y');
        $payslipInfo['net_salary'] = number_format($data['net_salary'],2);
        $payslipInfo['basic_salary'] = number_format($data['basic_salary'],2);
        $payslipInfo['period'] = ucfirst($data['period']);
        $payslipInfo['consider_overtime'] = $data['consider_overtime'];
        $payslipInfo['consider_type'] = ucfirst($data['consider_type']);

        //beneficiaries
        $allowances = [];
        $deductions = [];
        $totalAllowance = 0;
        $totalDeduction = 0;

        if (count($data['beneficiaries']) > 0){
            foreach ($data['beneficiaries'] as $key => $beneficiary) {
                if ($beneficiary['beneficiary']['type'] === 'allowance'){
                    $allowances[$key]['name'] = $beneficiary['beneficiary']['name'];
                    $allowances[$key]['type'] = $beneficiary['beneficiary']['type'];
                    $allowances[$key]['value'] = $beneficiary['amount'];
                    $allowances[$key]['is_percentage'] = $beneficiary['is_percentage'];
                    if ($beneficiary['is_percentage']){
                        $allowances[$key]['amount'] = number_format(($data['basic_salary']/100)*$beneficiary['amount'],2);
                        $totalAllowance += ($data['basic_salary']/100)*$beneficiary['amount'];
                    }else{
                        $allowances[$key]['amount'] = number_format($beneficiary['amount'],2);
                        $totalAllowance += $beneficiary['amount'];
                    }
                }else{
                    $deductions[$key]['name'] = $beneficiary['beneficiary']['name'];
                    $deductions[$key]['type'] = $beneficiary['beneficiary']['type'];
                    $deductions[$key]['value'] = $beneficiary['amount'];
                    $deductions[$key]['is_percentage'] = $beneficiary['is_percentage'];
                    if ($beneficiary['is_percentage']){
                        $deductions[$key]['amount'] = number_format(($data['basic_salary']/100)*$beneficiary['amount'],2);
                        $totalDeduction += ($data['basic_salary']/100)*$beneficiary['amount'];
                    }else{
                        $deductions[$key]['amount'] = number_format($beneficiary['amount'],2);
                        $totalDeduction += $beneficiary['amount'];
                    }
                }
            }
        }elseif (count($data['payrun']['beneficiaries']) > 0){
            foreach ($data['payrun']['beneficiaries'] as $key => $beneficiary) {
                if ($beneficiary['beneficiary']['type'] === 'allowance'){
                    $allowances[$key]['name'] = $beneficiary['beneficiary']['name'];
                    $allowances[$key]['type'] = $beneficiary['beneficiary']['type'];
                    $allowances[$key]['value'] = $beneficiary['amount'];
                    $allowances[$key]['is_percentage'] = $beneficiary['is_percentage'];
                    if ($beneficiary['is_percentage']){
                        $allowances[$key]['amount'] = number_format(($data['basic_salary']/100)*$beneficiary['amount'],2);
                        $totalAllowance += ($data['basic_salary']/100)*$beneficiary['amount'];
                    }else{
                        $allowances[$key]['amount'] = number_format($beneficiary['amount'],2);
                        $totalAllowance += $beneficiary['amount'];
                    }
                }else{
                    $deductions[$key]['name'] = $beneficiary['beneficiary']['name'];
                    $deductions[$key]['type'] = $beneficiary['beneficiary']['type'];
                    $deductions[$key]['value'] = $beneficiary['amount'];
                    $deductions[$key]['is_percentage'] = $beneficiary['is_percentage'];
                    if ($beneficiary['is_percentage']){
                        $deductions[$key]['amount'] = number_format(($data['basic_salary']/100)*$beneficiary['amount'],2);
                        $totalDeduction += ($data['basic_salary']/100)*$beneficiary['amount'];
                    }else{
                        $deductions[$key]['amount'] = number_format($beneficiary['amount'],2);
                        $totalDeduction += $beneficiary['amount'];
                    }
                }
            }
        }

        $payslipInfo['total_allowance'] = number_format($totalAllowance,2);
        $payslipInfo['total_deduction'] = number_format($totalDeduction,2);

        $payslipInfo['status'] = [
            'id' => $data['status']['id'],
            'status_class' => $data['status']['class'],
            'status_name' => $data['status']['translated_name'],
            'type' => $data['status']['type'],
        ];

        return [
            'payslip' => $payslipInfo,
            'allowances' => $allowances,
            'deductions' => $deductions
        ];
    }
}
