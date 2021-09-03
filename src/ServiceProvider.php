<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus;

use ElasticScoutDriver\Engine;
use ElasticScoutDriver\Factories\DocumentFactoryInterface;
use ElasticScoutDriverPlus\Engine as EnginePlus;
use ElasticScoutDriverPlus\Factories\DocumentFactory;
use Illuminate\Support\ServiceProvider as AbstractServiceProvider;

final class ServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    public $bindings = [
        Engine::class => EnginePlus::class,
        DocumentFactoryInterface::class => DocumentFactory::class,
    ];
}
