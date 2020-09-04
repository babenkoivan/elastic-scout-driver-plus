<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus;

use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use ElasticScoutDriverPlus\Builders\NestedQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchAllQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchNoneQueryBuilder;
use ElasticScoutDriverPlus\Builders\RawQueryBuilder;
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
}
