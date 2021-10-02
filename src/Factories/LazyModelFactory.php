<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Factories;

use ElasticAdapter\Indices\IndexManager;
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

        foreach ($searchResponse->hits() as $hit) {
            $this->mappedIds[$hit->indexName()][] = $hit->document()->id();
        }
    }

    public function makeByIndexNameAndDocumentId(string $indexName, string $documentId): ?Model
    {
        if (!isset($this->mappedModels[$indexName])) {
            $this->mappedModels[$indexName] = $this->mapModels($indexName);
        }

        return $this->mappedModels[$indexName][$documentId] ?? null;
    }

    private function mapModels(string $indexName): Collection
    {
        $aliasName = $this->resolveAlias($indexName) ?? $indexName;
        $modelClass = $this->modelScope->resolveModelClass($aliasName);

        if (!isset($this->mappedIds[$indexName], $modelClass)) {
            return new Collection();
        }

        $ids = $this->mappedIds[$indexName];
        $model = new $modelClass();
        $relations = $this->modelScope->resolveRelations($modelClass);
        $queryCallback = $this->modelScope->resolveQueryCallback($modelClass);

        $query = in_array(SoftDeletes::class, class_uses_recursive($model), true)
            ? $model->withTrashed()
            : $model->newQuery();

        if (isset($queryCallback)) {
            $queryCallback($query);
        }

        if (isset($relations)) {
            $query->with($relations);
        }

        $result = $query->whereIn($model->getScoutKeyName(), $ids)->get();

        return $result->mapWithKeys(static function (Model $model) {
            return [(string)$model->getScoutKey() => $model];
        });
    }

    private function resolveAlias(string $indexName): ?string
    {
        $indexNames = $this->modelScope->resolveIndexNames();

        // if the index name can be found in the scope, then we can be sure,
        // that an actual index name is used to map models to the index
        if ($indexNames->contains($indexName)) {
            return null;
        }

        // otherwise, we get all aliases for the given index and
        // try to find the one, which is in the scope
        foreach (app(IndexManager::class)->getAliases($indexName) as $alias) {
            if ($indexNames->contains($alias->name())) {
                return $alias->name();
            }
        }

        return null;
    }
}
