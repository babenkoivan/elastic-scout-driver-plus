<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration;

use Carbon\Carbon;
use ElasticAdapter\Documents\Document;
use ElasticAdapter\Search\Highlight;
use ElasticScoutDriverPlus\Match;
use ElasticScoutDriverPlus\SearchResult;
use ElasticScoutDriverPlus\Tests\App\Author;
use ElasticScoutDriverPlus\Tests\App\Book;
use ElasticScoutDriverPlus\Tests\App\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use const SORT_STRING;
use stdClass;

/**
 * @covers \ElasticScoutDriverPlus\Builders\RawQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
 * @covers \ElasticScoutDriverPlus\CustomSearch
 * @covers \ElasticScoutDriverPlus\Decorators\EngineDecorator
 * @covers \ElasticScoutDriverPlus\Factories\LazyModelFactory
 *
 * @uses   \ElasticScoutDriverPlus\Factories\SearchResultFactory
 * @uses   \ElasticScoutDriverPlus\Match
 * @uses   \ElasticScoutDriverPlus\SearchResult
 * @uses   \ElasticScoutDriverPlus\Support\ModelScope
 */
final class RawSearchTest extends TestCase
{
    public function test_models_can_be_found_using_raw_query(): void
    {
        // additional mixin
        factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create();

        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['title' => uniqid('test')]);

        $found = Book::rawSearch()
            ->query([
                'match' => [
                    'title' => $target->title,
                ],
            ])
            ->execute();

