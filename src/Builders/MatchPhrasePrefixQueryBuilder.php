<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Builders;

use Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\AnalyzerParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\FieldParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\MaxExpansionsParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\QueryStringParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\SlopParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\ZeroTermsQueryParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Transformers\GroupedArrayTransformer;
use Elastic\ScoutDriverPlus\QueryParameters\Validators\AllOfValidator;

final class MatchPhrasePrefixQueryBuilder extends AbstractParameterizedQueryBuilder
{
    use FieldParameter;
    use QueryStringParameter;
    use AnalyzerParameter;
    use MaxExpansionsParameter;
    use SlopParameter;
    use ZeroTermsQueryParameter;

    protected string $type = 'match_phrase_prefix';

    public function __construct()
    {
        $this->parameters = new ParameterCollection();
        $this->parameterValidator = new AllOfValidator(['field', 'query']);
        $this->parameterTransformer = new GroupedArrayTransformer('field');
    }
}
