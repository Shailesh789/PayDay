<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Attendance\AttendanceDetails;
use App\Repositories\Core\Status\StatusRepository;
use App\Services\Tenant\Attendance\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function __construct(AttendanceService $service)
    {
        activity()->disableLogging();

        $this->service = $service;
    }

    public function punchIn(Request $request)
    {

        /** @var User $user */
        $user = auth()->user();

        $status = resolve(StatusRepository::class)->attendanceApprove();
        DB::transaction(function () use ($user, $request, $status) {
            $this->service
                ->setModel($user)
                ->setAttributes(array_merge($request->only('note', 'today', 'ip_data'), ['status_id' => $status, 'punch_in' => true]))
                ->validatePunchInDate($request->get('today'))
                ->validateIp()
                ->punchIn();
        });

        return response()->json([
            'status' => true,
            'message' => __t('punched_in_successfully'),
            'data' => []
        ]);
    }

    public function punchOut(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        $status = resolve(StatusRepository::class)->attendanceApprove();

        DB::transaction(
            function () use ($user, $request, $status) {
                $this->service
                    ->setModel($user)
                    ->setAttributes(array_merge($request->only('note', 'ip_data'), ['status_id' => $status, 'punch_in' => false]))
                    ->punchOut();
            }
        );

        return response()->json([
            'status' => true,
            'message' => __t('punched_out_successfully'),
            'data' => []
        ]);
    }

    public function startBreak(Request $request, AttendanceDetails $attendanceDetails)
    {
        $request->validate(['break_time' => 'required']);
        throw_if(
            $attendanceDetails->breakTimes()->whereNull('end_at')->first(),
            new GeneralException(__t('already_on_break'))
        );
        $attendanceDetails->breakTimes()->create([
           'break_time_id' => $request->break_time,
           'attendance_id' => $attendanceDetails->attendance->id,
           'start_at' => nowFromApp()
        ]);
        return response()->json(['status' => true, 'message' => __t('break_time_started') ], 200);
    }

    public function endBreak(Request $request, AttendanceDetails $attendanceDetails)
    {
        $request->validate(['break_time' => 'required']);
        throw_if(
            !$attendanceDetails->breakTimes()->whereNull('end_at')->first(),
            new GeneralException(__t('not_on_break'))
        );
        $attendanceDetails->breakTimes()->whereNull('end_at')->update([
           'end_at' => nowFromApp()
        ]);
        return response()->json(['status' => true, 'message' => __t('break_time_ended') ], 200);
    }

}
