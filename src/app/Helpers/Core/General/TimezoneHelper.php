<?php

namespace App\Helpers\Core\General;

use Carbon\Carbon;

/**
 * Class Timezone.
 */
class TimezoneHelper
{

    public function getCachedTimezoneFromSetting()
    {
        return cache()->remember('app-timezone-from-setting', 86400, function () {
            return settings('time_zone');
        });
    }

    /**
     * @param Carbon $date
     * @param string $format
     *
     * @return Carbon
     */
    public function convertToLocal(Carbon $date, $format = 'D M j G:i:s T Y') : string
    {
        return $date->setTimezone(auth()->user()->timezone ?? config('app.timezone'))->format($format);
    }

    /**
     * @param $date
     *
     * @return Carbon
     */
    public function convertFromLocal($date) : Carbon
    {
        return Carbon::parse($date, auth()->user()->timezone)->setTimezone('UTC');
    }
}
