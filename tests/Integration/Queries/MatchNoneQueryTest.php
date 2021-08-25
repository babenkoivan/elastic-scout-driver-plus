<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration\Queries;

use ElasticAdapter\Search\Suggestion;
use ElasticScoutDriverPlus\Factories\QueryFactory as Query;
use ElasticScoutDriverPlus\Tests\App\Book;
use ElasticScoutDriverPlus\Tests\Integration\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\Builders\MatchNoneQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
 * @covers \ElasticScoutDriverPlus\Engine
 * @covers \ElasticScoutDriverPlus\Factories\LazyModelFactory
 */
final class MatchNoneQueryTest extends TestCase
{
    public function test_none_models_can_be_found(): void
    {
        factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create();

        $found = Book::searchRequest()
            ->query(Query::matchNone())
            ->execute();

        $this->assertSame(0, $found->total());
    }

    public function test_terms_can_be_suggested(): void
    {
        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['title' => 'world']);

        $found = Book::searchRequest()
            ->query(Query::matchNone())
            ->suggest('title', [
                'text' => 'word',
                'term' => [
                    'field' => 'title',
                ],
            ])
            ->execute();

        /** @var Suggestion $suggestion */
        $suggestion = $found->suggestions()->get('title')->first();

        $this->assertSame('word', $suggestion->text());
        $this->assertSame($target->title, $suggestion->options()->first()['text']);
        $this->assertSame(0, $found->total());
    }
}