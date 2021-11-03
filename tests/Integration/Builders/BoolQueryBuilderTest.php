<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration\Builders;

use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use ElasticScoutDriverPlus\Exceptions\QueryBuilderException;
use ElasticScoutDriverPlus\Support\Query;
use ElasticScoutDriverPlus\Tests\Integration\TestCase;
use stdClass;

/**
 * @covers \ElasticScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\BoolQueryBuilder
 *
 * @uses   \ElasticScoutDriverPlus\Builders\MatchAllQueryBuilder
 * @uses   \ElasticScoutDriverPlus\Builders\TermQueryBuilder
 * @uses   \ElasticScoutDriverPlus\Factories\ParameterFactory
 * @uses   \ElasticScoutDriverPlus\QueryParameters\ParameterCollection
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Shared\FieldParameter
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Shared\ValueParameter
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Transformers\FlatArrayTransformer
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Transformers\GroupedArrayTransformer
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Validators\AllOfValidator
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Validators\OneOfValidator
 * @uses   \ElasticScoutDriverPlus\Support\Arr
 * @uses   \ElasticScoutDriverPlus\Support\Query
 */
final class BoolQueryBuilderTest extends TestCase
{
    /**
     * @var BoolQueryBuilder
     */
    private $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new BoolQueryBuilder();
    }

    public function test_exception_is_thrown_when_building_query_with_empty_clauses(): void
    {
        $this->expectException(QueryBuilderException::class);

        $this->builder
            ->withTrashed()
            ->buildQuery();
    }

    public function test_query_with_trashed_can_be_built(): void
    {
        $expected = [
            'bool' => [
                'must' => [
                    ['match_all' => new stdClass()],
                ],
            ],
        ];

        $actual = $this->builder
            ->withTrashed()
            ->must(Query::matchAll())
            ->buildQuery();

        $this->assertEquals($expected, $actual);
    }

    public function test_query_with_only_trashed_can_be_built(): void
    {
        $this->app['config']->set('scout.soft_delete', true);

        $expected = [
            'bool' => [
                'must' => [
                    ['match_all' => new stdClass()],
                ],
                'filter' => [
                    ['term' => ['__soft_deleted' => 1]],
                ],
            ],
        ];

        $actual = $this->builder
            ->must(Query::matchAll())
            ->onlyTrashed()
            ->buildQuery();

        $this->assertEquals($expected, $actual);
    }

    public function test_query_with_must_can_be_built(): void
    {
        $expected = [
            'bool' => [
                'must' => [
                    ['term' => ['year' => ['value' => 2020]]],
                ],
            ],
        ];

        $actual = $this->builder
            ->must(
                Query::term()
                    ->field('year')
                    ->value('2020')
            )
            ->buildQuery();

        $this->assertEquals($expected, $actual);
    }

    public function test_query_with_raw_must_can_be_built(): void
    {
        $expected = [
            'bool' => [
                'must' => [
                    'term' => ['year' => 2020],
                ],
            ],
        ];

        $actual = $this->builder
            ->mustRaw(['term' => ['year' => 2020]])
            ->buildQuery();

        $this->assertEquals($expected, $actual);
    }

    public function test_query_with_consecutive_usage_of_must_and_must_raw_can_be_built(): void
    {
        $expected = [
            'bool' => [
                'must' => [
                    ['term' => ['year' => 2019]],
                    ['term' => ['year' => ['value' => 2020]]],
                ],
            ],
        ];

        $actual = $this->builder
            ->mustRaw(['term' => ['year' => 2019]])
            ->must(
                Query::term()
                    ->field('year')
                    ->value('2020')
            )
            ->buildQuery();

        $this->assertEquals($expected, $actual);
    }

    public function test_query_with_must_not_can_be_built(): void
    {
        $expected = [
            'bool' => [
                'must_not' => [
                    ['term' => ['year' => ['value' => 2020]]],
                ],
            ],
        ];

        $actual = $this->builder
            ->mustNot(
                Query::term()
                    ->field('year')
                    ->value('2020')
            )
            ->buildQuery();

        $this->assertEquals($expected, $actual);
    }

    public function test_query_with_raw_must_not_can_be_built(): void
    {
        $expected = [
            'bool' => [
                'must_not' => [
                    'term' => ['year' => 2020],
                ],
            ],
        ];

        $actual = $this->builder
            ->mustNotRaw(['term' => ['year' => 2020]])
            ->buildQuery();

        $this->assertEquals($expected, $actual);
    }

    public function test_query_with_should_can_be_built(): void
    {
        $expected = [
            'bool' => [
                'should' => [
                    ['term' => ['year' => ['value' => 2019]]],
                    ['term' => ['year' => ['value' => 2020]]],
                ],
            ],
        ];

        $actual = $this->builder
            ->should(
                Query::term()
                    ->field('year')
                    ->value('2019')
            )
            ->should(
                Query::term()
                    ->field('year')
                    ->value('2020')
            )
            ->buildQuery();

        $this->assertEquals($expected, $actual);
    }

    public function test_query_with_raw_should_can_be_built(): void
    {
        $expected = [
            'bool' => [
                'should' => [
                    ['term' => ['year' => 2019]],
                    ['term' => ['year' => 2020]],
                ],
            ],
        ];

        $actual = $this->builder
            ->shouldRaw([
                ['term' => ['year' => 2019]],
                ['term' => ['year' => 2020]],
            ])
            ->buildQuery();

        $this->assertEquals($expected, $actual);
    }

    public function test_query_with_minimum_should_match_can_be_built(): void
    {
        $expected = [
            'bool' => [
                'should' => [
                    ['term' => ['year' => ['value' => 2019]]],
                    ['term' => ['year' => ['value' => 2020]]],
                ],
                'minimum_should_match' => 1,
            ],
        ];

        $actual = $this->builder
            ->should(
                Query::term()
                    ->field('year')
                    ->value('2019')
            )
            ->should(
                Query::term()
                    ->field('year')
                    ->value('2020')
            )
            ->minimumShouldMatch(1)
            ->buildQuery();

        $this->assertEquals($expected, $actual);
    }

    public function test_query_with_filter_can_be_built(): void
    {
        $expected = [
            'bool' => [
                'filter' => [
                    ['term' => ['year' => ['value' => 2020]]],
                ],
            ],
        ];

        $actual = $this->builder
            ->filter(
                Query::term()
                    ->field('year')
                    ->value('2020')
            )
            ->buildQuery();

        $this->assertEquals($expected, $actual);
    }

    public function test_query_with_raw_filter_can_be_built(): void
    {
        $expected = [
            'bool' => [
                'filter' => [
                    'term' => ['year' => 2020],
                ],
            ],
        ];

        $actual = $this->builder
            ->filterRaw(['term' => ['year' => 2020]])
            ->buildQuery();

        $this->assertEquals($expected, $actual);
    }

    public function test_query_with_raw_filter_and_soft_deletes_can_be_built(): void
    {
        $this->app['config']->set('scout.soft_delete', true);

        $expected = [
            'bool' => [
                'filter' => [
                    ['term' => ['year' => 2020]],
                    ['term' => ['__soft_deleted' => 0]],
                ],
            ],
        ];

        $actual = $this->builder
            ->filterRaw(['term' => ['year' => 2020]])
            ->buildQuery();

        $this->assertEquals($expected, $actual);
    }
}
