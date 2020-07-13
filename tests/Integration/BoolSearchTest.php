<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration;

use Carbon\Carbon;
use ElasticScoutDriverPlus\Tests\App\Book;

/**
 * @covers \ElasticScoutDriverPlus\CustomSearch
 * @covers \ElasticScoutDriverPlus\Decorators\EngineDecorator
 * @covers \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
 * @covers \ElasticScoutDriverPlus\Builders\BoolQueryBuilder
 *
 * @uses   \ElasticScoutDriverPlus\Factories\LazyModelFactory
 * @uses   \ElasticScoutDriverPlus\Factories\SearchResultFactory
 * @uses   \ElasticScoutDriverPlus\Match
 * @uses   \ElasticScoutDriverPlus\SearchResult
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

        $found = Book::boolSearch()->execute();

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
            ->onlyTrashed()
            ->execute();

        $this->assertCount(1, $found->models());
        $this->assertEquals($target->toArray(), $found->models()->first()->toArray());
    }
}
