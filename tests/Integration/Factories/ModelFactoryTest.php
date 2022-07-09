<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Integration\Factories;

use Elastic\ScoutDriverPlus\Builders\DatabaseQueryBuilder;
use Elastic\ScoutDriverPlus\Factories\ModelFactory;
use Elastic\ScoutDriverPlus\Tests\App\Author;
use Elastic\ScoutDriverPlus\Tests\App\Book;
use Elastic\ScoutDriverPlus\Tests\Integration\TestCase;

/**
 * @covers \Elastic\ScoutDriverPlus\Factories\ModelFactory
 *
 * @uses   \Elastic\ScoutDriverPlus\Builders\DatabaseQueryBuilder
 * @uses   \Elastic\ScoutDriverPlus\Engine
 * @uses   \Elastic\ScoutDriverPlus\Factories\DocumentFactory
 * @uses   \Elastic\ScoutDriverPlus\Factories\RoutingFactory
 * @uses   \Elastic\ScoutDriverPlus\Searchable
 */
final class ModelFactoryTest extends TestCase
{
    private Author $author;
    private Book $book;
    private ModelFactory $modelFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->author = factory(Author::class)->create();
        $this->book = factory(Book::class)->create(['author_id' => $this->author->getKey()]);

        $this->modelFactory = new ModelFactory([
            $this->author->searchableAs() => new DatabaseQueryBuilder($this->author),
            $this->book->searchableAs() => new DatabaseQueryBuilder($this->book),
        ]);
    }

    public function test_empty_collection_returned_when_document_ids_are_empty(): void
    {
        $this->assertEmpty(
            $this->modelFactory->makeFromIndexNameAndDocumentIds(
                $this->author->searchableAs(),
                []
            )
        );
    }

    public function test_empty_collection_returned_when_document_ids_do_not_exist(): void
    {
        $this->assertEmpty(
            $this->modelFactory->makeFromIndexNameAndDocumentIds(
                $this->author->searchableAs(),
                ['0']
            )
        );
    }

    public function test_models_are_returned_when_index_name_is_used(): void
    {
        $this->assertEquals(
            [$this->author->toArray()],
            $this->modelFactory->makeFromIndexNameAndDocumentIds(
                $this->author->searchableAs(),
                [(string)$this->author->getScoutKey()]
            )->toArray()
        );

        $this->assertEquals(
            [$this->book->toArray()],
            $this->modelFactory->makeFromIndexNameAndDocumentIds(
                $this->book->searchableAs(),
                [(string)$this->book->getScoutKey()]
            )->toArray()
        );
    }

    public function test_models_are_returned_when_alias_name_is_used(): void
    {
        $this->assertEquals(
            [$this->author->toArray()],
            $this->modelFactory->makeFromIndexNameAndDocumentIds(
                'book-authors',
                [(string)$this->author->getScoutKey()]
            )->toArray()
        );
    }
}
