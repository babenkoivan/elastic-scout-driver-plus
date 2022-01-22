<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus;

use ElasticScoutDriver\Engine;
use ElasticScoutDriver\Factories\DocumentFactoryInterface;
use ElasticScoutDriverPlus\Engine as EnginePlus;
use ElasticScoutDriverPlus\Factories\DocumentFactory;
use ElasticScoutDriverPlus\Factories\RoutingFactory;
use ElasticScoutDriverPlus\Factories\RoutingFactoryInterface;
use ElasticScoutDriverPlus\Jobs\RemoveFromSearch;
use Illuminate\Support\ServiceProvider as AbstractServiceProvider;
use Laravel\Scout\Scout;

final class ServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    public $bindings = [
        Engine::class => EnginePlus::class,
        DocumentFactoryInterface::class => DocumentFactory::class,
        RoutingFactoryInterface::class => RoutingFactory::class,
    ];

    /**
     * {@inheritDoc}
     */
    public function register()
    {
        if (config('scout.driver') === 'elastic' && property_exists(Scout::class, 'removeFromSearchJob')) {
            Scout::$removeFromSearchJob = RemoveFromSearch::class;
        }
    }
}
