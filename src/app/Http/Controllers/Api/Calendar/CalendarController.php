<?php

namespace App\Http\Controllers\Api\Calendar;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tenant\Holiday\HolidayController;
use App\Http\Resources\Payday\Calendar\HolidayResource;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function getHoliDays()
    {
        try {
            $holidays = resolve(HolidayController::class)->index();
            $data = new HolidayResource($holidays);
            return success_response('Holiday data!', $data);
        } catch (\Exception $ex) {
            return error_response('Server Error!', [], 500);
        }
    }
}
