<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration\Builders;

use ElasticAdapter\Search\SearchRequest;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;
use ElasticScoutDriverPlus\Exceptions\ModelClassNotFoundInScopeException;
use ElasticScoutDriverPlus\Tests\App\Author;
use ElasticScoutDriverPlus\Tests\App\Book;
use ElasticScoutDriverPlus\Tests\Integration\TestCase;
use InvalidArgumentException;
use stdClass;

/**
 * @covers \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
 * @covers \ElasticScoutDriverPlus\Exceptions\ModelClassNotFoundInScopeException
 *
 * @uses   \ElasticScoutDriverPlus\Engine
 * @uses   \ElasticScoutDriverPlus\Factories\ParameterFactory
 * @uses   \ElasticScoutDriverPlus\Support\ModelScope
 */
final class SearchRequestBuilderTest extends TestCase
{
    /**
     * @var array
     */
    private $matchAllQuery;
    /**
     * @var array
     */
    private $matchNoneQuery;

    protected function setUp(): void
    {
        parent::setUp();

        $this->matchAllQuery = ['match_all' => new stdClass()];
        $this->matchNoneQuery = ['match_none' => new stdClass()];
    }

    public function test_search_request_can_be_built_when_query_is_specified(): void
    {
        $searchRequest = new SearchRequest($this->matchAllQuery);
        $builder = new SearchRequestBuilder($this->matchAllQuery, new Book());

        $this->assertEquals($searchRequest, $builder->buildSearchRequest());
    }

