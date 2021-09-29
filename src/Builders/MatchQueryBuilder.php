<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\QueryParameters\ParameterCollection;
use ElasticScoutDriverPlus\QueryParameters\Shared\AnalyzerParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\AutoGenerateSynonymsPhraseQueryParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\BoostParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\FieldParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\FuzzinessParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\FuzzyRewriteParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\FuzzyTranspositionsParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\LenientParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\MaxExpansionsParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\MinimumShouldMatchParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\OperatorParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\PrefixLengthParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\QueryStringParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\ZeroTermsQueryParameter;
use ElasticScoutDriverPlus\QueryParameters\Transformers\GroupedArrayTransformer;
use ElasticScoutDriverPlus\QueryParameters\Validators\AllOfValidator;

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
    use BoostParameter;

    /**
     * @var string
     */
    protected $type = 'match';

    public function __construct()
    {
        $this->parameters = new ParameterCollection();
        $this->parameterValidator = new AllOfValidator(['field', 'query']);
        $this->parameterTransformer = new GroupedArrayTransformer('field');
    }
}
