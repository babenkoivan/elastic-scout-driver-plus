<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration;

use ElasticScoutDriverPlus\Tests\App\Book;

/**
 * @covers \ElasticScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\FuzzyQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
 * @covers \ElasticScoutDriverPlus\QueryDsl
 * @covers \ElasticScoutDriverPlus\Decorators\EngineDecorator
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
final class FuzzySearchTest extends TestCase
{
    public function test_models_can_be_found_using_field_and_value(): void
    {
        // additional mixin
        factory(Book::class)
            ->state('belongs_to_author')
            ->create(['title' => 'The white book']);

        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['title' => 'The black book']);

        $found = Book::fuzzySearch()
            ->field('title')
            ->value('lack')
            ->execute();

        $this->assertCount(1, $found->models());
        $this->assertEquals($target->toArray(), $found->models()->first()->toArray());
    }

    public function test_models_can_be_found_using_field_and_value_and_transpositions(): void
    {
        $target = factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create(['title' => 'The book']);

        $found = Book::fuzzySearch()
            ->field('title')
            ->value('boko')
            ->transpositions(true)
            ->sort('author_id')
            ->execute();

        $this->assertCount($target->count(), $found->models());
        $this->assertEquals($target->toArray(), $found->models()->toArray());
    }
}
