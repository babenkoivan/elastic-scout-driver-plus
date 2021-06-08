<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus;

use ElasticAdapter\Search\SearchRequest;
use ElasticAdapter\Search\SearchResponse;
use ElasticScoutDriver\Engine as BaseEngine;
use ElasticScoutDriverPlus\Support\ModelScope;

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

        $model = $models->first();
        $index = $model->searchableAs();
        $routingPath = in_array(ShardRouting::class, class_uses_recursive($model)) ? $model->getRoutingPath() : null;
        $documents = $this->documentFactory->makeFromModels($models, false);

        $this->documentManager->index($index, $documents->all(), $this->refreshDocuments, $routingPath);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($models)
    {
        if ($models->isEmpty()) {
            return;
        }

        $model = $models->first();
        $index = $model->searchableAs();
        $routingPath = in_array(ShardRouting::class, class_uses_recursive($model)) ? $model->getRoutingPath() : null;
        $documents = $this->documentFactory->makeFromModels($models, true);

        $this->documentManager->delete($index, $documents->all(), $this->refreshDocuments, $routingPath);
    }

    public function executeSearchRequest(SearchRequest $searchRequest, ModelScope $modelScope): SearchResponse
    {
        $indexName = $modelScope->resolveIndexNames()->join(',');
        return $this->documentManager->search($indexName, $searchRequest);
    }
}
