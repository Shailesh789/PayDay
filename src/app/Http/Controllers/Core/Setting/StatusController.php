<?php

namespace App\Http\Controllers\Core\Setting;

use App\Http\Controllers\Controller;
use App\Models\Core\Status;
use App\Repositories\Core\Status\StatusRepository;
use Illuminate\Http\Request;

class StatusController extends Controller
{

    public function index()
    {
        return resolve(StatusRepository::class)
            ->statuses(request()->get('type', ''));
    }

    public function getStatusTypeWise()
    {
        $statuses = Status::query()->select('id','name','class','type')->where('type', request()->get('type'))->get();

        return success_response('Statuses', $statuses);
    }
}
