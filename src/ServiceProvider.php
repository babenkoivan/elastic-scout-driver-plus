<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus;

use ElasticScoutDriver\Engine;
use ElasticScoutDriver\Factories\DocumentFactoryInterface;
use ElasticScoutDriverPlus\Engine as EnginePlus;
use ElasticScoutDriverPlus\Factories\DocumentFactory;
use ElasticScoutDriverPlus\Factories\RoutingFactory;
use ElasticScoutDriverPlus\Factories\RoutingFactoryInterface;
use Illuminate\Support\ServiceProvider as AbstractServiceProvider;

final class ServiceProvider extends AbstractServiceProvider
{
    public array $bindings = [
        Engine::class => EnginePlus::class,
        DocumentFactoryInterface::class => DocumentFactory::class,
        RoutingFactoryInterface::class => RoutingFactory::class,
    ];
}
