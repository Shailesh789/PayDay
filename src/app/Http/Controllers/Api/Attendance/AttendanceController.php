<?php

namespace App\Http\Controllers\Api\Attendance;

use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Tenant\Attendance\AttendanceDetailsController;
use App\Http\Controllers\Tenant\Attendance\AttendanceStatusController;
use App\Http\Controllers\Tenant\Attendance\AttendanceSummaryController;
use App\Http\Controllers\Tenant\Employee\AttendancePunchInController;
use App\Http\Controllers\Tenant\Employee\ManualAttendanceController;
use App\Http\Requests\Tenant\Attendance\AttendanceRequest;
use App\Http\Resources\Payday\Attendance\AttendanceDetailsLogResource;
use App\Http\Resources\Payday\Attendance\SummaryLogResource;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Attendance\AttendanceDetails;
use App\Services\Api\Attendance\AttendanceDailyLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(AttendanceDailyLogService $attendanceDailyLogService)
    {
        $this->attendanceDailyLogService = $attendanceDailyLogService;
    }

    public function checkIsPunchIn()
    {
        try {
            $isPunchIn = resolve(AttendancePunchInController::class)->checkPunchIn();

            // Convert duration values to a more readable format and remove the pivot object from the list.
            $breakTimes = [];
            foreach ($isPunchIn['break_times'] as $breakTime) {
                $duration = $this->format_duration($breakTime['duration']);

                $breakTimes[] = [
                    'id' => $breakTime['id'],
                    'name' => $breakTime['name'],
                    'duration' => $duration,
                ];
            }

            if ($isPunchIn['punched']) {

                $breakDetails = null;
                if (isset($isPunchIn['on_break'])) {
                    $breakDetails['id'] = $isPunchIn['on_break']['id'];
                    $breakDetails['attendance_id'] = $isPunchIn['on_break']['attendance_id'];
                    $breakDetails['break_time_id'] = $isPunchIn['on_break']['break_time_id'];
                    $breakDetails['start_at'] = Carbon::parse($isPunchIn['on_break']['start_at'])->setTimezone(request('timezone'))->format('Y-m-d H:i:s');
                    $breakDetails['break_reason'] = $isPunchIn['on_break']['breakTime']['name'];

                    //break duration default time as coming from model not realtime duration
                    $breakDetails['break_duration'] = $this->format_duration($isPunchIn['on_break']['breakTime']['duration']);
                }

                return success_response(__t('you_are_already_punch_in'),
                    [
                        'punch_in' => true,
                        'attendance_details_id' => @$isPunchIn['details'],
                        'break_times' => $breakTimes,
                        'break_details' => $breakDetails
                    ]);
            } else {
                return success_response(__t('you_are_not_punch_in'),
                    [
                        'punch_in' => false,
                        'attendance_details_id' => null,
                        'break_times' => $breakTimes,
                        'break_details' => null
                    ]);
            }
        } catch (\Exception $exception) {
            return error_response('Server Error', [], 500);
        }
    }

    function format_duration($duration)
    {
        $seconds = strtotime($duration) - strtotime('TODAY');

        if ($seconds >= 3600) {
            $hours = floor($seconds / 3600);
            return $hours . 'h';
        } elseif ($seconds >= 60) {
            $minutes = floor($seconds / 60);
            return $minutes . 'm';
        } else {
            return $seconds . 's';
        }
    }


    public function punchIn(AttendanceRequest $request)
    {
        return resolve(\App\Http\Controllers\Tenant\Employee\AttendanceController::class)->punchIn($request);
    }

    public function punchOut(AttendanceRequest $request)
    {
        return resolve(\App\Http\Controllers\Tenant\Employee\AttendanceController::class)->punchOut($request);
    }

    public function show(AttendanceDetails $attendanceDetails)
    {
        $attendanceDetails = resolve(\App\Http\Controllers\Tenant\Attendance\AttendanceUpdateController::class)->index($attendanceDetails);

        try {
            $data = new AttendanceDetailsLogResource($attendanceDetails);
            return success_response('Attendance Details', $data);
        } catch (\Exception $exception) {
            return error_response($exception->getMessage(), [], 500);
        }
    }

    public function dailyLog(Request $request)
    {
        try {
            $data = $this->attendanceDailyLogService->dailyLog();
            return success_response('Attendance Daily Log', $data);
        } catch (\Exception $exception) {
            return $exception;
            return error_response($exception, [], 500);
        }
    }

    public function attendanceRequest(Request $request, AttendanceDetails $attendanceDetails)
    {
        $attendanceDetails = resolve(\App\Http\Controllers\Tenant\Attendance\AttendanceUpdateController::class)->request($request, $attendanceDetails);
        try {
            return success_response('Attendance request created');
        } catch (\Exception $exception) {
            return error_response($exception->getMessage(), [], 500);
        }
    }

    public function attendanceLog($attendanceDetails)
    {
        try {
            $attendanceDetails = AttendanceDetails::query()
                ->with([
                    'parentAttendanceDetails:id,in_time,out_time,status_id,review_by,attendance_details_id,created_at,updated_at,added_by',
                    'comments',
                    'comments.user:id,first_name,last_name',
                    'status:id,name',
                    'reviewer:id,first_name,last_name',
                    'attendance:id,user_id,behavior,in_date',
                    'attendance.user:id,first_name,last_name',
                    'assignBy:id,first_name,last_name'
                ])->find($attendanceDetails);

            if ($attendanceDetails) {
                $data = new AttendanceDetailsLogResource($attendanceDetails);
                return success_response('Attendance Log', $data);
            }
            return error_response('Attendance Log not found', [], 404);

        } catch (\Exception $exception) {
            return error_response('Server Error', [], 500);
        }
    }


    public function addRequest(Request $request): \Illuminate\Http\JsonResponse
    {
        // Convert 'in_time' and 'out_time' from local time to UTC time
        $request['in_time'] = $this->convertToUtc($request->in_time, $request->timezone);
        $request['out_time'] = $this->convertToUtc($request->out_time, $request->timezone);

        $request['employee_id'] = auth()->id();

        // Store the attendance request
        try {
            resolve(ManualAttendanceController::class)->store($request);
            return success_response('Attendance request created', []);
        } catch (\Exception $exception) {
            return error_response($exception, [], 500);
        }
    }

    private function convertToUtc($localTime, $timeZone): string
    {
        return Carbon::parse($localTime, $timeZone)->utc()->format('Y-m-d H:i:s');
    }

    public function summary()
    {
        $data = $this->attendanceDailyLogService->summary();
        try {
            return success_response('Attendance Summary', $data);
        } catch (\Exception $exception) {
            return error_response('Server Error', [], 500);
        }
    }

    public function summaryDatatable()
    {
        $data = $this->attendanceDailyLogService->summaryDetailsLog();
        try {
            return success_response('Attendance Summary Logs', $data);
        } catch (\Exception $exception) {
            return error_response($exception->getMessage(), [], 500);
        }
    }

    public function attendanceDetails()
    {
        $attendanceDetails = resolve(AttendanceDetailsController::class)->attendanceDetailsShortForm();
        try {
            $queryString['start'] = request()->query('date_range') ? json_decode(request()->query('date_range'))->start : '';
            $queryString['end'] = request()->query('date_range') ? json_decode(request()->query('date_range'))->end : '';
            return success_response('Attendance Details', ['query_string' => $queryString, 'attendance_details' => $attendanceDetails]);
        } catch (\Exception $exception) {
            return error_response($exception, [], 500);
        }
    }

    public function cancelAttendance(AttendanceDetails $details)
    {

        $request = new Request();
        $request->merge(['attendance_details_id' => $details->id]);
        $request->merge(['attendance_id' => $details->attendance_id]);
        $request->merge(['in_time' => $details->in_time]);
        $request->merge(['out_time' => $details->out_time]);
        $request->merge(['in_ip_data' => $details->in_ip_data]);
        $request->merge(['out_ip_data' => $details->out_ip_data]);
        $request->merge(['review_by' => $details->review_by]);
        $request->merge(['status_id' => $details->status_id]);
        $request->merge(['added_by' => $details->added_by]);
        $request->merge(['comments' => $details->comments->toArray()]);
        $request->merge(['status_name' => 'cancel']);
        $attendance = resolve(AttendanceStatusController::class)->update($details, $request);

        try {
            return success_response('Attendance Cancelled');
        } catch (\Exception $exception) {
            return error_response($exception, [], 500);
        }
    }


    public function startBreak(Request $request, AttendanceDetails $attendanceDetails)
    {
        $request->validate(['break_time' => 'required']);
        $isOnBreak = $attendanceDetails->breakTimes()->whereNull('end_at')->first();

        if ($isOnBreak)
            return error_response(__t('already_on_break'));


        $attendanceDetails->breakTimes()->create([
            'break_time_id' => $request->break_time,
            'attendance_id' => $attendanceDetails->attendance->id,
            'start_at' => nowFromApp()
        ]);
        return success_response(__t('break_time_started'));
    }

    public function endBreak(Request $request, AttendanceDetails $attendanceDetails)
    {
        $request->validate(['break_time' => 'required']);

        $isNotOnBreak = $attendanceDetails->breakTimes()->whereNull('end_at')->first();

        if (!$isNotOnBreak)
            return error_response(__t('not_on_break'));


        $attendanceDetails->breakTimes()->whereNull('end_at')->update([
            'end_at' => nowFromApp()
        ]);

        return success_response(__t('break_time_ended'));
    }

}
