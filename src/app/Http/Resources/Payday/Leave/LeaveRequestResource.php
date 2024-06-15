<?php

namespace App\Http\Resources\Payday\Leave;

use App\Helpers\Traits\DateTimeHelper;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Carbon;

class LeaveRequestResource extends ResourceCollection
{
    use DateTimeHelper;

    public function toArray($request)
    {
        return $this->collection->map(function ($data) {
            $leaveDuration = '';
            switch ($data->duration_type) {
                case 'single_day':
                    $leaveDuration = '1 day';
                    break;
                case 'multi_day':
                    $leaveDuration = Carbon::parse($data->start_at)->diffInDays(Carbon::parse($data->end_at)) + 1;
                    $leaveDuration .= $leaveDuration > 1 ? ' days' : ' day';
                    break;
                case 'first_half':
                    $leaveDuration = 'First Half';
                    break;
                case 'last_half':
                    $leaveDuration = 'Last Half';
                    break;
                case 'hours':
                    $leaveDuration = Carbon::parse($data->start_at)->diffInHours(Carbon::parse($data->end_at));
                    $leaveDuration .= $leaveDuration > 1 ? ' hrs' : ' hr';
                    break;
            }

            return [
                'id' => $data->id,
                'user_id' => $data->user_id,
                'leave_duration' => $leaveDuration,
                'date' => date('j', strtotime($data->date)),
                'month' => date('M', strtotime($data->date)),
                'test' => $data->date,
                'duration_type' => $data->duration_type,
                'leave_status' => $data->status->translated_name ?? '',
                'leave_status_class' => $data->status->class ?? '',
                'leave_type' => $data->type->name ?? '',
                'leave_start_at' => dateTimeInAmPm($data->start_at, request()->get('timezone')) ?? '',
                'leave_end_at' => dateTimeInAmPm($data->end_at, request()->get('timezone')) ?? '',
            ];
        });
    }
}
