<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Integration\Factories;

use Elastic\Adapter\Search\SearchResult;
use Elastic\ScoutDriverPlus\Factories\LazyModelFactory;
use Elastic\ScoutDriverPlus\Support\ModelScope;
use Elastic\ScoutDriverPlus\Tests\App\Author;
use Elastic\ScoutDriverPlus\Tests\App\Book;
use Elastic\ScoutDriverPlus\Tests\Integration\TestCase;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;

/**
 * @covers \Elastic\ScoutDriverPlus\Factories\LazyModelFactory
 *
 * @uses   \Elastic\ScoutDriverPlus\Engine
 * @uses   \Elastic\ScoutDriverPlus\Factories\DocumentFactory
 * @uses   \Elastic\ScoutDriverPlus\Factories\RoutingFactory
 * @uses   \Elastic\ScoutDriverPlus\Searchable
 * @uses   \Elastic\ScoutDriverPlus\Support\ModelScope
 */
final class LazyModelFactoryTest extends TestCase
{
    public function test_null_value_is_returned_when_trying_to_make_model_from_empty_search_result(): void
    {
        $model = new Book();

        $factory = new LazyModelFactory(
            new SearchResult([
                'hits' => [
                    'total' => ['value' => 0],
                    'hits' => [],
                ],
            ]),
            new ModelScope(get_class($model))
        );

        $this->assertNull($factory->makeByIndexNameAndDocumentId($model->searchableAs(), '123'));
    }

    public function test_models_can_be_lazy_made_from_not_empty_search_result(): void
    {
        $author = factory(Author::class)->create();
        $book = factory(Book::class)->create(['author_id' => $author->getKey()]);

        $models = collect([$author, $book]);

        $modelScope = new ModelScope(Author::class);
        $modelScope->push(Book::class);

        /** @var Connection $connection */
        $connection = DB::connection();
        $connection->enableQueryLog();

        $factory = new LazyModelFactory(
            new SearchResult([
                'hits' => [
                    'total' => ['value' => $models->count()],
                    'hits' => $models->map(static fn ($model) => [
                        '_id' => (string)$model->getKey(),
                        '_index' => $model->searchableAs(),
                        '_source' => [],
                    ])->all(),
                ],
            ]),
            $modelScope
        );

        // assert that related to search response models are returned
        $models->each(function ($expected) use ($factory) {
            /** @var Author|Book $expected */
            /** @var Author|Book $actual */
            $actual = $factory->makeByIndexNameAndDocumentId(
                $expected->searchableAs(),
                (string)$expected->getScoutKey()
            );

            $this->assertNotNull($actual);
            $this->assertEquals($expected->toArray(), $actual->toArray());
        });

        // assert that only one query per index is made
        $this->assertCount(2, $connection->getQueryLog());
    }
}
