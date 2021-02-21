<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration;

use Carbon\Carbon;
use ElasticScoutDriverPlus\Builders\RangeQueryBuilder;
use ElasticScoutDriverPlus\Tests\App\Author;
use ElasticScoutDriverPlus\Tests\App\Book;

/**
 * @covers \ElasticScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\BoolQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
 * @covers \ElasticScoutDriverPlus\QueryDsl
 * @covers \ElasticScoutDriverPlus\Decorators\EngineDecorator
 * @covers \ElasticScoutDriverPlus\Factories\LazyModelFactory
 *
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Collection
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Factory
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Transformers\FlatArrayTransformer
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Transformers\GroupedArrayTransformer
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Validators\AllOfValidator
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Validators\CompoundValidator
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Validators\OneOfValidator
 * @uses   \ElasticScoutDriverPlus\Builders\RangeQueryBuilder
 * @uses   \ElasticScoutDriverPlus\Factories\SearchResultFactory
 * @uses   \ElasticScoutDriverPlus\QueryMatch
 * @uses   \ElasticScoutDriverPlus\SearchResult
 * @uses   \ElasticScoutDriverPlus\Support\Arr
 * @uses   \ElasticScoutDriverPlus\Support\ModelScope
 */
final class BoolSearchTest extends TestCase
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

        $found = Book::boolSearch()
            ->must('match', ['title' => $target->title])
            ->execute();

        $this->assertCount(1, $found->models());
        $this->assertEquals($target->toArray(), $found->models()->first()->toArray());
    }

    public function test_models_can_be_found_using_must_not(): void
    {
        $mixin = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['title' => uniqid('test')]);

        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create();

        $found = Book::boolSearch()
            ->mustNot('match', ['title' => $mixin->title])
            ->execute();

        $this->assertCount(1, $found->models());
        $this->assertEquals($target->toArray(), $found->models()->first()->toArray());
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
        });

        $found = Book::boolSearch()
            ->should('term', ['published' => '2018-04-23'])
            ->should('term', ['published' => '2020-03-07'])
            ->minimumShouldMatch(1)
            ->execute();

        $this->assertCount(2, $found->models());

        $this->assertSame(
            $target->pluck('id')->sort()->values()->all(),
            $found->models()->pluck('id')->sort()->values()->all()
        );
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

        $found = Book::boolSearch()
            ->filter('term', ['published' => '2020-06-07'])
            ->execute();

        $this->assertCount(1, $found->models());
        $this->assertEquals($target->toArray(), $found->models()->first()->toArray());
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

        $found = Book::boolSearch()
            ->must('match_all')
            ->execute();

        $this->assertCount(1, $found->models());
        $this->assertEquals($target->toArray(), $found->models()->first()->toArray());
    }

    public function test_trashed_models_can_be_found(): void
    {
        $this->app['config']->set('scout.soft_delete', true);

        $target = factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create();

        // soft delete some models
        $target->first()->delete();

        $found = Book::boolSearch()
            ->must('match_all')
            ->withTrashed()
            ->execute();

        $this->assertCount($target->count(), $found->models());

        $this->assertSame(
            $target->pluck('id')->sort()->values()->all(),
            $found->models()->pluck('id')->sort()->values()->all()
        );
    }

    public function test_only_trashed_models_can_be_found(): void
    {
        $this->app['config']->set('scout.soft_delete', true);

        $source = factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create();

        $target = $source->first();
        $target->delete();

        $found = Book::boolSearch()
            ->must('match_all')
            ->onlyTrashed()
            ->execute();

        $this->assertCount(1, $found->models());
        $this->assertEquals($target->toArray(), $found->models()->first()->toArray());
    }

    public function test_only_trashed_models_can_be_found_in_multiple_indices(): void
    {
        $this->app['config']->set('scout.soft_delete', true);

        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create();

        $target->delete();

        $found = Author::boolSearch()
            ->join(Book::class)
            ->must('match_all')
            ->onlyTrashed()
            ->execute();

        $this->assertCount(1, $found->models());
        $this->assertEquals($target->toArray(), $found->models()->first()->toArray());
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

        $found = Author::boolSearch()
            ->join(Book::class)
            ->should('match', ['name' => $firstTarget->name])
            ->should('match', ['title' => $secondTarget->title])
            ->minimumShouldMatch(1)
            ->sort('_index', 'asc')
            ->execute();

        $this->assertCount(2, $found->models());
        $this->assertEquals($firstTarget->toArray(), $found->models()->first()->toArray());
        $this->assertEquals($secondTarget->toArray(), $found->models()->last()->toArray());
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

        $found = Book::boolSearch()
            ->must(
                (new RangeQueryBuilder())
                    ->field('published')
                    ->gte('2020')
                    ->format('yyyy')
            )
            ->execute();

        $this->assertCount(1, $found->models());
        $this->assertEquals($target->toArray(), $found->models()->first()->toArray());
    }
}
