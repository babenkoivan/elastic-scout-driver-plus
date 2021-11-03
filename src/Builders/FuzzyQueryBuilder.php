<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\QueryParameters\ParameterCollection;
use ElasticScoutDriverPlus\QueryParameters\Shared\FieldParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\FuzzinessParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\MaxExpansionsParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\PrefixLengthParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\RewriteParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\ValueParameter;
use ElasticScoutDriverPlus\QueryParameters\Transformers\GroupedArrayTransformer;
use ElasticScoutDriverPlus\QueryParameters\Validators\AllOfValidator;

final class FuzzyQueryBuilder extends AbstractParameterizedQueryBuilder
{
    use FieldParameter;
    use ValueParameter;
    use FuzzinessParameter;
    use MaxExpansionsParameter;
    use PrefixLengthParameter;
    use RewriteParameter;

    /**
     * @var string
     */
    protected $type = 'fuzzy';

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
