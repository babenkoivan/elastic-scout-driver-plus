<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Integration\Factories;

use Elastic\ScoutDriverPlus\Engine;
use Elastic\ScoutDriverPlus\Factories\DocumentFactory;
use Elastic\ScoutDriverPlus\Factories\RoutingFactory;
use Elastic\ScoutDriverPlus\Searchable;
use Elastic\ScoutDriverPlus\Tests\App\Book;
use Elastic\ScoutDriverPlus\Tests\Integration\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(DocumentFactory::class)]
#[UsesClass(Engine::class)]
#[UsesClass(RoutingFactory::class)]
#[UsesClass(Searchable::class)]
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
