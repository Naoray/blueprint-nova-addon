<?php

namespace Naoray\BlueprintNovaAddon\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Naoray\BlueprintNovaAddon\BlueprintNovaAddonServiceProvider;

class TestCase extends Orchestra
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('blueprint.namespace', 'App');
        $app['config']->set('blueprint.controllers_namespace', 'Http\\Controllers');
        $app['config']->set('blueprint.models_namespace', '');
        $app['config']->set('blueprint.app_path', 'app');
    }

    public function fixture(string $path)
    {
        return file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR));
    }
    
    protected function getPackageProviders($app)
    {
        return [
            BlueprintNovaAddonServiceProvider::class,
        ];
    }
}
