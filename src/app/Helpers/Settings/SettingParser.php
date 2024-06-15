<?php


namespace App\Helpers\Settings;


use App\Helpers\Core\Traits\InstanceCreator;
use App\Helpers\Core\Traits\Memoization;
use App\Services\Settings\SettingService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class SettingParser
{
    use InstanceCreator, Memoization;

    public function parse(string $key = null, $alternate = null)
    {
        if (is_null($key)) {
            return $this->getSettings();
        }

        $value = $this->memoize($key, fn () => data_get($this->getSettings(), $key));

        if (!$value && !is_null($alternate)) {
            $value = $this->memoize(
                $alternate,
                fn () => data_get($this->getSettings(), $alternate)
            );
        }

        if (($key == 'app_name' || $key == 'tenant_name') && !$value) {
            $value = config('app.name');
        }

        return $value;
    }

    public function getSettings(): array
    {
        if (tenant()) {
            return $this->memoize('global-settings-app-or-tenant', function () {
                return $this->getTenantSettings();
            });
        }
        return $this->getAppSettings();
    }

    public function getAppSettings()
    {
        return resolve(SettingService::class)
            ->getCachedFormattedSettings();
    }

    public function getTenantSettings()
    {
        return resolve(SettingService::class)
            ->getTenantFormattedSettings();
    }
}
