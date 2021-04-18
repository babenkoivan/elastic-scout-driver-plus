<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration\Builders;

use ElasticAdapter\Search\SearchRequest;
use ElasticScoutDriverPlus\Builders\RawQueryBuilder;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;
use ElasticScoutDriverPlus\Exceptions\ModelClassNotFoundInScopeException;
use ElasticScoutDriverPlus\Tests\App\Author;
use ElasticScoutDriverPlus\Tests\App\Book;
use ElasticScoutDriverPlus\Tests\Integration\TestCase;
use InvalidArgumentException;
use stdClass;

/**
 * @covers \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
 *
 * @uses   \ElasticScoutDriverPlus\Builders\RawQueryBuilder
 * @uses   \ElasticScoutDriverPlus\Decorators\EngineDecorator
 * @uses   \ElasticScoutDriverPlus\Exceptions\ModelClassNotFoundInScopeException
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
        $builder = $this->makeBuilderWithQuery($this->matchAllQuery);

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
            ->setHighlight($highlight);

        $actual = $this->makeBuilderWithQuery($this->matchAllQuery)
            ->highlightRaw($highlight)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_highlight_can_be_built(): void
    {
        $expected = (new SearchRequest($this->matchAllQuery))
            ->setHighlight([
                'fields' => [
                    'body' => new stdClass(),
                    'blog.title' => ['number_of_fragments' => 0],
                ],
            ]);

        $actual = $this->makeBuilderWithQuery($this->matchAllQuery)
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
            ->setSort($sort);

        $actual = $this->makeBuilderWithQuery($this->matchAllQuery)
            ->sortRaw($sort)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_sort_can_be_built(): void
    {
        $expected = (new SearchRequest($this->matchAllQuery))
            ->setSort([
                ['post_date' => 'asc'],
                ['name' => 'desc'],
            ]);

        $actual = $this->makeBuilderWithQuery($this->matchAllQuery)
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
            ->setRescore($rescore);

        $actual = $this->makeBuilderWithQuery($this->matchAllQuery)
            ->rescoreRaw($rescore)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_from_can_be_built(): void
    {
        $from = rand(2, 1000);

        $expected = (new SearchRequest($this->matchAllQuery))
            ->setFrom($from);

        $actual = $this->makeBuilderWithQuery($this->matchAllQuery)
            ->from($from)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_size_can_be_built(): void
    {
        $size = rand(2, 1000);

        $expected = (new SearchRequest($this->matchAllQuery))
            ->setSize($size);

        $actual = $this->makeBuilderWithQuery($this->matchAllQuery)
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
            ->setSuggest($suggest);

        $actual = $this->makeBuilderWithQuery($this->matchNoneQuery)
            ->suggestRaw($suggest)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_suggest_can_be_built(): void
    {
        $expected = (new SearchRequest($this->matchNoneQuery))
            ->setSuggest([
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

        $actual = $this->makeBuilderWithQuery($this->matchNoneQuery)
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
            ->setSource($source);

        $actual = $this->makeBuilderWithQuery($this->matchAllQuery)
            ->sourceRaw($source)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_source_can_be_built(): void
    {
        $source = ['title', 'description'];

        $expected = (new SearchRequest($this->matchAllQuery))
            ->setSource($source);

        $actual = $this->makeBuilderWithQuery($this->matchAllQuery)
            ->source($source)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_raw_collapse_can_be_built(): void
    {
        $collapse = ['field' => 'user'];

        $expected = (new SearchRequest($this->matchAllQuery))
            ->setCollapse($collapse);

        $actual = $this->makeBuilderWithQuery($this->matchAllQuery)
            ->collapseRaw($collapse)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_collapse_can_be_built(): void
    {
        $expected = (new SearchRequest($this->matchAllQuery))
            ->setCollapse(['field' => 'user']);

        $actual = $this->makeBuilderWithQuery($this->matchAllQuery)
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

        $expected = (new SearchRequest($this->matchAllQuery))
            ->setAggregations($aggregations);

        $actual = $this->makeBuilderWithQuery($this->matchAllQuery)
            ->aggregateRaw($aggregations)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_aggregate_can_be_built(): void
    {
        $expected = (new SearchRequest($this->matchAllQuery))
            ->setAggregations([
                'max_price' => [
                    'max' => [
                        'field' => 'price',
                    ],
                ],
            ]);

        $actual = $this->makeBuilderWithQuery($this->matchAllQuery)
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

        $this->makeBuilderWithQuery($this->matchAllQuery)->join(__CLASS__);
    }

    public function test_search_request_with_raw_post_filter_can_be_built(): void
    {
        $postFilter = [
            'term' => [
                'published' => '2020-06-07',
            ],
        ];

        $expected = (new SearchRequest($this->matchAllQuery))
            ->setPostFilter($postFilter);

        $actual = $this->makeBuilderWithQuery($this->matchAllQuery)
            ->postFilterRaw($postFilter)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_post_filter_can_be_built(): void
    {
        $expected = (new SearchRequest($this->matchAllQuery))
            ->setPostFilter(['term' => ['published' => '2020-06-07']]);

        $actual = $this->makeBuilderWithQuery($this->matchAllQuery)
            ->postFilter('term', ['published' => '2020-06-07'])
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_track_total_hits_can_be_built(): void
    {
        $expected = (new SearchRequest($this->matchAllQuery))
            ->setTrackTotalHits(100);

        $actual = $this->makeBuilderWithQuery($this->matchAllQuery)
            ->trackTotalHits(100)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_callback_is_applied_when_value_is_true(): void
    {
        $expected = (new SearchRequest($this->matchAllQuery))
            ->setSize(999);

        $actual = $this->makeBuilderWithQuery($this->matchAllQuery)
            ->when(true, static function (SearchRequestBuilder $builder) {
                $builder->size(999);
            })
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_default_callback_is_applied_when_value_is_false(): void
    {
        $expected = (new SearchRequest($this->matchAllQuery))
            ->setFrom(333);

        $actual = $this->makeBuilderWithQuery($this->matchAllQuery)
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
            ->setTrackScores(true);

        $actual = $this->makeBuilderWithQuery($this->matchAllQuery)
            ->trackScores(true)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_exception_is_thrown_when_trying_to_boost_out_of_scope_index(): void
    {
        $this->expectException(ModelClassNotFoundInScopeException::class);

        $this->makeBuilderWithQuery($this->matchAllQuery)->boostIndex(Author::class, 2);
    }

    public function test_search_request_with_index_boost_can_be_built(): void
    {
        $expected = (new SearchRequest($this->matchAllQuery))
            ->setIndicesBoost([['books' => 2]]);

        $actual = $this->makeBuilderWithQuery($this->matchAllQuery)
            ->boostIndex(Book::class, 2)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    private function makeBuilderWithQuery(array $query): SearchRequestBuilder
    {
        $model = new Book();
        $queryBuilder = (new RawQueryBuilder())->query($query);

        return new SearchRequestBuilder($model, $queryBuilder);
    }
}
