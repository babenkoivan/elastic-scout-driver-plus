<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration\Queries;

use ElasticScoutDriverPlus\Support\Query;
use ElasticScoutDriverPlus\Tests\App\Book;
use ElasticScoutDriverPlus\Tests\Integration\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\TermQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
 * @covers \ElasticScoutDriverPlus\Engine
 */
final class TermQueryTest extends TestCase
{
    public function test_models_can_be_found_using_field_and_value(): void
    {
        // additional mixin
        factory(Book::class)
            ->state('belongs_to_author')
            ->create(['price' => 150]);

        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['price' => 300]);

        $found = Book::searchRequest()
            ->query(
                Query::term()
                    ->field('price')
                    ->value('300')
            )
            ->execute();

        $this->assertFoundModel($target, $found);
    }
}
