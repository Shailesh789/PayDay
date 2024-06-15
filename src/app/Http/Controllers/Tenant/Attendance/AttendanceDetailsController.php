<?php

namespace App\Http\Controllers\Tenant\Attendance;

use App\Filters\Tenant\AttendanceDetailsFilter;
use App\Helpers\Core\Traits\Memoization;
use App\Helpers\Traits\AssignRelationshipToPaginate;
use App\Helpers\Traits\DateRangeHelper;
use App\Helpers\Traits\DateTimeHelper;
use App\Http\Controllers\Controller;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Holiday\Holiday;
use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Models\Tenant\WorkingShift\WorkingShiftDetails;
use App\Repositories\Core\BaseRepository;
use App\Repositories\Core\Status\StatusRepository;
use App\Repositories\Tenant\Attendance\UserRepository;
use App\Services\Tenant\Attendance\AttendanceSummaryService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class AttendanceDetailsController extends Controller
{
    use DateRangeHelper, AssignRelationshipToPaginate, DateTimeHelper, Memoization;

    protected BaseRepository $repository;
    public $summaryService;
    public $defaultWorkingShift;
    public $range;

    public function __construct(AttendanceDetailsFilter $filter, UserRepository $repository, AttendanceSummaryService $summaryService)
    {
        $this->filter = $filter;
        $this->repository = $repository;
        $this->summaryService = $summaryService;
        $this->defaultWorkingShift = WorkingShift::getDefault(['id', 'name'])->load('details');
        $this->range = [];
    }

    public function index()
    {
        $within = request()->get('within');
        $month = $within ?: request('month_number') + 1;

        $attendanceActive = resolve(StatusRepository::class)
            ->attendanceApprove();

        $ranges = $this->getStartAndEndOf($month, request()->get('year'));

        if (request()->has('time_range') && request()->get('time_range')) {
            $dateRange = json_decode(htmlspecialchars_decode(request()->get('time_range')), true);
            $ranges = [
                $this->carbon($dateRange['start'])->parse(),
                $this->carbon($dateRange['end'])->parse()
            ];
        }

        $holidays = Holiday::generalHolidays($ranges);
        $leaveApprovedStatus = resolve(StatusRepository::class)->leaveApproved();
        $paginated = $this->repository
            ->setFilter($this->filter)
            ->setRelationships([
                'department:id,name',
                'attendances' => $this->filter->detailsFilter($attendanceActive, $ranges),
                'attendances.breakTimes',
                'attendances.details' => function (HasMany $details) use ($attendanceActive) {
                    $details->where('status_id', $attendanceActive)
                        ->select(['id', 'in_time', 'out_time', 'attendance_id']);
                },
                'profilePicture:id,fileable_type,fileable_id,path',
                'workingShifts:id,name',
                'workingShifts.details:id,working_shift_id,is_weekend,start_at,end_at,weekday',
                'department.holidays' => $this->filter->departmentHolidayFilter($ranges),
                'leaves' => function (HasMany $leave) use ($ranges, $leaveApprovedStatus) {
                    $leave->where('status_id', $leaveApprovedStatus)
                        ->whereHas('type', fn(Builder $leaveType) => $leaveType->where('type', 'paid'))
                        ->where(function (Builder $builder) use ($ranges) {
                            $builder->whereBetween(DB::raw('DATE(start_at)'), count($ranges) == 1 ? [$ranges[0], $ranges[0]] : $ranges)
                                ->orWhereBetween(DB::raw('DATE(end_at)'), count($ranges) == 1 ? [$ranges[0], $ranges[0]] : $ranges);
                        });
                }
            ])->get();

        return [
            'range' => request()->has('time_range') && request()->get('time_range') ?
                $this->dateRange($ranges[0], $ranges[1])
                : $this->getDateRange($month, request()->get('year')),
            'attendances' => $this->paginated($paginated)
                ->setRelation($this->generateHolidayListFromDates($holidays))
                ->get(),
            'default' => WorkingShift::getDefault(['id', 'name'])->load('details')
        ];
    }

    public function attendancePeriods()
    {
        return Attendance::selectRaw("DATE_FORMAT(in_date, '%Y') as year")
            ->groupBy('year')
            ->pluck('year')
            ->map(fn($year) => ['id' => $year, 'value' => $year]);
    }

    public function generateHolidayListFromDates(Collection $holidays)
    {
        return function (User $user) use ($holidays) {
            $user->setAttribute(
                'holidays',
                Holiday::getDatesFromHolidays($holidays->merge(optional($user->department)->holidays ?: collect([])))
            );
        };
    }

    public function attendanceDetailsShortForm(): array
    {
        $within = request()->get('within');
        $month = $within ? $within : request('month_number') + 1;

        $attendanceActive = resolve(StatusRepository::class)->attendanceApprove();
        $leaveApprovedStatus = resolve(StatusRepository::class)->leaveApproved();


        $ranges = $this->getStartAndEndOf($month, request()->get('year'));

        if (request()->has('date_range')) {
            $dateRange = json_decode(htmlspecialchars_decode(request()->get('date_range')), true);
            $ranges = [
                $this->carbon($dateRange['start'])->parse(),
                $this->carbon($dateRange['end'])->parse()
            ];
        }

        $userInformation = User::query()
            ->with([
                'department:id,name',
                'workingShifts:id,name',
                'workingShift.details:id,working_shift_id,is_weekend,start_at,end_at,weekday',
                'department.holidays' => $this->filter->departmentHolidayFilter($ranges),
                'leaves' => function (HasMany $leave) use ($ranges, $leaveApprovedStatus) {
                    $leave->where('status_id', $leaveApprovedStatus)
                        ->whereHas('type', fn(Builder $leaveType) => $leaveType->where('type', 'paid'))
                        ->where(function (Builder $builder) use ($ranges) {
                            $builder->whereBetween(DB::raw('DATE(start_at)'), count($ranges) == 1 ? [$ranges[0], $ranges[0]] : $ranges)
                                ->orWhereBetween(DB::raw('DATE(end_at)'), count($ranges) == 1 ? [$ranges[0], $ranges[0]] : $ranges);
                        });
                }
            ])
            ->where('id', auth()->id())
            ->first();


        $ranges = request()->has('date_range') && request()->get('date_range') ?
            $this->dateRange($ranges[0], $ranges[1])
            : $this->getDateRange($month, request()->get('year'));
        $this->range = $ranges;


        $monthDays = [];

        foreach ($ranges as $key => $range) {

            $todayInThreeWord = strtolower(Carbon::parse($range)->format('D'));

            //calculate scheduled hours
            $today = Carbon::parse($range)->format('Y-m-d');
            $scheduledHours = $this->getWorkingShift($range);

            //calculate is employee present or not
            $isPresent = Attendance::query()
                ->where('user_id', auth()->id())
                ->where('in_date', $today)
                ->where('status_id', $attendanceActive)
                ->with(['breakTimes', 'details' => function (HasMany $details) use ($attendanceActive) {
                    $details->where('status_id', $attendanceActive)
                        ->select(['id', 'in_time', 'out_time', 'attendance_id']);
                }])
                ->first();

            //calculate employees total working hours
            $totalWorkingHours = 0.00;
            $totalBreakHours = 0.00;

            if ($isPresent) {
                foreach ($isPresent->details as $detail) {
                    $totalWorkingHours += totalTimeDifference($detail->in_time, $detail->out_time);
                }

                foreach ($isPresent->breakTimes as $breakTime) {
                    $totalBreakHours += totalTimeDifference($breakTime->start_at, $breakTime->end_at);
                }
            }

            //calculate paid leaves
            $paidLeaves = 0.00;
            foreach ($userInformation->leaves as $leave) {
                $startDate = Carbon::parse($leave->start_at)->format('Y-m-d');
                $endDate = Carbon::parse($leave->end_at)->format('Y-m-d');
                if ($today >= $startDate && $today <= $endDate) {
                    $paidLeaves += totalTimeDifference($leave->start_at, $leave->end_at);
                }
            }

            //calculate everyday balance
            $decimalScheduledHours = (float) $scheduledHours;
            $balance = ($totalWorkingHours + $paidLeaves) - $decimalScheduledHours;


            $monthDays[] = [
                'month' => Carbon::parse($range)->format('M'),
                'date_in_number' => Carbon::parse($range)->format('d'),
                'scheduled_hours' => convertDecimalToHourMinute($scheduledHours),
                'total_working_hours' => convertDecimalToHourMinute($totalWorkingHours),
                'paid_leaves' => convertDecimalToHourMinute($paidLeaves),
                'balance' => convertDecimalToHourMinute($balance),
                'break_times' => convertDecimalToHourMinute($totalBreakHours)
            ];
        }
        return $monthDays;
    }

    public function getWorkingShift($date)
    {
        if ($this->checkHoliday($date)) {
            return 0;
        } elseif ($this->getWorkShiftDetails($this->getEmployeeWorkingShiftFromDate($date), $date)->is_weekend) {
            return 0;
        } else {
            if ($this->checkHoliday($date)) return 0;

            $work_shift_details = $this->getWorkShiftDetails($this->getEmployeeWorkingShiftFromDate($date), $date);

            if ($work_shift_details->is_weekend) return 0;

            return number_format(($work_shift_details->getWorkingHourInSeconds() / 3600), 2);
        }
    }

    public function checkHoliday($date)
    {
        return $this->memoize("holiday-{$date->format('d')}", function () use ($date) {
            return collect(auth()->user()->holidays)->contains(fn($holiday) => $date->equalTo($holiday));
        });
    }

    public function getWorkShiftDetails(WorkingShift $workingShift, Carbon $date): WorkingShiftDetails
    {
        return $this->memoize("details-{$workingShift->id}-" . strtolower($date->getTranslatedShortDayName()), function () use ($workingShift, $date) {
            return $workingShift->details->first(function (WorkingShiftDetails $workingShiftDetails) use ($date) {
                return $workingShiftDetails->weekday == strtolower($date->getTranslatedShortDayName());
            });
        });
    }

    public function getEmployeeWorkingShiftFromDate($date): WorkingShift
    {
        if (!auth()->user()->workingShifts->count()) {
            return $this->defaultWorkingShift;
        }
        return auth()->user()->workingShifts->first(function (WorkingShift $workingShift) use ($date) {
            if (($date->isSameAs('Y-m-d', $workingShift->pivot->start_date) || $date->isAfter($workingShift->pivot->start_date))
                && !$workingShift->pivot->end_date) {
                return true;
            }
            if ($workingShift->pivot->end_date && $date->isBetween($workingShift->pivot->start_date, $workingShift->pivot->end_date)) {
                return true;
            }
            return false;
        }) ?: $this->defaultWorkingShift;
    }

}