<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Factories;

use Elastic\Adapter\Search\Hit as BaseHit;
use Elastic\Adapter\Search\SearchResult as BaseSearchResult;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as BaseCollection;

class LazyModelFactory
{
    private array $documentIds = [];
    private ModelFactory $modelFactory;
    private array $models = [];

    public function __construct(BaseSearchResult $searchResult, ModelFactory $modelFactory)
    {
        $searchResult->hits()->each(function (BaseHit $baseHit) {
            $this->documentIds[$baseHit->indexName()][] = $baseHit->document()->id();

            $baseHit->innerHits()->each(function (BaseCollection $baseInnerHits) {
                $baseInnerHits->each(function (BaseHit $baseInnerHit) {
                    $this->documentIds[$baseInnerHit->indexName()][] = $baseInnerHit->document()->id();
                });
            });
        });

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
