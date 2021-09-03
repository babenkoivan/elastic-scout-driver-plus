<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Factories;

use ElasticAdapter\Documents\Routing;
use Illuminate\Support\Collection;

interface RoutingFactoryInterface
{
    public function makeFromModels(Collection $models): Routing;
}
