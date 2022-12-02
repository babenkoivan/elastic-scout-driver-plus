<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Builders;

use Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\FieldParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\IgnoreUnmappedParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\ValidationMethodParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Transformers\CallbackArrayTransformer;
use Elastic\ScoutDriverPlus\QueryParameters\Validators\AllOfValidator;

final class GeoBoundingBoxQueryBuilder extends AbstractParameterizedQueryBuilder
{
    use FieldParameter;
    use ValidationMethodParameter;
    use IgnoreUnmappedParameter;

    protected string $type = 'geo_bounding_box';

    public function __construct()
    {
        $this->parameters = new ParameterCollection();

        $this->parameterValidator = new AllOfValidator(['field', 'top_left', 'bottom_right']);

        $this->parameterTransformer = new CallbackArrayTransformer(
            static fn (ParameterCollection $parameters) => array_merge(
                [$parameters->get('field') => $parameters->only(['top_left', 'bottom_right'])->toArray()],
                $parameters->except(['field', 'top_left', 'bottom_right'])->excludeEmpty()->toArray()
            )
        );
    }

    public function topLeft(float $lat, float $lon): self
    {
        $this->parameters->put('top_left', ['lat' => $lat, 'lon' => $lon]);
        return $this;
    }

    public function bottomRight(float $lat, float $lon): self
    {
        $this->parameters->put('bottom_right', ['lat' => $lat, 'lon' => $lon]);
        return $this;
    }
}