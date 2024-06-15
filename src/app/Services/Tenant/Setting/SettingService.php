<?php


namespace App\Services\Tenant\Setting;

use App\Helpers\Traits\TenantAble;
use App\Models\Core\Setting\Setting;
use App\Services\Core\BaseService;
use App\Helpers\Core\Traits\FileHandler;
use App\Repositories\Core\Setting\SettingRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class SettingService extends BaseService
{
    use FileHandler, TenantAble;

    public function __construct(Setting $model)
    {
        $this->model = $model;
    }

    public function update()
    {
        $settings = request()->except('allowed_resource', 'tenant_id', 'tenant_short_name');

        collect(array_keys($settings))->each(function ($key) use ($settings) {

            [$setting_able_id, $setting_able_type] = $this->tenantAble();

            $setting = resolve(SettingRepository::class)
                ->createSettingInstance(
                    $key,
                    'tenant',
                    $setting_able_type,
                    $setting_able_id
                );

            if (request()->file($key)) {
                $this->deleteImage(optional($setting)->value);
                $settings[$key] = $this->uploadImage(request()->file($key), config('file.' . $key . '.folder'), config('file.' . $key . '.height'));
            }

            $this->setModel($setting);

            return parent::save([
                'name' => $key,
                'value' => $settings[$key],
                'context' => 'tenant',
                'settingable_id' => $setting_able_id,
                'settingable_type' => $setting_able_type,
            ]);
        });
    }


    public function getFormattedTenantSettings($context = 'tenant')
    {
        [$setting_able_id, $setting_able_type] = $this->tenantAble();

        $settings = resolve(SettingRepository::class)
            ->getFormattedSettings($context, $setting_able_type, $setting_able_id);

        $storage_disk = Storage::disk(config('filesystems.default'));
        $data = [];
        foreach ($settings as $key => $setting) {
            $data[$key] = $setting;
            if (in_array($key, ['app_logo', 'app_icon', 'app_banner', 'tenant_logo', 'tenant_icon', 'tenant_banner'])) {
                if (!$setting){
                    $setting = $this->defaultSetting($key);
                }
                $data[$key] = $storage_disk->exists($setting) ? $storage_disk->url($setting) : $setting;
            }
        }
        return array_merge($settings, $data);
    }

    public function defaultSetting($key)
    {
        $defaultSetting = Arr::first(config('settings.app'), function ($value) use ($key) {
            return $value['name'] === $key;
        });

        return $defaultSetting['value'] ?? null;
    }
}
