<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\Builders\QueryParameters\Collection;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\AnalyzerParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\AutoGenerateSynonymsPhraseQueryParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\BoostParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\FieldsParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\FuzzinessParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\FuzzyRewriteParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\FuzzyTranspositionsParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\LenientParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\MaxExpansionsParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\MinimumShouldMatchParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\OperatorParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\PrefixLengthParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\QueryStringParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\SlopParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\TieBreakerParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\TypeParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\ZeroTermsQueryParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Transformers\FlatArrayTransformer;
use ElasticScoutDriverPlus\Builders\QueryParameters\Validators\AllOfValidator;

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
        $this->parameters = new Collection();
        $this->validator = new AllOfValidator(['fields', 'query']);
        $this->transformer = new FlatArrayTransformer();
    }
}
