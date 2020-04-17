<?php

namespace Naoray\BlueprintNovaAddon;

use Blueprint\Blueprint;
use Blueprint\Models\Model;
use Illuminate\Support\Str;
use Illuminate\Pipeline\Pipeline;
use Blueprint\Contracts\Generator;
use Naoray\BlueprintNovaAddon\Tasks\RemapImports;
use Naoray\BlueprintNovaAddon\Tasks\AddRegularFields;
use Naoray\BlueprintNovaAddon\Tasks\AddIdentifierField;
use Naoray\BlueprintNovaAddon\Tasks\AddTimestampFields;
use Naoray\BlueprintNovaAddon\Tasks\AddRelationshipFields;

class NovaGenerator implements Generator
{
    use HasStubPath;

    /** @var \Illuminate\Contracts\Filesystem\Filesystem */
    private $files;

    /** @var array */
    private $imports = [];

    public function __construct($files)
    {
        $this->files = $files;
    }

    public function output(array $tree): array
    {
        $output = [];

        $stub = $this->files->get($this->stubPath() . DIRECTORY_SEPARATOR . 'class.stub');

        /** @var \Blueprint\Models\Model $model */
        foreach ($tree['models'] as $model) {
            $path = $this->getPath($model);

            if (!$this->files->exists(dirname($path))) {
                $this->files->makeDirectory(dirname($path), 0755, true);
            }

            $this->files->put($path, $this->populateStub($stub, $model));

            $output['created'][] = $path;
        }

        return $output;
    }

    protected function getPath(Model $model): string
    {
        $path = str_replace('\\', '/', Blueprint::relativeNamespace($this->getNovaNamespace($model) . '/' . $model->name()));

        return config('blueprint.app_path') . '/' . $path . '.php';
    }

    protected function populateStub(string $stub, Model $model): string
    {
        $data = [
            'fields' => '',
            'imports' => [],
        ];

        $data = resolve(Pipeline::class)
            ->send($data)
            ->through([
                new AddIdentifierField($model),
                new AddRegularFields($model),
                new AddRelationshipFields($model),
                new AddTimestampFields($model),
                new RemapImports(),
            ])
            ->thenReturn();

        $stub = str_replace('DummyNamespace', $this->getNovaNamespace($model), $stub);
        $stub = str_replace('DummyClass', $model->name(), $stub);
        $stub = str_replace('DummyModel', '\\' . $model->fullyQualifiedClassName(), $stub);
        $stub = str_replace('// fields...', $data['fields'], $stub);
        $stub = str_replace('use Illuminate\Http\Request;', implode(PHP_EOL, $data['imports']), $stub);

        return $stub;
    }

    private function getNovaNamespace(Model $model): string
    {
        $namespace = Str::of($model->fullyQualifiedNamespace())
            ->after(config('blueprint.namespace'))
            ->prepend(config('blueprint.namespace') . '\Nova');

        if (config('blueprint.models_namespace')) {
            $namespace = $namespace->replace('\\' . config('blueprint.models_namespace'), '');
        }

        return $namespace->__toString();
    }
}
