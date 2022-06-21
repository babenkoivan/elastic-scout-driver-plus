<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Builders;

use Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\AnalyzerParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\FieldParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\QueryStringParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\SlopParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\ZeroTermsQueryParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Transformers\GroupedArrayTransformer;
use Elastic\ScoutDriverPlus\QueryParameters\Validators\AllOfValidator;

final class MatchPhraseQueryBuilder extends AbstractParameterizedQueryBuilder
{
    use FieldParameter;
    use QueryStringParameter;
    use SlopParameter;
    use AnalyzerParameter;
    use ZeroTermsQueryParameter;

    protected string $type = 'match_phrase';

    public function __construct()
    {
        $this->parameters = new ParameterCollection();
        $this->parameterValidator = new AllOfValidator(['field', 'query']);
        $this->parameterTransformer = new GroupedArrayTransformer('field');
    }
}
