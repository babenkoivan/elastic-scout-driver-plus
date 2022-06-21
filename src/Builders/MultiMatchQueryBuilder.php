<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Builders;

use Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\AnalyzerParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\AutoGenerateSynonymsPhraseQueryParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\BoostParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\FieldsParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\FuzzinessParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\FuzzyRewriteParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\FuzzyTranspositionsParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\LenientParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\MaxExpansionsParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\MinimumShouldMatchParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\OperatorParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\PrefixLengthParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\QueryStringParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\SlopParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\TieBreakerParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\TypeParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\ZeroTermsQueryParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Transformers\FlatArrayTransformer;
use Elastic\ScoutDriverPlus\QueryParameters\Validators\AllOfValidator;

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

    protected string $type = 'multi_match';

    public function __construct()
    {
        $this->parameters = new ParameterCollection();
        $this->parameterValidator = new AllOfValidator(['fields', 'query']);
        $this->parameterTransformer = new FlatArrayTransformer();
    }
}
