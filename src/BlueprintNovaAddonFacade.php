<?php

namespace Naoray\BlueprintNovaAddon;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Naoray\BlueprintNovaAddon\Skeleton\SkeletonClass
 */
class BlueprintNovaAddonFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'blueprint-nova-addon';
    }
}
