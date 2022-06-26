<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Integration\Builders;

use Elastic\ScoutDriverPlus\Builders\DatabaseQueryBuilder;
use Elastic\ScoutDriverPlus\Tests\App\Author;
use Elastic\ScoutDriverPlus\Tests\App\Book;
use Elastic\ScoutDriverPlus\Tests\Integration\TestCase;
use Illuminate\Database\Eloquent\Builder;

/**
 * @covers \Elastic\ScoutDriverPlus\Builders\DatabaseQueryBuilder
 *
 * @uses \Elastic\ScoutDriverPlus\Engine
 * @uses \Elastic\ScoutDriverPlus\Factories\DocumentFactory
 * @uses \Elastic\ScoutDriverPlus\Factories\RoutingFactory
 * @uses \Elastic\ScoutDriverPlus\Searchable
 */
final class DatabaseQueryBuilderTest extends TestCase
{
    public function test_query_with_model_that_supports_soft_deletes(): void
    {
        $models = factory(Book::class, 5)->state('belongs_to_author')->create();
        $ids = $models->pluck('id')->all();
        $query = (new DatabaseQueryBuilder($models->first()))->buildQuery($ids);

        // delete one model
        $models->first()->delete();

        // the deleted model is present in the result
        $this->assertEquals($ids, $query->pluck('id')->all());
    }

    public function test_query_with_model_that_does_not_support_soft_deletes(): void
    {
        $models = factory(Author::class, 5)->create();
        $ids = $models->pluck('id')->all();
        $query = (new DatabaseQueryBuilder($models->first()))->buildQuery($ids);

        // delete one model
        $models->first()->delete();

        // the deleted model is not present in the result
        $this->assertEquals(array_slice($ids, 1), $query->pluck('id')->all());
    }

    public function test_query_with_relations(): void
    {
        $model = factory(Book::class)->state('belongs_to_author')->create();
        $query = (new DatabaseQueryBuilder($model))->with(['author'])->buildQuery([$model->id]);

        $this->assertTrue($query->first()->relationLoaded('author'));
    }

    public function test_query_with_callback(): void
    {
        $models = factory(Author::class, 5)->create();

        $sourceIds = $models->pluck('id')->all();
        $targetIds = array_slice($sourceIds, 1, 3);

        $callback = static function (Builder $query) use ($targetIds) {
            $query->whereIn('id', $targetIds);
        };

        $query = (new DatabaseQueryBuilder($models->first()))->callback($callback)->buildQuery($sourceIds);

        $this->assertEquals($targetIds, $query->pluck('id')->all());
    }
}
