<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Integration\Queries;

use Elastic\ScoutDriverPlus\Builders\NestedQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\TermQueryBuilder;
use Elastic\ScoutDriverPlus\Decorators\Hit;
use Elastic\ScoutDriverPlus\Support\Query;
use Elastic\ScoutDriverPlus\Tests\App\Author;
use Elastic\ScoutDriverPlus\Tests\App\Book;
use Elastic\ScoutDriverPlus\Tests\Integration\TestCase;

/**
 * @covers \Elastic\ScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder
 * @covers \Elastic\ScoutDriverPlus\Builders\NestedQueryBuilder
 * @covers \Elastic\ScoutDriverPlus\Engine
 * @covers \Elastic\ScoutDriverPlus\Factories\LazyModelFactory
 * @covers \Elastic\ScoutDriverPlus\Factories\ModelFactory
 * @covers \Elastic\ScoutDriverPlus\Support\Query
 *
 * @uses   \Elastic\ScoutDriverPlus\Builders\DatabaseQueryBuilder
 * @uses   \Elastic\ScoutDriverPlus\Builders\MatchQueryBuilder
 * @uses   \Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder
 * @uses   \Elastic\ScoutDriverPlus\Builders\TermQueryBuilder
 * @uses   \Elastic\ScoutDriverPlus\Decorators\Hit
 * @uses   \Elastic\ScoutDriverPlus\Decorators\SearchResult
 * @uses   \Elastic\ScoutDriverPlus\Factories\DocumentFactory
 * @uses   \Elastic\ScoutDriverPlus\Factories\ParameterFactory
 * @uses   \Elastic\ScoutDriverPlus\Factories\RoutingFactory
 * @uses   \Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection
 * @uses   \Elastic\ScoutDriverPlus\QueryParameters\Shared\FieldParameter
 * @uses   \Elastic\ScoutDriverPlus\QueryParameters\Shared\QueryStringParameter
 * @uses   \Elastic\ScoutDriverPlus\QueryParameters\Shared\ValueParameter
 * @uses   \Elastic\ScoutDriverPlus\QueryParameters\Transformers\FlatArrayTransformer
 * @uses   \Elastic\ScoutDriverPlus\QueryParameters\Transformers\GroupedArrayTransformer
 * @uses   \Elastic\ScoutDriverPlus\QueryParameters\Validators\AllOfValidator
 * @uses   \Elastic\ScoutDriverPlus\Searchable
 */
final class NestedQueryTest extends TestCase
{
    public function test_models_can_be_found_using_path_and_query(): void
    {
        // additional mixin
        factory(Book::class, rand(2, 10))->create([
            'author_id' => factory(Author::class)->create([
                'name' => 'John',
            ]),
        ]);

        $target = factory(Book::class)->create([
            'author_id' => factory(Author::class)->create([
                'name' => 'Steven',
            ]),
        ]);

        $query = Query::nested()
            ->path('author')
            ->query(
                Query::match()
                    ->field('author.name')
                    ->query('Steven')
            )
            ->innerHits(['name' => 'authors']);

        $found = Book::searchQuery($query)->execute();

        $this->assertFoundModel($target, $found);

        /** @var Hit $hit */
        foreach ($found->hits() as $hit) {
            $this->assertCount(1, $hit->innerHits()->get('authors'));
        }
    }

    public function test_models_can_be_found_using_path_and_query_builder(): void
    {
        // additional mixin
        factory(Book::class)->create([
            'author_id' => factory(Author::class)->create([
                'phone_number' => '202-555-0165',
            ]),
        ]);

        $target = factory(Book::class)->create([
            'author_id' => factory(Author::class)->create([
                'phone_number' => '202-555-0139',
            ]),
        ]);

        $builder = (new NestedQueryBuilder())
            ->path('author')
            ->query(
                (new TermQueryBuilder())
                    ->field('author.phone_number')
                    ->value('202-555-0139')
            );

        $found = Book::searchQuery($builder)->execute();

        $this->assertFoundModel($target, $found);
    }
}
