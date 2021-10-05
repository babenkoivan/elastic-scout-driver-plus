<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Unit\Factories;

use ElasticScoutDriverPlus\Builders\MatchAllQueryBuilder;
use ElasticScoutDriverPlus\Factories\ParameterFactory;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @covers \ElasticScoutDriverPlus\Factories\ParameterFactory
 *
 * @uses   \ElasticScoutDriverPlus\Builders\MatchAllQueryBuilder
 */
final class ParameterFactoryTest extends TestCase
{
    public function test_query_can_be_made_from_builder(): void
    {
        $builder = new MatchAllQueryBuilder();

        $this->assertEquals(['match_all' => new stdClass()], ParameterFactory::makeQuery($builder));
    }

    public function test_query_can_be_made_from_callback(): void
    {
        $callback = static function () {
            return new MatchAllQueryBuilder();
        };

        $this->assertEquals(['match_all' => new stdClass()], ParameterFactory::makeQuery($callback));
    }

    public function test_query_can_be_made_from_array(): void
    {
        $query = ['match_all' => new stdClass()];

        $this->assertEquals($query, ParameterFactory::makeQuery($query));
    }
}
