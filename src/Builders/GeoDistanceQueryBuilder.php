<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\QueryParameters\ParameterCollection;
use ElasticScoutDriverPlus\QueryParameters\Shared\FieldParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\IgnoreUnmappedParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\LatParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\LonParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\ValidationMethodParameter;
use ElasticScoutDriverPlus\QueryParameters\Transformers\CallbackArrayTransformer;
use ElasticScoutDriverPlus\QueryParameters\Validators\AllOfValidator;

final class GeoDistanceQueryBuilder extends AbstractParameterizedQueryBuilder
{
    use FieldParameter;
    use LatParameter;
    use LonParameter;
    use ValidationMethodParameter;
    use IgnoreUnmappedParameter;

    /**
     * @var string
     */
    protected $type = 'geo_distance';

    public function __construct()
    {
        $this->parameters = new ParameterCollection();

        $this->parameterValidator = new AllOfValidator(['field', 'distance', 'lat', 'lon']);

        $this->parameterTransformer = new CallbackArrayTransformer(static function (ParameterCollection $parameters) {
            return array_merge(
                [$parameters->get('field') => $parameters->only(['lat', 'lon'])->toArray()],
                $parameters->except(['field', 'lat', 'lon'])->excludeEmpty()->toArray()
            );
        });
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
