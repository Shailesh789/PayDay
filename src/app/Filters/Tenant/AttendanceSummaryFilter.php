<?php


namespace App\Filters\Tenant;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AttendanceSummaryFilter extends AttendanceDailLogFilter
{

    public function rangeFilter($attendanceActive, $ranges)
    {
        return function (Builder $builder) use ($attendanceActive, $ranges) {
            if (count($ranges) == 1) {
                return $builder->whereDate('in_date', $ranges[0])
                    ->where('status_id', $attendanceActive);
            }

            return $builder->whereDate('in_date', '>=', $ranges[0])
                ->where('status_id', $attendanceActive)
                ->whereHas(
                    'details',
                    fn(Builder $bl) => $bl->whereDate('out_time', '<=', $ranges[1])
                        ->where('status_id', $attendanceActive)
                );
        };
    }

    public function date($date = null)
    {
        $date = json_decode(htmlspecialchars_decode($date), true);

        $this->builder->when($date, function (Builder $builder) use ($date) {
            $builder->whereBetween(\DB::raw('DATE(in_date)'), array_values($date));
        });
    }

    public function attendanceBehavior($behavior = null)
    {
        $this->builder->when($behavior, function (Builder $builder) use ($behavior) {
            $builder->where('behavior', $behavior);
        });
    }

    public function entryType()
    {
        $this->builder->when(request()->get('entry_type'), function (Builder $builder) {
            $builder->when(request()->get('entry_type') == 'single', function (Builder $builder) {
                $builder->has('details', '=', 1);
            })->when(request()->get('entry_type') == 'multiple', function (Builder $builder) {
                $builder->has('details', '>', 1);
            });
        });
    }

    public function status()
    {
        $this->builder->when(request()->get('status'), function (Builder $builder) {
            $builder->whereHas('details', function (Builder $builder) {
                $builder->where('status_id', request()->get('status'));
            });
        });
    }

}