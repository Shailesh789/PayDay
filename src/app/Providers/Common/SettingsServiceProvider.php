<?php

namespace App\Providers\Common;

use App\Config\SetMailConfig;
use App\Config\SetStorageConfig;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        try {
            SetMailConfig::new(true)
                ->clear()
                ->set();
            SetStorageConfig::new(true)
                ->set();
        }catch (\Exception $exception) {}
    }
}
