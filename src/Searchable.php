<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus;

use Closure;
use ElasticScoutDriverPlus\Builders\QueryBuilderInterface;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;
use ElasticScoutDriverPlus\Jobs\RemoveFromSearch;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Scout\Searchable as BaseSearchable;

trait Searchable
{
    use BaseSearchable {
        queueRemoveFromSearch as public parentQueueRemoveFromSearch;
    }

    /**
     * @param Closure|QueryBuilderInterface|array $query
     */
    public static function searchQuery($query): SearchRequestBuilder
    {
        return new SearchRequestBuilder($query, new static());
    }

    /**
     * @return string|int|null
     */
    public function shardRouting()
    {
        return null;
    }

    /**
     * @return array|string|null
     */
    public function searchableWith()
    {
        return null;
    }

    /**
     * @param Collection $models
     *
     * @return void
     */
    public function queueRemoveFromSearch($models)
    {
        if ($models->isEmpty()) {
            return;
        }
        
        if (config('scout.driver') !== 'elastic') {
            return $this->parentQueueRemoveFromSearch($models);
        }

        if (!config('scout.queue')) {
            return $models->first()->searchableUsing()->delete($models);
        }

        dispatch(new RemoveFromSearch($models))
            ->onQueue($models->first()->syncWithSearchUsingQueue())
            ->onConnection($models->first()->syncWithSearchUsing());
    }
}
