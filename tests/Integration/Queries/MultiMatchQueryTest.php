<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration\Queries;

use ElasticScoutDriverPlus\Support\Query;
use ElasticScoutDriverPlus\Tests\App\Book;
use ElasticScoutDriverPlus\Tests\Integration\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\MultiMatchQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
 * @covers \ElasticScoutDriverPlus\Engine
 * @covers \ElasticScoutDriverPlus\Factories\LazyModelFactory
 */
final class MultiMatchQueryTest extends TestCase
{
    public function test_models_can_be_found_using_fields_and_text(): void
    {
        // additional mixin
        factory(Book::class)
            ->state('belongs_to_author')
            ->create([
                'title' => 'mixin title',
                'description' => 'mixin description',
            ]);

        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create([
                'title' => 'foo',
                'description' => 'bar',
            ]);

        $found = Book::searchRequest()
            ->query(
                Query::multiMatch()
                    ->fields(['title', 'description'])
                    ->query('foo bar')
            )
            ->execute();

        $this->assertFoundModel($target, $found);
    }
}
