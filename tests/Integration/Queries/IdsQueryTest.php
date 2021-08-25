<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration\Queries;

use ElasticScoutDriverPlus\Factories\QueryFactory as Query;
use ElasticScoutDriverPlus\Tests\App\Book;
use ElasticScoutDriverPlus\Tests\Integration\TestCase;
use Illuminate\Database\Eloquent\Model;

/**
 * @covers \ElasticScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\IdsQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
 * @covers \ElasticScoutDriverPlus\Engine
 */
final class IdsQueryTest extends TestCase
{
    public function test_models_can_be_found_by_ids(): void
    {
        $models = collect(range(1, 10))->map(static function (int $i): Model {
            return factory(Book::class)
                ->state('belongs_to_author')
                ->create(['id' => $i]);
        });

        $target = $models->where('id', '>', 7)->sortBy('id', SORT_NUMERIC);

        $found = Book::searchRequest()
            ->query(Query::ids()->values(['8', '9', '10']))
            ->sort('id')
            ->execute();

        $this->assertFoundModels($target, $found);
    }
}
