<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration\Queries;

use ElasticScoutDriverPlus\Factories\QueryFactory as Query;
use ElasticScoutDriverPlus\Tests\App\Book;
use ElasticScoutDriverPlus\Tests\Integration\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\ExistsQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
 * @covers \ElasticScoutDriverPlus\Engine
 */
final class ExistsQueryTest extends TestCase
{
    public function test_models_with_existing_fields_can_be_found(): void
    {
        // additional mixin
        factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create(['description' => null]);

        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['description' => 'The best book ever']);

        $found = Book::searchRequest()
            ->query(Query::exists()->field('description'))
            ->execute();

        $this->assertFoundModel($target, $found);
    }
}
