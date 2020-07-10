<?php
declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration;

use Carbon\Carbon;
use ElasticAdapter\Documents\Document;
use ElasticScoutDriverPlus\Match;
use ElasticScoutDriverPlus\Searchable\ObjectIdEncrypter;
use ElasticScoutDriverPlus\SearchResult;
use ElasticScoutDriverPlus\Tests\App\Article;
use ElasticScoutDriverPlus\Tests\App\Book;
use ElasticScoutDriverPlus\Tests\App\Mixed;
use stdClass;

/**
 * @covers \ElasticScoutDriverPlus\CustomSearch
 * @covers \ElasticScoutDriverPlus\Decorators\EngineDecorator
 * @covers \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
 * @covers \ElasticScoutDriverPlus\Builders\RawQueryBuilder
 * @uses   \ElasticScoutDriverPlus\Factories\LazyModelFactory
 * @uses   \ElasticScoutDriverPlus\Factories\SearchResultFactory
 * @uses   \ElasticScoutDriverPlus\Match
 * @uses   \ElasticScoutDriverPlus\SearchResult
 */
final class AggregatedRawSearchTest extends TestCase
{
    public function test_mixed_models_can_be_found_using_raw_query(): void
    {
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

        $found = Mixed::rawSearch()
            ->query([
                'match' => [
                    'title' => $query
                ]
            ])
            ->execute();

        $this->assertCount(2, $found->models());
        $this->assertContains($targetArticle->toArray(),$found->models()->toArray());
        $this->assertContains($targetBook->toArray(),$found->models()->toArray());
    }

    public function test_mixed_models_can_be_found_using_raw_query_and_highlight(): void
    {
        $query = uniqid('test');

        $targetArticle = factory(Article::class)
            ->state('belongs_to_author')
            ->create(['title' => $query]);
        $targetBook = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['title' => $query]);

        // mixed target
        $target = collect([$targetArticle, $targetBook]);

        $found = Mixed::rawSearch()
            ->query([
                'match' => [
                    'title' => $query
                ]
            ])
            ->highlight('title')
            ->execute();

        $this->assertCount($target->count(), $found->models());
        $this->assertEquals($target->toArray(), $found->models()->toArray());

