<?php

namespace App\Jobs\Tenant;

use App\Helpers\Traits\SettingHelper;
use App\Models\Tenant\Holiday\Holiday;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RenewHolidayJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, SettingHelper;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $holidays = Holiday::query()->where('repeats_annually', 1)
            ->whereBetween('start_date', $this->leaveYear())
//            ->whereYear('start_date', nowFromApp()->year)
            ->get();

        $holidays->map(function ($holiday) {
            if (Holiday::query()
                ->whereDate('start_date', $this->carbon($holiday->start_date)->parse()->addYear())
                ->doesntExist()
            ) {
                $newHoliday = $holiday->replicate()->fill([
                    'start_date' => $this->carbon($holiday->start_date)->parse()->addYear(),
                    'end_date' => $this->carbon($holiday->end_date)->parse()->addYear(),
                ]);
                $newHoliday->save();
                $newHoliday->departments()->sync($holiday->departments);
            }
        });
    }
}
