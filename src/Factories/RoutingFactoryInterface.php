<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Factories;

use Elastic\Adapter\Documents\Routing;
use Illuminate\Support\Collection;

interface RoutingFactoryInterface
{
    public function makeFromModels(Collection $models): Routing;
}
