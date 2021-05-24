<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus;

use ElasticScoutDriver\Engine;
use ElasticScoutDriverPlus\Engine as EnginePlus;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider as AbstractServiceProvider;

final class ServiceProvider extends AbstractServiceProvider
{
    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->app->extend(Engine::class, static function (Engine $engine, Application $app) {
            return $app->make(EnginePlus::class);
        });
    }
}
