<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Factories;

use Elastic\Adapter\Documents\Routing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class RoutingFactory implements RoutingFactoryInterface
{
    public function makeFromModels(Collection $models): Routing
    {
        $routing = new Routing();

        foreach ($models->withSearchableRelations() as $model) {
            /** @var Model $model */
            if ($value = $model->searchableRouting()) {
                $routing->add((string)$model->getScoutKey(), (string)$value);
            }
        }

        return $routing;
    }
}
