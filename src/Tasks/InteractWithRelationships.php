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
                return $column->dataType() === 'id' || in_array('primary', $column->modifiers());
            })
            // map id columns to match related relationships
            ->map(function (Column $column) {
                return empty($column->attributes()) || in_array('primary', $column->modifiers())
                    ? $column->name()
                    : implode(':', $column->attributes()).":{$column->name()}";
            });
    }
}
