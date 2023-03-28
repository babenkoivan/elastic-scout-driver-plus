<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Integration\Factories;

use Elastic\Adapter\Documents\Routing;
use Elastic\ScoutDriverPlus\Factories\RoutingFactory;
use Elastic\ScoutDriverPlus\Tests\App\Book;
use Elastic\ScoutDriverPlus\Tests\Integration\TestCase;

/**
 * @covers \Elastic\ScoutDriverPlus\Factories\RoutingFactory
 *
 * @uses   \Elastic\ScoutDriverPlus\Engine
 * @uses   \Elastic\ScoutDriverPlus\Factories\DocumentFactory
 * @uses   \Elastic\ScoutDriverPlus\Searchable
 */
final class RoutingFactoryTest extends TestCase
{
    private RoutingFactory $routingFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->routingFactory = new RoutingFactory();
    }

    public function test_routing_can_be_made_from_models(): void
    {
        $models = factory(Book::class, rand(2, 10))->state('belongs_to_author')->create();
        $routing = new Routing();

        foreach ($models as $model) {
            $routing->add((string)$model->getScoutKey(), (string)$model->searchableRouting());
        }

        $this->assertEquals($routing, $this->routingFactory->makeFromModels($models));
    }

    public function test_relations_can_be_preloaded(): void
    {
        $models = factory(Book::class, 5)
            ->state('belongs_to_author')
            ->create()
            ->fresh();

        $this->assertDatabaseQueriesCount(1, function () use ($models) {
            $this->routingFactory->makeFromModels($models);
        });
    }
}
