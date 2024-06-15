<?php

namespace App\Http\Resources\Payday\Payroll;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Carbon;

class PayslipResource extends ResourceCollection
{

    public function toArray($request)
    {
        return [
            'payslips' => $this->collection->map(function ($data) {
                return [
                    'id' => intval($data->id),
                    'payslip_id' => $data->payrun->uid,
                    'user_id' => intval($data->user_id),
                    'payrun_id' => intval($data->payrun_id),
                    'date_in_number' => \Carbon\Carbon::parse($data->created_at)->format('d'),
                    'month' => Carbon::parse($data->created_at)->format('M'),
                    'start_date' => Carbon::parse($data->start_date)->format('d.m.y'),
                    'end_date' => Carbon::parse($data->end_date)->format('d.m.y'),
                    'net_salary' => number_format($data->net_salary, 2),
                    'basic_salary' => number_format($data->basic_salary, 2),
                    'period' => ucfirst($data->period),
                    'consider_overtime' => $data->consider_overtime,
                    'consider_type' => ucfirst($data->consider_type),
                    'without_beneficiary' => $data->without_beneficiary,
                    'conflicted' => $data->conflicted,
                    'status_name' => $data->status->translated_name,
                    'status_class' => $data->status->class,
                ];

            }),
            'links' => [
                "first" => request()->url() . "?page=1",
                "last" => request()->url() . "?page=" . $this->lastPage(),
                "prev" => $this->previousPageUrl(),
                "next" => $this->nextPageUrl()
            ],
            'meta' => [
                'total' => $this->total(),
                'count' => $this->count(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'total_pages' => $this->lastPage()
            ]
        ];
    }
}
