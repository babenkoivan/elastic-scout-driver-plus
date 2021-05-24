<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus;

use ElasticScoutDriverPlus\Engine;
use ElasticScoutDriver\Engine as BaseEngine;
use Illuminate\Support\ServiceProvider as AbstractServiceProvider;

final class ServiceProvider extends AbstractServiceProvider
{
    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->app->extend(BaseEngine::class, function ($baseEngine, $app) {
            return $app->make(Engine::class);
        });
    }
}
