<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus;

use Elastic\ScoutDriver\Engine;
use Elastic\ScoutDriver\Factories\DocumentFactoryInterface;
use Elastic\ScoutDriverPlus\Engine as EnginePlus;
use Elastic\ScoutDriverPlus\Factories\DocumentFactory;
use Elastic\ScoutDriverPlus\Factories\RoutingFactory;
use Elastic\ScoutDriverPlus\Factories\RoutingFactoryInterface;
use Illuminate\Support\ServiceProvider as AbstractServiceProvider;

final class ServiceProvider extends AbstractServiceProvider
{
    public array $bindings = [
        Engine::class => EnginePlus::class,
        DocumentFactoryInterface::class => DocumentFactory::class,
        RoutingFactoryInterface::class => RoutingFactory::class,
    ];
}
