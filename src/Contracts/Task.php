<?php

namespace Naoray\BlueprintNovaAddon\Contracts;

use Closure;

interface Task
{
    public function handle(array $data, Closure $next): array;
}
