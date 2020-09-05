<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\Builders\SharedParameters\AnalyzerParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\FieldParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\MaxExpansionsParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\SlopParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\TextParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\ZeroTermsQueryParameter;
use ElasticScoutDriverPlus\Exceptions\QueryBuilderException;

final class MatchPhrasePrefixQueryBuilder implements QueryBuilderInterface
{
    use FieldParameter;
    use TextParameter;
    use AnalyzerParameter;
    use MaxExpansionsParameter;
    use SlopParameter;
    use ZeroTermsQueryParameter;

    public function buildQuery(): array
    {
        if (!isset($this->field, $this->text)) {
            throw new QueryBuilderException('Field and text have to be specified');
        }

        $matchPhrasePrefix = [
            $this->field => [
                'query' => $this->text,
            ],
        ];

        if (isset($this->analyzer)) {
            $matchPhrasePrefix[$this->field]['analyzer'] = $this->analyzer;
        }

        if (isset($this->maxExpansions)) {
            $matchPhrasePrefix[$this->field]['max_expansions'] = $this->maxExpansions;
        }

        if (isset($this->slop)) {
            $matchPhrasePrefix[$this->field]['slop'] = $this->slop;
        }

        if (isset($this->zeroTermsQuery)) {
            $matchPhrasePrefix[$this->field]['zero_terms_query'] = $this->zeroTermsQuery;
        }

        return [
            'match_phrase_prefix' => $matchPhrasePrefix,
        ];
    }
}
