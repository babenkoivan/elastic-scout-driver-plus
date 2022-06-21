<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Builders;

use Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\FieldParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\IgnoreUnmappedParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\LatParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\LonParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\ValidationMethodParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Transformers\CallbackArrayTransformer;
use Elastic\ScoutDriverPlus\QueryParameters\Validators\AllOfValidator;

final class GeoDistanceQueryBuilder extends AbstractParameterizedQueryBuilder
{
    use FieldParameter;
    use LatParameter;
    use LonParameter;
    use ValidationMethodParameter;
    use IgnoreUnmappedParameter;

    protected string $type = 'geo_distance';

    public function __construct()
    {
        $this->parameters = new ParameterCollection();

        $this->parameterValidator = new AllOfValidator(['field', 'distance', 'lat', 'lon']);

        $this->parameterTransformer = new CallbackArrayTransformer(
            static fn (ParameterCollection $parameters) => array_merge(
                [$parameters->get('field') => $parameters->only(['lat', 'lon'])->toArray()],
                $parameters->except(['field', 'lat', 'lon'])->excludeEmpty()->toArray()
            )
        );
    }

    public function distance(string $distance): self
    {
        $this->parameters->put('distance', $distance);
        return $this;
    }

    public function distanceType(string $distanceType): self
    {
        $this->parameters->put('distance_type', $distanceType);
        return $this;
    }
}
