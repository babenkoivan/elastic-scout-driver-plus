<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Factories;

use ElasticAdapter\Search\Hit;
use ElasticAdapter\Search\SearchResponse;
use ElasticScoutDriverPlus\Searchable\Aggregator;
use ElasticScoutDriverPlus\Searchable\ObjectIdEncrypter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class LazyModelFactory
{
    /**
     * @var Model
     */
    private $model;
    /**
     * @var SearchResponse
     */
    private $searchResponse;
    /**
     * @var Collection
     */
    private $cache;

    public function __construct(Model $model, SearchResponse $searchResponse)
    {
        $this->model = $model;
        $this->searchResponse = $searchResponse;
    }

    /**
     * @param int|string $id
     */
    public function makeById($id): ?Model
    {
        if (!isset($this->cache)) {
            if ($this->model instanceof Aggregator) {
                $this->cache = $this->fetchAggregatedModels();
            } else {
                $this->cache = $this->fetchModels();
            }
        }

        return $this->cache->get($id);
    }

    private function fetchModels(): Collection
    {
        if ($this->searchResponse->getHitsTotal() == 0) {
            return $this->model->newCollection();
        }

        // find document ids and their positions
        $documentIds = collect($this->searchResponse->getHits())->map(static function (Hit $hit) {
            return $hit->getDocument()->getId();
        })->all();

        $documentIdPositions = array_flip($documentIds);

        // make a query depending on soft deletes usage
        $modelQuery = in_array(SoftDeletes::class, class_uses_recursive(get_class($this->model))) ?
            $this->model->withTrashed() : $this->model->newQuery();

        // find models, filter and sort them according to the matched documents
        return $modelQuery->whereIn($this->model->getScoutKeyName(), $documentIds)->get()
            ->filter(static function (Model $model) use ($documentIds) {
                return in_array($model->getScoutKey(), $documentIds);
            })
            ->sortBy(static function (Model $model) use ($documentIdPositions) {
                return $documentIdPositions[$model->getScoutKey()];
            })
            ->mapWithKeys(static function (Model $model) {
                return [$model->getScoutKey() => $model];
            });
    }

    private function fetchAggregatedModels(): Collection
    {
        if ($this->searchResponse->getHitsTotal() == 0) {
            return $this->model->newCollection();
        }

        // find document ids and their positions
        $documentIds = collect($this->searchResponse->getHits())->map(function (Hit $hit) {
            return ObjectIdEncrypter::decryptSearchableKey($hit->getDocument()->getId());
        })->all();

        $documentIdPositions = array_flip($documentIds);

        $modelCollection = collect();
        foreach ($this->model->getModels() as $model) {
            // make a query depending on soft deletes usage
            $modelQuery = in_array(SoftDeletes::class, class_uses_recursive($model)) ?
                $model::query()->withTrashed() : $model::query();
            // find models, filter
            $modelCollection = $modelCollection->merge(
                $modelQuery->whereIn((new $model)->getScoutKeyName(), $documentIds)->get()
                    ->filter(function (Model $model) use ($documentIds) {
                        return in_array($model->getScoutKey(), $documentIds);
                    })
            );
        }

        // find models, filter and sort them according to the matched documents
        return $modelCollection->sortBy(function (Model $model) use ($documentIdPositions) {
                return $documentIdPositions[$model->getScoutKey()];
            })
            ->mapWithKeys(function (Model $model) {
                return [ObjectIdEncrypter::encrypt($model) => $model];
            });
    }
}
