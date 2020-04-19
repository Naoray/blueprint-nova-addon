<?php

namespace Naoray\BlueprintNovaAddon\Tasks;

use Blueprint\Models\Column;
use Illuminate\Support\Collection;

trait InteractWithRelationships
{
    private function relationshipIdentifiers(array $columns): Collection
    {
        return collect($columns)
            ->filter(function (Column $column) {
                return $column->dataType() === 'id';
            })
            // map id columns to match related relationships
            ->map(function (Column $column) {
                return empty($column->attributes())
                    ? $column->name()
                    : implode(':', $column->attributes()).":{$column->name()}";
            });
    }
}
