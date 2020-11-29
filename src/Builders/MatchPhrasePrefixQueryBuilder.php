<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\Builders\QueryParameters\Collection;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\AnalyzerParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\FieldParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\MaxExpansionsParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\QueryStringParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\SlopParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\ZeroTermsQueryParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Transformers\GroupedArrayTransformer;
use ElasticScoutDriverPlus\Builders\QueryParameters\Validators\AllOfValidator;

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
        $this->parameters = new Collection();
        $this->parameterValidator = new AllOfValidator(['field', 'query']);
        $this->parameterTransformer = new GroupedArrayTransformer('field');
    }
}
