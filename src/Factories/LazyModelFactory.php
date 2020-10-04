<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Factories;

use ElasticAdapter\Search\SearchResponse;
use ElasticScoutDriverPlus\Support\ModelScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class LazyModelFactory
{
    /**
     * @var array
     */
    private $mappedQueries;
    /**
     * @var array
     */
    private $mappedIds = [];
    /**
     * @var array
     */
    private $mappedModels = [];

    public function __construct(SearchResponse $searchResponse, ModelScope $modelScope)
    {
        $this->mappedQueries = $modelScope->keyQueriesByIndexName()->all();

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

    private function mapModelsForIndex(string $indexName): Collection
    {
        if (!isset($this->mappedIds[$indexName], $this->mappedQueries[$indexName])) {
            return new Collection();
        }

        $ids = $this->mappedIds[$indexName];
        $query = clone $this->mappedQueries[$indexName];
        $scoutKeyName = $query->getModel()->getScoutKeyName();

        $mappedModels = $query->whereIn($scoutKeyName, $ids)->get();

        return $mappedModels->mapWithKeys(static function (Model $model) {
            return [(string)$model->getScoutKey() => $model];
        });
    }
}
