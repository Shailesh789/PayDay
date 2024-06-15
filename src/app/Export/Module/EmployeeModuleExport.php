<?php

namespace App\Export\Module;

use App\Helpers\Traits\DateRangeHelper;
use App\Helpers\Traits\DateTimeHelper;
use App\Models\Tenant\WorkingShift\WorkingShift;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class EmployeeModuleExport implements FromArray, WithHeadings, ShouldAutoSize, WithTitle
{
    use Exportable, DateTimeHelper, DateRangeHelper;


    private Collection $users;
    private WorkingShift $defaultWorkingShift;

    public function __construct(Collection $users, $shift)
    {
        $this->users = $users;
        $this->defaultWorkingShift = $shift;
    }

    public function headings(): array
    {
        return [
            'Email',
            'First_name',
            'Last_name',
            'Department_name',
            'Is_department_manager',
            'Parent_department',
            'WorkShift',
            'Employment_status',
        ];
    }

    public function array(): array
    {
        return $this->users->map(function ($user) {
            return $this->makeUserRow($user);
        })->toArray();
    }

    public function makeUserRow($user): array
    {
        $is_manager = $user->id == $user->department->manager_id ? '1' : '0';
        $parent_department = $user->department->parentDepartment ? $user->department->parentDepartment->name : '';
        $work_shift = $user->workingShift ? $user->workingShift->name : $this->defaultWorkingShift->name;
        return [
            $user->email,
            $user->first_name,
            $user->last_name,
            $user->department->name,
            $is_manager,
            $parent_department,
            $work_shift,
            $user->employmentStatus->name
        ];
    }


    public function title(): string
    {
        return __t('employees');
    }
}
