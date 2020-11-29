<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\Builders\QueryParameters\Collection;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\FieldParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\FuzzinessParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\MaxExpansionsParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\PrefixLengthParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\RewriteParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\ValueParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Transformers\GroupedArrayTransformer;
use ElasticScoutDriverPlus\Builders\QueryParameters\Validators\AllOfValidator;

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
        $this->parameters = new Collection();
        $this->parameterValidator = new AllOfValidator(['field', 'value']);
        $this->parameterTransformer = new GroupedArrayTransformer('field');
    }

    public function transpositions(bool $transpositions): self
    {
        $this->parameters->put('transpositions', $transpositions);
        return $this;
    }
}
