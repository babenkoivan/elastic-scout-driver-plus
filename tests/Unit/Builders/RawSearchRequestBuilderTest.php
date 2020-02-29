<?php
declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Unit\Builders;

use ElasticAdapter\Search\SearchRequest;
use ElasticScoutDriverPlus\Builders\RawSearchRequestBuilder;
use ElasticScoutDriverPlus\Exceptions\SearchRequestBuilderException;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @covers \ElasticScoutDriverPlus\Builders\RawSearchRequestBuilder
 * @covers \ElasticScoutDriverPlus\Builders\AbstractSearchRequestBuilder
 */
final class RawSearchRequestBuilderTest extends TestCase
{
    /**
     * @var RawSearchRequestBuilder
     */
    private $builder;
    /**
     * @var array
     */
    private $matchAllQuery;

    protected function setUp(): void
    {
        parent::setUp();

        $model = $this->createMock(Model::class);

        $this->builder = new RawSearchRequestBuilder($model);
        $this->matchAllQuery = ['match_all' => new stdClass()];
    }

    public function test_exception_is_thrown_when_query_is_not_specified(): void
    {
        $this->expectException(SearchRequestBuilderException::class);

        $this->builder->buildSearchRequest();
    }

    public function test_search_request_can_be_built_when_query_is_specified(): void
    {
        $searchRequest = new SearchRequest($this->matchAllQuery);

        $this->builder->query($this->matchAllQuery);

        $this->assertEquals($searchRequest, $this->builder->buildSearchRequest());
    }

    public function test_search_request_with_raw_highlight_can_be_built(): void
    {
        $highlight = [
            'number_of_fragments' => 3,
            'fragment_size' => 150,
            'fields' => [
                'body' => ['pre_tags' => ['<em>'], 'post_tags' => ['</em>']],
                'blog.title' => ['number_of_fragments' => 0]
            ]
        ];

        $searchRequest = (new SearchRequest($this->matchAllQuery))
            ->setHighlight($highlight);

        $this->builder
            ->query($this->matchAllQuery)
            ->highlightRaw($highlight);

        $this->assertEquals($searchRequest, $this->builder->buildSearchRequest());
    }

    public function test_search_request_with_highlight_can_be_built(): void
    {
        $searchRequest = (new SearchRequest($this->matchAllQuery))
            ->setHighlight([
                'fields' => [
                    'body' => new stdClass(),
                    'blog.title' => ['number_of_fragments' => 0]
                ]
            ]);

        $this->builder
            ->query($this->matchAllQuery)
            ->highlight('body')
            ->highlight('blog.title', ['number_of_fragments' => 0]);

        $this->assertEquals($searchRequest, $this->builder->buildSearchRequest());
    }

    public function test_search_request_with_raw_sort_can_be_built(): void
    {
        $sort = [
            ['post_date' => ['order' => 'asc']],
            'user',
            ['name' => 'desc'],
            '_score'
        ];

        $searchRequest = (new SearchRequest($this->matchAllQuery))
            ->setSort($sort);

        $this->builder
            ->query($this->matchAllQuery)
            ->sortRaw($sort);

        $this->assertEquals($searchRequest, $this->builder->buildSearchRequest());
    }

    public function test_search_request_with_sort_can_be_built(): void
    {
        $searchRequest = (new SearchRequest($this->matchAllQuery))
            ->setSort([
                ['post_date' => 'asc'],
                ['name' => 'desc'],
            ]);

        $this->builder
            ->query($this->matchAllQuery)
            ->sort('post_date')
            ->sort('name', 'desc');

        $this->assertEquals($searchRequest, $this->builder->buildSearchRequest());
    }

    public function test_search_request_with_from_can_be_built(): void
    {
        $from = rand(2, 1000);

        $searchRequest = (new SearchRequest($this->matchAllQuery))
            ->setFrom($from);

        $this->builder
            ->query($this->matchAllQuery)
            ->from($from);

        $this->assertEquals($searchRequest, $this->builder->buildSearchRequest());
    }

    public function test_search_request_with_size_can_be_built(): void
    {
        $size = rand(2, 1000);

        $searchRequest = (new SearchRequest($this->matchAllQuery))
            ->setSize($size);

        $this->builder
            ->query($this->matchAllQuery)
            ->size($size);

        $this->assertEquals($searchRequest, $this->builder->buildSearchRequest());
    }
}
