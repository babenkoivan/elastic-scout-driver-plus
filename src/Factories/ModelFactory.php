<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Factories;

use Elastic\Adapter\Indices\IndexManager;
use Elastic\ScoutDriverPlus\Builders\DatabaseQueryBuilder;
use Illuminate\Database\Eloquent\Collection;

class ModelFactory
{
    private array $databaseQueryBuilders;

    /**
     * @param array<string, DatabaseQueryBuilder> $databaseQueryBuilders ['my_index' => new DatabaseQueryBuilder($model), ...]
     */
    public function __construct(array $databaseQueryBuilders)
    {
        $this->databaseQueryBuilders = $databaseQueryBuilders;
    }

    public function makeFromIndexNameAndDocumentIds(string $indexName, array $documentIds): Collection
    {
        $databaseQueryBuilder = $this->resolveDatabaseQueryBuilder($indexName);

        if (empty($documentIds) || is_null($databaseQueryBuilder)) {
            return new Collection();
        }

        return $databaseQueryBuilder->buildQuery($documentIds)->get();
    }

    private function resolveDatabaseQueryBuilder(string $indexName): ?DatabaseQueryBuilder
    {
        if (isset($this->databaseQueryBuilders[$indexName])) {
            return $this->databaseQueryBuilders[$indexName];
        }

        /** @var IndexManager $indexManager */
        $indexManager = app(IndexManager::class);

        foreach ($indexManager->getAliases($indexName) as $aliasName) {
            /** @var string $aliasName */
            if (isset($this->databaseQueryBuilders[$aliasName])) {
                return $this->databaseQueryBuilders[$aliasName];
            }
        }

        return null;
    }
}
