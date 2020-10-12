<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Factories;

use ElasticAdapter\Search\SearchResponse;
use ElasticScoutDriverPlus\Support\ModelScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class LazyModelFactory
{
    /**
     * @var ModelScope
     */
    private $modelScope;
    /**
     * List of model ids keyed by index name
     *
     * @var array
     */
    private $mappedIds = [];
    /**
     * List of models keyed by index name
     *
     * @var array
     */
    private $mappedModels = [];

    public function __construct(SearchResponse $searchResponse, ModelScope $modelScope)
    {
        $this->modelScope = $modelScope;

        foreach ($searchResponse->getHits() as $hit) {
            $this->mappedIds[$hit->getIndexName()][] = $hit->getDocument()->getId();
        }
    }

    public function makeByIndexNameAndDocumentId(string $indexName, string $documentId): ?Model
    {
        if (!isset($this->mappedModels[$indexName])) {
            $this->mappedModels[$indexName] = $this->findModelsForIndex($indexName);
        }

        return $this->mappedModels[$indexName][$documentId] ?? null;
    }

    private function findModelsForIndex(string $indexName): Collection
    {
        $modelClass = $this->modelScope->resolveModelClass($indexName);

        if (!isset($this->mappedIds[$indexName], $modelClass)) {
            return new Collection();
        }

        $ids = $this->mappedIds[$indexName];
        $model = new $modelClass();
        $scoutKeyName = $model->getScoutKeyName();
        $relations = $this->modelScope->resolveRelations($modelClass);

        $query = in_array(SoftDeletes::class, class_uses_recursive($model), true)
            ? $model->withTrashed()
            : $model->newQuery();

        if (isset($relations)) {
            $query->with($relations);
        }

        $result = $query->whereIn($scoutKeyName, $ids)->get();

        return $result->mapWithKeys(static function (Model $model) {
            return [(string)$model->getScoutKey() => $model];
        });
    }
}
