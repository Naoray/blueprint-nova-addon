<?php

namespace Naoray\BlueprintNovaAddon;

use Blueprint\Blueprint;
use Illuminate\Support\ServiceProvider;

class BlueprintNovaAddonServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->extend(Blueprint::class, function ($blueprint, $app) {
            $blueprint->registerGenerator(new NovaGenerator($app['files']));

            return $blueprint;
        });
    }
}
