<?php

namespace App\Http\Resources\Payday\Employee;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Carbon;

class AnnouncementResource extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'announcements' => $this->collection->map(function ($data) {

                return [
                    'id' => intval($data->id),
                    'name' => $data->name ?? '',
                    'description' => $data->description ?? '',
                    'start_date' => $data->start_date ? Carbon::parse($data->start_date)->format('d M Y') : '',
                    'end_date' => $data->end_date ? Carbon::parse($data->end_date)->format('d M Y') : '',
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
