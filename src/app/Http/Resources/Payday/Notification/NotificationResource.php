<?php

namespace App\Http\Resources\Payday\Notification;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class NotificationResource extends ResourceCollection
{

    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id' => $data->id,
                    'notifier_id' => $data->data ? $data->data['notifier_id'] : '',
                    'name' => $data->data ? $data->data['name'] : '',
                    'url' => $data->data ? $data->data['url'] : '',
                    'title' => $data->data ? $data->data['message'] : '',
                    'read' => $data->read_at ? true : false,
                    //check if read_at is today then show today else show date and time
                    'date' => Carbon::parse($data->created_at)->format('Y-m-d') == Carbon::now()->format('Y-m-d') ? 'Today' : Carbon::parse($data->created_at)->format('d M'),
                    'time' => dateTimeInAmPm($data->created_at, request()->get('timezone'))
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