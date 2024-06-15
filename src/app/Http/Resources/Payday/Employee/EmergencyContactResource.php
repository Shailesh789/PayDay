<?php

namespace App\Http\Resources\Payday\Employee;


use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Carbon;

class EmergencyContactResource extends ResourceCollection
{

    public function toArray($request)
    {
        return  $this->collection->map(function ($data) {
                return [
                    'id' => intval($data->id),
                    'user_id' => intval($data->user->id),
                    'name' => $data->value->name ?? '',
                    'country' => $data->value->country ?? '',
                    'details' => $data->value->details ?? '',
                    'area' => $data->value->area ?? '',
                    'email' => $data->value->email ?? '',
                    'phone_number' => $data->value->phone_number ?? '',
                    'relationship' => $data->value->relationship ?? '',
                ];
            });
    }
}
