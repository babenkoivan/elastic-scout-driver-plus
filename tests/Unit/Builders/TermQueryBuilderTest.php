<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Unit\Builders;

use Elastic\ScoutDriverPlus\Builders\TermQueryBuilder;
use Elastic\ScoutDriverPlus\Exceptions\QueryBuilderValidationException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Elastic\ScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder
 * @covers \Elastic\ScoutDriverPlus\Builders\TermQueryBuilder
 *
 * @uses   \Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection
 * @uses   \Elastic\ScoutDriverPlus\QueryParameters\Transformers\GroupedArrayTransformer
 * @uses   \Elastic\ScoutDriverPlus\QueryParameters\Validators\AllOfValidator
 */
final class TermQueryBuilderTest extends TestCase
{
    private TermQueryBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new TermQueryBuilder();
    }

    public function test_exception_is_thrown_when_field_is_not_specified(): void
    {
        $this->expectException(QueryBuilderValidationException::class);

        $this->builder
            ->value('100')
            ->buildQuery();
    }

    public function test_exception_is_thrown_when_value_is_not_specified(): void
    {
        $this->expectException(QueryBuilderValidationException::class);

        $this->builder
            ->field('price')
            ->buildQuery();
    }

    public function test_query_with_field_and_value_can_be_built(): void
    {
        $expected = [
            'term' => [
                'price' => [
                    'value' => '100',
                ],
            ],
        ];

        $actual = $this->builder
            ->field('price')
            ->value('100')
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_field_and_value_and_boost_can_be_built(): void
    {
        $expected = [
            'term' => [
                'price' => [
                    'value' => 100,
                    'boost' => 1.0,
                ],
            ],
        ];

        $actual = $this->builder
            ->field('price')
            ->value(100)
            ->boost(1.0)
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_field_and_value_and_case_insensitive_can_be_built(): void
    {
        $expected = [
            'term' => [
                'price' => [
                    'value' => 100,
                    'case_insensitive' => true,
                ],
            ],
        ];

        $actual = $this->builder
            ->field('price')
            ->value(100)
            ->caseInsensitive(true)
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }
}
