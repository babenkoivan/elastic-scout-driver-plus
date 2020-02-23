<?php
declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration\Factories;

use ElasticAdapter\Search\SearchResponse;
use ElasticScoutDriverPlus\Factories\LazyModelFactory;
use ElasticScoutDriverPlus\Tests\App\Author;
use ElasticScoutDriverPlus\Tests\App\Book;
use ElasticScoutDriverPlus\Tests\Integration\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @covers \ElasticScoutDriverPlus\Factories\LazyModelFactory
 */
final class LazyModelFactoryTest extends TestCase
{
    public function test_null_value_is_returned_when_trying_to_make_model_from_empty_search_response(): void
    {
        $factory = new LazyModelFactory(new Book(), new SearchResponse([
            'hits' => [
                'total' => ['value' => 0],
                'hits' => [],
            ]
        ]));

        $this->assertNull($factory->makeById(123));
    }

    public function test_model_can_be_lazy_made_from_not_empty_search_response(): void
    {
        $target = factory(Book::class, rand(2, 10))->create([
            'author_id' => factory(Author::class)->create()->getKey(),
        ]);

        DB::enableQueryLog();

        $factory = new LazyModelFactory(new Book(), new SearchResponse([
            'hits' => [
                'total' => ['value' => $target->count()],
                'hits' => $target->map(function (Model $model) {
                    return [
                        '_id' => (string)$model->getKey(),
                        '_source' => [],
                    ];
                })->all(),
            ]
        ]));

        // assert that related to search response models are returned
        $target->each(function (Model $model) use ($factory) {
            $this->assertEquals($model->toArray(), $factory->makeById($model->getKey())->toArray());
        });

        // assert that the only one query to the database is made
        $this->assertCount(1, DB::getQueryLog());
    }
}
