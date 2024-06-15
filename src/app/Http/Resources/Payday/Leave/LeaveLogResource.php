<?php

namespace App\Http\Resources\Payday\Leave;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class LeaveLogResource extends JsonResource
{

    public function toArray($request)
    {
        $leaveDuration = '';
        switch ($this->duration_type) {
            case 'single_day':
                $leaveDuration = '1 day';
                break;
            case 'multi_day':
                $leaveDuration = Carbon::parse($this->start_at)->diffInDays(Carbon::parse($this->end_at)) + 1;
                $leaveDuration .= $leaveDuration > 1 ? ' days' : ' day';
                break;
            case 'first_half':
                $leaveDuration = 'First Half';
                break;
            case 'last_half':
                $leaveDuration = 'Last Half';
                break;
            case 'hours':
                $leaveDuration = Carbon::parse($this->start_at)->diffInHours(Carbon::parse($this->end_at));
                $leaveDuration .= $leaveDuration > 1 ? ' hrs' : ' hr';
                break;
        }

        $logs = [
            [
                'level' => 'Applied',
                'log_date' => date('d M Y', strtotime($this->created_at)),
                'log_time' => dateTimeInAmPm($this->created_at,request()->get('timezone')),
                'log_by' => $this->user->full_name,
                'comment' => ''
            ]
        ];

        $reviews = $this->reviews->map(function ($review) {
            return [
                'level' => $review->status->translated_name,
                'log_date' => date('d M Y', strtotime($review->created_at)),
                'log_time' => dateTimeInAmPm($review->created_at,request()->get('timezone')),
                'log_by' => $review->reviewedBy->full_name,
                'comment' =>  isset($review->comments[0]) ? $review->comments[0]->comment : '',
            ];
        });

        return [
            'id' => $this->id,
            'leave_type' => $this->type->name,
            'leave_status' => $this->status->translated_name,
            'clas_name' => $this->status->class,
            'leave_duration' => $leaveDuration,
            'attachment_count' => count($this->attachments),
            'start_at' => date('d M Y', strtotime($this->start_at)),
            'end_at' => date('d M Y', strtotime($this->end_at)),
            'reason_note' => isset($this->comments[0]) ? $this->comments[0]->comment : '',
            'logs' => array_merge($logs, $reviews->toArray()),
            'leave_start_at' => dateTimeInAmPm($this->start_at, request()->get('timezone')) ?? '',
            'leave_end_at' => dateTimeInAmPm($this->end_at, request()->get('timezone')) ?? '',
            'attachments' => $this->attachments->map(function ($attachment) {
                return [
                    'type' => $attachment->type,
                    'full_url' => $attachment->full_url
                ];
            })
        ];
    }
}
