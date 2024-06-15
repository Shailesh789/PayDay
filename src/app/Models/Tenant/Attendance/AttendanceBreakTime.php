<?php

namespace App\Models\Tenant\Attendance;

use App\Models\Tenant\TenantModel;
use App\Models\Tenant\WorkingShift\BreakTime\BreakTime;

class AttendanceBreakTime extends TenantModel
{
    protected $fillable = ['attendance_id', 'attendance_details_id', 'break_time_id', 'start_at', 'end_at'];

    public function attendanceDetails()
    {
        return $this->belongsTo(AttendanceDetails::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function breakTime()
    {
        return $this->belongsTo(BreakTime::class);
    }


}
