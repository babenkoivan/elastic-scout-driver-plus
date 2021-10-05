<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration\Queries;

use ElasticScoutDriverPlus\Support\Query;
use ElasticScoutDriverPlus\Tests\App\Book;
use ElasticScoutDriverPlus\Tests\Integration\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\WildcardQueryBuilder
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
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Transformers\GroupedArrayTransformer
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Validators\AllOfValidator
 * @uses   \ElasticScoutDriverPlus\Searchable
 * @uses   \ElasticScoutDriverPlus\Support\ModelScope
 */
final class WildcardQueryTest extends TestCase
{
    public function test_models_can_be_found_using_field_and_value(): void
    {
        // additional mixin
        factory(Book::class)
            ->state('belongs_to_author')
            ->create(['title' => 'The wrong book']);

        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['title' => 'The right book']);

        $query = Query::wildcard()
            ->field('title')
            ->value('ri*t');

        $found = Book::searchQuery($query)->execute();

        $this->assertFoundModel($target, $found);
    }
}
