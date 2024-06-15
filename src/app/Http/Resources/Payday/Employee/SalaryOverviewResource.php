<?php

namespace App\Http\Resources\Payday\Employee;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Carbon;

class SalaryOverviewResource extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function ($data) {
            $basicSalary = false;
            $message = '';
            $level = '';
            $increment = false;
            $effectiveDate = '';
            $createdDate = '';

            if (!$data->end_at) {
                if (Carbon::parse($data->start_at)->gt(Carbon::now())) {
                    $level = "Salary Increment";
                    $message = "Will be effective from " . date('d M Y', strtotime($data->start_at));
                    $increment = true;
                    $effectiveDate = date('d M Y', strtotime($data->start_at));
                    $createdDate = date('d M Y', strtotime($data->created_at));
                } else {
                    $level = date('d M Y', strtotime($data->start_at)) . ' - Present';
                }
            }

            if (Carbon::parse($data->start_at)->lt(Carbon::now()) && ($data->end_at && Carbon::parse($data->end_at)->gt(Carbon::now()))) {
                $level = date('d M Y', strtotime($data->start_at)) . ' - Present';
                $basicSalary = true;
            } else if (Carbon::parse($data->start_at)->lt(Carbon::now()) && ($data->end_at && Carbon::parse($data->end_at)->lt(Carbon::now()))) {
                $level = date('d M Y', strtotime($data->start_at)) . ' - ' . date('d M Y', strtotime($data->end_at));
            }


            return [
                'added_by' => $data->addedBy->full_name,
                'increment' => $increment,
                'effective_date' => $effectiveDate,
                'created_date' => $createdDate,
                'basic_salary' => $basicSalary,
                'level' => $level,
                'message' => $message,
                'amount' => number_format($data->amount, 2),
            ];
        });
    }
}
