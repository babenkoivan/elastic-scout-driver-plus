<?php
declare(strict_types=1);

namespace ElasticScoutDriverPlus;

use ElasticScoutDriver\Engine;
use ElasticScoutDriverPlus\Decorators\EngineDecorator;
use ElasticScoutDriverPlus\Searchable\AggregatorObserver;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider as AbstractServiceProvider;

final class ServiceProvider extends AbstractServiceProvider
{
    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->app->extend(Engine::class, function (Engine $engine, Application $app) {
            return $app->make(EngineDecorator::class, compact('engine'));
        });

        $this->app->singleton(AggregatorObserver::class, AggregatorObserver::class);
    }
}
