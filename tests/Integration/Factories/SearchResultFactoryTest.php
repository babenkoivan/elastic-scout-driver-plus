<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration\Factories;

use ElasticAdapter\Search\SearchResponse;
use ElasticAdapter\Search\Suggestion;
use ElasticScoutDriverPlus\Factories\SearchResultFactory;
use ElasticScoutDriverPlus\Support\ModelScope;
use ElasticScoutDriverPlus\Tests\App\Author;
use ElasticScoutDriverPlus\Tests\App\Book;
use ElasticScoutDriverPlus\Tests\Integration\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\Factories\SearchResultFactory
 *
 * @uses   \ElasticScoutDriverPlus\Engine
 * @uses   \ElasticScoutDriverPlus\Factories\LazyModelFactory
 * @uses   \ElasticScoutDriverPlus\QueryMatch
 * @uses   \ElasticScoutDriverPlus\SearchResult
 * @uses   \ElasticScoutDriverPlus\Support\ModelScope
 */
final class SearchResultFactoryTest extends TestCase
{
    public function test_search_result_can_be_made_from_search_response_for_provided_model(): void
    {
        $models = factory(Book::class, rand(2, 10))->create([
            'author_id' => factory(Author::class)->create()->getKey(),
        ]);

        $searchResult = SearchResultFactory::makeFromSearchResponseUsingModelScope(new SearchResponse([
            'hits' => [
                'total' => [
                    'value' => $models->count(),
                ],
                'hits' => $models->map(static function (Book $model) {
                    return [
                        '_id' => (string)$model->getKey(),
                        '_index' => $model->searchableAs(),
                        '_source' => [],
                        '_score' => 1.0,
                    ];
                })->all(),
            ],
            'suggest' => [
                'title_suggest' => [
                    [
                        'text' => 'bar',
                        'offset' => 0,
                        'length' => 3,
                        'options' => [],
                    ],
                ],
            ],
            'aggregations' => [
                'max_price' => [
                    'value' => 100,
                ],
            ],
        ]), new ModelScope(Book::class));

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
                    'options' => [],
                ]),
            ]),
        ]), $searchResult->suggestions());

        $this->assertEquals(collect([
            'max_price' => [
                'value' => 100,
            ],
        ]), $searchResult->aggregations());
    }
}
