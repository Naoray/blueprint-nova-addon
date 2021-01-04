<?php

namespace Naoray\BlueprintNovaAddon\Tasks;

use Blueprint\Models\Column;
use Blueprint\Models\Model;
use Closure;
use Naoray\BlueprintNovaAddon\Contracts\Task;

class AddIdentifierField implements Task
{
    const INDENT = '            ';

    public function handle($data, Closure $next): array
    {
        $column = $this->identifierColumn($data['model']);

        $identifierName = $column->name() === 'id' ? '' : "'".$column->name()."'";
        $data['fields'] .= 'ID::make('.$identifierName.')->sortable(),'.PHP_EOL.PHP_EOL;
        $data['imports'][] = 'ID';

        return $next($data);
    }

    private function identifierColumn(Model $model): Column
    {
        return $model->columns()[$model->primaryKey()];
    }
}
