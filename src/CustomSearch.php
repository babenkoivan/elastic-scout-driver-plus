<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus;

use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use ElasticScoutDriverPlus\Builders\ExistsQueryBuilder;
use ElasticScoutDriverPlus\Builders\FuzzyQueryBuilder;
use ElasticScoutDriverPlus\Builders\IdsQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchAllQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchNoneQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchPhrasePrefixQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchPhraseQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchQueryBuilder;
use ElasticScoutDriverPlus\Builders\MultiMatchQueryBuilder;
use ElasticScoutDriverPlus\Builders\NestedQueryBuilder;
use ElasticScoutDriverPlus\Builders\PrefixQueryBuilder;
use ElasticScoutDriverPlus\Builders\RangeQueryBuilder;
use ElasticScoutDriverPlus\Builders\RawQueryBuilder;
use ElasticScoutDriverPlus\Builders\RegexpQueryBuilder;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;

trait CustomSearch
{
    /**
     * @return SearchRequestBuilder&BoolQueryBuilder
     */
    public static function boolSearch(): SearchRequestBuilder
    {
        return new SearchRequestBuilder(new static(), new BoolQueryBuilder());
    }

    /**
     * @return SearchRequestBuilder&RawQueryBuilder
     */
    public static function rawSearch(): SearchRequestBuilder
    {
        return new SearchRequestBuilder(new static(), new RawQueryBuilder());
    }

    /**
     * @return SearchRequestBuilder&NestedQueryBuilder
     */
    public static function nestedSearch(): SearchRequestBuilder
    {
        return new SearchRequestBuilder(new static(), new NestedQueryBuilder());
    }

    /**
     * @return SearchRequestBuilder&MatchAllQueryBuilder
     */
    public static function matchAllSearch(): SearchRequestBuilder
    {
        return new SearchRequestBuilder(new static(), new MatchAllQueryBuilder());
    }

    /**
     * @return SearchRequestBuilder&MatchNoneQueryBuilder
     */
    public static function matchNoneSearch(): SearchRequestBuilder
    {
        return new SearchRequestBuilder(new static(), new MatchNoneQueryBuilder());
    }

    /**
     * @return SearchRequestBuilder&MatchQueryBuilder
     */
    public static function matchSearch(): SearchRequestBuilder
    {
        return new SearchRequestBuilder(new static(), new MatchQueryBuilder());
    }

    /**
     * @return SearchRequestBuilder&MatchPhraseQueryBuilder
     */
    public static function matchPhraseSearch(): SearchRequestBuilder
    {
        return new SearchRequestBuilder(new static(), new MatchPhraseQueryBuilder());
    }

    /**
     * @return SearchRequestBuilder&MatchPhrasePrefixQueryBuilder
     */
    public static function matchPhrasePrefixSearch(): SearchRequestBuilder
    {
        return new SearchRequestBuilder(new static(), new MatchPhrasePrefixQueryBuilder());
    }

    /**
     * @return SearchRequestBuilder&MultiMatchQueryBuilder
     */
    public static function multiMatchSearch(): SearchRequestBuilder
    {
        return new SearchRequestBuilder(new static(), new MultiMatchQueryBuilder());
    }

    /**
     * @return SearchRequestBuilder&ExistsQueryBuilder
     */
    public static function existsSearch(): SearchRequestBuilder
    {
        return new SearchRequestBuilder(new static(), new ExistsQueryBuilder());
    }

    /**
     * @return SearchRequestBuilder&FuzzyQueryBuilder
     */
    public static function fuzzySearch(): SearchRequestBuilder
    {
        return new SearchRequestBuilder(new static(), new FuzzyQueryBuilder());
    }

    /**
     * @return SearchRequestBuilder&IdsQueryBuilder
     */
    public static function idsSearch(): SearchRequestBuilder
    {
        return new SearchRequestBuilder(new static(), new IdsQueryBuilder());
    }

    /**
     * @return SearchRequestBuilder&PrefixQueryBuilder
     */
    public static function prefixSearch(): SearchRequestBuilder
    {
        return new SearchRequestBuilder(new static(), new PrefixQueryBuilder());
    }

    /**
     * @return SearchRequestBuilder&RangeQueryBuilder
     */
    public static function rangeSearch(): SearchRequestBuilder
    {
        return new SearchRequestBuilder(new static(), new RangeQueryBuilder());
    }

    /**
     * @return SearchRequestBuilder&RegexpQueryBuilder
     */
    public static function regexpSearch(): SearchRequestBuilder
    {
        return new SearchRequestBuilder(new static(), new RegexpQueryBuilder());
    }
}
