<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Builders;

use Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\FieldParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\FuzzinessParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\MaxExpansionsParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\PrefixLengthParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\RewriteParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\ValueParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Transformers\GroupedArrayTransformer;
use Elastic\ScoutDriverPlus\QueryParameters\Validators\AllOfValidator;

final class FuzzyQueryBuilder extends AbstractParameterizedQueryBuilder
{
    use FieldParameter;
    use ValueParameter;
    use FuzzinessParameter;
    use MaxExpansionsParameter;
    use PrefixLengthParameter;
    use RewriteParameter;

    protected string $type = 'fuzzy';

    public function __construct()
    {
        $this->parameters = new ParameterCollection();
        $this->parameterValidator = new AllOfValidator(['field', 'value']);
        $this->parameterTransformer = new GroupedArrayTransformer('field');
    }

    public function transpositions(bool $transpositions): self
    {
        $this->parameters->put('transpositions', $transpositions);
        return $this;
    }
}
