<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration\Queries;

use Carbon\Carbon;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use ElasticScoutDriverPlus\Builders\RangeQueryBuilder;
use ElasticScoutDriverPlus\Support\Query;
use ElasticScoutDriverPlus\Tests\App\Author;
use ElasticScoutDriverPlus\Tests\App\Book;
use ElasticScoutDriverPlus\Tests\Integration\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\BoolQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
 * @covers \ElasticScoutDriverPlus\Engine
 * @covers \ElasticScoutDriverPlus\Factories\LazyModelFactory
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

        $found = Book::searchRequest()
            ->query(
                Query::bool()->must(
                    Query::match()
                        ->field('title')
                        ->query($target->title)
                )
            )
            ->execute();

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

        $found = Book::searchRequest()
            ->query(
                Query::bool()->mustNot(
                    Query::match()
                        ->field('title')
                        ->query($mixin->title)
                )
            )
            ->execute();

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

        $found = Book::searchRequest()
            ->query(
                Query::bool()
                    ->should(
                        Query::term()
                            ->field('published')
                            ->value('2018-04-23')
                    )
                    ->should(
                        Query::term()
                            ->field('published')
                            ->value('2020-03-07')
                    )
            )
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

        $found = Book::searchRequest()
            ->query(
                Query::bool()->filter(
                    Query::term()
                        ->field('published')
                        ->value('2020-06-07')
                )
            )
            ->execute();

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

        $found = Book::searchRequest()
            ->query(
                Query::bool()->must(
                    Query::matchAll()
                )
            )
            ->execute();

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

        $found = Book::searchRequest()
            ->query(
                Query::bool()
                    ->must(Query::matchAll())
                    ->withTrashed()
            )
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

        $found = Book::searchRequest()
            ->query(
                Query::bool()
                    ->must(Query::matchAll())
                    ->onlyTrashed()
            )
            ->execute();

        $this->assertFoundModel($target, $found);
    }

    public function test_only_trashed_models_can_be_found_in_multiple_indices(): void
    {
        $this->app['config']->set('scout.soft_delete', true);

        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create();

        $target->delete();

        $found = Author::searchRequest()
            ->query(
                Query::bool()
                    ->must(Query::matchAll())
                    ->onlyTrashed()
            )
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

        $found = Author::searchRequest()
            ->query(
                Query::bool()
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
                    ->minimumShouldMatch(1)
            )
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

        $found = Book::searchRequest()
            ->query(
                (new BoolQueryBuilder())->must(
                    (new RangeQueryBuilder())
                        ->field('published')
                        ->gte('2020')
                        ->format('yyyy')
                )
            )
            ->execute();

        $this->assertFoundModel($target, $found);
    }
}
