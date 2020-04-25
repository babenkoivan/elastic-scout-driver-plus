<?php
declare(strict_types=1);

namespace ElasticScoutDriverPlus;

use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use ElasticScoutDriverPlus\Builders\RawQueryBuilder;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;

trait CustomSearch
{
    public static function boolSearch(): SearchRequestBuilder
    {
        return new SearchRequestBuilder(new static(), new BoolQueryBuilder(new static()));
    }

    public static function rawSearch(): SearchRequestBuilder
    {
        return new SearchRequestBuilder(new static(), new RawQueryBuilder());
    }
}
