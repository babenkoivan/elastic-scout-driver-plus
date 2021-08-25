<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Factories;

use ElasticAdapter\Documents\Routing;
use ElasticScoutDriverPlus\ShardRouting;
use Illuminate\Support\Collection;

class RoutingFactory
{
    public static function makeFromModels(Collection $models): Routing
    {
        $routing = new Routing();

        foreach ($models as $model) {
            if (in_array(ShardRouting::class, class_uses_recursive($model), true)) {
                $routing->add((string)$model->getScoutKey(), $model->getRouting());
            }
        }

        return $routing;
    }
}
