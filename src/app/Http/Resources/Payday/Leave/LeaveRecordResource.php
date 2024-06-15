<?php

namespace App\Http\Resources\Payday\Leave;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Carbon;

class LeaveRecordResource extends ResourceCollection
{

    public function toArray($request)
    {
        return [
            'leave_records' => $this->collection->map(function ($groupData) {
                return $groupData->map(function ($data) {
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
                        'leave_type_id' => $data->leave_type_id,
                        'status_id' => $data->status_id,
                        'working_shift_details_id' => $data->working_shift_details_id,
                        'date' => date('j', strtotime($data->date)),
                        'month' => date('M', strtotime($data->date)),
                        'start_at' => $data->start_at,
                        'end_at' => $data->end_at,
                        'leave_duration' => $leaveDuration,
                        'duration_type' => $data->duration_type,
                        'attachments' => $data->attachments->map(function ($attachment) {
                            return [
                                'type' => $attachment->type,
                                'full_url' => $attachment->full_url
                            ];
                        }),
                        'comments' => $data->comments->map(function ($comment) {
                            return [
                                'type' => $comment->type,
                                'note' => $comment->comment,
                            ];
                        }),
                        'leave_status' => $data->status->translated_name ?? '',
                        'leave_status_class' => $data->status->class ?? '',
                        'leave_type' => $data->type->name ?? '',
                        'created_at' => $data->created_at ? Carbon::parse($data->created_at)->format('Y-m-d H:i:s') : null,
                        'updated_at' => $data->updated_at ? Carbon::parse($data->updated_at)->format('Y-m-d H:i:s') : null,
                    ];
                });
            })
        ];
    }
}
