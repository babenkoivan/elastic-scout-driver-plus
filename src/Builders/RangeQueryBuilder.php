<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Builders;

use Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\BoostParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\FieldParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\RelationParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\TimeZoneParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Transformers\GroupedArrayTransformer;
use Elastic\ScoutDriverPlus\QueryParameters\Validators\AllOfValidator;
use Elastic\ScoutDriverPlus\QueryParameters\Validators\CompoundValidator;
use Elastic\ScoutDriverPlus\QueryParameters\Validators\OneOfValidator;

final class RangeQueryBuilder extends AbstractParameterizedQueryBuilder
{
    use FieldParameter;
    use RelationParameter;
    use BoostParameter;
    use TimeZoneParameter;

    protected string $type = 'range';

    public function __construct()
    {
        $this->parameters = new ParameterCollection();

        $this->parameterValidator = new CompoundValidator(
            new AllOfValidator(['field']),
            new OneOfValidator(['gt', 'gte', 'lt', 'lte'])
        );

        $this->parameterTransformer = new GroupedArrayTransformer('field');
    }

    /**
     * @param string|int $value
     */
    public function gt($value): self
    {
        $this->parameters->put('gt', $value);
        return $this;
    }

    /**
     * @param string|int $value
     */
    public function gte($value): self
    {
        $this->parameters->put('gte', $value);
        return $this;
    }

    /**
     * @param string|int $value
     */
    public function lt($value): self
    {
        $this->parameters->put('lt', $value);
        return $this;
    }

    /**
     * @param string|int $value
     */
    public function lte($value): self
    {
        $this->parameters->put('lte', $value);
        return $this;
    }

    public function format(string $format): self
    {
        $this->parameters->put('format', $format);
        return $this;
    }
}
