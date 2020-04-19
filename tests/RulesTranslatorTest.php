<?php

namespace Naoray\BlueprintNovaAddon\Tests;

use Blueprint\Models\Column;
use Naoray\BlueprintNovaAddon\Translators\Rules;

class RulesTranslatorTest extends TestCase
{
    /** @test */
    public function forColumn_returns_required_by_default()
    {
        $column = new Column('test', 'unknown');

        $this->assertEquals(['required'], Rules::fromColumn('context', $column));
    }

    /** @test */
    public function forColumn_returns_rejects_required_rule_for_the_nullable_modifier()
    {
        $column = new Column('test', 'unknown', ['nullable']);

        $this->assertEquals([], Rules::fromColumn('context', $column));
    }

    /** @test */
    public function forColumn_returns_json_for_the_json_type()
    {
        $column = new Column('test', 'json');

        $this->assertContains('json', Rules::fromColumn('context', $column));
    }
}
