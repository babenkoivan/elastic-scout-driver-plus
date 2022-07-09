<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Integration\Queries;

use Elastic\Adapter\Search\Suggestion;
use Elastic\ScoutDriverPlus\Support\Query;
use Elastic\ScoutDriverPlus\Tests\App\Book;
use Elastic\ScoutDriverPlus\Tests\Integration\TestCase;

/**
 * @covers \Elastic\ScoutDriverPlus\Builders\MatchNoneQueryBuilder
 * @covers \Elastic\ScoutDriverPlus\Engine
 * @covers \Elastic\ScoutDriverPlus\Factories\LazyModelFactory
 * @covers \Elastic\ScoutDriverPlus\Factories\ModelFactory
 * @covers \Elastic\ScoutDriverPlus\Support\Query
 *
 * @uses   \Elastic\ScoutDriverPlus\Builders\DatabaseQueryBuilder
 * @uses   \Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder
 * @uses   \Elastic\ScoutDriverPlus\Decorators\Hit
 * @uses   \Elastic\ScoutDriverPlus\Decorators\SearchResult
 * @uses   \Elastic\ScoutDriverPlus\Decorators\Suggestion
 * @uses   \Elastic\ScoutDriverPlus\Factories\DocumentFactory
 * @uses   \Elastic\ScoutDriverPlus\Factories\ParameterFactory
 * @uses   \Elastic\ScoutDriverPlus\Factories\RoutingFactory
 * @uses   \Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection
 * @uses   \Elastic\ScoutDriverPlus\Searchable
 */
final class MatchNoneQueryTest extends TestCase
{
    public function test_none_models_can_be_found(): void
    {
        factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create();

        $found = Book::searchQuery(Query::matchNone())->execute();

        $this->assertSame(0, $found->total());
    }

    public function test_terms_can_be_suggested(): void
    {
        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['title' => 'world']);

        $found = Book::searchQuery(Query::matchNone())
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
