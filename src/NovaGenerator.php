<?php

namespace Naoray\BlueprintNovaAddon;

use Blueprint\Blueprint;
use Blueprint\Contracts\Generator;
use Blueprint\Models\Model;
use Blueprint\Tree;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Str;
use Naoray\BlueprintNovaAddon\Contracts\Task;
use Naoray\BlueprintNovaAddon\Tasks\AddResourceImportIfRequired;
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
        $path = str_replace('\\', '/', Blueprint::relativeNamespace($this->getNovaResourceClassName($model)));

        return config('blueprint.app_path').'/'.$path.'.php';
    }

    protected function populateStub(string $stub, Model $model): string
    {
        $resourceClassName = $this->getNovaResourceClassName($model);
        $resourceNamespace = Str::beforeLast($resourceClassName, '\\');

        $data = resolve(Pipeline::class)
            ->send([
                'fields' => '',
                'imports' => [],
                'model' => $model,
                'resource' => $resourceClassName,
            ])
            ->through($this->filteredTasks())
            ->thenReturn();

        $stub = str_replace('DummyNamespace', $resourceNamespace, $stub);
        $stub = str_replace('DummyClass', $model->name(), $stub);
        $stub = str_replace('DummyModel', '\\'.$model->fullyQualifiedClassName(), $stub);
        $stub = str_replace('// fields...', $data['fields'], $stub);
        $stub = str_replace('use Illuminate\Http\Request;', implode(PHP_EOL, $data['imports']), $stub);

        return $stub;
    }

    /**
     * Resolves a classname to a part of Nova, where componentType is the type as used in the
     * config, and the localClassName is the relative path to a class from it's defined namespace.
     *
     * @example For a model with FQCN `App\User`, you'd call getNovaNamespaceFor('resource', 'User') to resolve the path to this
     * Model's Resource.
     * @example For a model with FQCN `App\Models\Assets\Video`, with Blueprint configured for Laravel 8, you'd call `getNovaNamespaceFor('resource', 'Assets\Video')
     *
     * @param string $type Nova component type, lowercase (e.g. resource, lens, action)
     * @param string $localClassName see examples.
     * @return string FQCN to the $componentType's class namespace.
     */
    protected function getFullyQualifiedClassNameForComponent(string $type, string $localClassName): string
    {
        return implode('\\', array_filter([
            config('blueprint.namespace'),
            config('nova_blueprint.namespace', 'Nova'),
            config("nova_blueprint.{$type}_namespace", null),
            ltrim($localClassName, '\\'),
        ]));
    }

    /**
     * Returns the fully qualified class name of the Nova resource for this model.
     *
     * @param Model $model
     * @return string
     */
    protected function getNovaResourceClassName(Model $model): string
    {
        $afterSelector = config("blueprint.models_namespace") ?: config('blueprint.namespace');
        $modelBaseClass = Str::after($model->fullyQualifiedClassName(), $afterSelector);

        return $this->getFullyQualifiedClassNameForComponent('resource', $modelBaseClass);
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

        $additionalTasks = [
            new AddResourceImportIfRequired($this->getFullyQualifiedClassNameForComponent('resource', 'Resource')),
            new RemapImports
        ];


        return array_merge($tasks, $additionalTasks);
    }

    public function types(): array
    {
        return ['nova'];
    }
}
