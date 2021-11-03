<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\QueryParameters\ParameterCollection;
use ElasticScoutDriverPlus\QueryParameters\Shared\BoostParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\FieldParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\RelationParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\TimeZoneParameter;
use ElasticScoutDriverPlus\QueryParameters\Transformers\GroupedArrayTransformer;
use ElasticScoutDriverPlus\QueryParameters\Validators\AllOfValidator;
use ElasticScoutDriverPlus\QueryParameters\Validators\CompoundValidator;
use ElasticScoutDriverPlus\QueryParameters\Validators\OneOfValidator;

final class RangeQueryBuilder extends AbstractParameterizedQueryBuilder
{
    use FieldParameter;
    use RelationParameter;
    use BoostParameter;
    use TimeZoneParameter;

    /**
     * @var string
     */
    protected $type = 'range';

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
