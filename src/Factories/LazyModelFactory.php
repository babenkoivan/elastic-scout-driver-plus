<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Factories;

use Elastic\Adapter\Indices\IndexManager;
use Elastic\Adapter\Search\SearchResult;
use ElasticScoutDriverPlus\Support\ModelScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class LazyModelFactory
{
    private ModelScope $modelScope;
    private array $mappedIds = [];
    private array $mappedModels = [];

    public function __construct(SearchResult $searchResult, ModelScope $modelScope)
    {
        $this->modelScope = $modelScope;

        foreach ($searchResult->hits() as $hit) {
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
        /** @var Model $model */
        $model = new $modelClass();
        $relations = $this->modelScope->resolveRelations($modelClass);
        $queryCallback = $this->modelScope->resolveQueryCallback($modelClass);

        $query = in_array(SoftDeletes::class, class_uses_recursive($model), true)
            ? $model->withTrashed()
            : $model->newQuery();

        if (isset($relations)) {
            $query->with($relations);
        }

        $query->whereIn($model->getScoutKeyName(), $ids);

        if (isset($queryCallback)) {
            $queryCallback($query);
        }

        $result = $query->get();

        return $result->mapWithKeys(
            static fn (Model $model) => [(string)$model->getScoutKey() => $model]
        );
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
        /** @var IndexManager $indexManager */
        $indexManager = resolve(IndexManager::class);

        foreach ($indexManager->getAliases($indexName) as $alias) {
            /** @var string $alias */
            if ($indexNames->contains($alias)) {
                return $alias;
            }
        }

        return null;
    }
}
