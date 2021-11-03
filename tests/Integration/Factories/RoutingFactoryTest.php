<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration\Factories;

use ElasticAdapter\Documents\Routing;
use ElasticScoutDriverPlus\Factories\RoutingFactory;
use ElasticScoutDriverPlus\Tests\App\Book;
use ElasticScoutDriverPlus\Tests\Integration\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\Factories\RoutingFactory
 *
 * @uses   \ElasticScoutDriverPlus\Engine
 * @uses   \ElasticScoutDriverPlus\Factories\DocumentFactory
 * @uses   \ElasticScoutDriverPlus\Searchable
 */
final class RoutingFactoryTest extends TestCase
{
    /**
     * @var RoutingFactory
     */
    private $routingFactory;

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
            $routing->add((string)$model->getScoutKey(), (string)$model->shardRouting());
        }

        $this->assertEquals($routing, $this->routingFactory->makeFromModels($models));
    }
}
