<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Unit\Builders;

use ElasticScoutDriverPlus\Builders\IdsQueryBuilder;
use ElasticScoutDriverPlus\Exceptions\QueryBuilderException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\IdsQueryBuilder
 *
 * @uses   \ElasticScoutDriverPlus\QueryParameters\ParameterCollection
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Transformers\FlatArrayTransformer
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Validators\AllOfValidator
 */
final class IdsQueryBuilderTest extends TestCase
{
    /**
     * @var IdsQueryBuilder
     */
    private $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new IdsQueryBuilder();
    }

    public function test_exception_is_thrown_when_values_are_not_specified(): void
    {
        $this->expectException(QueryBuilderException::class);

        $this->builder
            ->buildQuery();
    }

    public function test_query_with_values_can_be_built(): void
    {
        $expected = [
            'ids' => [
                'values' => ['1', '2', '3'],
            ],
        ];

        $actual = $this->builder
            ->values(['1', '2', '3'])
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }
}
