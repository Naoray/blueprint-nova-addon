<?php

namespace Naoray\BlueprintNovaAddon\Tasks;

use Closure;
use Blueprint\Models\Model;
use Illuminate\Support\Str;

class AddRelationshipFields
{
    const INDENT = '            ';

    public function handle(array $data, Closure $next)
    {
        /** @var Model */
        $relationships = $data['model']->relationships();
        $fields = $data['fields'];
        $imports = $data['imports'];

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

                $methodName = $type === 'hasMany' ? Str::plural($name) : $name;
                $label = Str::studly($methodName);

                $fieldType = $this->fieldType($type);
                $imports[] = $fieldType;

                $fields .= self::INDENT . $fieldType . "::make('" . $label . "'";

                if ($label !== $class && $label !== Str::plural($class)) {
                    $fields .= ", '" . $methodName . "', " . $class . '::class';
                }

                $fields .= '),' . PHP_EOL;
            }

            $fields .= PHP_EOL;
        }

        $data['fields'] = $fields;
        $data['imports'] = $imports;

        return $next($data);
    }

    private function fieldType(string $dataType): string
    {
        static $fieldTypes = [
            'belongsto' => 'BelongsTo',
            'hasone' => 'HasOne',
            'hasmany' => 'HasMany',
        ];

        return $fieldTypes[strtolower($dataType)];
    }
}
