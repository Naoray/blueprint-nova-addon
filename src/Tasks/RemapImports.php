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
            ->map(function ($import) {
                if (preg_match('/use ([A-Z][\w_]+\\\\)+[A-Z][\w_]+;$/', $import)) {
                    return $import;
                }

                return 'use Laravel\Nova\Fields\\'.$import.';';
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
