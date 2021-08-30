<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration\Queries;

use ElasticScoutDriverPlus\Support\Query;
use ElasticScoutDriverPlus\Tests\App\Book;
use ElasticScoutDriverPlus\Tests\Integration\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\TermsQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
 * @covers \ElasticScoutDriverPlus\Engine
 */
final class TermsQueryTest extends TestCase
{
    public function test_models_can_be_found_using_terms(): void
    {
        // additional mixin
        factory(Book::class)
            ->state('belongs_to_author')
            ->create(['tags' => ['bestseller', 'discount']]);

        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['tags' => ['available', 'new']]);

        $found = Book::searchRequest()
            ->query(Query::terms()->terms('tags', ['available', 'new']))
            ->execute();

        $this->assertFoundModel($target, $found);
    }
}
