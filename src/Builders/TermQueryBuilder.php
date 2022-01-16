<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\QueryParameters\ParameterCollection;
use ElasticScoutDriverPlus\QueryParameters\Shared\BoostParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\FieldParameter;
use ElasticScoutDriverPlus\QueryParameters\Transformers\GroupedArrayTransformer;
use ElasticScoutDriverPlus\QueryParameters\Validators\AllOfValidator;

final class TermQueryBuilder extends AbstractParameterizedQueryBuilder
{
    use FieldParameter;
    use BoostParameter;

    /**
     * @var string
     */
    protected $type = 'term';

    public function __construct()
    {
        $this->parameters = new ParameterCollection();
        $this->parameterValidator = new AllOfValidator(['field', 'value']);
        $this->parameterTransformer = new GroupedArrayTransformer('field');
    }

    /**
     * @param string|int|float|bool $value
     */
    public function value($value): self
    {
        $this->parameters->put('value', $value);
        return $this;
    }
}
