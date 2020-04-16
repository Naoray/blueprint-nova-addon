<?php

namespace Naoray\BlueprintNovaAddon\Tasks;

use Closure;
use Blueprint\Models\Model;

class AddTimestampFields
{
    const INDENT = '            ';

    public function handle($data, Closure $next): array
    {
        /** @var Model */
        $model = $data['model'];
        $fields = $data['fields'];
        $imports = $data['imports'];

        if ($model->usesTimestamps()) {
            $imports[] = 'DateTime';

            $fields .= self::INDENT . "DateTime::make('Created at')," . PHP_EOL . self::INDENT . "DateTime::make('Updated at'),";
        }

        if ($model->usesSoftDeletes()) {
            $imports[] = 'DateTime';
            $fields .= PHP_EOL . self::INDENT . "DateTime::make('Deleted at'),";
        }

        $data['fields'] = $fields;
        $data['imports'] = $imports;

        return $next($data);
    }
}
