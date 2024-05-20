<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Integration\Queries;

use Carbon\Carbon;
use Elastic\Adapter\Documents\Document;
use Elastic\Adapter\Search\Highlight;
use Elastic\ScoutDriverPlus\Decorators\Hit;
use Elastic\ScoutDriverPlus\Tests\App\Author;
use Elastic\ScoutDriverPlus\Tests\App\Book;
use Elastic\ScoutDriverPlus\Tests\App\Model;
use Elastic\ScoutDriverPlus\Tests\Integration\TestCase;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Facades\Cache;
use RuntimeException;
use const SORT_NUMERIC;
use stdClass;

/**
 * @covers \Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder
 * @covers \Elastic\ScoutDriverPlus\Engine
 * @covers \Elastic\ScoutDriverPlus\Factories\LazyModelFactory
 * @covers \Elastic\ScoutDriverPlus\Factories\ModelFactory
 * @covers \Elastic\ScoutDriverPlus\Support\Query
 *
 * @uses   \Elastic\ScoutDriverPlus\Builders\DatabaseQueryBuilder
 * @uses   \Elastic\ScoutDriverPlus\Decorators\Hit
 * @uses   \Elastic\ScoutDriverPlus\Decorators\SearchResult
 * @uses   \Elastic\ScoutDriverPlus\Decorators\Suggestion
 * @uses   \Elastic\ScoutDriverPlus\Exceptions\NotSearchableModelException
 * @uses   \Elastic\ScoutDriverPlus\Factories\DocumentFactory
 * @uses   \Elastic\ScoutDriverPlus\Factories\ParameterFactory
 * @uses   \Elastic\ScoutDriverPlus\Factories\RoutingFactory
 * @uses   \Elastic\ScoutDriverPlus\Paginator
 * @uses   \Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection
 * @uses   \Elastic\ScoutDriverPlus\Searchable
 */
