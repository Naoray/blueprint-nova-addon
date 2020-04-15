<?php

namespace Naoray\BlueprintNovaAddon\Tests;

use Orchestra\Testbench\TestCase;
use Naoray\BlueprintNovaAddon\BlueprintNovaAddonServiceProvider;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [BlueprintNovaAddonServiceProvider::class];
    }
    
    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
