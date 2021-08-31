<?php

namespace Naoray\BlueprintNovaAddon\Tests;

use Blueprint\Blueprint;
use Blueprint\Tree;
use Naoray\BlueprintNovaAddon\HasStubPath;
use Naoray\BlueprintNovaAddon\NovaGenerator;
use Naoray\BlueprintNovaAddon\Tasks\AddIdentifierField;
use Naoray\BlueprintNovaAddon\Tasks\AddRegularFields;
use Naoray\BlueprintNovaAddon\Tasks\AddRelationshipFields;
use Naoray\BlueprintNovaAddon\Tasks\AddTimestampFields;

class NovaGeneratorTest extends TestCase
{
    use HasStubPath;

    private $blueprint;

    private $files;

    /** @var NovaGenerator */
    private $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->files = \Mockery::mock();
        $this->subject = new NovaGenerator($this->files);
        $this->subject->registerTask(new AddIdentifierField());
        $this->subject->registerTask(new AddRegularFields());
        $this->subject->registerTask(new AddRelationshipFields());
        $this->subject->registerTask(new AddTimestampFields());

        $this->blueprint = new Blueprint();
        $this->blueprint->registerLexer(new \Blueprint\Lexers\ModelLexer());
        $this->blueprint->registerGenerator($this->subject);
    }

    /**
     * @test
     */
    public function output_generates_nothing_for_empty_tree()
    {
        $this->files->expects('get')
            ->with($this->stubPath().DIRECTORY_SEPARATOR.'class.stub')
            ->andReturn(file_get_contents('stubs/class.stub'));

        $this->files->shouldNotHaveReceived('put');

        $this->assertEquals([], $this->subject->output(new Tree(['models' => []])));
    }

    /**
     * @test
     * @dataProvider novaTreeDataProvider
     */
    public function output_generates_nova_resources($definition, $path, $novaResource)
    {
        $this->files->expects('get')
            ->with($this->stubPath().DIRECTORY_SEPARATOR.'class.stub')
            ->andReturn(file_get_contents('stubs/class.stub'));

        $this->files->expects('exists')
            ->with(dirname($path))
            ->andReturnTrue();

        $this->files->expects('put')
            ->with($path, $this->fixture($novaResource));

        $tokens = $this->blueprint->parse($this->fixture($definition));
        $tree = $this->blueprint->analyze($tokens);

        $this->assertEquals(['created' => [$path]], $this->subject->output($tree));
    }

    /**
     * @test
     */
    public function output_generates_relationships()
    {
        $this->files->expects('get')
            ->with($this->stubPath().DIRECTORY_SEPARATOR.'class.stub')
            ->andReturn(file_get_contents('stubs/class.stub'));

        $this->files->expects('exists')
            ->with('app/Nova')
            ->andReturnTrue();
        $this->files->expects('put')
            ->with('app/Nova/Subscription.php', $this->fixture('nova/model-relationships.php'));

        $tokens = $this->blueprint->parse($this->fixture('definitions/model-relationships.bp'));
        $tree = $this->blueprint->analyze($tokens);

        $this->assertEquals(['created' => ['app/Nova/Subscription.php']], $this->subject->output($tree));
    }

    /**
     * @test
     */
    public function output_respects_blueprint_configurations()
    {
        $this->app['config']->set('blueprint.app_path', 'src/path');
        $this->app['config']->set('blueprint.namespace', 'Some\\App');
        $this->app['config']->set('blueprint.models_namespace', 'Models');

        $this->files->expects('get')
            ->with($this->stubPath().DIRECTORY_SEPARATOR.'class.stub')
            ->andReturn(file_get_contents('stubs/class.stub'));

        $this->files->expects('exists')
            ->with('src/path/Nova')
            ->andReturnFalse();
        $this->files->expects('makeDirectory')
            ->with('src/path/Nova', 0755, true);
        $this->files->expects('put')
            ->with('src/path/Nova/Comment.php', $this->fixture('nova/model-configured.php'));

        $tokens = $this->blueprint->parse($this->fixture('definitions/relationships.bp'));
        $tree = $this->blueprint->analyze($tokens);

        $this->assertEquals(['created' => ['src/path/Nova/Comment.php']], $this->subject->output($tree));
    }

    /**
     * @test
     */
    public function output_respects_packages_configuration()
    {
        $this->app['config']->set('nova_blueprint.timestamps', false);

        $this->files->expects('get')
            ->with($this->stubPath().DIRECTORY_SEPARATOR.'class.stub')
            ->andReturn(file_get_contents('stubs/class.stub'));

        $this->files->expects('exists')
            ->with('app/Nova')
            ->andReturnFalse();
        $this->files->expects('makeDirectory')
            ->with('app/Nova', 0755, true);
        $this->files->expects('put')
            ->with('app/Nova/Comment.php', $this->fixture('nova/model-configured-without-timestamps.php'));

        $tokens = $this->blueprint->parse($this->fixture('definitions/relationships.bp'));
        $tree = $this->blueprint->analyze($tokens);

        $this->assertEquals(['created' => ['app/Nova/Comment.php']], $this->subject->output($tree));
    }

    /**
     * @test
     */
    public function output_respects_namespace_configurations()
    {
        $this->app['config']->set('nova_blueprint.namespace', 'Admin');
        $this->app['config']->set('nova_blueprint.resource_namespace', 'Resources');

        $this->files->expects('get')
            ->with($this->stubPath().DIRECTORY_SEPARATOR.'class.stub')
            ->andReturn(file_get_contents('stubs/class.stub'));

        $this->files->expects('exists')
            ->with('app/Admin/Resources')
            ->andReturnFalse();
        $this->files->expects('makeDirectory')
            ->with('app/Admin/Resources', 0755, true);
        $this->files->expects('put')
            ->with('app/Admin/Resources/Video.php', $this->fixture('nova/custom-namespace.php'));

        $tokens = $this->blueprint->parse($this->fixture('definitions/custom-namespace.bp'));
        $tree = $this->blueprint->analyze($tokens);

        $this->assertEquals(['created' => ['app/Admin/Resources/Video.php']], $this->subject->output($tree));
    }

    /**
     * @test
     */
    public function output_respects_namespace_configurations_with_nested_components()
    {
        $this->app['config']->set('nova_blueprint.namespace', 'Admin');
        $this->app['config']->set('nova_blueprint.resource_namespace', 'Resources');

        $this->files->expects('get')
            ->with($this->stubPath().DIRECTORY_SEPARATOR.'class.stub')
            ->andReturn(file_get_contents('stubs/class.stub'));

        $this->files->expects('exists')
            ->with('app/Admin/Resources/Admin')
            ->andReturnFalse();

        $this->files->expects('makeDirectory')
            ->with('app/Admin/Resources/Admin', 0755, true);

        $this->files->expects('put')
            ->with('app/Admin/Resources/Admin/User.php', $this->fixture('nova/nested-components-custom-namespace.php'));

        $tokens = $this->blueprint->parse($this->fixture('definitions/nested-components-custom-namespace.bp'));
        $tree = $this->blueprint->analyze($tokens);

        $this->assertEquals(['created' => ['app/Admin/Resources/Admin/User.php']], $this->subject->output($tree));
    }

    public function novaTreeDataProvider()
    {
        return [
            ['definitions/readme-example.bp', 'app/Nova/Post.php', 'nova/readme-example.php'],
            ['definitions/with-timezones.bp', 'app/Nova/Comment.php', 'nova/with-timezones.php'],
            ['definitions/relationships.bp', 'app/Nova/Comment.php', 'nova/relationships.php'],
            ['definitions/unconventional.bp', 'app/Nova/Team.php', 'nova/unconventional.php'],
            ['definitions/nullable-relationships.bp', 'app/Nova/Subscription.php', 'nova/nullable-relationships.php'],
            /*
             * @todo work on this
             */
            // ['definitions/nested-components.bp', 'app/Nova/Admin/User.php', 'nova/nested-components.php'],
        ];
    }
}
