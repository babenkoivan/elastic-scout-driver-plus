<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration\Queries;

use ElasticScoutDriverPlus\Support\Query;
use ElasticScoutDriverPlus\Tests\App\Book;
use ElasticScoutDriverPlus\Tests\Integration\TestCase;
use const SORT_NUMERIC;

/**
 * @covers \ElasticScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\FuzzyQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
 * @covers \ElasticScoutDriverPlus\Engine
 */
final class FuzzyQueryTest extends TestCase
{
    public function test_models_can_be_found_using_field_and_value(): void
    {
        // additional mixin
        factory(Book::class)
            ->state('belongs_to_author')
            ->create(['title' => 'The white book']);

        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['title' => 'The black book']);

        $found = Book::searchRequest()
            ->query(
                Query::fuzzy()
                    ->field('title')
                    ->value('lack')
            )
            ->execute();

        $this->assertFoundModel($target, $found);
    }

    public function test_models_can_be_found_using_field_and_value_and_transpositions(): void
    {
        $target = factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create(['title' => 'The book'])
            ->sortBy('id', SORT_NUMERIC);

        $found = Book::searchRequest()
            ->query(
                Query::fuzzy()
                    ->field('title')
                    ->value('boko')
                    ->transpositions(true)
            )
            ->sort('id')
            ->execute();

        $this->assertFoundModels($target, $found);
    }
}
