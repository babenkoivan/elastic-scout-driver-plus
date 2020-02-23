<?php
declare(strict_types=1);

namespace ElasticScoutDriverPlus\Factories;

use ElasticAdapter\Search\Hit;
use ElasticAdapter\Search\SearchResponse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class LazyModelFactory implements LazyModelFactoryInterface
{
    /**
     * @var Model
     */
    private $model;
    /**
     * @var SearchResponse
     */
    private $searchResponse;

    public function __construct(Model $model, SearchResponse $searchResponse)
    {
        $this->model = $model;
        $this->searchResponse = $searchResponse;
    }

    /**
     * {@inheritDoc}
     */
    public function makeById($id): ?Model
    {
        if (!isset($this->models)) {
            $this->models = $this->mapModels();
        }

        return $this->models->get($id);
    }

    private function mapModels(): Collection
    {
        if ($this->searchResponse->getHitsTotal() == 0) {
            return $this->model->newCollection();
        }

        // find document ids and their positions
        $documentIds = collect($this->searchResponse->getHits())->map(function (Hit $hit) {
            return $hit->getDocument()->getId();
        })->all();

        $documentIdPositions = array_flip($documentIds);

        // make a query depending on soft deletes usage
        $modelQuery = in_array(SoftDeletes::class, class_uses_recursive(get_class($this->model))) ?
            $this->model->withTrashed() : $this->model->newQuery();

        // find models, filter and sort them according to the matched documents
        return $modelQuery->whereIn($this->model->getScoutKeyName(), $documentIds)->get()
            ->filter(function (Model $model) use ($documentIds) {
                return in_array($model->getScoutKey(), $documentIds);
            })
            ->sortBy(function (Model $model) use ($documentIdPositions) {
                return $documentIdPositions[$model->getScoutKey()];
            })
            ->mapWithKeys(function (Model $model) {
                return [$model->getScoutKey() => $model];
            });
    }
}