        $this->assertCount(1, $found->models());
        $this->assertEquals($target->toArray(), $found->models()->first()->toArray());
    }

    public function test_models_can_be_found_using_raw_query_and_highlight(): void
    {
        $target = factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create(['title' => uniqid('test')]);

        $found = Book::rawSearch()
            ->query([
                'match' => [
                    'title' => $target->first()->title,
                ],
            ])
            ->highlight('title')
            ->execute();

        $this->assertCount($target->count(), $found->models());
        $this->assertEquals($target->toArray(), $found->models()->toArray());

        $found->matches()->each(function (Match $match) {
            /** @var Book $model */
            $model = $match->model();
            $highlight = $match->highlight();

            $this->assertNotNull($highlight);
            /** @var Highlight $highlight */
            $this->assertSame(['title' => ['<em>' . $model->title . '</em>']], $highlight->getRaw());
        });
    }

    public function test_models_can_be_found_using_raw_query_and_sort(): void
    {
        $target = factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create();

        $found = Book::rawSearch()
            ->query(['match_all' => new stdClass()])
            ->sort('price')
            ->execute();

        $this->assertCount($target->count(), $found->models());
        $this->assertEquals($target->sortBy('price')->values()->toArray(), $found->models()->toArray());
    }

    public function test_models_can_be_found_using_raw_query_and_from(): void
    {
        factory(Book::class, 10)
            ->state('belongs_to_author')
            ->create();

        $found = Book::rawSearch()
            ->query(['match_all' => new stdClass()])
            ->from(5)
            ->execute();

        $this->assertCount(5, $found->models());
        $this->assertSame(10, $found->total());
    }

    public function test_models_can_be_found_using_raw_query_and_size(): void
    {
        factory(Book::class, 4)
            ->state('belongs_to_author')
            ->create();

        $found = Book::rawSearch()
            ->query(['match_all' => new stdClass()])
            ->size(2)
            ->execute();

        $this->assertCount(2, $found->models());
        $this->assertSame(4, $found->total());
    }

    public function test_search_result_can_be_retrieved(): void
    {
        factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create();

        $found = Book::rawSearch()
            ->query(['match_all' => new stdClass()])
            ->execute();

        $this->assertInstanceOf(SearchResult::class, $found);
    }

    public function test_raw_result_can_be_retrieved(): void
    {
        factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create();

        $found = Book::rawSearch()
            ->query(['match_all' => new stdClass()])
            ->raw();

        $this->assertIsArray($found);
    }

    public function test_terms_can_be_suggested(): void
    {
        $target = collect(['world', 'word'])->map(static function (string $title) {
            return factory(Book::class)
                ->state('belongs_to_author')
                ->create(compact('title'));
        });

        $found = Book::rawSearch()
            ->query(['match_none' => new stdClass()])
            ->suggest('title', [
                'text' => 'wirld',
                'term' => [
                    'field' => 'title',
                ],
            ])
            ->execute();

        $suggestionOptions = $found->suggestions()
            ->get('title')
            ->first()
            ->getOptions();

        $this->assertSame(
            $target->pluck('title')->sort()->values()->toArray(),
            collect($suggestionOptions)->pluck('text')->sort()->values()->toArray()
        );
    }

    public function test_document_fields_can_be_filtered_using_raw_source(): void
    {
        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create();

        $found = Book::rawSearch()
            ->query(['match_all' => new stdClass()])
            ->sourceRaw(false)
            ->execute();

        $this->assertCount(1, $found->documents());

        $this->assertEquals(
            new Document((string)$target->id, []),
            $found->documents()->first()
        );
    }

    public function test_document_fields_can_be_filtered_using_source(): void
    {
        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create();

        $found = Book::rawSearch()
            ->query(['match_all' => new stdClass()])
            ->source(['title', 'description'])
            ->execute();

        $this->assertCount(1, $found->documents());

        $this->assertEquals(
            new Document((string)$target->id, [
                'title' => $target->title,
                'description' => $target->description,
            ]),
            $found->documents()->first()
        );
    }

    public function test_models_can_be_found_using_raw_field_collapsing(): void
    {
        $firstTarget = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['price' => 100]);

        $secondTarget = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['price' => 200]);

        // additional mixin
        factory(Book::class, 10)->create([
            'price' => static function () {
                return random_int(500, 1000);
            },
            'author_id' => $firstTarget->author_id,
        ]);

        // find the cheapest books by author
        $found = Book::rawSearch()
            ->query(['match_all' => new stdClass()])
            ->collapseRaw(['field' => 'author_id'])
            ->sort('price', 'asc')
            ->execute();

        $this->assertCount(2, $found->models());
        $this->assertEquals($firstTarget->toArray(), $found->models()->first()->toArray());
        $this->assertEquals($secondTarget->toArray(), $found->models()->last()->toArray());
    }

    public function test_models_can_be_found_using_field_collapsing(): void
    {
        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['published' => Carbon::createFromFormat('Y-m-d', '2020-06-20')]);

        // additional mixin
        factory(Book::class, 10)->create([
            'published' => static function () use ($target) {
                return $target->published->subDays(rand(1, 10));
            },
            'author_id' => $target->author_id,
        ]);

        // find the most recent book of the author
        $found = Book::rawSearch()
            ->query(['match_all' => new stdClass()])
            ->collapse('author_id')
            ->sort('published', 'desc')
            ->execute();

        $this->assertCount(1, $found->models());
        $this->assertEquals($target->toArray(), $found->models()->first()->toArray());
    }

    public function test_document_data_can_be_analyzed_using_raw_aggregations(): void
    {
        $source = factory(Book::class, rand(5, 10))
            ->state('belongs_to_author')
            ->create();

        $minPrice = $source->min('price');
        $maxPrice = $source->max('price');

        $found = Book::rawSearch()
            ->query(['match_all' => new stdClass()])
            ->aggregateRaw([
                'min_price' => [
                    'min' => [
                        'field' => 'price',
                    ],
                ],
                'max_price' => [
                    'max' => [
                        'field' => 'price',
                    ],
                ],
            ])
            ->size(0)
            ->execute();

        $this->assertEquals($minPrice, $found->aggregations()->get('min_price')['value']);
        $this->assertEquals($maxPrice, $found->aggregations()->get('max_price')['value']);
    }

    public function test_document_data_can_be_analyzed_using_aggregations(): void
    {
        $source = factory(Book::class, rand(2, 5))
            ->state('belongs_to_author')
            ->create();

        $found = Book::rawSearch()
            ->query(['match_all' => new stdClass()])
            ->aggregate('max_price', [
                'max' => [
                    'field' => 'price',
                ],
            ])
            ->size(0)
            ->execute();

        $this->assertEquals($source->max('price'), $found->aggregations()->get('max_price')['value']);
    }

    public function test_models_can_be_found_using_post_filter(): void
    {
        // additional mixin
        factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create();

        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['published' => Carbon::create(2020, 6, 7)]);

        $found = Book::rawSearch()
            ->query(['match_all' => new stdClass()])
            ->postFilter('term', ['published' => '2020-06-07'])
            ->execute();

        $this->assertCount(1, $found->models());
        $this->assertEquals($target->toArray(), $found->models()->first()->toArray());
    }

    public function test_models_can_be_paginated(): void
    {
        $target = factory(Book::class, 5)
            ->state('belongs_to_author')
            ->create()
            ->sortBy('id', SORT_STRING)
            ->chunk(3);

        $builder = Book::rawSearch()
            ->query(['match_all' => new stdClass()])
            ->sort('_id', 'asc');

        $firstPage = $builder->paginate(3, 'customName', 1);
        $secondPage = $builder->paginate(3, 'customName', 2);

        // assert each paginator has expected attributes
        $this->assertSame(1, $firstPage->currentPage());
        $this->assertSame(2, $secondPage->currentPage());

        $this->assertSame(5, $firstPage->total());
        $this->assertSame(5, $secondPage->total());

        $this->assertSame(3, $firstPage->perPage());
        $this->assertSame(3, $secondPage->perPage());

        $this->assertCount(3, $firstPage->items());
        $this->assertCount(2, $secondPage->items());

        // assert each page contains expected models
        $getPageModels = static function (LengthAwarePaginator $paginator): Collection {
            return collect($paginator->items())->map(static function (Match $match) {
                return $match->model();
            });
        };

        $this->assertEquals(
            $target->first()->pluck('id')->toArray(),
            $getPageModels($firstPage)->pluck('id')->toArray()
        );

        $this->assertEquals(
            $target->last()->pluck('id')->toArray(),
            $getPageModels($secondPage)->pluck('id')->toArray()
        );
    }

    public function test_models_can_be_found_with_relations_in_a_single_index(): void
    {
        factory(Book::class, 5)
            ->state('belongs_to_author')
            ->create();

        $found = Book::rawSearch()
            ->query(['match_all' => new stdClass()])
            ->load(['author'])
            ->execute();

        $found->models()->each(function (Model $model) {
            $this->assertTrue($model->relationLoaded('author'));
        });
    }

    public function test_models_can_be_found_with_relations_in_multiple_indices(): void
    {
        factory(Book::class, 5)
            ->state('belongs_to_author')
            ->create();

        $found = Book::rawSearch()
            ->query(['match_all' => new stdClass()])
            ->join(Author::class)
            ->load(['author'], Book::class)
            ->load(['books'], Author::class)
            ->execute();

        $found->models()->each(function (Model $model) {
            $relation = $model instanceof Book ? 'author' : 'books';
            $this->assertTrue($model->relationLoaded($relation));
        });
    }

    public function test_search_result_can_be_cached(): void
    {
        $source = factory(Book::class, rand(2, 5))
            ->state('belongs_to_author')
            ->create();

        $cacheStore = Cache::store('file');
        $cacheStore->delete('raw_search_result');

        $searchResult = $cacheStore->rememberForever('raw_search_result', static function () {
            return Book::rawSearch()
                ->query(['match_all' => new stdClass()])
                ->execute();
        });

        $this->assertEquals($source->toArray(), $searchResult->models()->toArray());
    }
}
