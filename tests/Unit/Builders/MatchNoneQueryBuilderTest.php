<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Unit\Builders;

use ElasticScoutDriverPlus\Builders\MatchNoneQueryBuilder;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @covers \ElasticScoutDriverPlus\Builders\MatchNoneQueryBuilder
 */
final class MatchNoneQueryBuilderTest extends TestCase
{
    /**
     * @var MatchNoneQueryBuilder
     */
    private $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new MatchNoneQueryBuilder();
    }

    public function test_query_can_be_built(): void
    {
        $expected = ['match_none' => new stdClass()];
        $actual = $this->builder->buildQuery();

        $this->assertEquals($expected, $actual);
    }
}
