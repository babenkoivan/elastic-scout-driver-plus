<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\QueryParameters\ParameterCollection;
use ElasticScoutDriverPlus\QueryParameters\Shared\AnalyzerParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\AutoGenerateSynonymsPhraseQueryParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\BoostParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\FieldsParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\FuzzinessParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\FuzzyRewriteParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\FuzzyTranspositionsParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\LenientParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\MaxExpansionsParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\MinimumShouldMatchParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\OperatorParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\PrefixLengthParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\QueryStringParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\SlopParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\TieBreakerParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\TypeParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\ZeroTermsQueryParameter;
use ElasticScoutDriverPlus\QueryParameters\Transformers\FlatArrayTransformer;
use ElasticScoutDriverPlus\QueryParameters\Validators\AllOfValidator;

final class MultiMatchQueryBuilder extends AbstractParameterizedQueryBuilder
{
    use FieldsParameter;
    use QueryStringParameter;
    use TypeParameter;
    use AnalyzerParameter;
    use BoostParameter;
    use OperatorParameter;
    use MinimumShouldMatchParameter;
    use FuzzinessParameter;
    use LenientParameter;
    use PrefixLengthParameter;
    use MaxExpansionsParameter;
    use FuzzyRewriteParameter;
    use ZeroTermsQueryParameter;
    use AutoGenerateSynonymsPhraseQueryParameter;
    use FuzzyTranspositionsParameter;
    use TieBreakerParameter;
    use SlopParameter;

    /**
     * @var string
     */
    protected $type = 'multi_match';

    public function __construct()
    {
        $this->parameters = new ParameterCollection();
        $this->parameterValidator = new AllOfValidator(['fields', 'query']);
        $this->parameterTransformer = new FlatArrayTransformer();
    }
}
