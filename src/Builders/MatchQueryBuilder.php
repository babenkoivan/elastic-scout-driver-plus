<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Builders;

use Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\AnalyzerParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\AutoGenerateSynonymsPhraseQueryParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\BoostParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\FieldParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\FuzzinessParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\FuzzyRewriteParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\FuzzyTranspositionsParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\LenientParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\MaxExpansionsParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\MinimumShouldMatchParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\OperatorParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\PrefixLengthParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\QueryStringParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\ZeroTermsQueryParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Transformers\GroupedArrayTransformer;
use Elastic\ScoutDriverPlus\QueryParameters\Validators\AllOfValidator;

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

    protected string $type = 'match';

    public function __construct()
    {
        $this->parameters = new ParameterCollection();
        $this->parameterValidator = new AllOfValidator(['field', 'query']);
        $this->parameterTransformer = new GroupedArrayTransformer('field');
    }
}
