<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Integration\Queries;

use Carbon\Carbon;
use Elastic\ScoutDriverPlus\Builders\BoolQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\RangeQueryBuilder;
use Elastic\ScoutDriverPlus\Support\Query;
use Elastic\ScoutDriverPlus\Tests\App\Author;
use Elastic\ScoutDriverPlus\Tests\App\Book;
use Elastic\ScoutDriverPlus\Tests\Integration\TestCase;

use const SORT_NUMERIC;

/**
 * @covers \Elastic\ScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder
 * @covers \Elastic\ScoutDriverPlus\Builders\BoolQueryBuilder
 * @covers \Elastic\ScoutDriverPlus\Engine
 * @covers \Elastic\ScoutDriverPlus\Factories\LazyModelFactory
 * @covers \Elastic\ScoutDriverPlus\Factories\ModelFactory
 * @covers \Elastic\ScoutDriverPlus\Support\Query
 *
 * @uses   \Elastic\ScoutDriverPlus\Builders\DatabaseQueryBuilder
 * @uses   \Elastic\ScoutDriverPlus\Builders\MatchAllQueryBuilder
 * @uses   \Elastic\ScoutDriverPlus\Builders\MatchQueryBuilder
 * @uses   \Elastic\ScoutDriverPlus\Builders\RangeQueryBuilder
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
 * @uses   \Elastic\ScoutDriverPlus\QueryParameters\Validators\CompoundValidator
 * @uses   \Elastic\ScoutDriverPlus\QueryParameters\Validators\OneOfValidator
 * @uses   \Elastic\ScoutDriverPlus\Searchable
 * @uses   \Elastic\ScoutDriverPlus\Support\Arr
 */
final class BoolQueryTest extends TestCase
{
    public function test_models_can_be_found_using_must(): void
    {
        // additional mixin
        factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create();

        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['title' => uniqid('test')]);

        $query = Query::bool()->must(
            Query::match()
                ->field('title')
                ->query($target->title)
        );

        $found = Book::searchQuery($query)->execute();

        $this->assertFoundModel($target, $found);
    }

    public function test_models_can_be_found_using_must_not(): void
    {
        $mixin = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['title' => uniqid('test')]);

        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create();

        $query = Query::bool()->mustNot(
            Query::match()
                ->field('title')
                ->query($mixin->title)
        );

        $found = Book::searchQuery($query)->execute();

        $this->assertFoundModel($target, $found);
    }

    public function test_models_can_be_found_using_should(): void
    {
        $source = collect(['2018-04-23', '2003-01-14', '2020-03-07'])->map(
            static fn (string $published) => factory(Book::class)
                ->state('belongs_to_author')
                ->create(['published' => Carbon::createFromFormat('Y-m-d', $published)])
        );

        $target = $source->filter(
            static fn (Book $model) => $model->published->year > 2003
        )->sortBy('id', SORT_NUMERIC);

        $query = Query::bool()
            ->should(
                Query::term()
                    ->field('published')
                    ->value('2018-04-23')
            )
            ->should(
                Query::term()
                    ->field('published')
                    ->value('2020-03-07')
            );

        $found = Book::searchQuery($query)
            ->sort('id')
            ->execute();

        $this->assertFoundModels($target, $found);
    }

    public function test_models_can_be_found_using_filter(): void
    {
        // additional mixin
        factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create(['published' => Carbon::create(2010, 5, 10)]);

        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['published' => Carbon::create(2020, 6, 7)]);

        $query = Query::bool()->filter(
            Query::term()
                ->field('published')
                ->value('2020-06-07')
        );

        $found = Book::searchQuery($query)->execute();

        $this->assertFoundModel($target, $found);
    }

    public function test_not_trashed_models_can_be_found(): void
    {
        $this->config->set('scout.soft_delete', true);

        $source = factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create();

        $target = $source->first();

        $source->where('id', '!=', $target->id)->each(static function (Book $model) {
            $model->delete();
        });

        $query = Query::bool()->must(
            Query::matchAll()
        );

        $found = Book::searchQuery($query)->execute();

        $this->assertFoundModel($target, $found);
    }

    public function test_trashed_models_can_be_found(): void
    {
        $this->config->set('scout.soft_delete', true);

        $target = factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create()
            ->sortBy('id', SORT_NUMERIC);

        // soft delete some models
        $target->first()->delete();

        $query = Query::bool()
            ->must(Query::matchAll())
            ->withTrashed();

        $found = Book::searchQuery($query)
            ->sort('id')
            ->execute();

        $this->assertFoundModels($target, $found);
    }

    public function test_only_trashed_models_can_be_found(): void
    {
        $this->config->set('scout.soft_delete', true);

        $source = factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create();

        $target = $source->first();
        $target->delete();

        $query = Query::bool()
            ->must(Query::matchAll())
            ->onlyTrashed();

        $found = Book::searchQuery($query)->execute();

        $this->assertFoundModel($target, $found);
    }

    public function test_only_trashed_models_can_be_found_in_multiple_indices(): void
    {
        $this->config->set('scout.soft_delete', true);

        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create();

        $target->delete();

        $query = Query::bool()
            ->must(Query::matchAll())
            ->onlyTrashed();

        $found = Author::searchQuery($query)
            ->join(Book::class)
            ->execute();

        $this->assertFoundModel($target, $found);
    }

    public function test_models_can_be_found_in_multiple_indices(): void
    {
        // additional mixins
        factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create();

        $firstTarget = factory(Author::class)
            ->state('has_books')
            ->create(['name' => uniqid('author', true)]);

        $secondTarget = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['title' => uniqid('book', true)]);

        $query = Query::bool()
            ->should(
                Query::match()
                    ->field('name')
                    ->query($firstTarget->name)
            )
            ->should(
                Query::match()
                    ->field('title')
                    ->query($secondTarget->title)
            )
            ->minimumShouldMatch(1);

        $found = Author::searchQuery($query)
            ->join(Book::class)
            ->sort('_index')
            ->execute();

        $this->assertFoundModels(collect([$firstTarget, $secondTarget]), $found);
    }

    public function test_models_can_be_found_using_query_builder(): void
    {
        // additional mixin
        factory(Book::class, rand(2, 5))
            ->state('belongs_to_author')
            ->create(['published' => '2019-03-07']);

        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['published' => '2020-12-07']);

        $builder = (new BoolQueryBuilder())->must(
            (new RangeQueryBuilder())
                ->field('published')
                ->gte('2020')
                ->format('yyyy')
        );

        $found = Book::searchQuery($builder)->execute();

        $this->assertFoundModel($target, $found);
    }
}
