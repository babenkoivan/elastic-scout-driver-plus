<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Factories;

use Elastic\Adapter\Indices\IndexManager;
use Elastic\ScoutDriverPlus\Builders\DatabaseQueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class LazyModelFactory
{
    private array $databaseQueryBuilders;
    private array $documentIds;
    private array $models = [];

    /**
     * @param array<string, DatabaseQueryBuilder> $databaseQueryBuilders ['my_index' => new DatabaseQueryBuilder($model), ...]
     * @param array<string, array>                $documentIds           ['my_index' => ['1', '2', '3'], ...]
     */
    public function __construct(array $databaseQueryBuilders, array $documentIds)
    {
        $this->databaseQueryBuilders = $databaseQueryBuilders;
        $this->documentIds = $documentIds;
    }

    public function makeFromIndexNameAndDocumentId(string $indexName, string $documentId): ?Model
    {
        if (!isset($this->models[$indexName])) {
            $this->models[$indexName] = $this->resolveModels($indexName);
        }

        return $this->models[$indexName][$documentId] ?? null;
    }

    private function resolveModels(string $indexName): Collection
    {
        $databaseQueryBuilder = $this->resolveDatabaseQueryBuilder($indexName);

        if (is_null($databaseQueryBuilder) || !isset($this->documentIds[$indexName])) {
            return new Collection();
        }

        $databaseQuery = $databaseQueryBuilder->buildQuery($this->documentIds[$indexName]);

        return $databaseQuery->get()->mapWithKeys(
            static fn (Model $model) => [(string)$model->getScoutKey() => $model]
        );
    }

    private function resolveDatabaseQueryBuilder(string $indexName): ?DatabaseQueryBuilder
    {
        if (isset($this->databaseQueryBuilders[$indexName])) {
            return $this->databaseQueryBuilders[$indexName];
        }

        /** @var IndexManager $indexManager */
        $indexManager = resolve(IndexManager::class);

        foreach ($indexManager->getAliases($indexName) as $aliasName) {
            /** @var string $aliasName */
            if (isset($this->databaseQueryBuilders[$aliasName])) {
                return $this->databaseQueryBuilders[$aliasName];
            }
        }

        return null;
    }
}
