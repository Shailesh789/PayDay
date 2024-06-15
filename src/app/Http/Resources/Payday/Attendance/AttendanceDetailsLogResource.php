<?php

namespace App\Http\Resources\Payday\Attendance;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceDetailsLogResource extends JsonResource
{
    public function toArray($request)
    {
        $data = parent::toArray($request);
        $data['id'] = intval($data['id']);
        $data['attendance_id'] = intval($data['attendance_id']);
        $breakTimes = 0.00;
        foreach ($data['break_times'] as $break_time) {
            $breakTimes += totalTimeDifference($break_time['start_at'], $break_time['end_at'] ?? Carbon::parse(now())->format('Y-m-d H:i:s'));
        }

        $data['break_times'] = $breakTimes;

        $data['status_id'] = intval($data['status_id']);
        $data['behavior'] = $this->attendance->behavior != '' ? ucfirst($this->attendance->behavior) : '';
        $data['in_date'] = $this->attendance->in_date ? Carbon::parse($this->attendance->in_date)->format('d M Y') : '';
        $data['check_in_time'] = $data['in_time'] ? dateTimeInAmPm($data['in_time'], request()->get('timezone')) : '';
        $data['check_out_time'] = $data['out_time'] ? dateTimeInAmPm($data['out_time'], request()->get('timezone')) : '';
        $data['total_hours'] = $data['out_time'] ? totalTimeDifference($data['in_time'], $data['out_time']) : 0.00;
        $data['in_ip_data'] = $data['in_ip_data'] ? json_decode($data['in_ip_data']) : null;
        $data['out_ip_data'] = $data['out_ip_data'] ? json_decode($data['out_ip_data']) : null;
        $data['review_by'] = $data['review_by'] ? intval($data['review_by']) : null;
        $data['punch_in_status'] = $data['review_by'] ? 'Manual' : 'Auto';
        $data['log_date'] = Carbon::parse($data['updated_at'])->format('d M Y');
        $data['log_time'] = $data['updated_at'] ? dateTimeInAmPm($data['updated_at'], request()->get('timezone')) : '';
        $data['created_at'] = $data['created_at'] ? dateTimeInAmPm($data['created_at'], request()->get('timezone')) : '';
        $data['updated_at'] = $data['updated_at'] ? dateTimeInAmPm($data['updated_at'], request()->get('timezone')) : '';
        //parent_attendance_details relationship
        $data['parent_attendance_details'] = $this->parentAttendanceDetails ? [
            'id' => intval($this->parentAttendanceDetails->id),
            'in_time' => $this->parentAttendanceDetails->in_time ? dateTimeInAmPm($this->parentAttendanceDetails->in_time, request()->get('timezone')) : '',
            'out_time' => $this->parentAttendanceDetails->out_time ? dateTimeInAmPm($this->parentAttendanceDetails->out_time, request()->get('timezone')) : '',
            'log_date' => Carbon::parse($this->parentAttendanceDetails->updated_at)->format('d M Y'),
            'log_time' => $this->parentAttendanceDetails->updated_at ? dateTimeInAmPm($this->parentAttendanceDetails->updated_at, request()->get('timezone')) : '',
            'status_id' => intval($this->parentAttendanceDetails->status_id),
            'review_by' => $this->parentAttendanceDetails->review_by ? intval($this->parentAttendanceDetails->review_by) : null,
            'attendance_details_id' => $this->parentAttendanceDetails->attendance_details_id ? intval($this->parentAttendanceDetails->attendance_details_id) : '',
            'created_at' => $this->parentAttendanceDetails->created_at ? dateTimeInAmPm($this->parentAttendanceDetails->created_at, request()->get('timezone')) : '',
            'updated_at' => $this->parentAttendanceDetails->updated_at ? dateTimeInAmPm($this->parentAttendanceDetails->updated_at, request()->get('timezone')) : '',
            'added_by' => $this->parentAttendanceDetails->added_by ? intval($this->parentAttendanceDetails->added_by) : null,
            'status' => $this->parentAttendanceDetails->status,
            'parent_attendance_details' => $this->parentAttendanceDetails->parentAttendanceDetails ? [
                'id' => intval($this->parentAttendanceDetails->parentAttendanceDetails->id),
                'in_time' => $this->parentAttendanceDetails->parentAttendanceDetails->in_time ? dateTimeInAmPm($this->parentAttendanceDetails->parentAttendanceDetails->in_time, request()->get('timezone')) : '',
                'out_time' => $this->parentAttendanceDetails->parentAttendanceDetails->out_time ? dateTimeInAmPm($this->parentAttendanceDetails->parentAttendanceDetails->out_time, request()->get('timezone')) : '',
                'log_date' => Carbon::parse($this->parentAttendanceDetails->parentAttendanceDetails->updated_at)->format('d M Y'),
                'log_time' => $this->parentAttendanceDetails->parentAttendanceDetails->updated_at ? dateTimeInAmPm($this->parentAttendanceDetails->parentAttendanceDetails->updated_at, request()->get('timezone')) : '',
                'status' => $this->parentAttendanceDetails->parentAttendanceDetails->status,
                'review_by' => $this->parentAttendanceDetails->parentAttendanceDetails->review_by ? intval($this->parentAttendanceDetails->parentAttendanceDetails->review_by) : null,

            ] : null,

        ] : null;

        $data['comments'] = $this->comments->map(function ($comment) {
            return [
                'id' => intval($comment->id),
                'user_id' => intval($comment->user_id),
                'type' => $comment->type,
                'comment' => $comment->comment,
            ];
        });

        return $data;
    }
}
