<?php

namespace Gcaimarket\Douyin;

use Illuminate\Support\ServiceProvider;

class DouyinServiceProvider extends ServiceProvider
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
        $this->loadMigrationsFrom(__DIR__ . '/../migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'gc-douyin');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'gc-douyin');
        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/gc-douyin'),
            __DIR__ . '/../resources/views' => resource_path('views/vendor/gc-douyin'),
        ]);
    }
}
