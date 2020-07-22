<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Factories;

use ElasticAdapter\Search\SearchResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Collection as EloquentCollection;

class LazyModelFactory
{
    /**
     * @var array
     */
    private $searchableModels = [];
    /**
     * @var array
     */
    private $mappedIds = [];
    /**
     * @var array
     */
    private $mappedModels = [];

    public function __construct(BaseCollection $models, SearchResponse $searchResponse)
    {
        foreach ($models as $model) {
            $this->searchableModels[$model->searchableAs()] = $model;
        }

        foreach ($searchResponse->getHits() as $hit) {
            $this->mappedIds[$hit->getIndexName()][] = $hit->getDocument()->getId();
        }
    }

    public function makeByIndexNameAndDocumentId(string $indexName, string $documentId): ?Model
    {
        if (!isset($this->mappedModels[$indexName])) {
            $this->mappedModels[$indexName] = $this->mapModelsForIndex($indexName);
        }

        return $this->mappedModels[$indexName][$documentId] ?? null;
    }

    private function mapModelsForIndex(string $indexName): EloquentCollection
    {
        if (!isset($this->mappedIds[$indexName], $this->searchableModels[$indexName])) {
            return new EloquentCollection();
        }

        $ids = $this->mappedIds[$indexName];
        $searchableModel = $this->searchableModels[$indexName];

        $query = in_array(SoftDeletes::class, class_uses_recursive($searchableModel), true) ?
            $searchableModel->withTrashed() : $searchableModel->newQuery();

        $mappedModels = $query->whereIn($searchableModel->getScoutKeyName(), $ids)->get();

        return $mappedModels->mapWithKeys(static function (Model $model) {
            return [(string)$model->getScoutKey() => $model];
        });
    }
}
