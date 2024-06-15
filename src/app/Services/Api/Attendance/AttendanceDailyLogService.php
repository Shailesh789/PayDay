<?php

namespace App\Services\Api\Attendance;

use App\Http\Controllers\Tenant\Attendance\AttendanceDetailsController;
use App\Http\Controllers\Tenant\Attendance\AttendanceSummaryController;
use App\Http\Controllers\Tenant\Dashboard\EmployeeDashboardController;
use App\Http\Resources\Payday\Attendance\DailyLogResource;
use App\Http\Resources\Payday\Attendance\SummaryLogResource;
use App\Models\Core\Auth\User;
use App\Services\Core\BaseService;
use Carbon\Carbon;

class AttendanceDailyLogService extends BaseService
{

    public function dailyLog()
    {

        $dailyAttendanceLog = resolve(EmployeeDashboardController::class)->employeeMonthlyAttendanceLog();
        $behavior = '';
        if (isset($dailyAttendanceLog['today_attendance'])) {
            $behavior = $dailyAttendanceLog['today_attendance']['behavior'];
        }

        $dailyLogs = new DailyLogResource($dailyAttendanceLog['today_attendance'] ? $dailyAttendanceLog['today_attendance']->details : []);

        $totalScheduled = floatval(number_format($dailyAttendanceLog['total_scheduled'] / 3600, 2));
        $todayScheduled = floatval(number_format($dailyAttendanceLog['today_scheduled'] / 3600, 2));
        $totalWorkedHour = floatval(number_format($dailyAttendanceLog['total_worked'] / 3600, 2));
        $shortage = floatval(number_format($dailyAttendanceLog['shortage'] / 3600, 2));
        $overTime = floatval(number_format($dailyAttendanceLog['over_time'] / 3600, 2));

        $todayWorked = 0.00;
        $todayShortage = 0.00;
        $todayOverTime = 0.00;
        $totalBreakTime = 0.00;

        if (isset($dailyAttendanceLog['today_attendance']['breakTimes'])) {
            foreach ($dailyAttendanceLog['today_attendance']['breakTimes'] as $breakTime) {
                $totalBreakTime += totalTimeDifference($breakTime->start_at, $breakTime->end_at ?? Carbon::parse(now())->format('Y-m-d H:i:s'));
            }
        }


        foreach ($dailyLogs as $dailyLog) {
            $todayWorked += totalTimeDifference($dailyLog->in_time, $dailyLog->out_time ?? now());
        }

        //calculate today shortage time if today worked time is less than today scheduled time
        if ($todayScheduled > $todayWorked) {
            $todayShortage = floatval(number_format($todayScheduled - $todayWorked, 2));
        }

        //calculate today overtime if today worked time is greater than today scheduled time
        if ($todayScheduled < $todayWorked) {
            $todayOverTime = floatval(number_format($todayWorked - $todayScheduled, 2));
        }


        return [
            'total_scheduled' => $totalScheduled,
            'today_scheduled' => $todayScheduled,
            'total_worked' => $totalWorkedHour,
            'total_break' => floatval(number_format($totalBreakTime, 2)),
            'today_worked' => floatval(number_format($todayWorked, 2)),
            'today_shortage' => $todayShortage,
            'today_overtime' => $todayOverTime,
            'total_shortage' => $shortage,
            'total_over_time' => $overTime,
            'behavior' => ucfirst($behavior),
            'daily_logs' => $dailyLogs,
        ];

    }

    public function summary()
    {
        $employee = auth()->id();
        $user = User::query()->find($employee);
        $summary = resolve(AttendanceSummaryController::class)->index($user);

        try {

            $data['behavior'] = ucfirst($summary['average']);
            $workedData = explode(':', $summary['worked']);
            $worked = "$workedData[0]:$workedData[1]";

            $scheduledData = explode(':', $summary['scheduled']);
            $scheduled = "$scheduledData[0]:$scheduledData[1]";

            $paid_leaveData = explode(':', $summary['paid_leave']);
            $paid_leave = "$paid_leaveData[0]:$paid_leaveData[1]";

            $balanceData = explode(':', $summary['balance']);
            $balance = "$balanceData[0]:$balanceData[1]";

            $data['worked'] = $worked;
            $data['scheduled'] = $scheduled;
            $data['paid_leave'] = $paid_leave;
            $data['availablity'] = $summary['percentage'];
            $data['balance'] = $balance;

        } catch (\Exception $exception) {

            $data['behavior'] = '';
            $data['worked'] = "00.00";
            $data['scheduled'] = "00.00";
            $data['paid_leave'] = "00.00";
            $data['availablity'] = "00.00";
            $data['balance'] = "00.00";

        }
        return $data;
    }

    public function summaryDetailsLog()
    {
        $employee = auth()->user();
        $summaryLogs = resolve(AttendanceSummaryController::class)->summaries($employee);

        return new SummaryLogResource($summaryLogs);
    }


}