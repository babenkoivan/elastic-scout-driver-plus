<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration;

use ElasticScoutDriverPlus\Tests\App\Author;
use ElasticScoutDriverPlus\Tests\App\Book;

/**
 * @covers \ElasticScoutDriverPlus\CustomSearch
 * @covers \ElasticScoutDriverPlus\Decorators\EngineDecorator
 * @covers \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
 * @covers \ElasticScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\NestedQueryBuilder
 *
 * @uses   \ElasticScoutDriverPlus\Builders\QueryParameters\Transformers\FlatArrayTransformer
 * @uses   \ElasticScoutDriverPlus\Builders\QueryParameters\Validators\AllOfValidator
 * @uses   \ElasticScoutDriverPlus\Builders\QueryParameters\Collection
 * @uses   \ElasticScoutDriverPlus\Factories\LazyModelFactory
 * @uses   \ElasticScoutDriverPlus\Factories\SearchResultFactory
 * @uses   \ElasticScoutDriverPlus\Match
 * @uses   \ElasticScoutDriverPlus\SearchResult
 */
final class NestedSearchTest extends TestCase
{
    public function test_models_can_be_found_using_path_and_query(): void
    {
        // additional mixin
        factory(Book::class, rand(2, 10))->create([
            'author_id' => factory(Author::class)->create([
                'name' => 'John',
            ]),
        ]);

        $target = factory(Book::class)->create([
            'author_id' => factory(Author::class)->create([
                'name' => 'Steven',
            ]),
        ]);

        $found = Book::nestedSearch()
            ->path('author')
            ->query([
                'match' => [
                    'author.name' => 'Steven',
                ],
            ])
            ->execute();

        $this->assertCount(1, $found->models());
        $this->assertEquals($target->toArray(), $found->models()->first()->toArray());
    }
}
