<?php

namespace Naoray\BlueprintNovaAddon;

use Blueprint\Blueprint;
use Illuminate\Support\ServiceProvider;

class BlueprintNovaAddonServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/nova_generator.php' => config_path('nova_generator.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/nova_generator.php', 'config');

        $this->app->extend(Blueprint::class, function ($blueprint, $app) {
            $blueprint->registerGenerator(new NovaGenerator($app['files']));

            return $blueprint;
        });
    }
}
