<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration;

use ElasticScoutDriverPlus\Tests\App\Book;

/**
 * @covers \ElasticScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\TermQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
 * @covers \ElasticScoutDriverPlus\CustomSearch
 * @covers \ElasticScoutDriverPlus\Decorators\EngineDecorator
 *
 * @uses   \ElasticScoutDriverPlus\Builders\QueryParameters\Collection
 * @uses   \ElasticScoutDriverPlus\Builders\QueryParameters\Transformers\GroupedArrayTransformer
 * @uses   \ElasticScoutDriverPlus\Builders\QueryParameters\Validators\AllOfValidator
 * @uses   \ElasticScoutDriverPlus\Factories\LazyModelFactory
 * @uses   \ElasticScoutDriverPlus\Factories\SearchResultFactory
 * @uses   \ElasticScoutDriverPlus\Match
 * @uses   \ElasticScoutDriverPlus\SearchResult
 * @uses   \ElasticScoutDriverPlus\Support\ModelScope
 */
final class TermSearchTest extends TestCase
{
    public function test_models_can_be_found_using_field_and_value(): void
    {
        // additional mixin
        factory(Book::class)
            ->state('belongs_to_author')
            ->create(['price' => 150]);

        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['price' => 300]);

        $found = Book::termSearch()
            ->field('price')
            ->value('300')
            ->execute();

        $this->assertCount(1, $found->models());
        $this->assertEquals($target->toArray(), $found->models()->first()->toArray());
    }
}
