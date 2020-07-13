<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus;

use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use ElasticScoutDriverPlus\Builders\RawQueryBuilder;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;

trait CustomSearch
{
    /**
     * @return SearchRequestBuilder&BoolQueryBuilder
     */
    public static function boolSearch(): SearchRequestBuilder
    {
        return new SearchRequestBuilder(new static(), new BoolQueryBuilder(new static()));
    }

    /**
     * @return SearchRequestBuilder&RawQueryBuilder
     */
    public static function rawSearch(): SearchRequestBuilder
    {
        return new SearchRequestBuilder(new static(), new RawQueryBuilder());
    }
}
