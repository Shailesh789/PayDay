<?php

use App\Helpers\Core\General\TimezoneHelper;

if (! function_exists('timezone')) {
    /**
     * Access the timezone helper.
     */
    function timezone()
    {
        return resolve(TimezoneHelper::class)->getCachedTimezoneFromSetting();
    }
}