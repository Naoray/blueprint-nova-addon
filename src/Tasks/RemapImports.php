<?php

namespace Naoray\BlueprintNovaAddon\Tasks;

use Closure;
use Naoray\BlueprintNovaAddon\Contracts\Task;

class RemapImports implements Task
{
    public function handle(array $data, Closure $next): array
    {
        $data['imports'] = collect($data['imports'])
            ->unique()
            ->map(function ($type) {
                return 'use Laravel\Nova\Fields\\' . $type . ';';
            })
            ->prepend('use Illuminate\Http\Request;')
            ->sort(function ($a, $b) {
                return  strlen($a) - strlen($b) ?: strcmp($a, $b);
            })
            ->values()
            ->all();

        return $next($data);
    }
}
