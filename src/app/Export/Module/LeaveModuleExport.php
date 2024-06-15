<?php

namespace App\Export\Module;

use App\Helpers\Traits\DateRangeHelper;
use App\Helpers\Traits\DateTimeHelper;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class LeaveModuleExport implements FromArray, WithHeadings, ShouldAutoSize, WithTitle
{
    use Exportable, DateTimeHelper, DateRangeHelper;


    private Collection $leaves;

    public function __construct(Collection $leaves)
    {
        $this->leaves = $leaves;
    }

    public function headings(): array
    {
        return [
            'Start_date',
            'End_date',
            'Status',
            'description',
            'User',
        ];
    }

    public function array(): array
    {
        return $this->leaves->map(function ($leave) {
            return $this->makeAttendanceRow($leave);
        })->toArray();

    }

    public function makeAttendanceRow($leave): array
    {
        $start_at = $this->carbon($leave->start_at)->parse()->format('Y-m-d\TH:i:s.u\Z');
        $end_at = $this->carbon($leave->end_at)->parse()->format('Y-m-d\TH:i:s.u\Z');

        return [
            $start_at,
            $end_at,
            $leave->status->translated_name,
            $this->getNote($leave->comments, 'reason-note'),
            $leave->user->email,
        ];
    }

    private function getNote(Collection $comments, $type)
    {
        if (!$comments->count()) return null;

        return optional($comments->where('type', $type)->sortByDesc('parent_id')->first())->comment;
    }


    public function title(): string
    {
        return __t('leaves');
    }
}