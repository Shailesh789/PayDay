<?php

namespace App\Http\Controllers\Tenant\Export;

use App\Exceptions\GeneralException;
use App\Export\Module\AttendanceModuleExport;
use App\Export\Module\EmployeeModuleExport;
use App\Export\Module\ExportModules;
use App\Export\Module\LeaveModuleExport;
use App\Export\Module\WorkShiftModuleExport;
use App\Helpers\Traits\DateRangeHelper;
use App\Helpers\Traits\SettingKeyHelper;
use App\Http\Controllers\Controller;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Leave\Leave;
use App\Models\Tenant\WorkingShift\WorkingShift;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModuleExportController extends Controller
{
    use DateRangeHelper, SettingKeyHelper;


    public function __construct()
    {

    }

    public function download()
    {
        if (\request()->isNotFilled('fileName')) {
            throw new GeneralException(__t('action_not_allowed'));
        }
        try {
            return Storage::disk(config('filesystems.default'))->download('export/' . \request('fileName'));
        } catch (\Exception $exception) {
            throw new GeneralException(__t('resource_not_found'), 404);
        }
    }

    public function export(Request $request)
    {
        $request->validate([
            'fields' => ['required', 'array']
        ]);

        if (count($request->fields) == 1 && !in_array('all_data', $request->fields)) {
            $exportable = match ($request->fields[0]) {
                'employee' => $this->exportEmployee(),
                'leave' => $this->exportLeave(),
                'attendance' => $this->exportAttendance(),
                'work_shift' => $this->exportWorkShift(),
            };
            $file_name = $request->fields[0] . '_' . now()->toDateString() . '.xlsx';
            $exportable->store("export/$file_name", config('filesystems.default'));
            return response()->json([
                'message' => __t('export_file_saved_successfully'),
                'file_name' => $file_name
            ]);
        }

        $sheets = [];
        if (in_array('all_data', $request->fields)) {
            $sheets = [
                $this->exportEmployee(),
                $this->exportWorkShift(),
                $this->exportAttendance(),
                $this->exportLeave(),
            ];
            $file_name = 'all_data_' . nowFromApp()->toDateString() . '.xlsx';
        } else {
            if (in_array('employee', $request->fields)) {
                $sheets[] = $this->exportEmployee();
            }
            if (in_array('leave', $request->fields)) {
                $sheets[] = $this->exportLeave();
            }
            if (in_array('attendance', $request->fields)) {
                $sheets[] = $this->exportAttendance();
            }
            if (in_array('work_shift', $request->fields)) {
                $sheets[] = $this->exportWorkShift();
            }
            $file_name = implode('_', $request->fields) . '_' . nowFromApp()->toDateString() . '.xlsx';
        }

        (new ExportModules($sheets))->store("export/$file_name", config('filesystems.default'));
        return response()->json([
            'message' => __t('export_file_saved_successfully'),
            'file_name' => $file_name
        ]);
    }

    public function exportEmployee()
    {

        $users = User::query()
            ->select('id', 'email', 'first_name', 'last_name')
            ->with([
                'department:id,name,manager_id,department_id',
                'department.parentDepartment',
                'workingShift',
                'employmentStatus:id,name',

            ])->get();

        return (new EmployeeModuleExport($users, WorkingShift::getDefault()));

    }

    public function exportWorkShift()
    {

        $shifts = WorkingShift::query()
            ->with([
                'details',
            ])->get();

        return (new WorkShiftModuleExport($shifts));

    }

    public function exportAttendance()
    {

        $attendances = Attendance::query()
            ->select(['id', 'in_date', 'user_id', 'behavior'])
            ->with([
                'user:id,first_name,last_name,email',
                'details' => function (HasMany $details) {
                    $details->orderBy('in_time', 'DESC')
                        ->select([
                            'id',
                            'in_time',
                            'out_time',
                            'attendance_id',
                            'status_id',
                            'review_by',
                            'added_by',
                            'attendance_details_id',
                            'in_ip_data',
                            'out_ip_data'
                        ]);
                },
                'details.comments' => fn(MorphMany $morphMany) => $morphMany->orderBy('parent_id', 'DESC')
                    ->select('id', 'commentable_type', 'commentable_id', 'user_id', 'type', 'comment', 'parent_id'),
                'details.status',
                'details.breakTimes'
            ])->get();

        return (new AttendanceModuleExport($attendances));

    }

    public function exportLeave()
    {

        $leaves = Leave::query()
            ->with([
                'status:id,name,class',
                'type:id,name',
                'user:id,email',
                'lastReview.department:id,manager_id',
                'comments' => fn(MorphMany $many) => $many->orderBy('parent_id', 'DESC')
                    ->select('id', 'commentable_type', 'commentable_id', 'user_id', 'type', 'comment', 'parent_id'),
            ])
            ->latest('date')
            ->get();

        return (new LeaveModuleExport($leaves));

    }

}
