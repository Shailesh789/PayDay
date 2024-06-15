<?php

namespace App\Http\Resources\Payday\Attendance;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Carbon\Carbon;

class SummaryLogResource extends ResourceCollection
{

    public function toArray($request)
    {
        $queryString['start'] = request()->query('date_range') ? json_decode(request()->query('date_range'))->start : '';
        $queryString['end'] = request()->query('date_range') ? json_decode(request()->query('date_range'))->end : '';
        return [
            'query_string' => $queryString,
            'data' => $this->collection->map(function ($data) {

                $totalComments = $data->details()
                    ->with('comments')
                    ->get()
                    ->pluck('comments')
                    ->flatten()
                    ->count();
                return [
                    'id' => intval($data->id),
                    'date' => $data->in_date,
                    'date_in_number' => Carbon::parse($data->in_date)->format('d'),
                    'month' => Carbon::parse($data->in_date)->format('M'),
                    'user_id' => intval($data->user_id),
                    'behavior' => ucfirst($data->behavior),
                    'total_hours' => workingHorFromList($data->details, true),
                    'total_comments' => $totalComments,
                    'details' => $data->details->map(function ($detail) {
                        return [
                            'id' => intval($detail->id),
                            'attendance_id' => intval($detail->attendance_id),
                            'status_id' => intval($detail->status_id),
                            'in_time' => $detail->in_time ? dateTimeInAmPm($detail->in_time, request()->get('timezone')) : '',
                            'out_time' => $detail->out_time ? dateTimeInAmPm($detail->out_time, request()->get('timezone')) : '',
                            'start_time' => $detail->in_time ?? '',
                            'total_hours' => workingHourFromSecond($detail),
                            'in_ip_data' => $detail->in_ip_data ? json_decode($detail->in_ip_data) : null,
                            'out_ip_data' => $detail->out_ip_data ? json_decode($detail->out_ip_data) : null,
                            'status_name' => $detail->status->name ?? '',
                            'status_class' => $detail->status->class ?? '',
                            'comments' => $detail->comments->map(function ($comment) {
                                return [
                                    'id' => intval($comment->id),
                                    'user_id' => intval($comment->user_id),
                                    'type' => $comment->type ?? '',
                                    'comment' => $comment->comment ?? '',
                                ];
                            }),
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

