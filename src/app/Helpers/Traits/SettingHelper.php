<?php


namespace App\Helpers\Traits;


trait SettingHelper
{
    use SettingKeyHelper,
        DateRangeHelper;

    public function leaveYear(): array
    {
        $month = $this->getSettingFromKey('leave')('start_month') ?: 'jan';
        $year = (nowFromApp() < $this->carbon($month)->parse()->startOfMonth()) ? nowFromApp()->subYear()->year : nowFromApp()->year;

        return $this->convertRangesToStringFormat(
            $this->yearRange($month, $year)
        );
    }
}