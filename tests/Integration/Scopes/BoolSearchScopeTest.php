<?php
declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration\Scopes;

use Carbon\Carbon;
use ElasticScoutDriverPlus\Tests\App\Book;
use ElasticScoutDriverPlus\Tests\Integration\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\Scopes\BoolSearchScope
 * @covers \ElasticScoutDriverPlus\Decorators\EngineDecorator
 * @uses   \ElasticScoutDriverPlus\Builders\AbstractSearchRequestBuilder
 * @uses   \ElasticScoutDriverPlus\Builders\BoolSearchRequestBuilder
 * @uses   \ElasticScoutDriverPlus\Factories\LazyModelFactory
 * @uses   \ElasticScoutDriverPlus\Factories\SearchResultFactory
 * @uses   \ElasticScoutDriverPlus\Output\Match
 * @uses   \ElasticScoutDriverPlus\Output\SearchResult
 */
final class BoolSearchScopeTest extends TestCase
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

        $found = Book::boolSearchQuery()
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

        $found = Book::boolSearchQuery()
            ->mustNot('match', ['title' => $mixin->title])
            ->execute();

        $this->assertCount(1, $found->models());
        $this->assertEquals($target->toArray(), $found->models()->first()->toArray());
    }

    public function test_models_can_be_found_using_should(): void
    {
        $source = collect(['2018-04-23', '2003-01-14', '2020-03-07'])->map(function (string $published) {
            return factory(Book::class)
                ->state('belongs_to_author')
                ->create(['published' => Carbon::createFromFormat('Y-m-d', $published)]);
        });

        $target = $source->filter(function (Book $model) {
            return $model->published->year > 2003;
        });

        $found = Book::boolSearchQuery()
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

        $found = Book::boolSearchQuery()
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

        $source->where('id', '!=', $target->id)->each(function (Book $model) {
            $model->delete();
        });

        $found = Book::boolSearchQuery()->execute();

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

        $found = Book::boolSearchQuery()
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

        $found = Book::boolSearchQuery()
            ->onlyTrashed()
            ->execute();

        $this->assertCount(1, $found->models());
        $this->assertEquals($target->toArray(), $found->models()->first()->toArray());
    }
}
