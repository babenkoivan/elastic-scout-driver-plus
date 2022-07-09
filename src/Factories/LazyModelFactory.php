<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Factories;

use Elastic\Adapter\Search\Hit as BaseHit;
use Elastic\Adapter\Search\SearchResult as BaseSearchResult;
use Illuminate\Database\Eloquent\Model;

class LazyModelFactory
{
    private array $documentIds;
    private ModelFactory $modelFactory;
    private array $models = [];

    public function __construct(BaseSearchResult $searchResult, ModelFactory $modelFactory)
    {
        $this->documentIds = $searchResult->hits()->mapToGroups(
            static fn (BaseHit $baseHit) => [$baseHit->indexName() => $baseHit->document()->id()]
        )->toArray();

        $this->modelFactory = $modelFactory;
    }

    public function makeFromIndexNameAndDocumentId(string $indexName, string $documentId): ?Model
    {
        if (!isset($this->models[$indexName])) {
            $this->models[$indexName] = $this->modelFactory->makeFromIndexNameAndDocumentIds(
                $indexName,
                $this->documentIds[$indexName] ?? []
            )->keyBy(static fn (Model $model) => (string)$model->getScoutKey());
        }

        return $this->models[$indexName][$documentId] ?? null;
    }
}
