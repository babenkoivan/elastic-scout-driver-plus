<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Unit\Builders;

use ElasticAdapter\Search\SearchRequest;
use ElasticScoutDriverPlus\Builders\RawQueryBuilder;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @covers \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
 *
 * @uses   \ElasticScoutDriverPlus\Builders\RawQueryBuilder
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

    private function makeBuilderWithQuery(array $query): SearchRequestBuilder
    {
        $model = $this->createMock(Model::class);
        $queryBuilder = (new RawQueryBuilder())->query($query);

        return new SearchRequestBuilder($model, $queryBuilder);
    }
}
