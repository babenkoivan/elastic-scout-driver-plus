<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration\Queries;

use ElasticScoutDriverPlus\Factories\QueryFactory as Query;
use ElasticScoutDriverPlus\Tests\App\Book;
use ElasticScoutDriverPlus\Tests\Integration\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\RegexpQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
 * @covers \ElasticScoutDriverPlus\Engine
 */
final class RegexpQueryTest extends TestCase
{
    public function test_models_can_be_found_using_field_and_value(): void
    {
        // additional mixin
        factory(Book::class)
            ->state('belongs_to_author')
            ->create(['title' => 'Number one']);

        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['title' => 'Number two']);

        $found = Book::searchRequest()
            ->query(
                Query::regexp()
                    ->field('title')
                    ->value('t.o')
            )
            ->execute();

        $this->assertFoundModel($target, $found);
    }
}
