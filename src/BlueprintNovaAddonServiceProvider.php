<?php

namespace Naoray\BlueprintNovaAddon;

use Blueprint\Blueprint;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

class BlueprintNovaAddonServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                dirname(__DIR__) . '/config/nova_generator.php' => config_path('nova_generator.php'),
            ], 'nova_generator');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/config/nova_generator.php',
            'nova_generator'
        );

        $this->app->extend(Blueprint::class, function ($blueprint, $app) {
            $blueprint->registerGenerator(new NovaGenerator($app['files']));

            return $blueprint;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'command.blueprint.build',
            Blueprint::class,
        ];
    }
}
