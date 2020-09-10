<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration;

use ElasticScoutDriverPlus\Tests\App\Book;

/**
 * @covers \ElasticScoutDriverPlus\CustomSearch
 * @covers \ElasticScoutDriverPlus\Decorators\EngineDecorator
 * @covers \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
 * @covers \ElasticScoutDriverPlus\Builders\ParameterizedQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\MatchPhrasePrefixQueryBuilder
 *
 * @uses   \ElasticScoutDriverPlus\Builders\QueryParameters\Transformers\GroupedArrayTransformer
 * @uses   \ElasticScoutDriverPlus\Builders\QueryParameters\Validators\AllOfValidator
 * @uses   \ElasticScoutDriverPlus\Builders\QueryParameters\Collection
 * @uses   \ElasticScoutDriverPlus\Factories\LazyModelFactory
 * @uses   \ElasticScoutDriverPlus\Factories\SearchResultFactory
 * @uses   \ElasticScoutDriverPlus\Match
 * @uses   \ElasticScoutDriverPlus\SearchResult
 */
final class MatchPhrasePrefixSearchTest extends TestCase
{
    public function test_models_can_be_found_using_field_and_text(): void
    {
        // additional mixin
        factory(Book::class)
            ->state('belongs_to_author')
            ->create(['title' => 'the first book']);

        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['title' => 'the second book']);

        $found = Book::matchPhrasePrefixSearch()
            ->field('title')
            ->query('second bo')
            ->execute();

        $this->assertCount(1, $found->models());
        $this->assertEquals($target->toArray(), $found->models()->first()->toArray());
    }
}
