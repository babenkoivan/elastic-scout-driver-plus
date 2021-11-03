<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\QueryParameters\ParameterCollection;
use ElasticScoutDriverPlus\QueryParameters\Shared\AnalyzerParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\FieldParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\MaxExpansionsParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\QueryStringParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\SlopParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\ZeroTermsQueryParameter;
use ElasticScoutDriverPlus\QueryParameters\Transformers\GroupedArrayTransformer;
use ElasticScoutDriverPlus\QueryParameters\Validators\AllOfValidator;

final class MatchPhrasePrefixQueryBuilder extends AbstractParameterizedQueryBuilder
{
    use FieldParameter;
    use QueryStringParameter;
    use AnalyzerParameter;
    use MaxExpansionsParameter;
    use SlopParameter;
    use ZeroTermsQueryParameter;

    /**
     * @var string
     */
    protected $type = 'match_phrase_prefix';

    public function __construct()
    {
        $this->parameters = new ParameterCollection();
        $this->parameterValidator = new AllOfValidator(['field', 'query']);
        $this->parameterTransformer = new GroupedArrayTransformer('field');
    }
}
