<?php

namespace App\Http\Resources\Payday\Employee;

use Illuminate\Http\Resources\Json\JsonResource;

class JobHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'joining_date' => $this->profile->joining_date ? date('d M Y', strtotime($this->profile->joining_date)) : '',
            'designations' => $this->designations->map(function ($designation) {
                return [
                    'id' => $designation->id,
                    'name' => $designation->name,
                    'designation_id' => $designation->pivot->designation_id,
                    'start_date' => date('d M Y', strtotime($designation->pivot->start_date)),
                    'end_date' => $designation->pivot->end_date ? date('d M Y', strtotime($designation->pivot->end_date)) : 'Present',
                ];
            }),
            'departments' => $this->departments->map(function ($department) {
                return [
                    'id' => $department->id,
                    'name' => $department->name,
                    'department_id' => $department->pivot->department_id,
                    'start_date' => date('d M Y', strtotime($department->pivot->start_date)),
                    'end_date' => $department->pivot->end_date ? date('d M Y', strtotime($department->pivot->end_date)) : 'Present',
                ];
            }),
            'roles' => $this->roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                ];
            }),
            'working_shifts' => $this->workingShifts->map(function ($workingShift) {
                return [
                    'id' => $workingShift->id,
                    'name' => $workingShift->name,
                    'working_shift_id' => $workingShift->pivot->working_shift_id,
                    'start_date' => date('d M Y', strtotime($workingShift->pivot->start_date)),
                    'end_date' => $workingShift->pivot->end_date ? date('d M Y', strtotime($workingShift->pivot->end_date)) : 'Present',
                ];
            }),
            'employment_statuses' => $this->employmentStatuses->map(function ($status) {
                return [
                    'id' => $status->id,
                    'name' => $status->name,
                    'employment_status_id' => $status->pivot->employment_status_id,
                    'start_date' => date('d M Y', strtotime($status->pivot->start_date)),
                    'end_date' => $status->pivot->end_date ? date('d M Y', strtotime($status->pivot->end_date)) : 'Present',
                ];
            }),
        ];
    }
}
