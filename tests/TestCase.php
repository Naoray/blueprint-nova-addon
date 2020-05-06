<?php

namespace Naoray\BlueprintNovaAddon\Tests;

use Naoray\BlueprintNovaAddon\BlueprintNovaAddonServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getEnvironmentSetUp($app)
    {
        // blueprint config
        $app['config']->set('blueprint.namespace', 'App');
        $app['config']->set('blueprint.models_namespace', '');
        $app['config']->set('blueprint.app_path', 'app');

        // nova blueprint config
        $app['config']->set('nova_blueprint.timestamps', true);
    }

    public function fixture(string $path)
    {
        return file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR.ltrim($path, DIRECTORY_SEPARATOR));
    }

    protected function getPackageProviders($app)
    {
        return [
            BlueprintNovaAddonServiceProvider::class,
        ];
    }
}
