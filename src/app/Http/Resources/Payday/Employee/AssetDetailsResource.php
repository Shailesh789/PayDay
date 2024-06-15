<?php

namespace App\Http\Resources\Payday\Employee;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class AssetDetailsResource extends JsonResource
{
    public function toArray($request)
    {
        $data = parent::toArray($request);

        $status = $data['is_working'];
        if ($status == 'yes') {
            $status = 'working';
        } else if ($status == 'no') {
            $status = 'not working';
        }

        return [
            'id' => intval($data['id']),
            'name' => $data['name'] ?? '',
            'code' => $data['code'] ?? '',
            'serial_number' => $data['serial_number'] ?? '',
            'date' => $data['date'] ? Carbon::parse($data['date'])->format('d M Y') : '',
            'status' => $status ?? '',
            'type_name' => $data['type']['name'] ?? '',
            'note' => $data['note'] ?? ''
        ];


    }
}
