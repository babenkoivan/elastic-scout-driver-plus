<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\Builders\SharedParameters\AnalyzerParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\AutoGenerateSynonymsPhraseQueryParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\BoostParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\FieldsParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\FuzzinessParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\FuzzyTranspositionsParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\LenientParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\MaxExpansionsParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\MinimumShouldMatchParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\OperatorParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\PrefixLengthParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\RewriteParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\SlopParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\TextParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\TieBreakerParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\TypeParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\ZeroTermsQueryParameter;
use ElasticScoutDriverPlus\Exceptions\QueryBuilderException;
use ElasticScoutDriverPlus\Support\ObjectVariables;

final class MultiMatchQueryBuilder implements QueryBuilderInterface
{
    use TextParameter;
    use FieldsParameter;
    use TypeParameter;
    use AnalyzerParameter;
    use BoostParameter;
    use OperatorParameter;
    use MinimumShouldMatchParameter;
    use FuzzinessParameter;
    use LenientParameter;
    use PrefixLengthParameter;
    use MaxExpansionsParameter;
    use RewriteParameter;
    use ZeroTermsQueryParameter;
    use AutoGenerateSynonymsPhraseQueryParameter;
    use FuzzyTranspositionsParameter;
    use TieBreakerParameter;
    use SlopParameter;
    use ObjectVariables;

    public function buildQuery(): array
    {
        if (!isset($this->fields, $this->text)) {
            throw new QueryBuilderException('Fields and text have to be specified');
        }

        return [
            'multi_match' => array_merge(
                ['query' => $this->text],
                $this->getObjectVariables()
                    ->except('text')
                    ->whereNotNull()
                    ->toArray()
            ),
        ];
    }
}
