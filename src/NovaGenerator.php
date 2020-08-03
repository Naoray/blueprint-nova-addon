<?php

namespace Naoray\BlueprintNovaAddon;

use Blueprint\Blueprint;
use Blueprint\Contracts\Generator;
use Blueprint\Models\Model;
use Blueprint\Tree;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Str;
use Naoray\BlueprintNovaAddon\Contracts\Task;
use Naoray\BlueprintNovaAddon\Tasks\AddTimestampFields;
use Naoray\BlueprintNovaAddon\Tasks\RemapImports;

class NovaGenerator implements Generator
{
    use HasStubPath;

    /** @var \Illuminate\Contracts\Filesystem\Filesystem */
    private $files;

    /** @var array */
    private $imports = [];

    /** @var array */
    private $tasks = [];

    public function __construct($files)
    {
        $this->files = $files;
    }

    public function output(Tree $tree): array
    {
        $output = [];

        $stub = $this->files->get($this->stubPath().DIRECTORY_SEPARATOR.'class.stub');

        /** @var \Blueprint\Models\Model $model */
        foreach ($tree->models() as $model) {
            $path = $this->getPath($model);

            if (! $this->files->exists(dirname($path))) {
                $this->files->makeDirectory(dirname($path), 0755, true);
            }

            $this->files->put($path, $this->populateStub($stub, $model));

            $output['created'][] = $path;
        }

        return $output;
    }

    protected function getPath(Model $model): string
    {
        $path = str_replace('\\', '/', Blueprint::relativeNamespace($this->getNovaNamespace($model).'/'.$model->name()));

        return config('blueprint.app_path').'/'.$path.'.php';
    }

    protected function populateStub(string $stub, Model $model): string
    {
        $data = resolve(Pipeline::class)
            ->send([
                'fields' => '',
                'imports' => [],
                'model' => $model,
            ])
            ->through($this->filteredTasks())
            ->thenReturn();

        $stub = str_replace('DummyNamespace', $this->getNovaNamespace($model), $stub);
        $stub = str_replace('DummyClass', $model->name(), $stub);
        $stub = str_replace('DummyModel', '\\'.$model->fullyQualifiedClassName(), $stub);
        $stub = str_replace('// fields...', $data['fields'], $stub);
        $stub = str_replace('use Illuminate\Http\Request;', implode(PHP_EOL, $data['imports']), $stub);

        return $stub;
    }

    protected function getNovaNamespace(Model $model): string
    {
        $namespace = Str::after($model->fullyQualifiedNamespace(), config('blueprint.namespace'));
        $namespace = config('blueprint.namespace').'\Nova'.$namespace;

        if (config('blueprint.models_namespace')) {
            $namespace = str_replace('\\'.config('blueprint.models_namespace'), '', $namespace);
        }

        return $namespace;
    }

    public function registerTask(Task $task): void
    {
        $this->tasks[get_class($task)] = $task;
    }

    public function removeTask(string $taskName)
    {
        $taskClassNames = array_map(function ($taskObj) {
            return get_class($taskObj);
        }, $this->tasks);

        $targetIndex = array_search($taskName, $taskClassNames);
        array_splice($this->tasks, $targetIndex, 1);
    }

    protected function filteredTasks(): array
    {
        $tasks = $this->tasks;

        if (! config('nova_blueprint.timestamps')) {
            $tasks = array_filter($tasks, function ($key) {
                return $key !== AddTimestampFields::class;
            }, ARRAY_FILTER_USE_KEY);
        }

        return array_merge($tasks, [new RemapImports]);
    }

    public function types(): array
    {
        return ['nova'];
    }
}
