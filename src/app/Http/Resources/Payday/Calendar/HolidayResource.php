<?php

namespace App\Http\Resources\Payday\Calendar;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Carbon;

class HolidayResource extends ResourceCollection
{

    public function toArray($request)
    {
        return [
            'holidays' => $this->collection->map(function ($data) {
                return [
                    'id' => intval($data->id),
                    'name' => $data->name,
                    'description' => $data->description,
                    'start_date' => Carbon::parse($data->start_date)->format('Y-m-d'),
                    'end_date' => $data->end_date ? Carbon::parse($data->end_date)->format('Y-m-d') : null,
                    'repeats_annually' => intval($data->repeats_annually),
                    'departments' => $data->departments->map(function ($department) {
                        return [
                            'id' => $department->id,
                            'name' => $department->name,
                        ];
                    })
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
