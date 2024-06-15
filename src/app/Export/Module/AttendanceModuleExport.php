<?php

namespace App\Export\Module;

use App\Helpers\Traits\DateRangeHelper;
use App\Helpers\Traits\DateTimeHelper;
use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Attendance\AttendanceDetails;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class AttendanceModuleExport implements FromArray, WithHeadings, ShouldAutoSize, WithTitle
{
    use Exportable, DateTimeHelper, DateRangeHelper;


    private Collection $attendances;
    private bool $daily_log;
    private array $summery;

    public function __construct(Collection $attendances)
    {
        $this->attendances = $attendances;
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
        return $this->attendances->map(function (Attendance $attendance) {
            return $this->makeAttendanceRow($attendance);
        })->flatten(1)->toArray();
    }

    public function makeAttendanceRow($attendance): array
    {
        return $attendance->details->map(function (AttendanceDetails $attendanceDetails) use ($attendance) {
            $in_time = $this->carbon($attendanceDetails->in_time)->parse()->format('Y-m-d\TH:i:s.u\Z');
            $out_time = $attendanceDetails->out_time ?
                $this->carbon($attendanceDetails->out_time)->parse()->format('Y-m-d\TH:i:s.u\Z')
                : '';

            return [
                $in_time,
                $out_time,
                $attendanceDetails->status->translated_name,
                $attendanceDetails->comments->count() ? $this->getComment($attendanceDetails) : '',
                $attendance->user->email,
            ];
        })->toArray();

    }

    private function getComment(AttendanceDetails $attendanceDetails)
    {
        if ($attendanceDetails->review_by || $attendanceDetails->added_by) {
            $note = $this->getNote($attendanceDetails->comments, 'manual');
            return $note ? "Reason Note:'$note'" : '';
        }
        $in_note = $this->getNote($attendanceDetails->comments, 'in-note');
        $out_note = $this->getNote($attendanceDetails->comments, 'out-note');

        if ($in_note && $out_note) {
            return "PUNCH-IN:'$in_note' || PUNCH-OUT:'$out_note'";
        }

        if ($in_note) {
            return "PUNCH-IN:'$in_note'";
        }
        if ($out_note) {
            return "PUNCH-OUT:'$out_note'";
        }

        $note = $this->getNote($attendanceDetails->comments, 'request');
        return $note ? "Reason Note:'$note'" : '';
    }

    private function getNote(Collection $comments, $type)
    {
        if (!$comments->count()) return null;

        return optional($comments->where('type', $type)->sortByDesc('parent_id')->first())->comment;
    }


    public function title(): string
    {
        return __t('attendances');
    }
}