final class RawQueryTest extends TestCase
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

        $found = Book::searchQuery([
            'match' => [
                'title' => $target->title,
            ],
        ])->execute();

        $this->assertFoundModel($target, $found);
    }

    public function test_models_can_be_found_using_raw_query_and_highlight(): void
    {
        $target = factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create(['title' => uniqid('test')])
            ->sortBy('id', SORT_NUMERIC);

        $query = [
            'match' => [
                'title' => $target->first()->title,
            ],
        ];

        $found = Book::searchQuery($query)
            ->sort('id')
            ->highlight('title')
            ->sort('id')
            ->execute();

        $this->assertFoundModels($target, $found);

        $found->hits()->each(function (Hit $hit) {
            /** @var Book $model */
            $model = $hit->model();
            $highlight = $hit->highlight();

            $this->assertNotNull($highlight);
            /** @var Highlight $highlight */
            $this->assertSame(['title' => ['<em>' . $model->title . '</em>']], $highlight->raw());
        });
    }

    public function test_models_can_be_found_using_raw_query_and_sort(): void
    {
        $target = factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create()
            ->sortBy('id', SORT_NUMERIC);

        $found = Book::searchQuery(['match_all' => new stdClass()])
            ->sort('id')
            ->execute();

        $this->assertFoundModels($target, $found);
    }

    public function test_models_can_be_found_using_raw_query_and_from(): void
    {
        factory(Book::class, 10)
            ->state('belongs_to_author')
            ->create();

        $found = Book::searchQuery(['match_all' => new stdClass()])
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

        $found = Book::searchQuery(['match_all' => new stdClass()])
            ->size(2)
            ->execute();

        $this->assertCount(2, $found->models());
        $this->assertSame(4, $found->total());
    }

    public function test_raw_result_can_be_retrieved(): void
    {
        factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create();

        $found = Book::searchQuery(['match_all' => new stdClass()])->raw();

        $this->assertIsArray($found);
    }

    public function test_terms_can_be_suggested(): void
    {
        $target = collect(['world', 'word'])->map(
            static fn (string $title) => factory(Book::class)
                ->state('belongs_to_author')
                ->create(compact('title'))
        );

        $found = Book::searchQuery(['match_none' => new stdClass()])
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
            ->options();

        $this->assertSame(
            $target->pluck('title')->sort()->values()->toArray(),
            $suggestionOptions->pluck('text')->sort()->values()->toArray()
        );
    }

    public function test_document_fields_can_be_filtered_using_raw_source(): void
    {
        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create();

        $found = Book::searchQuery(['match_all' => new stdClass()])
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

        $found = Book::searchQuery(['match_all' => new stdClass()])
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
            'price' => static fn () => random_int(500, 1000),
            'author_id' => $firstTarget->author_id,
        ]);

        // find the cheapest books by author
        $found = Book::searchQuery(['match_all' => new stdClass()])
            ->collapseRaw([
                'field' => 'author_id',
                'inner_hits' => [
                    'name' => 'cheapest',
                    'size' => 5,
                    'sort' => [['price' => 'asc']],
                ],
            ])
            ->sort('price')
            ->execute();

        $this->assertFoundModels(collect([$firstTarget, $secondTarget]), $found);

        $this->assertCount(5, $found->hits()->first()->innerHits()->get('cheapest')->map->model());
        $this->assertCount(1, $found->hits()->last()->innerHits()->get('cheapest')->map->model());
    }

    public function test_models_can_be_found_using_field_collapsing(): void
    {
        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['published' => Carbon::createFromFormat('Y-m-d', '2020-06-20')]);

        // additional mixin
        factory(Book::class, 10)->create([
            'published' => static fn () => $target->published->subDays(rand(1, 10)),
            'author_id' => $target->author_id,
        ]);

        // find the most recent book of the author
        $found = Book::searchQuery(['match_all' => new stdClass()])
            ->collapse('author_id')
            ->sort('published', 'desc')
            ->execute();

        $this->assertFoundModel($target, $found);
    }

    public function test_document_data_can_be_analyzed_using_raw_aggregations(): void
    {
        $source = factory(Book::class, rand(5, 10))
            ->state('belongs_to_author')
            ->create();

        $minPrice = $source->min('price');
        $maxPrice = $source->max('price');

        $found = Book::searchQuery()
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
            ->execute();

        $this->assertEquals($minPrice, $found->aggregations()->get('min_price')->raw()['value']);
        $this->assertEquals($maxPrice, $found->aggregations()->get('max_price')->raw()['value']);
    }

    public function test_document_data_can_be_analyzed_using_aggregations(): void
    {
        $source = factory(Book::class, rand(2, 5))
            ->state('belongs_to_author')
            ->create();

        $found = Book::searchQuery()
            ->aggregate('max_price', [
                'max' => [
                    'field' => 'price',
                ],
            ])
            ->execute();

        $this->assertEquals($source->max('price'), $found->aggregations()->get('max_price')->raw()['value']);
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

        $found = Book::searchQuery(['match_all' => new stdClass()])
            ->postFilter(['term' => ['published' => '2020-06-07']])
            ->execute();

        $this->assertFoundModel($target, $found);
    }

    public function test_models_can_be_paginated(): void
    {
        $target = factory(Book::class, 5)
            ->state('belongs_to_author')
            ->create()
            ->sortBy('id', SORT_NUMERIC)
            ->chunk(3);

        $builder = Book::searchQuery(['match_all' => new stdClass()])
            ->sort('id');

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
        $this->assertEquals($target->first()->values()->toArray(), $firstPage->models()->toArray());
        $this->assertEquals($target->last()->values()->toArray(), $secondPage->models()->toArray());
    }

    public function test_models_can_be_simple_paginated(): void
    {
        $target = factory(Book::class, 5)
            ->state('belongs_to_author')
            ->create()
            ->sortBy('id', SORT_NUMERIC)
            ->chunk(3);

        $builder = Book::searchQuery(['match_all' => new stdClass()])
            ->sort('id');

        $firstPage = $builder->simplePaginate(3, 'customName', 1);
        $secondPage = $builder->simplePaginate(3, 'customName', 2);

        // assert each paginator has expected attributes
        $this->assertSame(1, $firstPage->currentPage());
        $this->assertSame(2, $secondPage->currentPage());

        $this->assertSame(3, $firstPage->perPage());
        $this->assertSame(3, $secondPage->perPage());

        $this->assertCount(3, $firstPage->items());
        $this->assertCount(2, $secondPage->items());

        // assert each page contains expected models
        $this->assertEquals($target->first()->values()->toArray(), $firstPage->models()->toArray());
        $this->assertEquals($target->last()->values()->toArray(), $secondPage->models()->toArray());
    }

    public function test_exception_is_thrown_when_paginating_search_results_but_total_hits_are_not_tracked(): void
    {
        $this->expectException(RuntimeException::class);

        factory(Book::class, rand(2, 5))
            ->state('belongs_to_author')
            ->create();

        Book::searchQuery(['match_all' => new stdClass()])
            ->trackTotalHits(false)
            ->paginate();
    }

    public function test_models_can_be_found_with_relations_in_a_single_index(): void
    {
        factory(Book::class, 5)
            ->state('belongs_to_author')
            ->create();

        $found = Book::searchQuery(['match_all' => new stdClass()])
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

        $found = Book::searchQuery(['match_all' => new stdClass()])
            ->join(Author::class)
            ->load(['author'], Book::class)
            ->load(['books'], Author::class)
            ->execute();

        $found->models()->each(function (Model $model) {
            $relation = $model instanceof Book ? 'author' : 'books';
            $this->assertTrue($model->relationLoaded($relation));
        });
    }

    public function test_query_callback_executed_in_a_single_model_class(): void
    {
        factory(Book::class, 5)
            ->state('belongs_to_author')
            ->create();

        $selectedColumns = ['id', 'title', 'description'];
        $found = Book::searchQuery(['match_all' => new stdClass()])
            ->refineModels(static function (EloquentBuilder $query) use ($selectedColumns) {
                $query->select($selectedColumns);
            })
            ->execute();

        $found->models()->each(function (Model $model) use ($selectedColumns) {
            $this->assertEqualsCanonicalizing(array_keys($model->getAttributes()), $selectedColumns);
        });
    }

    public function test_query_callbacks_executed_in_multiple_model_classes(): void
    {
        factory(Book::class, 5)
            ->state('belongs_to_author')
            ->create();

        $bookSelectedColumns = ['id', 'title', 'description'];
        $authorSelectedColumns = ['id', 'name', 'last_name'];
        $found = Book::searchQuery(['match_all' => new stdClass()])
            ->join(Author::class)
            ->refineModels(static function (EloquentBuilder $query) use ($bookSelectedColumns) {
                $query->select($bookSelectedColumns);
            }, Book::class)
            ->refineModels(static function (EloquentBuilder $query) use ($authorSelectedColumns) {
                $query->select($authorSelectedColumns);
            }, Author::class)
            ->execute();

        $found->models()->each(function (Model $model) use ($bookSelectedColumns, $authorSelectedColumns) {
            $columns = $model instanceof Book ? $bookSelectedColumns : $authorSelectedColumns;
            $this->assertEqualsCanonicalizing(array_keys($model->getAttributes()), $columns);
        });
    }

    public function test_search_result_can_be_cached(): void
    {
        $target = factory(Book::class, rand(2, 5))
            ->state('belongs_to_author')
            ->create()
            ->sortBy('id', SORT_NUMERIC);

        $cacheStore = Cache::store('file');
        $cacheStore->clear();

        $found = $cacheStore->rememberForever('raw_search_result', static function () {
            return Book::searchQuery(['match_all' => new stdClass()])
                ->sort('id')
                ->execute();
        });

        $this->assertFoundModels($target, $found);
    }

    public function test_total_hits_calculation_can_be_skipped(): void
    {
        $target = factory(Book::class, rand(2, 5))
            ->state('belongs_to_author')
            ->create()
            ->sortBy('id', SORT_NUMERIC);

        $found = Book::searchQuery(['match_all' => new stdClass()])
            ->sort('id')
            ->trackTotalHits(false)
            ->execute();

        $this->assertFoundModels($target, $found);
        $this->assertNull($found->total());
    }

    public function test_total_hits_number_can_be_limited(): void
    {
        $target = factory(Book::class, 10)
            ->state('belongs_to_author')
            ->create()
            ->sortBy('id', SORT_NUMERIC);

        $found = Book::searchQuery(['match_all' => new stdClass()])
            ->sort('id')
            ->trackTotalHits(5)
            ->execute();

        $this->assertFoundModels($target, $found);
        $this->assertSame(5, $found->total());
    }

    public function test_scores_can_be_tracked_when_sorting_on_field(): void
    {
        factory(Book::class)
            ->state('belongs_to_author')
            ->create();

        $found = Book::searchQuery(['match_all' => new stdClass()])
            ->sort('price')
            ->trackScores(true)
            ->execute();

        $this->assertCount(1, $found->hits());
        $this->assertNotNull($found->hits()->first()->score());
    }

    public function test_index_results_can_be_boosted(): void
    {
        $firstTarget = factory(Book::class)
            ->state('belongs_to_author')
            ->create();

        $secondTarget = $firstTarget->author;

        $found = Book::searchQuery(['match_all' => new stdClass()])
            ->join(Author::class, 0.5)
            ->execute();

        $this->assertFoundModels(collect([$firstTarget, $secondTarget]), $found);
    }

    public function test_models_can_be_retrieved_from_suggestions(): void
    {
        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['title' => 'The Book']);

        $found = Book::searchQuery()
            ->suggest('suggestion', [
                'prefix' => 'the',
                'completion' => [
                    'field' => 'suggest',
                ],
            ])
            ->execute();

        $suggestion = $found->suggestions()->get('suggestion')->first();

        $this->assertCount(1, $suggestion->models());
        $this->assertEquals($target->toArray(), $suggestion->models()->first()->toArray());
    }

    public function test_models_can_be_found_using_search_after(): void
    {
        // create documents that should be in the search result
        $target = factory(Author::class, 2)
            ->create()
            ->sortBy('id', SORT_NUMERIC);

        $pit = Author::openPointInTime('5m');

        // create another document after opening a pit, to make sure it isn't present in the search result
        factory(Author::class)->create();

        $firstResult = Author::searchQuery()
            ->sort('id')
            ->pointInTime($pit)
            ->size(1)
            ->execute();

        $this->assertFoundModel($target->first(), $firstResult);

        $secondResult = Author::searchQuery()
            ->sort('id')
            ->pointInTime($pit)
            ->searchAfter($firstResult->hits()->last()->sort())
            ->size(1)
            ->execute();

        $this->assertFoundModel($target->last(), $secondResult);

        $thirdResult = Author::searchQuery()
            ->sort('id')
            ->pointInTime($pit)
            ->searchAfter($secondResult->hits()->last()->sort())
            ->size(1)
            ->execute();

        $this->assertCount(0, $thirdResult);

        // cleanup
        Author::closePointInTime($pit);
    }

    public function test_models_can_be_found_with_custom_routing(): void
    {
        $target = factory(Book::class)->create([
            'author_id' => factory(Author::class)->create([
                'name' => 'John Doe',
            ]),
        ]);

        // additional mixin
        factory(Book::class, rand(2, 5))->create([
            'author_id' => factory(Author::class)->create([
                'name' => 'Jane Roe',
            ]),
        ]);

        $found = Book::searchQuery()
            ->routing([$target->searchableRouting()])
            ->execute();

        $this->assertFoundModel($target, $found);
    }

    public function test_query_can_be_explained(): void
    {
        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['title' => 'test']);

        $found = Book::searchQuery([
            'match' => [
                'title' => $target->title,
            ],
        ])->explain(true)->execute();

        $this->assertGreaterThan(0, $found->hits()->first()->explanation()->value());
    }

    public function test_query_can_be_terminated(): void
    {
        factory(Book::class, 10)->create([
            'author_id' => factory(Author::class)->create(),
        ]);

        $found = Book::searchQuery()->terminateAfter(1)->execute();

        $this->assertCount(1, $found);
    }
}
