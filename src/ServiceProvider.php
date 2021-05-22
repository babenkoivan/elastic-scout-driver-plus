<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider as AbstractServiceProvider;

final class ServiceProvider extends AbstractServiceProvider
{
    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->app->bind(\ElasticScoutDriver\Engine::class, static function (Application $app) {
            return $app->make(\ElasticScoutDriverPlus\Engine::class);
        });
    }
}
