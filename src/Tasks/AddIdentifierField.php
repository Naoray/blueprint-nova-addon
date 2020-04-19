<?php

namespace Naoray\BlueprintNovaAddon\Tasks;

use Closure;
use Blueprint\Models\Model;
use Illuminate\Support\Arr;
use Blueprint\Models\Column;

class AddIdentifierField
{
    use InteractWithRelationships;

    const INDENT = '            ';

    /** @var Model */
    private $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function handle($data, Closure $next): array
    {
        $column = $this->identifierColumn();

        $identifierName = $column->name() === 'id' ? '' : "'" . $column->name() . "'";
        $data['fields'] .= 'ID::make(' . $identifierName . ')->sortable(),' . PHP_EOL . PHP_EOL;
        $data['imports'][] = 'ID';

        return $next($data);
    }

    private function identifierColumn(): Column
    {
        $name = $this->relationshipIdentifiers($this->model->columns())
            ->values()
            // filter out all relationships
            ->diff(Arr::get($this->model->relationships(), 'belongsTo', []))
            ->first();

        return $this->model->columns()[$name];
    }
}
