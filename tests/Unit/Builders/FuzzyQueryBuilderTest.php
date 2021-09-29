<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Unit\Builders;

use ElasticScoutDriverPlus\Builders\FuzzyQueryBuilder;
use ElasticScoutDriverPlus\Exceptions\QueryBuilderException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\FuzzyQueryBuilder
 *
 * @uses   \ElasticScoutDriverPlus\QueryParameters\ParameterCollection
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Transformers\GroupedArrayTransformer
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Validators\AllOfValidator
 */
final class FuzzyQueryBuilderTest extends TestCase
{
    /**
     * @var FuzzyQueryBuilder
     */
    private $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new FuzzyQueryBuilder();
    }

    public function test_exception_is_thrown_when_field_is_not_specified(): void
    {
        $this->expectException(QueryBuilderException::class);

        $this->builder
            ->value('lack')
            ->buildQuery();
    }

    public function test_exception_is_thrown_when_value_is_not_specified(): void
    {
        $this->expectException(QueryBuilderException::class);

        $this->builder
            ->field('title')
            ->buildQuery();
    }

    public function test_query_with_field_and_value_can_be_built(): void
    {
        $expected = [
            'fuzzy' => [
                'title' => [
                    'value' => 'lack',
                ],
            ],
        ];

        $actual = $this->builder
            ->field('title')
            ->value('lack')
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_field_and_value_and_fuzziness_can_be_built(): void
    {
        $expected = [
            'fuzzy' => [
                'title' => [
                    'value' => 'lack',
                    'fuzziness' => 'AUTO',
                ],
            ],
        ];

        $actual = $this->builder
            ->field('title')
            ->value('lack')
            ->fuzziness('AUTO')
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_field_and_value_and_max_expansions_can_be_built(): void
    {
        $expected = [
            'fuzzy' => [
                'title' => [
                    'value' => 'lack',
                    'max_expansions' => 50,
                ],
            ],
        ];

        $actual = $this->builder
            ->field('title')
            ->value('lack')
            ->maxExpansions(50)
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_field_and_value_and_prefix_length_can_be_built(): void
    {
        $expected = [
            'fuzzy' => [
                'title' => [
                    'value' => 'lack',
                    'prefix_length' => 0,
                ],
            ],
        ];

        $actual = $this->builder
            ->field('title')
            ->value('lack')
            ->prefixLength(0)
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_field_and_value_and_transpositions_can_be_built(): void
    {
        $expected = [
            'fuzzy' => [
                'title' => [
                    'value' => 'lack',
                    'transpositions' => true,
                ],
            ],
        ];

        $actual = $this->builder
            ->field('title')
            ->value('lack')
            ->transpositions(true)
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_field_and_value_and_rewrite_can_be_built(): void
    {
        $expected = [
            'fuzzy' => [
                'title' => [
                    'value' => 'lack',
                    'rewrite' => 'constant_score',
                ],
            ],
        ];

        $actual = $this->builder
            ->field('title')
            ->value('lack')
            ->rewrite('constant_score')
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }
}
