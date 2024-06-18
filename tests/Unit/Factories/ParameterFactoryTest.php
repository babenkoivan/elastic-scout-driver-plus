<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Unit\Factories;

use Elastic\ScoutDriverPlus\Builders\MatchAllQueryBuilder;
use Elastic\ScoutDriverPlus\Factories\ParameterFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(ParameterFactory::class)]
#[UsesClass(MatchAllQueryBuilder::class)]
final class ParameterFactoryTest extends TestCase
{
    public function test_query_can_be_made_from_builder(): void
    {
        $builder = new MatchAllQueryBuilder();
        $this->assertEquals(['match_all' => new stdClass()], ParameterFactory::makeQuery($builder));
    }

    public function test_query_can_be_made_from_callback(): void
    {
        $callback = static fn () => new MatchAllQueryBuilder();
        $this->assertEquals(['match_all' => new stdClass()], ParameterFactory::makeQuery($callback));
    }

    public function test_query_can_be_made_from_array(): void
    {
        $query = ['match_all' => new stdClass()];
        $this->assertEquals($query, ParameterFactory::makeQuery($query));
    }
}