        $found->matches()->each(function (Match $match) {
            $this->assertSame(
                ['title' => ['<em>'.$match->model()->title.'</em>']],
                $match->highlight()->getRaw()
            );
        });
    }

    public function test_mixed_models_can_be_found_using_raw_query_and_sort(): void
    {
        $query = uniqid('test');

        $targetArticle = factory(Article::class)
            ->state('belongs_to_author')
            ->create(['title' => $query]);
        $targetBook = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['title' => $query]);

        // mixed target
        $target = collect([$targetArticle, $targetBook]);

        $found = Mixed::rawSearch()
            ->query(['match_all' => new stdClass()])
            ->sort('price')
            ->execute();

        $this->assertCount($target->count(), $found->models());
        $this->assertEquals($target->sortBy('price')->values()->toArray(), $found->models()->toArray());
    }

    public function test_mixed_models_can_be_found_using_raw_query_and_from(): void
    {
        factory(Article::class, 5)
            ->state('belongs_to_author')
            ->create();
        factory(Book::class,5)
            ->state('belongs_to_author')
            ->create();

        $found = Mixed::rawSearch()
            ->query(['match_all' => new stdClass()])
            ->from(5)
            ->execute();

        $this->assertCount(5, $found->models());
        $this->assertSame(10, $found->total());
    }

    public function test_mixed_models_can_be_found_using_raw_query_and_size(): void
    {
        factory(Article::class, 2)
            ->state('belongs_to_author')
            ->create();
        factory(Book::class, 2)
            ->state('belongs_to_author')
            ->create();

        $found = Mixed::rawSearch()
            ->query(['match_all' => new stdClass()])
            ->size(2)
            ->execute();

        $this->assertCount(2, $found->models());
        $this->assertSame(4, $found->total());
    }

    public function test_mixed_search_result_can_be_retrieved(): void
    {
        factory(Article::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create();
        factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create();

        $found = Mixed::rawSearch()
            ->query(['match_all' => new stdClass()])
            ->execute();

        $this->assertInstanceOf(SearchResult::class, $found);
    }

    public function test_mixed_raw_result_can_be_retrieved(): void
    {
        factory(Article::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create();
        factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create();

        $found = Mixed::rawSearch()
            ->query(['match_all' => new stdClass()])
            ->raw();

        $this->assertIsArray($found);
    }

    public function test_mixed_terms_can_be_suggested(): void
    {
        $targetArticle = factory(Article::class)
            ->state('belongs_to_author')
            ->create(['title' => 'world']);
        $targetBook = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['title' => 'word']);

        // mixed target
        $target = collect([$targetArticle, $targetBook]);

        $found = Mixed::rawSearch()
            ->query(['match_none' => new stdClass()])
            ->suggest('title', [
                'text' => 'wirld',
                'term' => [
                    'field' => 'title'
                ]
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

    public function test_mixed_document_fields_can_be_filtered_using_raw_source(): void
    {
        $targetArticle = factory(Article::class)
            ->state('belongs_to_author')
            ->create();
        $targetBook = factory(Book::class)
            ->state('belongs_to_author')
            ->create();

        $found = Mixed::rawSearch()
            ->query(['match_all' => new stdClass()])
            ->sourceRaw(false)
            ->execute();

        $this->assertCount(2, $found->documents());

        $this->assertEquals(
            new Document((string) ObjectIdEncrypter::encrypt($targetArticle), []),
            $found->documents()->first()
        );
        $this->assertEquals(
            new Document((string) ObjectIdEncrypter::encrypt($targetBook), []),
            $found->documents()->last()
        );
    }

    public function test_mixed_document_fields_can_be_filtered_using_source(): void
    {
        $targetArticle = factory(Article::class)
            ->state('belongs_to_author')
            ->create();
        $targetBook = factory(Book::class)
            ->state('belongs_to_author')
            ->create();

        $found = Mixed::rawSearch()
            ->query(['match_all' => new stdClass()])
            ->source(['title', 'description'])
            ->execute();

        $this->assertCount(2, $found->documents());

        $this->assertEquals(
            new Document((string)ObjectIdEncrypter::encrypt($targetArticle), [
                'title' => $targetArticle->title,
                'description' => $targetArticle->description
            ]),
            $found->documents()->first()
        );
        $this->assertEquals(
            new Document((string)ObjectIdEncrypter::encrypt($targetBook), [
                'title' => $targetBook->title,
                'description' => $targetBook->description
            ]),
            $found->documents()->last()
        );
    }

    public function test_mixed_models_can_be_found_using_raw_field_collapsing(): void
    {
        $firstTarget = factory(Article::class)
            ->state('belongs_to_author')
            ->create(['price' => 100]);

        $secondTarget = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['price' => 200]);

        // additional mixin
        factory(Article::class, 10)->create([
            'price' => function () {
                return random_int(500, 1000);
            },
            'author_id' => $firstTarget->author_id
        ]);
        factory(Book::class, 10)->create([
            'price' => function () {
                return random_int(500, 1000);
            },
            'author_id' => $secondTarget->author_id
        ]);

        // find the cheapest books by author
        $found = Mixed::rawSearch()
            ->query(['match_all' => new stdClass()])
            ->collapseRaw(['field' => 'author_id'])
            ->sort('price', 'asc')
            ->execute();

        $this->assertCount(2, $found->models());
        $this->assertEquals($firstTarget->toArray(), $found->models()->first()->toArray());
        $this->assertEquals($secondTarget->toArray(), $found->models()->last()->toArray());
    }

    public function test_mixed_models_can_be_found_using_field_collapsing(): void
    {
        $targetArticle = factory(Article::class)
            ->state('belongs_to_author')
            ->create(['published' => Carbon::createFromFormat('Y-m-d', '2020-06-20')]);
        $targetBook = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['published' => Carbon::createFromFormat('Y-m-d', '2020-06-19')]);

        // additional mixin
        factory(Article::class, 10)->create([
            'published' => function () use ($targetArticle) {
                return $targetArticle->published->subDays(rand(1, 10));
            },
            'author_id' => $targetArticle->author_id
        ]);
        factory(Book::class, 10)->create([
            'published' => function () use ($targetBook) {
                return $targetBook->published->subDays(rand(1, 10));
            },
            'author_id' => $targetBook->author_id
        ]);

        // find the most recent book of the author
        $found = Mixed::rawSearch()
            ->query(['match_all' => new stdClass()])
            ->collapse('author_id')
            ->sort('published', 'desc')
            ->execute();

        $this->assertCount(2, $found->models());
        $this->assertEquals($targetArticle->toArray(), $found->models()->first()->toArray());
        $this->assertEquals($targetBook->toArray(), $found->models()->skip(1)->first()->toArray());
    }

    public function test_mixed_document_data_can_be_analyzed_using_raw_aggregations(): void
    {
        $sourceArticle = factory(Article::class, rand(5, 10))
            ->state('belongs_to_author')
            ->create();
        $sourceBook = factory(Book::class, rand(5, 10))
            ->state('belongs_to_author')
            ->create();

        $source = $sourceArticle
            ->toBase()
            ->merge($sourceBook);

        $minPrice = $source->min('price');
        $maxPrice = $source->max('price');

        $found = Mixed::rawSearch()
            ->query(['match_all' => new stdClass()])
            ->aggregateRaw([
                'min_price' => [
                    'min' => [
                        'field' => 'price'
                    ]
                ],
                'max_price' => [
                    'max' => [
                        'field' => 'price'
                    ]
                ]
            ])
            ->size(0)
            ->execute();

        $this->assertEquals($minPrice, $found->aggregations()->get('min_price')['value']);
        $this->assertEquals($maxPrice, $found->aggregations()->get('max_price')['value']);
    }

    public function test_mixed_document_data_can_be_analyzed_using_aggregations(): void
    {
        $sourceArticle = factory(Article::class, rand(5, 10))
            ->state('belongs_to_author')
            ->create();
        $sourceBook = factory(Book::class, rand(5, 10))
            ->state('belongs_to_author')
            ->create();

        $source = $sourceArticle
            ->toBase()
            ->merge($sourceBook);

        /** @var SearchResult $found */
        $found = Mixed::rawSearch()
            ->query(['match_all' => new stdClass()])
            ->aggregate('max_price', [
                'max' => [
                    'field' => 'price'
                ]
            ])
            ->size(0)
            ->execute();

        $this->assertEquals($source->max('price'), $found->aggregations()->get('max_price')['value']);
    }
}
