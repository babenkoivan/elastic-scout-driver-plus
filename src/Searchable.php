<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus;

use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;
use Laravel\Scout\Searchable as BaseSearchable;

trait Searchable
{
    use BaseSearchable;

    /**
     * @return string|int|null
     */
    public function shardRouting()
    {
        return null;
    }

    public static function searchRequest(): SearchRequestBuilder
    {
        return new SearchRequestBuilder(new static());
    }
}
