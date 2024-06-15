<?php

namespace App\Http\Resources\Payday\User;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
  
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'employye_id' => @$this->profile->employee_id,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'gender' => $this->profile->gender ?? '',
            'date_of_birth' => $this->profile->date_of_birth ? date('d M Y', strtotime($this->profile->date_of_birth)) : '',
            'address' => $this->profile->address ?? '',
            'contact' => $this->profile->contact ?? '',
            'about_me' => $this->profile->about_me ?? '',
            'profile_picture_url' => $this->profilePicture->full_url ?? '',
            'user_status' => $this->status->translated_name ?? '',
            'designation_name' => $this->designation->name ?? '',
            'department_name' => $this->department->name ?? '',
            'employment_status' => $this->employmentStatus->name ?? '',
            'working_shift_name' => $this->workingShift->name ?? '',
            'working_shift_type' => $this->workingShift->type ?? '',
        ];
    }
}
