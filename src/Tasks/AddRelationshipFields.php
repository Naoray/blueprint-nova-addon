<?php

namespace Naoray\BlueprintNovaAddon\Tasks;

use Blueprint\Models\Model;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Naoray\BlueprintNovaAddon\Contracts\Task;

class AddRelationshipFields implements Task
{
    use InteractWithRelationships;

    const INDENT = '            ';

    public function handle(array $data, Closure $next): array
    {
        $model = $data['model'];
        $fields = $data['fields'];
        $imports = $data['imports'];
        $relationships = $model->relationships();

        ksort($relationships);

        foreach ($relationships as $type => $references) {
            foreach ($references as $reference) {
                if (Str::contains($reference, ':')) {
                    [$class, $name] = explode(':', $reference);
                } else {
                    $name = $reference;
                    $class = null;
                }

                $name = Str::beforeLast($name, '_id');
                $class = Str::studly($class ?? $name);

                $methodName = $this->buildMethodName($name, $type);
                $label = Str::studly($methodName);

                $fieldType = $this->fieldType($type);
                $imports[] = $fieldType;

                if ($fieldType === 'MorphTo') {
                    $label .= 'able';
                }

                $fields .= self::INDENT.$fieldType."::make('".$label."'";

                if ($fieldType !== 'MorphTo' && $this->classNameNotGuessable($label, $class)) {
                    $fields .= ", '".$methodName."', ".$class.'::class';
                }

                $fields .= ')';

                if ($this->isNullable($reference, $model)) {
                    $fields .= '->nullable()';
                }

                $fields .= ','.PHP_EOL;
            }

            $fields .= PHP_EOL;
        }

        $data['fields'] = $fields;
        $data['imports'] = $imports;

        return $next($data);
    }

    private function buildMethodName(string $name, string $type)
    {
        static $pluralRelations = [
            'belongstomany',
            'hasmany',
            'morphmany',
        ];

        return in_array(strtolower($type), $pluralRelations)
            ? Str::plural($name)
            : $name;
    }

    private function classNameNotGuessable($label, $class): bool
    {
        return $label !== $class
            && $label !== Str::plural($class);
    }

    private function isNullable($relation, Model $model): bool
    {
        $relationColumnName = $this->relationshipIdentifiers($model->columns())
            ->filter(function ($relationReference, $columnName) use ($relation, $model) {
                return in_array($relationReference, Arr::get($model->relationships(), 'belongsTo', []))
                    && $columnName === $relation;
            })
            ->first();

        return ! is_null($relationColumnName)
            && in_array('nullable', $model->columns()[$relationColumnName]->modifiers());
    }

    private function fieldType(string $dataType): string
    {
        static $fieldTypes = [
            'belongsto' => 'BelongsTo',
            'belongstomany' => 'BelongsToMany',
            'hasone' => 'HasOne',
            'hasmany' => 'HasMany',
            'morphto' => 'MorphTo',
            'morphone' => 'MorphOne',
            'morphmany' => 'MorphMany',
        ];

        return $fieldTypes[strtolower($dataType)];
    }
}
