<?php

namespace App\Http\Controllers\Tenant\Attendance;

use App\Helpers\Traits\TenantAble;
use App\Http\Controllers\Controller;
use App\Repositories\Core\BaseRepository;
use App\Repositories\Core\Setting\SettingRepository;
use App\Services\Core\Setting\SettingService;
use Illuminate\Http\Request;

class AttendanceIpSettingController extends Controller
{
    use TenantAble;

    protected BaseRepository $repository;

    public function __construct(SettingService $service, SettingRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index()
    {
        [$setting_able_id, $setting_able_type] = $this->tenantAble();

        return $this->repository->getFormattedSettings(
            'attendance_ip', $setting_able_type, $setting_able_id
        );
    }

    public function update(Request $request)
    {
        $request->validate([
            'ip_list' => ['required_if:ip_validation,allow_ip,restrict_ip'],
            'ip_list.*' => ['required_if:ip_validation,allow_ip,restrict_ip','ip'],
        ], [
            'ip_list' => 'IP value field is required',
            'ip_list.*.required' => 'IP value field is required',
            'ip_list.*.ip' => 'IP must be a valid IP address.',
        ]);

        [$setting_able_id, $setting_able_type] = $this->tenantAble();

        $attributes = array_merge($request->only('ip_validation'), [
            'ip_list' => $request->ip_list ? json_encode($request->ip_list) : null
        ]);

        $this->service->saveSettings(
            $attributes,
            'attendance_ip',
            $setting_able_type,
            $setting_able_id
        );

        return updated_responses('attendance_ip');
    }
}
