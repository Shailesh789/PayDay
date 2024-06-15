<?php

namespace App\Http\Resources\Payday\Employee;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Carbon;

class AssetResource extends ResourceCollection
{

    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {

                $status = $data->is_working;
                if ($status == 'yes') {
                    $status = 'working';
                } else if ($status == 'no') {
                    $status = 'not working';
                }

                return [
                    'id' => intval($data->id),
                    'name' => $data->name ?? '',
                    'code' => $data->code ?? '',
                    'serial_number' => $data->serial_number ?? '',
                    'date' => $data->date ? Carbon::parse($data->date)->format('d M Y') : '',
                    'status' => $status ?? '',
                    'type_name' => $data->type->name ?? '',
                    'note' => $data->note ?? '',
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