    public function test_search_request_with_raw_highlight_can_be_built(): void
    {
        $highlight = [
            'number_of_fragments' => 3,
            'fragment_size' => 150,
            'fields' => [
                'body' => ['pre_tags' => ['<em>'], 'post_tags' => ['</em>']],
                'blog.title' => ['number_of_fragments' => 0],
            ],
        ];

        $expected = (new SearchRequest($this->matchAllQuery))
            ->highlight($highlight);

        $actual = (new SearchRequestBuilder($this->matchAllQuery, new Book()))
            ->highlightRaw($highlight)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_highlight_can_be_built(): void
    {
        $expected = (new SearchRequest($this->matchAllQuery))
            ->highlight([
                'fields' => [
                    'body' => new stdClass(),
                    'blog.title' => ['number_of_fragments' => 0],
                ],
            ]);

        $actual = (new SearchRequestBuilder($this->matchAllQuery, new Book()))
            ->highlight('body')
            ->highlight('blog.title', ['number_of_fragments' => 0])
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_raw_sort_can_be_built(): void
    {
        $sort = [
            ['post_date' => ['order' => 'asc']],
            'user',
            ['name' => 'desc'],
            '_score',
        ];

        $expected = (new SearchRequest($this->matchAllQuery))
            ->sort($sort);

        $actual = (new SearchRequestBuilder($this->matchAllQuery, new Book()))
            ->sortRaw($sort)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_sort_can_be_built(): void
    {
        $expected = (new SearchRequest($this->matchAllQuery))
            ->sort([
                ['post_date' => 'asc'],
                ['name' => 'desc'],
            ]);

        $actual = (new SearchRequestBuilder($this->matchAllQuery, new Book()))
            ->sort('post_date')
            ->sort('name', 'desc')
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_raw_rescore_can_be_built(): void
    {
        $rescore = [
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

        $expected = (new SearchRequest($this->matchAllQuery))
            ->rescore($rescore);

        $actual = (new SearchRequestBuilder($this->matchAllQuery, new Book()))
            ->rescoreRaw($rescore)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_rescore_query_can_be_built(): void
    {
        $rescoreQuery = [
            'match_phrase' => [
                'message' => [
                    'query' => 'the quick brown',
                    'slop' => 2,
                ],
            ],
        ];

        $expected = (new SearchRequest($this->matchAllQuery))
            ->rescore([
                'query' => [
                    'rescore_query' => $rescoreQuery,
                ],
            ]);

        $actual = (new SearchRequestBuilder($this->matchAllQuery, new Book()))
            ->rescoreQuery($rescoreQuery)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_rescore_weights_can_be_built(): void
    {
        $expected = (new SearchRequest($this->matchAllQuery))
            ->rescore([
                'query' => [
                    'query_weight' => 0.7,
                    'rescore_query_weight' => 1.2,
                ],
            ]);

        $actual = (new SearchRequestBuilder($this->matchAllQuery, new Book()))
            ->rescoreWeights(0.7, 1.2)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_rescore_window_size_can_be_built(): void
    {
        $expected = (new SearchRequest($this->matchAllQuery))
            ->rescore([
                'window_size' => 10,
            ]);

        $actual = (new SearchRequestBuilder($this->matchAllQuery, new Book()))
            ->rescoreWindowSize(10)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_from_can_be_built(): void
    {
        $from = rand(2, 1000);

        $expected = (new SearchRequest($this->matchAllQuery))
            ->from($from);

        $actual = (new SearchRequestBuilder($this->matchAllQuery, new Book()))
            ->from($from)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_size_can_be_built(): void
    {
        $size = rand(2, 1000);

        $expected = (new SearchRequest($this->matchAllQuery))
            ->size($size);

        $actual = (new SearchRequestBuilder($this->matchAllQuery, new Book()))
            ->size($size)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_raw_suggest_can_be_built(): void
    {
        $suggest = [
            'color_suggestion' => [
                'text' => 'red',
                'term' => [
                    'field' => 'color',
                ],
            ],
        ];

        $expected = (new SearchRequest($this->matchNoneQuery))
            ->suggest($suggest);

        $actual = (new SearchRequestBuilder($this->matchNoneQuery, new Book()))
            ->suggestRaw($suggest)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_suggest_can_be_built(): void
    {
        $expected = (new SearchRequest($this->matchNoneQuery))
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

        $actual = (new SearchRequestBuilder($this->matchNoneQuery, new Book()))
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
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_raw_source_can_be_built(): void
    {
        $source = false;

        $expected = (new SearchRequest($this->matchAllQuery))
            ->source($source);

        $actual = (new SearchRequestBuilder($this->matchAllQuery, new Book()))
            ->sourceRaw($source)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_source_can_be_built(): void
    {
        $source = ['title', 'description'];

        $expected = (new SearchRequest($this->matchAllQuery))
            ->source($source);

        $actual = (new SearchRequestBuilder($this->matchAllQuery, new Book()))
            ->source($source)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_raw_collapse_can_be_built(): void
    {
        $collapse = ['field' => 'user'];

        $expected = (new SearchRequest($this->matchAllQuery))
            ->collapse($collapse);

        $actual = (new SearchRequestBuilder($this->matchAllQuery, new Book()))
            ->collapseRaw($collapse)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_collapse_can_be_built(): void
    {
        $expected = (new SearchRequest($this->matchAllQuery))
            ->collapse(['field' => 'user']);

        $actual = (new SearchRequestBuilder($this->matchAllQuery, new Book()))
            ->collapse('user')
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_raw_aggregate_can_be_built(): void
    {
        $aggregations = [
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

        $expected = (new SearchRequest())
            ->aggregations($aggregations);

        $actual = (new SearchRequestBuilder(null, new Book()))
            ->aggregateRaw($aggregations)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_aggregate_can_be_built(): void
    {
        $expected = (new SearchRequest($this->matchAllQuery))
            ->aggregations([
                'max_price' => [
                    'max' => [
                        'field' => 'price',
                    ],
                ],
            ]);

        $actual = (new SearchRequestBuilder($this->matchAllQuery, new Book()))
            ->aggregate('max_price', [
                'max' => [
                    'field' => 'price',
                ],
            ])
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_exception_is_thrown_when_joining_not_a_searchable_model(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new SearchRequestBuilder($this->matchAllQuery, new Book()))->join(__CLASS__);
    }

    public function test_search_request_with_post_filter_can_be_built(): void
    {
        $postFilter = [
            'term' => [
                'published' => '2020-06-07',
            ],
        ];

        $expected = (new SearchRequest($this->matchAllQuery))
            ->postFilter($postFilter);

        $actual = (new SearchRequestBuilder($this->matchAllQuery, new Book()))
            ->postFilter($postFilter)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_track_total_hits_can_be_built(): void
    {
        $expected = (new SearchRequest($this->matchAllQuery))
            ->trackTotalHits(100);

        $actual = (new SearchRequestBuilder($this->matchAllQuery, new Book()))
            ->trackTotalHits(100)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_callback_is_applied_when_value_is_true(): void
    {
        $expected = (new SearchRequest($this->matchAllQuery))
            ->size(999);

        $actual = (new SearchRequestBuilder($this->matchAllQuery, new Book()))
            ->when(true, static function (SearchRequestBuilder $builder) {
                $builder->size(999);
            })
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_default_callback_is_applied_when_value_is_false(): void
    {
        $expected = (new SearchRequest($this->matchAllQuery))
            ->from(333);

        $actual = (new SearchRequestBuilder($this->matchAllQuery, new Book()))
            ->when(false, static function (SearchRequestBuilder $builder) {
                $builder->from(111);
            }, static function (SearchRequestBuilder $builder) {
                $builder->from(333);
            })
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_track_scores_can_be_built(): void
    {
        $expected = (new SearchRequest($this->matchAllQuery))
            ->trackScores(true);

        $actual = (new SearchRequestBuilder($this->matchAllQuery, new Book()))
            ->trackScores(true)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_exception_is_thrown_when_trying_to_boost_out_of_scope_index(): void
    {
        $this->expectException(ModelClassNotFoundInScopeException::class);

        (new SearchRequestBuilder($this->matchAllQuery, new Book()))->boostIndex(Author::class, 2);
    }

    public function test_search_request_with_index_boost_can_be_built(): void
    {
        $expected = (new SearchRequest($this->matchAllQuery))
            ->indicesBoost([['books' => 2]]);

        $actual = (new SearchRequestBuilder($this->matchAllQuery, new Book()))
            ->boostIndex(Book::class, 2)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_min_score_can_be_built(): void
    {
        $expected = (new SearchRequest($this->matchAllQuery))
            ->minScore(0.5);

        $actual = (new SearchRequestBuilder($this->matchAllQuery, new Book()))
            ->minScore(0.5)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_search_type_can_be_built(): void
    {
        $expected = (new SearchRequest($this->matchAllQuery))
            ->searchType('query_then_fetch');

        $actual = (new SearchRequestBuilder($this->matchAllQuery, new Book()))
            ->searchType('query_then_fetch')
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_preference_can_be_built(): void
    {
        $expected = (new SearchRequest($this->matchAllQuery))
            ->preference('_local');

        $actual = (new SearchRequestBuilder($this->matchAllQuery, new Book()))
            ->preference('_local')
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }
}
