<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Builders;

use Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\FieldParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\IgnoreUnmappedParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\RelationParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Transformers\GroupedArrayTransformer;
use Elastic\ScoutDriverPlus\QueryParameters\Validators\AllOfValidator;

final class GeoShapeQueryBuilder extends AbstractParameterizedQueryBuilder
{
    use FieldParameter;
    use RelationParameter;
    use IgnoreUnmappedParameter;

    protected string $type = 'geo_shape';

    public function __construct()
    {
        $this->parameters = new ParameterCollection();
        $this->parameterValidator = new AllOfValidator(['shape', 'relation']);
        $this->parameterTransformer = new GroupedArrayTransformer('field');
    }

    public function shape(string $type, array $coordinates): self
    {
        $this->parameters->put('shape', compact('type', 'coordinates'));
        return $this;
    }
}
