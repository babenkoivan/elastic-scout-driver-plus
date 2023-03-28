<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Integration\Factories;

use Elastic\Adapter\Search\SearchResult;
use Elastic\ScoutDriverPlus\Builders\DatabaseQueryBuilder;
use Elastic\ScoutDriverPlus\Factories\LazyModelFactory;
use Elastic\ScoutDriverPlus\Factories\ModelFactory;
use Elastic\ScoutDriverPlus\Tests\App\Author;
use Elastic\ScoutDriverPlus\Tests\App\Book;
use Elastic\ScoutDriverPlus\Tests\Integration\TestCase;

/**
 * @covers \Elastic\ScoutDriverPlus\Factories\LazyModelFactory
 *
 * @uses   \Elastic\ScoutDriverPlus\Builders\DatabaseQueryBuilder
 * @uses   \Elastic\ScoutDriverPlus\Engine
 * @uses   \Elastic\ScoutDriverPlus\Factories\DocumentFactory
 * @uses   \Elastic\ScoutDriverPlus\Factories\ModelFactory
 * @uses   \Elastic\ScoutDriverPlus\Factories\RoutingFactory
 * @uses   \Elastic\ScoutDriverPlus\Searchable
 */
final class LazyModelFactoryTest extends TestCase
{
    private Author $author;
    private Book $book;
    private LazyModelFactory $lazyModelFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->author = factory(Author::class)->create();
        $this->book = factory(Book::class)->create(['author_id' => $this->author->getKey()]);

        $searchResult = new SearchResult([
            'hits' => [
                'total' => [
                    'value' => 2,
                ],
                'hits' => [
                    [
                        '_id' => (string)$this->author->getScoutKey(),
                        '_index' => $this->author->searchableAs(),
                    ],
                    [
                        '_id' => (string)$this->book->getScoutKey(),
                        '_index' => $this->book->searchableAs(),
                    ],
                ],
            ],
        ]);

        $modelFactory = new ModelFactory([
            $this->author->searchableAs() => new DatabaseQueryBuilder($this->author),
            $this->book->searchableAs() => new DatabaseQueryBuilder($this->book),
        ]);

        $this->lazyModelFactory = new LazyModelFactory($searchResult, $modelFactory);
    }

    public function test_null_is_returned_when_document_is_not_in_search_result(): void
    {
        $this->assertNull(
            $this->lazyModelFactory->makeFromIndexNameAndDocumentId(
                $this->author->searchableAs(),
                '0'
            )
        );
    }

    public function test_models_are_returned_when_documents_are_in_search_result(): void
    {
        $this->assertDatabaseQueriesCount(1, function () {
            $this->assertEquals(
                $this->author->toArray(),
                $this->lazyModelFactory->makeFromIndexNameAndDocumentId(
                    $this->author->searchableAs(),
                    (string)$this->author->getScoutKey()
                )->toArray()
            );
        });

        $this->assertDatabaseQueriesCount(1, function () {
            $this->assertEquals(
                $this->book->toArray(),
                $this->lazyModelFactory->makeFromIndexNameAndDocumentId(
                    $this->book->searchableAs(),
                    (string)$this->book->getScoutKey()
                )->toArray()
            );
        });
    }
}
