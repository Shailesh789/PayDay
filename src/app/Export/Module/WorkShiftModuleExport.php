<?php

namespace App\Export\Module;

use App\Helpers\Traits\DateRangeHelper;
use App\Helpers\Traits\DateTimeHelper;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class WorkShiftModuleExport implements FromArray, WithHeadings, ShouldAutoSize, WithTitle
{
    use Exportable, DateTimeHelper, DateRangeHelper;


    private Collection $work_shifts;

    public function __construct(Collection $shifts)
    {
        $this->work_shifts = $shifts;
    }

    public function headings(): array
    {
        return [
            __t('monday'),
            __t('tuesday'),
            __t('wednesday'),
            __t('thursday'),
            __t('friday'),
            __t('saturday'),
            __t('sunday'),
            __t('name'),
        ];
    }

    public function array(): array
    {
        return $this->work_shifts->map(function ($shift) {
            return $this->makeWorkShiftRow($shift);
        })->toArray();

    }

    public function makeWorkShiftRow($shift): array
    {
        return [
            $this->getShiftTime($shift->details, 'mon'),
            $this->getShiftTime($shift->details, 'tue'),
            $this->getShiftTime($shift->details, 'wed'),
            $this->getShiftTime($shift->details, 'thu'),
            $this->getShiftTime($shift->details, 'fri'),
            $this->getShiftTime($shift->details, 'sat'),
            $this->getShiftTime($shift->details, 'sun'),
            $shift->name,

        ];
    }

    public function getShiftTime($details, $day): string
    {
        $start = $details->where('weekday', $day)->first()->start_at;
        $end = $details->where('weekday', $day)->first()->end_at;

        $start = $start ? Carbon::parse($start)->setTimezone(request('timeZone', timezone()))->format('H:i') : 'x';
        $end = $end ? Carbon::parse($end)->setTimezone(request('timeZone', timezone()))->format('H:i') : 'x';

        return $start == 'x' ? $start : "$start - $end";
    }


    public function title(): string
    {
        return __t('working_shifts');
    }
}