<?php

namespace App\Models\Tenant\WorkingShift\BreakTime;

use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakTime extends TenantModel
{
    use HasFactory;

    protected $fillable = ['name', 'duration'];
}
