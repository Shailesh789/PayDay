<?php

use Carbon\Carbon;


if (!function_exists('workingHourFromSecond')) {
    function workingHourFromSecond($attendanceDetails, $isTotalHour = false)
    {
        $startTime = $attendanceDetails->in_time;
        $endTime = $attendanceDetails->out_time;
        $hours = 0.00;

        if ($isTotalHour) {
            if ($attendanceDetails->out_time && $attendanceDetails->status->name === 'status_approve') {
                $startTime = Carbon::parse($startTime);
                $endTime = Carbon::parse($endTime);
                $diff = $endTime->diffInSeconds($startTime);
                $hours = ($diff / 3600);
            }
        }

        if ($isTotalHour == false) {
            if ($attendanceDetails->out_time) {
                $startTime = Carbon::parse($startTime);
                $endTime = Carbon::parse($endTime);
                $diff = $endTime->diffInSeconds($startTime);
                $hours = ($diff / 3600);
            }
        }

        return floatval(number_format($hours, 2));
    }
}

if (!function_exists('workingHorFromList')) {
    function workingHorFromList(object $attendanceDetails, $isTotalHour = false)
    {
        $hours = 0.00;
        foreach ($attendanceDetails as $detail) {
            $hours += workingHourFromSecond($detail, $isTotalHour);
        }
        return floatval(number_format($hours, 2));
    }
}

if (!function_exists('dateTimeInAmPm')) {
    function dateTimeInAmPm($time, $timeZone = 'UTC'): string
    {
        if ($time != null) {
            return Carbon::parse($time)->setTimezone($timeZone)->format('g:i A');
        } else {
            return '';
        }
    }
}

if (!function_exists('dateTimeInOwnRegion')) {
    function dateTimeInOwnRegion($time, $timeZone = 'UTC'): string
    {
        if ($time != null) {
            return Carbon::parse($time)->setTimezone($timeZone)->format('Y-m-d H:i:s');
        } else {
            return '';
        }
    }
}

if (!function_exists('timeDifference')) {
    function timeDifference($start, $end = null): string
    {
        $startTime = Carbon::parse($start);
        $endTime = Carbon::parse($end);
        $totalDuration = $startTime->diff($endTime)->format("%H Hr's:%I min");

        return $totalDuration;
    }
}

if (!function_exists('totalTimeDifference')) {
    function totalTimeDifference($start, $end)
    {
        $startTime = Carbon::parse($start);
        $endTime = Carbon::parse($end);
        $diff = $endTime->diffInSeconds($startTime);
        $hours = ($diff / 3600);
        return $hours;
    }
}

if (!function_exists('convertDecimalToHourMinute')) {
    function convertDecimalToHourMinute($decimal): string
    {
        $sign = ($decimal < 0) ? "-" : "";
        $decimal = abs($decimal);

        $hours = floor($decimal);
        $minutes = ($decimal - $hours) * 60;

        $formattedHours = sprintf("%02d", $hours);
        $formattedMinutes = sprintf("%02d", $minutes);

        return $sign . $formattedHours . ':' . $formattedMinutes;
    }
}

