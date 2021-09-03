<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Factories;

use ElasticAdapter\Documents\Routing;
use Illuminate\Support\Collection;

class RoutingFactory implements RoutingFactoryInterface
{
    public function makeFromModels(Collection $models): Routing
    {
        $routing = new Routing();

        foreach ($models as $model) {
            if ($value = $model->shardRouting()) {
                $routing->add((string)$model->getScoutKey(), (string)$value);
            }
        }

        return $routing;
    }
}
