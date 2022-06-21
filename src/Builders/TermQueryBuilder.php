<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Builders;

use Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\BoostParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\CaseInsensitiveParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\FieldParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Transformers\GroupedArrayTransformer;
use Elastic\ScoutDriverPlus\QueryParameters\Validators\AllOfValidator;

final class TermQueryBuilder extends AbstractParameterizedQueryBuilder
{
    use FieldParameter;
    use BoostParameter;
    use CaseInsensitiveParameter;

    protected string $type = 'term';

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
