<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration;

use ElasticScoutDriverPlus\Tests\App\Book;

/**
 * @covers \ElasticScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\RegexpQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
 * @covers \ElasticScoutDriverPlus\QueryDsl
 * @covers \ElasticScoutDriverPlus\Engine
 *
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Collection
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Transformers\GroupedArrayTransformer
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Validators\AllOfValidator
 * @uses   \ElasticScoutDriverPlus\Factories\LazyModelFactory
 * @uses   \ElasticScoutDriverPlus\Factories\SearchResultFactory
 * @uses   \ElasticScoutDriverPlus\QueryMatch
 * @uses   \ElasticScoutDriverPlus\SearchResult
 * @uses   \ElasticScoutDriverPlus\Support\ModelScope
 */
final class RegexpSearchTest extends TestCase
{
    public function test_models_can_be_found_using_field_and_value(): void
    {
        // additional mixin
        factory(Book::class)
            ->state('belongs_to_author')
            ->create(['title' => 'Number one']);

        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['title' => 'Number two']);

        $found = Book::regexpSearch()
            ->field('title')
            ->value('t.o')
            ->execute();

        $this->assertCount(1, $found->models());
        $this->assertEquals($target->toArray(), $found->models()->first()->toArray());
    }
}
