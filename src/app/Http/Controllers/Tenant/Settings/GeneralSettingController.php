<?php


namespace App\Http\Controllers\Tenant\Settings;


use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\TenantSettingRequest as Request;
use App\Notifications\Core\Settings\SettingsNotification;
use App\Services\Tenant\Setting\SettingService;
use Illuminate\Support\Facades\Artisan;

class GeneralSettingController extends Controller
{
    public function __construct(SettingService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return $this->service->getFormattedTenantSettings();
    }

    public function update(Request $request)
    {
        $this->service->update();

        notify()
            ->on('settings_updated')
            ->with(trans('default.general_settings'))
            ->send(SettingsNotification::class);

        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('queue:restart');

        return updated_responses('settings');
    }

    public function formattedSettings()
    {
        $settings = $this->service->getFormattedTenantSettings();

        $data = [];
        $data['company_name'] = $settings['tenant_name'];
        $data['company_icon'] = url($settings['tenant_icon'] ?? 'images/icon.png');
        $data['language'] = $settings['language'];
        $data['date_format'] = $settings['date_format'];
        $data['time_format'] = $settings['time_format'];
        $data['time_zone'] = $settings['time_zone'];
        $data['currency_symbol'] = $settings['currency_symbol'];
        $data['decimal_separator'] = $settings['decimal_separator'];
        $data['thousand_separator'] = $settings['thousand_separator'];
        $data['number_of_decimal'] = $settings['number_of_decimal'];
        $data['currency_position'] = $settings['currency_position'];
        $data['company_logo'] = url($settings['tenant_logo'] ?? 'images/logo.png');

        return success_response('Settings Fetched', $data);
    }
}
