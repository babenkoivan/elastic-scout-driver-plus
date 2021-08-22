<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration;

use ElasticScoutDriverPlus\Tests\App\Book;
use const SORT_STRING;

/**
 * @covers \ElasticScoutDriverPlus\Builders\MatchAllQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
 * @covers \ElasticScoutDriverPlus\QueryDsl
 * @covers \ElasticScoutDriverPlus\Engine
 * @covers \ElasticScoutDriverPlus\Factories\LazyModelFactory
 *
 * @uses   \ElasticScoutDriverPlus\Factories\RoutingFactory
 * @uses   \ElasticScoutDriverPlus\Factories\SearchResultFactory
 * @uses   \ElasticScoutDriverPlus\QueryMatch
 * @uses   \ElasticScoutDriverPlus\SearchResult
 * @uses   \ElasticScoutDriverPlus\Support\ModelScope
 */
final class MatchAllSearchTest extends TestCase
{
    public function test_all_models_can_be_found(): void
    {
        $target = factory(Book::class, rand(8, 10))
            ->state('belongs_to_author')
            ->create()
            ->sortBy('id', SORT_STRING)
            ->values();

        $found = Book::matchAllSearch()
            ->sort('_id', 'asc')
            ->execute();

        $this->assertEquals($target->toArray(), $found->models()->toArray());
    }
}
