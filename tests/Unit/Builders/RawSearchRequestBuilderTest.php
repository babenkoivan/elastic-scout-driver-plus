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

        $expected = (new SearchRequest($this->matchAllQuery))
            ->setHighlight($highlight);

        $actual = $this->builder
            ->query($this->matchAllQuery)
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
                    'blog.title' => ['number_of_fragments' => 0]
                ]
            ]);

        $actual = $this->builder
            ->query($this->matchAllQuery)
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
            '_score'
        ];

        $expected = (new SearchRequest($this->matchAllQuery))
            ->setSort($sort);

        $actual = $this->builder
            ->query($this->matchAllQuery)
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

        $actual = $this->builder
            ->query($this->matchAllQuery)
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

        $actual = $this->builder
            ->query($this->matchAllQuery)
            ->from($from)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }

    public function test_search_request_with_size_can_be_built(): void
    {
        $size = rand(2, 1000);

        $expected = (new SearchRequest($this->matchAllQuery))
            ->setSize($size);

        $actual = $this->builder
            ->query($this->matchAllQuery)
            ->size($size)
            ->buildSearchRequest();

        $this->assertEquals($expected, $actual);
    }
}
