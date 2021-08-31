<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus;

use Closure;
use ElasticScoutDriverPlus\Builders\QueryBuilderInterface;
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

    /**
     * @param Closure|QueryBuilderInterface|array $query
     */
    public static function searchQuery($query): SearchRequestBuilder
    {
        return new SearchRequestBuilder($query, new static());
    }
}
