<?php

namespace App\Http\Controllers;

use App\Helpers\Traits\SetIniTrait;
use App\Jobs\Tenant\AssignLeaveByStatusJob;
use App\Jobs\Tenant\AssignLeaveJob;
use App\Jobs\Tenant\AssignUpcomingWorkingShiftJob;
use App\Jobs\Tenant\RenewHolidayJob;
use App\Jobs\Tenant\UpdateWorkingShiftJob;
use Gainhq\Installer\App\Managers\StorageManager;
use Illuminate\Support\Facades\Artisan;

class InstallDemoDataController extends Controller
{
    use SetIniTrait;

    public function run()
    {
        if (env('INSTALL_DEMO_DATA')) {

            config()->set('database.connections.mysql.strict', false);
            define('STDIN',fopen("php://stdin","r"));

            $this->setMemoryLimit('500M');
            $this->setExecutionTime(50000);

            Artisan::call('optimize:clear');

            Artisan::call('clear-compiled');
            Artisan::call('view:clear');

            Artisan::call('config:clear');
            Artisan::call('cache:clear');

            Artisan::call('migrate:fresh --force');
            Artisan::call('db:demo');

            Artisan::call('storage:link');
            Artisan::call('queue:restart');

            resolve(StorageManager::class)->link();
            config()->set('database.connections.mysql.strict', true);
        }

        return true;
    }

    public function runJobs()
    {
        if (request('leave')){
            AssignLeaveByStatusJob::dispatchSync();
            AssignLeaveJob::dispatchSync();
        }
        if (request('workshift')){
            UpdateWorkingShiftJob::dispatchSync();
            AssignUpcomingWorkingShiftJob::dispatchSync();
        }
        if (request('holiday')){
            RenewHolidayJob::dispatchSync();
        }

        return 'done';
    }
}
