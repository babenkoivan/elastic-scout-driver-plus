<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration\Queries;

use ElasticScoutDriverPlus\Factories\QueryFactory as Query;
use ElasticScoutDriverPlus\Tests\App\Book;
use ElasticScoutDriverPlus\Tests\Integration\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\RangeQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
 * @covers \ElasticScoutDriverPlus\Engine
 */
final class RangeQueryTest extends TestCase
{
    public function test_models_can_be_found_using_field_and_gt(): void
    {
        // additional mixin
        factory(Book::class)
            ->state('belongs_to_author')
            ->create(['price' => 100]);

        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['price' => 200]);

        $found = Book::searchRequest()
            ->query(
                Query::range()
                    ->field('price')
                    ->gt(100)
            )
            ->execute();

        $this->assertFoundModel($target, $found);
    }

    public function test_models_can_be_found_using_field_and_lt_and_format(): void
    {
        // additional mixin
        factory(Book::class)
            ->state('belongs_to_author')
            ->create(['published' => '2020-10-18']);

        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['published' => '2010-06-17']);

        $found = Book::searchRequest()
            ->query(
                Query::range()
                    ->field('published')
                    ->lt('2020')
                    ->format('yyyy')
            )
            ->execute();

        $this->assertFoundModel($target, $found);
    }
}
