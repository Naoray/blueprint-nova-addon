<?php

namespace Naoray\BlueprintNovaAddon\Tasks;

use Closure;
use Blueprint\Models\Model;

class AddTimestampFields
{
    const INDENT = '            ';

    /** @var Model */
    private $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function handle($data, Closure $next): array
    {
        $fields = $data['fields'];
        $imports = $data['imports'];

        if ($this->model->usesTimestamps()) {
            $imports[] = 'DateTime';

            $fields .= self::INDENT . "DateTime::make('Created at')," . PHP_EOL . self::INDENT . "DateTime::make('Updated at'),";
        }

        if ($this->model->usesSoftDeletes()) {
            $imports[] = 'DateTime';
            $fields .= PHP_EOL . self::INDENT . "DateTime::make('Deleted at'),";
        }

        $data['fields'] = $fields;
        $data['imports'] = $imports;

        return $next($data);
    }
}
