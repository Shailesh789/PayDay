<?php

namespace App\Http\Controllers\Api\Leave;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tenant\Leave\LeaveAssignController;
use App\Http\Controllers\Tenant\Leave\LeaveLogController;
use App\Http\Controllers\Tenant\Leave\LeaveRequestController;
use App\Http\Controllers\Tenant\Leave\LeaveStatusController;
use App\Http\Controllers\Tenant\Leave\LeaveSummeryController;
use App\Http\Resources\Payday\Leave\LeaveLogResource;
use App\Http\Resources\Payday\Leave\LeaveRecordResource;
use App\Http\Resources\Payday\Leave\LeaveRequestResource;
use App\Http\Resources\Payday\Pagination\PaginationResource;
use App\Models\Tenant\Leave\Leave;
use App\Repositories\Core\Status\StatusRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function record()
    {
        try {

            $response = resolve(LeaveSummeryController::class)->summaries(Auth::user());

            $data = [];
            foreach ($response as $leave) {
                $monthYear = date('F Y', strtotime($leave->date));
                $leaveDuration = '';
                switch ($leave->duration_type) {
                    case 'single_day':
                        $leaveDuration = '1 day';
                        break;
                    case 'multi_day':
                        $leaveDuration = Carbon::parse($leave->start_at)->diffInDays(Carbon::parse($leave->end_at)) + 1;
                        $leaveDuration .= $leaveDuration > 1 ? ' days' : ' day';
                        break;
                    case 'first_half':
                        $leaveDuration = 'First Half';
                        break;
                    case 'last_half':
                        $leaveDuration = 'Last Half';
                        break;
                    case 'hours':
                        $leaveDuration = Carbon::parse($leave->start_at)->diffInHours(Carbon::parse($leave->end_at));
                        $leaveDuration .= $leaveDuration > 1 ? ' hrs' : ' hr';
                        break;
                }
                $data[$monthYear][] = [
                    'id' => $leave->id,
                    'user_id' => $leave->user_id,
                    'leave_duration' => $leaveDuration,
                    'date' => date('j', strtotime($leave->date)),
                    'month' => date('M', strtotime($leave->date)),
                    'start_at' => $leave->start_at,
                    'end_at' => $leave->end_at,
                    'duration_type' => $leave->duration_type,
                    'leave_status' => $leave->status->translated_name ?? '',
                    'leave_status_class' => $leave->status->class ?? '',
                    'leave_type' => $leave->type->name ?? '',
                ];
            }

            $transformedData = [];
            foreach ($data as $monthYear => $leaves) {
                $transformedData[] = [
                    'month' => $monthYear,
                    'leaves' => $leaves,
                ];
            }

            $paginate = new PaginationResource($response);
            return success_response('Leave Records!', ['leave_records' => $transformedData , 'pagination' => $paginate->toArray(request())]);


        } catch (\Exception $ex) {
            return error_response($ex->getMessage(), [], $ex->getCode() != 0 ? $ex->getCode() : 500);
        }
    }

    public function listView(){
        try {
            request()->merge(['per_page' => 50]);
            $response = resolve(LeaveRequestController::class)->index();
            $data = new LeaveRequestResource($response);
            return success_response('Leave requests list view !', $data);
        } catch (\Exception $ex) {
            return error_response($ex->getMessage(), [], $ex->getCode() != 0 ? $ex->getCode() : 500);
        }
    }

    public function summary()
    {
        try {
            $response = resolve(LeaveSummeryController::class)->index(Auth::user());
            $summary = $response['card_summaries'];
            $summary['available'] = number_format($summary['available'], 2);
            $summary['approved'] = number_format($summary['spent'], 2);
            $summary['upcoming'] = number_format($summary['upcoming'], 2);
            $summary['pending'] = number_format($summary['pending'], 2);
            unset($summary['spent']);
            return success_response('Leave summary!', $summary);
        } catch (\Exception $ex) {
            return error_response($ex->getMessage(), [], $ex->getCode() != 0 ? $ex->getCode() : 500);
        }
    }

    public function store()
    {
        try {
            request()->merge(['employee_id' => Auth::id()]);
            $response = resolve(LeaveAssignController::class)->store();
            if (is_array($response)) {
                $response['data'] = [];
            }
            return $response;
        } catch (\Exception $ex) {
            if (property_exists($ex, 'status') && $ex->status == 422) {
                return error_response($ex->errors(), [], 422);
            }
            return error_response($ex->getMessage(), [], $ex->getCode() != 0 ? $ex->getCode() : 500);
        }
    }

    public function log($leave_id)
    {
        try {
            $leave = Leave::where('user_id', Auth::id())->findOrFail($leave_id);
            $response = resolve(LeaveLogController::class)->index($leave);
            return success_response('Leave log!', new LeaveLogResource($response));
        } catch (\Exception $ex) {
            return error_response($ex->getMessage(), [], $ex->getCode() != 0 ? $ex->getCode() : 500);
        }
    }

    public function cancel(Request $request)
    {
        try {
            request()->merge(['status_name' => 'canceled']);
            $status_id = resolve(StatusRepository::class)->leavePending();
            $leave = Leave::where(['user_id' => Auth::id(), 'status_id' => $status_id])->findOrFail($request->leave_id);
            $response = resolve(LeaveStatusController::class)->update($leave);
            if (is_array($response)) {
                $response['data'] = [];
            }
            return $response;
        } catch (\Exception $ex) {
            return error_response($ex->getMessage(), [], $ex->getCode() != 0 ? $ex->getCode() : 500);
        }
    }
}
