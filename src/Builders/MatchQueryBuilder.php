<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\Builders\QueryParameters\Collection;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\AnalyzerParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\AutoGenerateSynonymsPhraseQueryParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\FieldParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\FuzzinessParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\FuzzyRewriteParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\FuzzyTranspositionsParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\LenientParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\MaxExpansionsParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\MinimumShouldMatchParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\OperatorParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\PrefixLengthParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\QueryStringParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\ZeroTermsQueryParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Transformers\GroupedArrayTransformer;
use ElasticScoutDriverPlus\Builders\QueryParameters\Validators\AllOfValidator;

final class MatchQueryBuilder extends AbstractParameterizedQueryBuilder
{
    use FieldParameter;
    use QueryStringParameter;
    use AnalyzerParameter;
    use AutoGenerateSynonymsPhraseQueryParameter;
    use FuzzinessParameter;
    use MaxExpansionsParameter;
    use PrefixLengthParameter;
    use FuzzyTranspositionsParameter;
    use FuzzyRewriteParameter;
    use LenientParameter;
    use OperatorParameter;
    use MinimumShouldMatchParameter;
    use ZeroTermsQueryParameter;

    /**
     * @var string
     */
    protected $query = 'match';

    public function __construct()
    {
        $this->parameters = new Collection();
        $this->parameterValidator = new AllOfValidator(['field', 'query']);
        $this->parameterTransformer = new GroupedArrayTransformer('field');
    }
}
