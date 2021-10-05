<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Unit\Builders;

use ElasticScoutDriverPlus\Builders\ExistsQueryBuilder;
use ElasticScoutDriverPlus\Exceptions\QueryBuilderException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\ExistsQueryBuilder
 *
 * @uses   \ElasticScoutDriverPlus\QueryParameters\ParameterCollection
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Transformers\FlatArrayTransformer
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Validators\AllOfValidator
 */
final class ExistsQueryBuilderTest extends TestCase
{
    /**
     * @var ExistsQueryBuilder
     */
    private $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new ExistsQueryBuilder();
    }

    public function test_exception_is_thrown_when_field_is_not_specified(): void
    {
        $this->expectException(QueryBuilderException::class);

        $this->builder
            ->buildQuery();
    }

    public function test_query_with_field_can_be_built(): void
    {
        $expected = [
            'exists' => [
                'field' => 'message',
            ],
        ];

        $actual = $this->builder
            ->field('message')
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }
}
