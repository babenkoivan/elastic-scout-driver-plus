<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Integration\Builders;

use Elastic\Adapter\Search\SearchParameters;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Exceptions\NotSearchableModelException;
use Elastic\ScoutDriverPlus\Tests\App\Author;
use Elastic\ScoutDriverPlus\Tests\App\Book;
use Elastic\ScoutDriverPlus\Tests\Integration\TestCase;
use stdClass;

/**
 * @covers \Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder
 * @covers \Elastic\ScoutDriverPlus\Exceptions\NotSearchableModelException
 * @covers \Elastic\ScoutDriverPlus\Support\Conditionable
 *
 * @uses   \Elastic\ScoutDriverPlus\Builders\DatabaseQueryBuilder
 * @uses   \Elastic\ScoutDriverPlus\Engine
 * @uses   \Elastic\ScoutDriverPlus\Factories\ParameterFactory
 * @uses   \Elastic\ScoutDriverPlus\Searchable
 */
final class SearchParametersBuilderTest extends TestCase
{
    public function test_search_parameters_with_query_can_be_built(): void
    {
        $matchAllQuery = ['match_all' => new stdClass()];

        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->query($matchAllQuery);

        $actual = (new SearchParametersBuilder(new Book()))
            ->query($matchAllQuery)
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_parameters_with_raw_highlight_can_be_built(): void
    {
        $rawHighlight = [
            'number_of_fragments' => 3,
            'fragment_size' => 150,
            'fields' => [
                'body' => ['pre_tags' => ['<em>'], 'post_tags' => ['</em>']],
                'blog.title' => ['number_of_fragments' => 0],
            ],
        ];

        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->highlight($rawHighlight);

        $actual = (new SearchParametersBuilder(new Book()))
            ->highlightRaw($rawHighlight)
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_parameters_with_highlight_can_be_built(): void
    {
        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->highlight([
                'fields' => [
                    'body' => new stdClass(),
                    'blog.title' => ['number_of_fragments' => 0],
                ],
            ]);

        $actual = (new SearchParametersBuilder(new Book()))
            ->highlight('body')
            ->highlight('blog.title', ['number_of_fragments' => 0])
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_parameters_with_raw_sort_can_be_built(): void
    {
        $rawSort = [
            ['post_date' => ['order' => 'asc']],
            'user',
            ['name' => 'desc'],
            '_score',
        ];

        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->sort($rawSort);

        $actual = (new SearchParametersBuilder(new Book()))
            ->sortRaw($rawSort)
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_parameters_with_sort_can_be_built(): void
    {
        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->sort([
                ['post_date' => 'asc'],
                ['name' => 'desc'],
            ]);

        $actual = (new SearchParametersBuilder(new Book()))
            ->sort('post_date')
            ->sort('name', 'desc')
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_parameters_with_raw_rescore_can_be_built(): void
    {
        $rawRescore = [
            'window_size' => 50,
            'query' => [
                'rescore_query' => [
                    'match_phrase' => [
                        'message' => [
                            'query' => 'the quick brown',
                            'slop' => 2,
                        ],
                    ],
                ],
                'query_weight' => 0.7,
                'rescore_query_weight' => 1.2,
            ],
        ];

        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->rescore($rawRescore);

        $actual = (new SearchParametersBuilder(new Book()))
            ->rescoreRaw($rawRescore)
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_parameters_with_rescore_query_can_be_built(): void
    {
        $rescoreQuery = [
            'match_phrase' => [
                'message' => [
                    'query' => 'the quick brown',
                    'slop' => 2,
                ],
            ],
        ];

        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->rescore([
                'query' => [
                    'rescore_query' => $rescoreQuery,
                ],
            ]);

        $actual = (new SearchParametersBuilder(new Book()))
            ->rescoreQuery($rescoreQuery)
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_parameters_with_rescore_weights_can_be_built(): void
    {
        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->rescore([
                'query' => [
                    'query_weight' => 0.7,
                    'rescore_query_weight' => 1.2,
                ],
            ]);

        $actual = (new SearchParametersBuilder(new Book()))
            ->rescoreWeights(0.7, 1.2)
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_parameters_with_rescore_window_size_can_be_built(): void
    {
        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->rescore([
                'window_size' => 10,
            ]);

        $actual = (new SearchParametersBuilder(new Book()))
            ->rescoreWindowSize(10)
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_parameters_with_from_can_be_built(): void
    {
        $from = rand(2, 1000);

        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->from($from);

        $actual = (new SearchParametersBuilder(new Book()))
            ->from($from)
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_parameters_with_size_can_be_built(): void
    {
        $size = rand(2, 1000);

        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->size($size);

        $actual = (new SearchParametersBuilder(new Book()))
            ->size($size)
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_parameters_with_raw_suggest_can_be_built(): void
    {
        $rawSuggest = [
            'color_suggestion' => [
                'text' => 'red',
                'term' => [
                    'field' => 'color',
                ],
            ],
        ];

        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->suggest($rawSuggest);

        $actual = (new SearchParametersBuilder(new Book()))
            ->suggestRaw($rawSuggest)
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_parameters_with_suggest_can_be_built(): void
    {
        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->suggest([
                'color_suggestion' => [
                    'text' => 'red',
                    'term' => [
                        'field' => 'color',
                    ],
                ],
                'shape_suggestion' => [
                    'text' => 'square',
                    'term' => [
                        'field' => 'shape',
                    ],
                ],
            ]);

        $actual = (new SearchParametersBuilder(new Book()))
            ->suggest('color_suggestion', [
                'text' => 'red',
                'term' => [
                    'field' => 'color',
                ],
            ])
            ->suggest('shape_suggestion', [
                'text' => 'square',
                'term' => [
                    'field' => 'shape',
                ],
            ])
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_parameters_with_raw_source_can_be_built(): void
    {
        $rawSource = false;

        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->source($rawSource);

        $actual = (new SearchParametersBuilder(new Book()))
            ->sourceRaw($rawSource)
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_parameters_with_source_can_be_built(): void
    {
        $source = ['title', 'description'];

        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->source($source);

        $actual = (new SearchParametersBuilder(new Book()))
            ->source($source)
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_parameters_with_raw_collapse_can_be_built(): void
    {
        $rawCollapse = ['field' => 'user'];

        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->collapse($rawCollapse);

        $actual = (new SearchParametersBuilder(new Book()))
            ->collapseRaw($rawCollapse)
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_parameters_with_collapse_can_be_built(): void
    {
        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->collapse(['field' => 'user']);

        $actual = (new SearchParametersBuilder(new Book()))
            ->collapse('user')
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_parameters_with_raw_aggregate_can_be_built(): void
    {
        $rawAggregations = [
            'max_price' => [
                'max' => [
                    'field' => 'price',
                ],
            ],
            'min_price' => [
                'min' => [
                    'field' => 'price',
                ],
            ],
        ];

        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->aggregations($rawAggregations);

        $actual = (new SearchParametersBuilder(new Book()))
            ->aggregateRaw($rawAggregations)
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_parameters_with_aggregate_can_be_built(): void
    {
        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->aggregations([
                'max_price' => [
                    'max' => [
                        'field' => 'price',
                    ],
                ],
            ]);

        $actual = (new SearchParametersBuilder(new Book()))
            ->aggregate('max_price', ['max' => ['field' => 'price']])
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_exception_is_thrown_when_joining_not_a_searchable_model(): void
    {
        $this->expectException(NotSearchableModelException::class);

        (new SearchParametersBuilder(new Book()))->join(__CLASS__);
    }

    public function test_joined_model_can_be_boosted(): void
    {
        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs(), (new Author())->searchableAs()])
            ->indicesBoost([['authors' => 2]]);

        $actual = (new SearchParametersBuilder(new Book()))
            ->join(Author::class, 2)
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_parameters_with_post_filter_can_be_built(): void
    {
        $postFilter = [
            'term' => [
                'published' => '2020-06-07',
            ],
        ];

        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->postFilter($postFilter);

        $actual = (new SearchParametersBuilder(new Book()))
            ->postFilter($postFilter)
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_parameters_with_track_total_hits_can_be_built(): void
    {
        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->trackTotalHits(100);

        $actual = (new SearchParametersBuilder(new Book()))
            ->trackTotalHits(100)
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_callback_is_applied_when_value_is_true(): void
    {
        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->size(999);

        $actual = (new SearchParametersBuilder(new Book()))
            ->when(true, static function (SearchParametersBuilder $builder) {
                $builder->size(999);
            })
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_default_callback_is_applied_when_value_is_false(): void
    {
        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->from(333);

        $actual = (new SearchParametersBuilder(new Book()))
            ->when(false, static function (SearchParametersBuilder $builder) {
                $builder->from(111);
            }, static function (SearchParametersBuilder $builder) {
                $builder->from(333);
            })
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_callback_is_applied_unless_value_is_true(): void
    {
        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->minScore(1);

        $actual = (new SearchParametersBuilder(new Book()))
            ->unless(false, static function (SearchParametersBuilder $builder) {
                $builder->minScore(1);
            })
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_default_callback_is_applied_unless_value_is_false(): void
    {
        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->minScore(2);

        $actual = (new SearchParametersBuilder(new Book()))
            ->unless(true, static function (SearchParametersBuilder $builder) {
                $builder->minScore(1);
            }, static function (SearchParametersBuilder $builder) {
                $builder->minScore(2);
            })
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_parameters_with_track_scores_can_be_built(): void
    {
        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->trackScores(true);

        $actual = (new SearchParametersBuilder(new Book()))
            ->trackScores(true)
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_parameters_with_min_score_can_be_built(): void
    {
        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->minScore(0.5);

        $actual = (new SearchParametersBuilder(new Book()))
            ->minScore(0.5)
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_parameters_with_search_type_can_be_built(): void
    {
        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->searchType('query_then_fetch');

        $actual = (new SearchParametersBuilder(new Book()))
            ->searchType('query_then_fetch')
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_parameters_with_preference_can_be_built(): void
    {
        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->preference('_local');

        $actual = (new SearchParametersBuilder(new Book()))
            ->preference('_local')
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_parameters_with_point_in_time_can_be_built(): void
    {
        $expected = (new SearchParameters())
            ->pointInTime([
                'id' => '46ToAwMDaWR5BXV1',
                'keep_alive' => '1m',
            ]);

        $actual = (new SearchParametersBuilder(new Book()))
            ->pointInTime('46ToAwMDaWR5BXV1', '1m')
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_parameters_with_search_after_can_be_built(): void
    {
        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->searchAfter([
                '2021-05-20T05:30:04.832Z',
                4294967298,
            ]);

        $actual = (new SearchParametersBuilder(new Book()))
            ->searchAfter([
                '2021-05-20T05:30:04.832Z',
                4294967298,
            ])
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_parameters_with_routing_can_be_built(): void
    {
        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->routing(['author1', 'author2']);

        $actual = (new SearchParametersBuilder(new Book()))
            ->routing(['author1', 'author2'])
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_parameters_with_explain_can_be_built(): void
    {
        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->explain(true);

        $actual = (new SearchParametersBuilder(new Book()))
            ->explain(true)
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_parameters_with_terminate_after_can_be_built(): void
    {
        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->terminateAfter(10);

        $actual = (new SearchParametersBuilder(new Book()))
            ->terminateAfter(10)
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_parameters_with_request_cache_can_be_built(): void
    {
        $expected = (new SearchParameters())
            ->indices([(new Book())->searchableAs()])
            ->requestCache(true);

        $actual = (new SearchParametersBuilder(new Book()))
            ->requestCache(true)
            ->buildSearchParameters();

        $this->assertEquals($expected, $actual);
    }
}
