<?php
declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration;

use Carbon\Carbon;
use ElasticScoutDriverPlus\Searchable\ObjectIdEncrypter;
use ElasticScoutDriverPlus\Tests\App\Article;
use ElasticScoutDriverPlus\Tests\App\Book;
use ElasticScoutDriverPlus\Tests\App\Mixed;
use Illuminate\Database\Eloquent\Model;

/**
 * @covers \ElasticScoutDriverPlus\CustomSearch
 * @covers \ElasticScoutDriverPlus\Decorators\EngineDecorator
 * @covers \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
 * @covers \ElasticScoutDriverPlus\Builders\BoolQueryBuilder
 * @uses   \ElasticScoutDriverPlus\Factories\LazyModelFactory
 * @uses   \ElasticScoutDriverPlus\Factories\SearchResultFactory
 * @uses   \ElasticScoutDriverPlus\Match
 * @uses   \ElasticScoutDriverPlus\SearchResult
 */
final class AggregatedBoolSearchTest extends TestCase
{
    public function test_mixed_models_can_be_found_using_must(): void
    {
        // additional mixin
        factory(Article::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create();
        factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create();

        $query = uniqid('test');

        $targetArticle = factory(Article::class)
            ->state('belongs_to_author')
            ->create(['title' => $query]);
        $targetBook = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['title' => $query]);

        // mixed target
        $target = collect([$targetArticle, $targetBook]);

        $found = Mixed::boolSearch()
            ->must('match', ['title' => $query])
            ->execute();

        $this->assertCount(2, $found->models());
        $this->assertContains($targetArticle->toArray(), $found->models()->toArray());
        $this->assertContains($targetBook->toArray(), $found->models()->toArray());
    }

    public function test_mixed_models_can_be_found_using_must_not(): void
    {
        $query = uniqid('test');

        $mixinArticle = factory(Article::class)
            ->state('belongs_to_author')
            ->create(['title' => $query]);
        $mixinBook = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['title' => $query]);

        $targetArticle = factory(Article::class)
            ->state('belongs_to_author')
            ->create();
        $targetBook = factory(Book::class)
            ->state('belongs_to_author')
            ->create();

        $found = Mixed::boolSearch()
            ->mustNot('match', ['title' => $query])
            ->execute();

        $this->assertCount(2, $found->models());
        $this->assertContains($targetArticle->toArray(), $found->models()->toArray());
        $this->assertContains($targetBook->toArray(), $found->models()->toArray());
    }

    public function test_mixed_models_can_be_found_using_should(): void
    {
        $sourceOne = factory(Article::class)
            ->state('belongs_to_author')
            ->create(['published' => Carbon::createFromFormat('Y-m-d', '2018-04-23')]);

        $sourceTwo = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['published' => Carbon::createFromFormat('Y-m-d', '2003-01-14')]);

        $sourceThree = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['published' => Carbon::createFromFormat('Y-m-d', '2020-03-07')]);

        $source = collect([$sourceOne, $sourceTwo, $sourceThree]);

        $target = $source->filter(function (Model $model) {
            return $model->published->year > 2003;
        });

        $found = Mixed::boolSearch()
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

    public function test_mixed_models_can_be_found_using_filter(): void
    {
        // additional mixin
        factory(Article::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create(['published' => Carbon::create(2010, 5, 10)]);
        factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create(['published' => Carbon::create(2010, 5, 10)]);

        $targetArticle = factory(Article::class)
            ->state('belongs_to_author')
            ->create(['published' => Carbon::create(2020, 6, 7)]);
        $targetBook = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['published' => Carbon::create(2020, 6, 7)]);

        $found = Mixed::boolSearch()
            ->filter('term', ['published' => '2020-06-07'])
            ->execute();

        $this->assertCount(2, $found->models());
        $this->assertEquals($targetArticle->toArray(), $found->models()->first()->toArray());
        $this->assertEquals($targetBook->toArray(), $found->models()->skip(1)->first()->toArray());
    }

    public function test_not_trashed_mixed_models_can_be_found(): void
    {
        $this->app['config']->set('scout.soft_delete', true);

        $sourceArticle = factory(Article::class, rand(2, 5))
            ->state('belongs_to_author')
            ->create();
        $sourceBook = factory(Book::class, rand(2, 5))
            ->state('belongs_to_author')
            ->create();

        $targetArticle = $sourceArticle->first();
        $targetBook = $sourceBook->first();

        $sourceArticle->where('id', '!=', $targetArticle->id)->each(function (Model $model) {
            $model->delete();
        });
        $sourceBook->where('id', '!=', $targetBook->id)->each(function (Model $model) {
            $model->delete();
        });

        $found = Mixed::boolSearch()->execute();

        $this->assertCount(2, $found->models());
        $this->assertContains($targetArticle->toArray(),$found->models()->toArray());
        $this->assertContains($targetBook->toArray(),$found->models()->toArray());
    }

    public function test_trashed_mixed_models_can_be_found(): void
    {
        $this->app['config']->set('scout.soft_delete', true);

        $targetArticle = factory(Article::class, rand(2, 5))
            ->state('belongs_to_author')
            ->create();
        $targetBook = factory(Book::class, rand(2, 5))
            ->state('belongs_to_author')
            ->create();

        // mixed target
        $target = $targetArticle
            ->toBase()
            ->merge($targetBook);

        // soft delete some models
        $targetArticle->first()->delete();
        $targetBook->first()->delete();

        $found = Mixed::boolSearch()
            ->must('match_all')
            ->withTrashed()
            ->execute();

        $this->assertCount($target->count(), $found->models());

        $this->assertSame(
            $target->pluck('id')->sort()->values()->all(),
            $found->models()->pluck('id')->sort()->values()->all()
        );
    }

    public function test_only_trashed_mixed_models_can_be_found(): void
    {
        $this->app['config']->set('scout.soft_delete', true);

        $sourceArticle = factory(Article::class, rand(2, 5))
            ->state('belongs_to_author')
            ->create();
        $sourceBook = factory(Book::class, rand(2, 5))
            ->state('belongs_to_author')
            ->create();

        // soft delete some models
        $targetArticle = $sourceArticle->first();
        $targetBook = $sourceBook->first();

        $targetArticle->delete();
        $targetBook->delete();

        $found = Mixed::boolSearch()
            ->onlyTrashed()
            ->execute();

        $this->assertCount(2, $found->models());
        $this->assertEquals($targetArticle->toArray(), $found->models()->first()->toArray());
        $this->assertEquals($targetBook->toArray(), $found->models()->skip(1)->first()->toArray());
    }
}
