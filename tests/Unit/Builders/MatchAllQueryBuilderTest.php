<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Unit\Builders;

use ElasticScoutDriverPlus\Builders\MatchAllQueryBuilder;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @covers \ElasticScoutDriverPlus\Builders\MatchAllQueryBuilder
 */
final class MatchAllQueryBuilderTest extends TestCase
{
    /**
     * @var MatchAllQueryBuilder
     */
    private $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new MatchAllQueryBuilder();
    }

    public function test_query_without_boost_can_be_built(): void
    {
        $expected = ['match_all' => new stdClass()];
        $actual = $this->builder->buildQuery();

        $this->assertEquals($expected, $actual);
    }

    public function test_query_with_boost_can_be_built(): void
    {
        $expected = [
            'match_all' => [
                'boost' => 1.2,
            ],
        ];

        $actual = $this->builder
            ->boost(1.2)
            ->buildQuery();

        $this->assertEquals($expected, $actual);
    }
}
