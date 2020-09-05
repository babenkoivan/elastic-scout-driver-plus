<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\Builders\SharedParameters\AnalyzerParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\FieldParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\SlopParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\TextParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\ZeroTermsQueryParameter;
use ElasticScoutDriverPlus\Exceptions\QueryBuilderException;

final class MatchPhraseQueryBuilder implements QueryBuilderInterface
{
    use FieldParameter;
    use TextParameter;
    use SlopParameter;
    use AnalyzerParameter;
    use ZeroTermsQueryParameter;

    public function buildQuery(): array
    {
        if (!isset($this->field, $this->text)) {
            throw new QueryBuilderException('Field and text have to be specified');
        }

        $matchPhrase = [
            $this->field => [
                'query' => $this->text,
            ],
        ];

        if (isset($this->slop)) {
            $matchPhrase[$this->field]['slop'] = $this->slop;
        }

        if (isset($this->analyzer)) {
            $matchPhrase[$this->field]['analyzer'] = $this->analyzer;
        }

        if (isset($this->zeroTermsQuery)) {
            $matchPhrase[$this->field]['zero_terms_query'] = $this->zeroTermsQuery;
        }

        return [
            'match_phrase' => $matchPhrase,
        ];
    }
}
