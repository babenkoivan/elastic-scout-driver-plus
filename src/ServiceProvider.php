<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus;

use Elastic\ScoutDriver\Engine;
use Elastic\ScoutDriver\Factories\DocumentFactoryInterface;
use Elastic\ScoutDriverPlus\Engine as EnginePlus;
use Elastic\ScoutDriverPlus\Factories\DocumentFactory;
use Elastic\ScoutDriverPlus\Factories\RoutingFactory;
use Elastic\ScoutDriverPlus\Factories\RoutingFactoryInterface;
use Elastic\ScoutDriverPlus\Jobs\RemoveFromSearch;
use Illuminate\Support\ServiceProvider as AbstractServiceProvider;
use Laravel\Scout\EngineManager;
use Laravel\Scout\Jobs\RemoveFromSearch as DefaultRemoveFromSearch;
use Laravel\Scout\Scout;

final class ServiceProvider extends AbstractServiceProvider
{
    public array $bindings = [
        Engine::class => EnginePlus::class,
        DocumentFactoryInterface::class => DocumentFactory::class,
        RoutingFactoryInterface::class => RoutingFactory::class,
    ];

    /**
     * @return void
     */
    public function boot()
    {
        if (
            config('scout.driver') === 'elastic' &&
            property_exists(Scout::class, 'removeFromSearchJob') &&
            Scout::$removeFromSearchJob === DefaultRemoveFromSearch::class
        ) {
            Scout::removeFromSearchUsing(RemoveFromSearch::class);
        }

        resolve(EngineManager::class)->extend('null', static fn () => resolve(NullEngine::class));
    }
}
