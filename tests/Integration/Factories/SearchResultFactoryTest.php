<?php
declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration\Factories;

use ElasticAdapter\Search\SearchResponse;
use ElasticAdapter\Search\Suggestion;
use ElasticScoutDriverPlus\Factories\SearchResultFactory;
use ElasticScoutDriverPlus\SearchResult;
use ElasticScoutDriverPlus\Tests\App\Author;
use ElasticScoutDriverPlus\Tests\App\Book;
use ElasticScoutDriverPlus\Tests\Integration\TestCase;
use Illuminate\Database\Eloquent\Model;

/**
 * @covers \ElasticScoutDriverPlus\Factories\SearchResultFactory
 * @uses   \ElasticScoutDriverPlus\Factories\LazyModelFactory
 * @uses   \ElasticScoutDriverPlus\Match
 * @uses   \ElasticScoutDriverPlus\SearchResult
 * @uses   \ElasticScoutDriverPlus\Decorators\EngineDecorator
 */
final class SearchResultFactoryTest extends TestCase
{
    /**
     * @var SearchResultFactory
     */
    private $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = resolve(SearchResultFactory::class);
    }

    public function test_search_result_can_be_made_from_search_response_for_provided_model(): void
    {
        $models = factory(Book::class, rand(2, 10))->create([
            'author_id' => factory(Author::class)->create()->getKey(),
        ]);

        $searchResult = $this->factory->makeFromSearchResponseForModel(new SearchResponse([
            'hits' => [
                'total' => [
                    'value' => $models->count()
                ],
                'hits' => $models->map(function (Model $model) {
                    return [
                        '_id' => (string)$model->getKey(),
                        '_source' => [],
                    ];
                })->all(),
            ],
            'suggest' => [
                'title_suggest' => [
                    [
                        'text' => 'bar',
                        'offset' => 0,
                        'length' => 3,
                        'options' => []
                    ]
                ]
            ],
            'aggregations' => [
                'max_price' => [
                    'value' => 100
                ]
            ]
        ]), new Book());

        $this->assertInstanceOf(SearchResult::class, $searchResult);
        $this->assertCount($models->count(), $searchResult->matches());
        $this->assertCount($models->count(), $searchResult->documents());
        $this->assertCount($models->count(), $searchResult->models());
        $this->assertCount(0, $searchResult->highlights());
        $this->assertSame($models->count(), $searchResult->total());
        $this->assertEquals($models->toArray(), $searchResult->models()->toArray());

        $this->assertEquals(collect([
            'title_suggest' => collect([
                new Suggestion([
                    'text' => 'bar',
                    'offset' => 0,
                    'length' => 3,
                    'options' => []
                ])
            ])
        ]), $searchResult->suggestions());

        $this->assertEquals(collect([
            'max_price' => [
                'value' => 100
            ]
        ]), $searchResult->aggregations());
    }
}
