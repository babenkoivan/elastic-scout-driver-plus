<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus;

use ElasticAdapter\Search\SearchRequest;
use ElasticAdapter\Search\SearchResponse;
use ElasticScoutDriver\Engine as BaseEngine;
use ElasticScoutDriverPlus\Factories\RoutingFactory;
use ElasticScoutDriverPlus\Support\ModelScope;
use Illuminate\Database\Eloquent\Model;

final class Engine extends BaseEngine
{
    /**
     * {@inheritDoc}
     */
    public function update($models)
    {
        if ($models->isEmpty()) {
            return;
        }

        $this->documentManager->index(
            $models->first()->searchableAs(),
            $this->documentFactory->makeFromModels($models),
            $this->refreshDocuments,
            RoutingFactory::makeFromModels($models)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function delete($models)
    {
        if ($models->isEmpty()) {
            return;
        }

        $documentIds = $models->map(static function (Model $model) {
            return (string)$model->getScoutKey();
        })->all();

        $this->documentManager->delete(
            $models->first()->searchableAs(),
            $documentIds,
            $this->refreshDocuments,
            RoutingFactory::makeFromModels($models)
        );
    }

    public function executeSearchRequest(SearchRequest $searchRequest, ModelScope $modelScope): SearchResponse
    {
        $indexName = $modelScope->resolveIndexNames()->join(',');
        return $this->documentManager->search($indexName, $searchRequest);
    }
}
