<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration\Factories;

use ElasticAdapter\Search\SearchResponse;
use ElasticScoutDriverPlus\Factories\LazyModelFactory;
use ElasticScoutDriverPlus\Support\ModelScope;
use ElasticScoutDriverPlus\Tests\App\Author;
use ElasticScoutDriverPlus\Tests\App\Book;
use ElasticScoutDriverPlus\Tests\Integration\TestCase;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;

/**
 * @covers \ElasticScoutDriverPlus\Factories\LazyModelFactory
 *
 * @uses   \ElasticScoutDriverPlus\Engine
 * @uses   \ElasticScoutDriverPlus\Factories\DocumentFactory
 * @uses   \ElasticScoutDriverPlus\Factories\RoutingFactory
 * @uses   \ElasticScoutDriverPlus\Searchable
 * @uses   \ElasticScoutDriverPlus\Support\ModelScope
 */
final class LazyModelFactoryTest extends TestCase
{
    public function test_null_value_is_returned_when_trying_to_make_model_from_empty_search_response(): void
    {
        $model = new Book();

        $factory = new LazyModelFactory(new SearchResponse([
            'hits' => [
                'total' => ['value' => 0],
                'hits' => [],
            ],
        ]), new ModelScope(get_class($model)));

        $this->assertNull($factory->makeByIndexNameAndDocumentId($model->searchableAs(), '123'));
    }

    public function test_models_can_be_lazy_made_from_not_empty_search_response(): void
    {
        $author = factory(Author::class)->create();
        $book = factory(Book::class)->create(['author_id' => $author->getKey()]);

        $models = collect([$author, $book]);

        $modelScope = new ModelScope(Author::class);
        $modelScope->push(Book::class);

        /** @var Connection $connection */
        $connection = DB::connection();
        $connection->enableQueryLog();

        $factory = new LazyModelFactory(new SearchResponse([
            'hits' => [
                'total' => ['value' => $models->count()],
                'hits' => $models->map(static function ($model) {
                    /** @var Author|Book $model */
                    return [
                        '_id' => (string)$model->getKey(),
                        '_index' => $model->searchableAs(),
                        '_source' => [],
                    ];
                })->all(),
            ],
        ]), $modelScope);

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
