<?php

namespace App\Http\Resources\Payday\Attendance;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DailyLogResource extends ResourceCollection
{

    public function toArray($request)
    {
        return $this->collection->map(function ($data) {
            return [
                'id' => intval($data->id),
                'attendance_id' => intval($data->attendance_id),
                'start_time' => dateTimeInOwnRegion($data->in_time ?? '', request()->get('timezone')),
                'in_time' => dateTimeInAmPm($data->in_time ?? '', request()->get('timezone')),
                'out_time' => $data->out_time ? dateTimeInAmPm($data->out_time, request()->get('timezone')) : '',
                'total_hours' => workingHourFromSecond($data),
                'in_ip_data' => $data->in_ip_data ? json_decode($data->in_ip_data) : null,
                'out_ip_data' => $data->out_ip_data ? json_decode($data->out_ip_data) : null,
                'notes' => $data->comments->map(function ($comment) {
                    return [
                        'id' => $comment->id,
                        'type' => $comment->type,
                        'comment' => $comment->comment
                    ];
                })
            ];
        });
    }

}
