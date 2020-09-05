<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\Builders\SharedParameters\AnalyzerParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\AutoGenerateSynonymsPhraseQueryParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\FieldParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\FuzzinessParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\FuzzyTranspositionsParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\LenientParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\MaxExpansionsParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\MinimumShouldMatchParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\OperatorParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\PrefixLengthParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\RewriteParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\TextParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\ZeroTermsQueryParameter;
use ElasticScoutDriverPlus\Exceptions\QueryBuilderException;

final class MatchQueryBuilder implements QueryBuilderInterface
{
    use FieldParameter;
    use TextParameter;
    use AnalyzerParameter;
    use AutoGenerateSynonymsPhraseQueryParameter;
    use FuzzinessParameter;
    use MaxExpansionsParameter;
    use PrefixLengthParameter;
    use FuzzyTranspositionsParameter;
    use RewriteParameter;
    use LenientParameter;
    use OperatorParameter;
    use MinimumShouldMatchParameter;
    use ZeroTermsQueryParameter;

    public function buildQuery(): array
    {
        if (!isset($this->field, $this->text)) {
            throw new QueryBuilderException('Field and text have to be specified');
        }

        $match = [
            $this->field => [
                'query' => $this->text,
            ],
        ];

        if (isset($this->analyzer)) {
            $match[$this->field]['analyzer'] = $this->analyzer;
        }

        if (isset($this->autoGenerateSynonymsPhraseQuery)) {
            $match[$this->field]['auto_generate_synonyms_phrase_query'] = $this->autoGenerateSynonymsPhraseQuery;
        }

        if (isset($this->fuzziness)) {
            $match[$this->field]['fuzziness'] = $this->fuzziness;
        }

        if (isset($this->maxExpansions)) {
            $match[$this->field]['max_expansions'] = $this->maxExpansions;
        }

        if (isset($this->prefixLength)) {
            $match[$this->field]['prefix_length'] = $this->prefixLength;
        }

        if (isset($this->fuzzyTranspositions)) {
            $match[$this->field]['fuzzy_transpositions'] = $this->fuzzyTranspositions;
        }

        if (isset($this->rewrite)) {
            $match[$this->field]['fuzzy_rewrite'] = $this->rewrite;
        }

        if (isset($this->lenient)) {
            $match[$this->field]['lenient'] = $this->lenient;
        }

        if (isset($this->operator)) {
            $match[$this->field]['operator'] = $this->operator;
        }

        if (isset($this->minimumShouldMatch)) {
            $match[$this->field]['minimum_should_match'] = $this->minimumShouldMatch;
        }

        if (isset($this->zeroTermsQuery)) {
            $match[$this->field]['zero_terms_query'] = $this->zeroTermsQuery;
        }

        return compact('match');
    }
}
