<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration\Queries;

use ElasticScoutDriverPlus\Support\Query;
use ElasticScoutDriverPlus\Tests\App\Book;
use ElasticScoutDriverPlus\Tests\Integration\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\Engine
 * @covers \ElasticScoutDriverPlus\Factories\LazyModelFactory
 * @covers \ElasticScoutDriverPlus\Support\Query
 */
final class CustomQueryTest extends TestCase
{
    public function test_models_can_be_found_using_custom_query(): void
    {
        // custom query
        Query::macro('tagged', static function () {
            return Query::exists()->field('tags');
        });

        // additional mixin
        factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create(['tags' => null]);

        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['tags' => ['new']]);

        $found = Book::searchQuery(Query::tagged())->execute();

        $this->assertFoundModel($target, $found);
    }
}
