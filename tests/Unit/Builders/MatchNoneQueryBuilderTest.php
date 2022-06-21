<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Unit\Builders;

use Elastic\ScoutDriverPlus\Builders\MatchNoneQueryBuilder;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @covers \Elastic\ScoutDriverPlus\Builders\MatchNoneQueryBuilder
 */
final class MatchNoneQueryBuilderTest extends TestCase
{
    private MatchNoneQueryBuilder $builder;

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
