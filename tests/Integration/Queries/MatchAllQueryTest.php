<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration\Queries;

use ElasticScoutDriverPlus\Support\Query;
use ElasticScoutDriverPlus\Tests\App\Book;
use ElasticScoutDriverPlus\Tests\Integration\TestCase;
use const SORT_NUMERIC;

/**
 * @covers \ElasticScoutDriverPlus\Builders\MatchAllQueryBuilder
 * @covers \ElasticScoutDriverPlus\Engine
 * @covers \ElasticScoutDriverPlus\Factories\LazyModelFactory
 * @covers \ElasticScoutDriverPlus\Support\Query
 *
 * @uses   \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
 * @uses   \ElasticScoutDriverPlus\Decorators\Hit
 * @uses   \ElasticScoutDriverPlus\Decorators\SearchResult
 * @uses   \ElasticScoutDriverPlus\Factories\DocumentFactory
 * @uses   \ElasticScoutDriverPlus\Factories\ParameterFactory
 * @uses   \ElasticScoutDriverPlus\Factories\RoutingFactory
 * @uses   \ElasticScoutDriverPlus\QueryParameters\ParameterCollection
 * @uses   \ElasticScoutDriverPlus\Searchable
 * @uses   \ElasticScoutDriverPlus\Support\ModelScope
 */
final class MatchAllQueryTest extends TestCase
{
    public function test_all_models_can_be_found(): void
    {
        $target = factory(Book::class, rand(8, 10))
            ->state('belongs_to_author')
            ->create()
            ->sortBy('id', SORT_NUMERIC);

        $found = Book::searchQuery(Query::matchAll())
            ->sort('id')
            ->execute();

        $this->assertFoundModels($target, $found);
    }
}
