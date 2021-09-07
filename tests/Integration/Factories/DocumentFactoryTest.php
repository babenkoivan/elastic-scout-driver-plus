<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration\Factories;

use ElasticScoutDriverPlus\Factories\DocumentFactory;
use ElasticScoutDriverPlus\Tests\App\Book;
use ElasticScoutDriverPlus\Tests\Integration\TestCase;
use Illuminate\Support\Facades\DB;

/**
 * @covers \ElasticScoutDriverPlus\Factories\DocumentFactory
 *
 * @uses   \ElasticScoutDriverPlus\Engine
 * @uses   \ElasticScoutDriverPlus\Factories\RoutingFactory
 * @uses   \ElasticScoutDriverPlus\Searchable
 */
final class DocumentFactoryTest extends TestCase
{
    /**
     * @var DocumentFactory
     */
    private $documentFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->documentFactory = new DocumentFactory();
    }

    public function test_relations_can_be_preloaded(): void
    {
        $models = factory(Book::class, rand(2, 5))
            ->state('belongs_to_author')
            ->create()
            ->fresh();

        DB::enableQueryLog();
        $this->documentFactory->makeFromModels($models);
        $queryLog = DB::getQueryLog();

        $this->assertCount(1, $queryLog);
    }
}
