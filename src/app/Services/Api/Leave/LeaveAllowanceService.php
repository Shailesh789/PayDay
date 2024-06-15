<?php

namespace App\Services\Api\Leave;

use App\Services\Core\BaseService;

class LeaveAllowanceService extends BaseService
{
    public function allowance($data)
    {

        $arrayOne = [];
        $arrayTwo = [];
        $arrayThree = [];
        $arrayFour = [];

        foreach ($data['allowances'] as $item) {

            if ($item->leaveType->type == 'paid') {
                $arrayOne['leave_status'] = 'Availability';
                $arrayOne['leave_type'] = ucfirst($item->leaveType->type);
                $arrayOne['values'][] = [
                    'leave_type' => $item->leaveType->name,
                    'value' => number_format(($item->amount - $item->taken), 2),
                ];
                $arrayThree['leave_status'] = 'Taken';
                $arrayThree['leave_type'] = ucfirst($item->leaveType->type);
                $arrayThree['values'][] = [
                    'leave_type' => $item->leaveType->name,
                    'value' => number_format(($item->taken), 2),
                ];
            }
            if ($item->leaveType->type == 'unpaid') {
                $arrayTwo['leave_status'] = 'Availability';
                $arrayTwo['leave_type'] = ucfirst($item->leaveType->type);
                $arrayTwo['values'][] = [
                    'leave_type' => $item->leaveType->name,
                    'value' => number_format(($item->amount - $item->taken), 2),
                ];
                $arrayFour['leave_status'] = 'Taken';
                $arrayFour['leave_type'] = ucfirst($item->leaveType->type);
                $arrayFour['values'][] = [
                    'leave_type' => $item->leaveType->name,
                    'value' => number_format(($item->taken), 2),
                ];
            }
        }

        return [$arrayOne, $arrayThree, $arrayTwo, $arrayFour];
    }

    public function availability($data)
    {

        $response = [
            'availability' => [
                'casual' => [
                    'paid' => '0.00',
                    'unpaid' => '0.00',
                ],
                'sick' => [
                    'paid' => '0.00',
                    'unpaid' => '0.00',
                ],
            ],
            'taken' => [
                'casual' => [
                    'paid' => '0.00',
                    'unpaid' => '0.00',
                ],
                'sick' => [
                    'paid' => '0.00',
                    'unpaid' => '0.00',
                ],
            ]
        ];

        foreach ($data['allowances'] as $item) {
            switch ($item['leaveType']['alias']) {
                case 'paid-casual':
                    $response['availability']['casual']['paid'] = number_format(($item->amount - $item->taken), 2);
                    $response['taken']['casual']['paid'] = number_format($item->taken, 2);
                    break;
                case 'unpaid-casual':
                    $response['availability']['casual']['unpaid'] = number_format(($item->amount - $item->taken), 2);
                    $response['taken']['casual']['unpaid'] = number_format($item->taken, 2);
                    break;
                case 'paid-sick':
                    $response['availability']['sick']['paid'] = number_format(($item->amount - $item->taken), 2);
                    $response['taken']['sick']['paid'] = number_format($item->taken, 2);
                    break;
                case 'unpaid-sick':
                    $response['availability']['sick']['unpaid'] = number_format(($item->amount - $item->taken), 2);
                    $response['taken']['sick']['unpaid'] = number_format($item->taken, 2);
                    break;
            }
        }

        return $response;
    }

    public function type($data)
    {

        $response = [];

        foreach ($data['allowances'] as $key => $item) {
            $response[$key]['id'] = $item['leaveType']->id;
            $response[$key]['name'] = $item['leaveType']->name;
        }

        return $response;
    }

    public function allowanceDetails($allowances)
    {
        $data = [];

        foreach ($allowances as $key => $allowance) {
            $data[$key]['type'] = $allowance->leaveType->name;
            $data[$key]['leave_type'] = ucfirst($allowance->leaveType->type);
            $data[$key]['allowance'] = number_format(floatval($allowance->leaveType->amount), 2);
            $data[$key]['earned'] = number_format(floatval($allowance->amount), 2);
            $data[$key]['taken'] = number_format(floatval($allowance->taken), 2);
            $data[$key]['availability'] = number_format(floatval($allowance->amount - $allowance->taken), 2);
        }
        return $data;
    }
}
