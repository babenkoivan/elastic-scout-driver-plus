<?php
declare(strict_types=1);

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
        queueRemoveFromSearch as baseQueueRemoveFromSearch;
    }

    /**
     * @param Closure|QueryBuilderInterface|array|null $query
     */
    public static function searchQuery($query = null): SearchRequestBuilder
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
        if (!$this->usesElasticDriver()) {
            $this->baseQueueRemoveFromSearch($models);
            return;
        }

        if ($models->isEmpty()) {
            return;
        }

        if (!config('scout.queue')) {
            $models->first()->searchableUsing()->delete($models);
            return;
        }

        dispatch(new RemoveFromSearch($models))
            ->onQueue($models->first()->syncWithSearchUsingQueue())
            ->onConnection($models->first()->syncWithSearchUsing());
    }

    protected function usesElasticDriver(): bool
    {
        return is_a($this->searchableUsing(), Engine::class);
    }
}
