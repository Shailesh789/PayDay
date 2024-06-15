<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\WorkingShift\BreakTime\BreakTime;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

class BreakTimeSeeder extends Seeder
{
    use DisableForeignKeys;

    public function run()
    {
        $this->disableForeignKeys();
        BreakTime::query()->truncate();

        $breakTimes = [
            [
                'name' => 'Lunch Break',
                'duration' => '01:00:00',
            ],
            [
                'name' => 'Tea Break',
                'duration' => '00:30:00',
            ],
            [
                'name' => 'Leisure Time',
                'duration' => '00:30:00',
            ],
        ];
        BreakTime::insert($breakTimes);
        $this->enableForeignKeys();
    }
}
