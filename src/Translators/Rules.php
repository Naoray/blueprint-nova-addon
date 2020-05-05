<?php

namespace Naoray\BlueprintNovaAddon\Translators;

use Blueprint\Models\Column;
use Blueprint\Translators\Rules as BlueprintRules;

class Rules
{
    public static function fromColumn(string $tableName, Column $column): array
    {
        $rules = BlueprintRules::fromColumn($tableName, $column);

        if (in_array('nullable', $column->modifiers())) {
            $rules = array_filter($rules, function ($rule) {
                return $rule !== 'required';
            });
        }

        return $rules;
    }
}
