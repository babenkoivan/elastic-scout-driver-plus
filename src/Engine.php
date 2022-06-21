<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus;

use Elastic\Adapter\Documents\DocumentManager;
use Elastic\Adapter\Indices\IndexManager;
use Elastic\Adapter\Search\SearchParameters;
use Elastic\Adapter\Search\SearchResult;
use Elastic\ScoutDriver\Engine as BaseEngine;
use Elastic\ScoutDriver\Factories\DocumentFactoryInterface;
use Elastic\ScoutDriver\Factories\ModelFactoryInterface;
use Elastic\ScoutDriver\Factories\SearchParametersFactoryInterface;
use Elastic\ScoutDriverPlus\Factories\RoutingFactoryInterface;
use Elastic\ScoutDriverPlus\Support\ModelScope;
use Illuminate\Database\Eloquent\Model;

final class Engine extends BaseEngine
{
    private RoutingFactoryInterface $routingFactory;

    public function __construct(
        DocumentManager $documentManager,
        DocumentFactoryInterface $documentFactory,
        SearchParametersFactoryInterface $searchParametersFactory,
        ModelFactoryInterface $modelFactory,
        IndexManager $indexManager,
        RoutingFactoryInterface $routingFactory
    ) {
        parent::__construct($documentManager, $documentFactory, $searchParametersFactory, $modelFactory, $indexManager);

        $this->routingFactory = $routingFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function update($models)
    {
        if ($models->isEmpty()) {
            return;
        }

        $indexName = $models->first()->searchableAs();
        $routing = $this->routingFactory->makeFromModels($models);
        $documents = $this->documentFactory->makeFromModels($models);

        $this->documentManager->index($indexName, $documents, $this->refreshDocuments, $routing);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($models)
    {
        if ($models->isEmpty()) {
            return;
        }

        $indexName = $models->first()->searchableAs();
        $routing = $this->routingFactory->makeFromModels($models);
        $documentIds = $models->map(static fn (Model $model) => (string)$model->getScoutKey())->all();

        $this->documentManager->delete($indexName, $documentIds, $this->refreshDocuments, $routing);
    }

    public function searchWithParameters(SearchParameters $searchParameters, ModelScope $modelScope): SearchResult
    {
        $indexName = $modelScope->resolveIndexNames()->join(',');
        return $this->documentManager->search($indexName, $searchParameters);
    }
}
