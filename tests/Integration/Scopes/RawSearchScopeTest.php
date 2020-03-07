<?php
declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration\Scopes;

use ElasticScoutDriverPlus\Output\Match;
use ElasticScoutDriverPlus\Output\SearchResult;
use ElasticScoutDriverPlus\Tests\App\Book;
use ElasticScoutDriverPlus\Tests\Integration\TestCase;
use stdClass;

/**
 * @covers \ElasticScoutDriverPlus\Scopes\RawSearchScope
 * @covers \ElasticScoutDriverPlus\Decorators\EngineDecorator
 * @uses   \ElasticScoutDriverPlus\Builders\AbstractSearchRequestBuilder
 * @uses   \ElasticScoutDriverPlus\Builders\RawSearchRequestBuilder
 * @uses   \ElasticScoutDriverPlus\Factories\LazyModelFactory
 * @uses   \ElasticScoutDriverPlus\Factories\SearchResultFactory
 * @uses   \ElasticScoutDriverPlus\Output\Match
 * @uses   \ElasticScoutDriverPlus\Output\SearchResult
 */
final class RawSearchScopeTest extends TestCase
{
    public function test_models_can_be_found_using_raw_query(): void
    {
        // additional mixin
        factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create();

        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['title' => uniqid('test')]);

        $found = Book::rawSearchQuery()
            ->query([
                'match' => [
                    'title' => $target->title
                ]
            ])
            ->execute();

        $this->assertCount(1, $found->models());
        $this->assertEquals($target->toArray(), $found->models()->first()->toArray());
    }

    public function test_models_can_be_found_using_raw_query_and_highlight(): void
    {
        $target = factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create(['title' => uniqid('test')]);

        $found = Book::rawSearchQuery()
            ->query([
                'match' => [
                    'title' => $target->first()->title
                ]
            ])
            ->highlight('title')
            ->execute();

        $this->assertCount($target->count(), $found->models());
        $this->assertEquals($target->toArray(), $found->models()->toArray());

        $found->matches()->each(function (Match $match) {
            $this->assertSame(
                ['title' => ['<em>'.$match->model()->title.'</em>']],
                $match->highlight()->getRaw()
            );
        });
    }

    public function test_models_can_be_found_using_raw_query_and_sort(): void
    {
        $target = factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create();

        $found = Book::rawSearchQuery()
            ->query(['match_all' => new stdClass()])
            ->sort('price')
            ->execute();

        $this->assertCount($target->count(), $found->models());
        $this->assertEquals($target->sortBy('price')->values()->toArray(), $found->models()->toArray());
    }

    public function test_models_can_be_found_using_raw_query_and_from(): void
    {
        factory(Book::class, 10)
            ->state('belongs_to_author')
            ->create();

        $found = Book::rawSearchQuery()
            ->query(['match_all' => new stdClass()])
            ->from(5)
            ->execute();

        $this->assertCount(5, $found->models());
        $this->assertSame(10, $found->total());
    }

    public function test_models_can_be_found_using_raw_query_and_size(): void
    {
        factory(Book::class, 4)
            ->state('belongs_to_author')
            ->create();

        $found = Book::rawSearchQuery()
            ->query(['match_all' => new stdClass()])
            ->size(2)
            ->execute();

        $this->assertCount(2, $found->models());
        $this->assertSame(4, $found->total());
    }

    public function test_search_result_can_be_retrieved(): void
    {
        factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create();

        $found = Book::rawSearchQuery()
            ->query(['match_all' => new stdClass()])
            ->execute();

        $this->assertInstanceOf(SearchResult::class, $found);
    }

    public function test_raw_result_can_be_retrieved(): void
    {
        factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create();

        $found = Book::rawSearchQuery()
            ->query(['match_all' => new stdClass()])
            ->raw();

        $this->assertIsArray($found);
    }
}
