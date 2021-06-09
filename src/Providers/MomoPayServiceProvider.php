<?php

namespace TTSoft\MomoPay\Providers;

use Botble\Base\Supports\Helper;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\ServiceProvider;

class MomoPayServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        Helper::autoload(__DIR__ . '/../../helpers');
    }

    /**
     * @throws FileNotFoundException
     */
    public function boot()
    {
        if (is_plugin_active('payment')) {
            $this->setNamespace('plugins/momopay')
                ->loadAndPublishViews()
                ->loadAndPublishConfigurations(['general'])
                ->loadRoutes(['web'])
                ->publishAssets();
            $this->app->register(HookServiceProvider::class);
        }
    }
}
