<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Integration\Factories;

use Elastic\ScoutDriverPlus\Builders\DatabaseQueryBuilder;
use Elastic\ScoutDriverPlus\Factories\LazyModelFactory;
use Elastic\ScoutDriverPlus\Tests\App\Author;
use Elastic\ScoutDriverPlus\Tests\App\Book;
use Elastic\ScoutDriverPlus\Tests\Integration\TestCase;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @covers \Elastic\ScoutDriverPlus\Factories\LazyModelFactory
 *
 * @uses   \Elastic\ScoutDriverPlus\Builders\DatabaseQueryBuilder
 * @uses   \Elastic\ScoutDriverPlus\Engine
 * @uses   \Elastic\ScoutDriverPlus\Factories\DocumentFactory
 * @uses   \Elastic\ScoutDriverPlus\Factories\RoutingFactory
 * @uses   \Elastic\ScoutDriverPlus\Searchable
 */
final class LazyModelFactoryTest extends TestCase
{
    public function test_null_is_returned_when_document_ids_are_empty(): void
    {
        $model = factory(Author::class)->create();
        $databaseQueryBuilder = new DatabaseQueryBuilder($model);
        $factory = new LazyModelFactory([$model->searchableAs() => $databaseQueryBuilder], []);

        $this->assertNull(
            $factory->makeFromIndexNameAndDocumentId(
                $model->searchableAs(),
                (string)$model->getScoutKey()
            )
        );
    }

    public function test_null_is_returned_when_database_query_builder_can_not_be_resolved(): void
    {
        $model = factory(Author::class)->create();
        $factory = new LazyModelFactory([], [$model->searchableAs() => [(string)$model->getScoutKey()]]);

        $this->assertNull(
            $factory->makeFromIndexNameAndDocumentId(
                $model->searchableAs(),
                (string)$model->getScoutKey()
            )
        );
    }

    public function test_models_are_returned_when_document_ids_are_not_empty(): void
    {
        $author = factory(Author::class)->create();
        $book = factory(Book::class)->create(['author_id' => $author->getKey()]);
        $models = collect([$author, $book]);

        $databaseQueryBuilders = $models->mapWithKeys(
            static fn (Model $model) => [$model->searchableAs() => new DatabaseQueryBuilder($model)]
        )->all();

        $documentIds = $models->mapWithKeys(
            static fn (Model $model) => [$model->searchableAs() => [(string)$model->getScoutKey()]]
        )->all();

        $factory = new LazyModelFactory($databaseQueryBuilders, $documentIds);

        /** @var Connection $connection */
        $connection = DB::connection();
        $connection->enableQueryLog();

        // assert that related to search response models are returned
        $models->each(function (Model $expected) use ($factory) {
            /** @var Author|Book $expected */
            /** @var Author|Book $actual */
            $actual = $factory->makeFromIndexNameAndDocumentId(
                $expected->searchableAs(),
                (string)$expected->getScoutKey()
            );

            $this->assertNotNull($actual);
            $this->assertEquals($expected->toArray(), $actual->toArray());
        });

        // assert that only one query per index is made
        $this->assertCount($models->count(), $connection->getQueryLog());
    }
}
