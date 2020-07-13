<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Unit\Builders;

use ElasticScoutDriverPlus\Builders\RawQueryBuilder;
use ElasticScoutDriverPlus\Exceptions\QueryBuilderException;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @covers \ElasticScoutDriverPlus\Builders\RawQueryBuilder
 */
final class RawQueryBuilderTest extends TestCase
{
    /**
     * @var RawQueryBuilder
     */
    private $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new RawQueryBuilder();
    }

    public function test_exception_is_thrown_when_building_query_with_empty_raw_query(): void
    {
        $this->expectException(QueryBuilderException::class);

        $this->builder->buildQuery();
    }

    public function test_query_can_be_built_when_raw_query_is_specified(): void
    {
        $matchAllQuery = ['match_all' => new stdClass()];

        $this->builder->query($matchAllQuery);

        $this->assertEquals($matchAllQuery, $this->builder->buildQuery());
    }
}
