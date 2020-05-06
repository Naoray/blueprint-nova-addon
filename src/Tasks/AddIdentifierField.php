<?php

namespace Naoray\BlueprintNovaAddon\Tasks;

use Closure;
use Blueprint\Models\Model;
use Illuminate\Support\Arr;
use Blueprint\Models\Column;
use Naoray\BlueprintNovaAddon\Contracts\Task;

class AddIdentifierField implements Task
{
    use InteractWithRelationships;

    const INDENT = '            ';

    public function handle($data, Closure $next): array
    {
        $column = $this->identifierColumn($data['model']);

        $identifierName = $column->name() === 'id' ? '' : "'" . $column->name() . "'";
        $data['fields'] .= 'ID::make(' . $identifierName . ')->sortable(),' . PHP_EOL . PHP_EOL;
        $data['imports'][] = 'ID';

        return $next($data);
    }

    private function identifierColumn(Model $model): Column
    {
        $name = $this->relationshipIdentifiers($model->columns())
            ->values()
            // filter out all relationships
            ->diff(Arr::get($model->relationships(), 'belongsTo', []))
            ->first();

        return $model->columns()[$name];
    }
}
