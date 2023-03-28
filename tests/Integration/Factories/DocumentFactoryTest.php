<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Integration\Factories;

use Elastic\ScoutDriverPlus\Factories\DocumentFactory;
use Elastic\ScoutDriverPlus\Tests\App\Book;
use Elastic\ScoutDriverPlus\Tests\Integration\TestCase;

/**
 * @covers \Elastic\ScoutDriverPlus\Factories\DocumentFactory
 *
 * @uses   \Elastic\ScoutDriverPlus\Engine
 * @uses   \Elastic\ScoutDriverPlus\Factories\RoutingFactory
 * @uses   \Elastic\ScoutDriverPlus\Searchable
 */
final class DocumentFactoryTest extends TestCase
{
    private DocumentFactory $documentFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->documentFactory = new DocumentFactory();
    }

    public function test_relations_can_be_preloaded(): void
    {
        $models = factory(Book::class, 5)
            ->state('belongs_to_author')
            ->create()
            ->fresh();

        $this->assertDatabaseQueriesCount(1, function () use ($models) {
            $this->documentFactory->makeFromModels($models);
        });
    }
}
