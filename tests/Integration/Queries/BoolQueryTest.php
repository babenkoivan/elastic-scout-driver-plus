<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration\Queries;

use Carbon\Carbon;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use ElasticScoutDriverPlus\Builders\RangeQueryBuilder;
use ElasticScoutDriverPlus\Support\Query;
use ElasticScoutDriverPlus\Tests\App\Author;
use ElasticScoutDriverPlus\Tests\App\Book;
use ElasticScoutDriverPlus\Tests\Integration\TestCase;
use const SORT_NUMERIC;

/**
 * @covers \ElasticScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\BoolQueryBuilder
 * @covers \ElasticScoutDriverPlus\Engine
 * @covers \ElasticScoutDriverPlus\Factories\LazyModelFactory
 * @covers \ElasticScoutDriverPlus\Support\Query
 *
 * @uses   \ElasticScoutDriverPlus\Builders\MatchAllQueryBuilder
 * @uses   \ElasticScoutDriverPlus\Builders\MatchQueryBuilder
 * @uses   \ElasticScoutDriverPlus\Builders\RangeQueryBuilder
 * @uses   \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
 * @uses   \ElasticScoutDriverPlus\Builders\TermQueryBuilder
 * @uses   \ElasticScoutDriverPlus\Decorators\Hit
 * @uses   \ElasticScoutDriverPlus\Decorators\SearchResult
 * @uses   \ElasticScoutDriverPlus\Factories\DocumentFactory
 * @uses   \ElasticScoutDriverPlus\Factories\ParameterFactory
 * @uses   \ElasticScoutDriverPlus\Factories\RoutingFactory
 * @uses   \ElasticScoutDriverPlus\QueryParameters\ParameterCollection
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Shared\FieldParameter
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Shared\QueryStringParameter
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Shared\ValueParameter
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Transformers\FlatArrayTransformer
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Transformers\GroupedArrayTransformer
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Validators\AllOfValidator
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Validators\CompoundValidator
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Validators\OneOfValidator
 * @uses   \ElasticScoutDriverPlus\Searchable
 * @uses   \ElasticScoutDriverPlus\Support\Arr
 * @uses   \ElasticScoutDriverPlus\Support\ModelScope
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
        $source = collect(['2018-04-23', '2003-01-14', '2020-03-07'])->map(static function (string $published) {
            return factory(Book::class)
                ->state('belongs_to_author')
                ->create(['published' => Carbon::createFromFormat('Y-m-d', $published)]);
        });

        $target = $source->filter(static function (Book $model) {
            return $model->published->year > 2003;
        })->sortBy('id', SORT_NUMERIC);

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
        $this->app['config']->set('scout.soft_delete', true);

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
        $this->app['config']->set('scout.soft_delete', true);

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
        $this->app['config']->set('scout.soft_delete', true);

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
        $this->app['config']->set('scout.soft_delete', true);

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
