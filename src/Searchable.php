<?php
declare(strict_types=1);

namespace Elastic\ScoutDriverPlus;

use Closure;
use Elastic\ScoutDriverPlus\Builders\QueryBuilderInterface;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Jobs\RemoveFromSearch;
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
    public static function searchQuery($query = null): SearchParametersBuilder
    {
        $builder = new SearchParametersBuilder(new static());

        if (isset($query)) {
            $builder->query($query);
        }

        return $builder;
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
