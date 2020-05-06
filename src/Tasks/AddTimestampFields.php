<?php

namespace Naoray\BlueprintNovaAddon\Tasks;

use Closure;
use Naoray\BlueprintNovaAddon\Contracts\Task;

class AddTimestampFields implements Task
{
    const INDENT = '            ';

    public function handle($data, Closure $next): array
    {
        $model = $data['model'];
        $fields = $data['fields'];
        $imports = $data['imports'];

        if ($model->usesTimestamps()) {
            $imports[] = 'DateTime';

            $fields .= self::INDENT."DateTime::make('Created at'),".PHP_EOL.self::INDENT."DateTime::make('Updated at'),";
        }

        if ($model->usesSoftDeletes()) {
            $imports[] = 'DateTime';
            $fields .= PHP_EOL.self::INDENT."DateTime::make('Deleted at'),";
        }

        $data['fields'] = $fields;
        $data['imports'] = $imports;

        return $next($data);
    }
}
