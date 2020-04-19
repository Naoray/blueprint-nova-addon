<?php

namespace Naoray\BlueprintNovaAddon\Tasks;

use Blueprint\Models\Column;
use Blueprint\Models\Model;
use Closure;
use Illuminate\Support\Collection;
use Naoray\BlueprintNovaAddon\Translators\Rules;

class AddRegularFields
{
    const INDENT = '            ';
    const INDENT_PLUS = '                ';

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

        $columns = $this->regularColumns($this->model->columns());
        foreach ($columns as $column) {
            $fieldType = $this->fieldType($column->dataType());
            $imports[] = $fieldType;

            $field = $fieldType."::make('".$this->fieldLabel($column->name())."')";
            $field .= $this->addRules($column);

            if ($column->dataType() === 'json') {
                $field .= PHP_EOL.self::INDENT_PLUS.'->json()';
            }

            $fields .= self::INDENT.$field.','.PHP_EOL.PHP_EOL;
        }

        $data['fields'] = $fields;
        $data['imports'] = $imports;

        return $next($data);
    }

    private function regularColumns(array $columns): Collection
    {
        return collect($columns)
            ->filter(function (Column $column) {
                return $column->dataType() !== 'id'
                    && ! collect(['id', 'deleted_at', 'created_at', 'updated_at'])->contains($column->name());
            });
    }

    private function fieldLabel($name): string
    {
        return str_replace('_', ' ', ucfirst($name));
    }

    private function addRules(Column $column): string
    {
        if (in_array($column->dataType(), ['id'])) {
            return '';
        }

        $rules = array_map(function ($rule) {
            return " '".$rule."'";
        }, Rules::fromColumn($this->model->tableName(), $column));

        if (empty($rules)) {
            return '';
        }

        return PHP_EOL.self::INDENT_PLUS.'->rules('.trim(implode(',', $rules)).')';
    }

    private function fieldType(string $dataType)
    {
        static $fieldTypes = [
            'id' => 'ID',
            'uuid' => 'Text',
            'bigincrements' => 'Number',
            'biginteger' => 'Number',
            'boolean' => 'Boolean',
            'date' => 'Date',
            'datetime' => 'DateTime',
            'datetimetz' => 'DateTime',
            'decimal' => 'Number',
            'double' => 'Number',
            'float' => 'Number',
            'increments' => 'Number',
            'integer' => 'Number',
            'json' => 'Code',
            'longtext' => 'Textarea',
            'mediumincrements' => 'Number',
            'mediuminteger' => 'Number',
            'nullabletimestamps' => 'DateTime',
            'smallincrements' => 'Number',
            'smallinteger' => 'Number',
            'softdeletes' => 'DateTime',
            'softdeletestz' => 'DateTime',
            'time' => 'DateTime',
            'timetz' => 'DateTime',
            'timestamp' => 'DateTime',
            'timestamptz' => 'DateTime',
            'timestamps' => 'DateTime',
            'timestampstz' => 'DateTime',
            'tinyincrements' => 'Number',
            'tinyinteger' => 'Number',
            'unsignedbiginteger' => 'Number',
            'unsigneddecimal' => 'Number',
            'unsignedinteger' => 'Number',
            'unsignedmediuminteger' => 'Number',
            'unsignedsmallinteger' => 'Number',
            'unsignedtinyinteger' => 'Number',
            'year' => 'Number',
        ];

        return $fieldTypes[strtolower($dataType)] ?? 'Text';
    }
}
