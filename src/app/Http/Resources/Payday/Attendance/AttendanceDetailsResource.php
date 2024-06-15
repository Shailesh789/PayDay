<?php

namespace App\Http\Resources\Payday\Attendance;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AttendanceDetailsResource extends ResourceCollection
{

    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id' => intval($data->id),
                    'date' => $data->in_date,
                    'date_in_number' => Carbon::parse($data->in_date)->format('d'),
                    'month' => Carbon::parse($data->in_date)->format('M'),
                    'user_id' => intval($data->user_id),
                    'behavior' => $data->behavior,
                    'details' => $data->details->map(function ($detail) {
                        return [
                            'id' => intval($detail->id),
                            'attendance_id' => intval($detail->attendance_id),
                            'status_id' => intval($detail->status_id),
                            'in_time' => $detail->in_time ? dateTimeInAmPm($detail->in_time,request()->get('timezone')) : '',
                            'out_time' => $detail->out_time ? dateTimeInAmPm($detail->out_time,request()->get('timezone')) : '',
                            'start_time' => $detail->in_time ?? '',
                            'total_hours' => workingHourFromSecond($detail),
                            'in_ip_data' => $detail->in_ip_data ? json_decode($detail->in_ip_data) : null,
                            'out_ip_data' => $detail->out_ip_data ? json_decode($detail->out_ip_data) : null,
                            'status_name' => $detail->status->name ?? '',
                            'status_class' => $detail->status->class ?? ''
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
