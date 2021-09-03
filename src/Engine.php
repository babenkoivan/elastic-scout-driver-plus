<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus;

use ElasticAdapter\Documents\DocumentManager;
use ElasticAdapter\Indices\IndexManager;
use ElasticAdapter\Search\SearchRequest;
use ElasticAdapter\Search\SearchResponse;
use ElasticScoutDriver\Engine as BaseEngine;
use ElasticScoutDriver\Factories\DocumentFactoryInterface;
use ElasticScoutDriver\Factories\ModelFactoryInterface;
use ElasticScoutDriver\Factories\SearchRequestFactoryInterface;
use ElasticScoutDriverPlus\Factories\RoutingFactoryInterface;
use ElasticScoutDriverPlus\Support\ModelScope;
use Illuminate\Database\Eloquent\Model;

final class Engine extends BaseEngine
{
    /**
     * @var RoutingFactoryInterface
     */
    private $routingFactory;

    public function __construct(
        DocumentManager $documentManager,
        DocumentFactoryInterface $documentFactory,
        SearchRequestFactoryInterface $searchRequestFactory,
        ModelFactoryInterface $modelFactory,
        IndexManager $indexManager,
        RoutingFactoryInterface $routingFactory
    ) {
        parent::__construct($documentManager, $documentFactory, $searchRequestFactory, $modelFactory, $indexManager);

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

        $documentIds = $models->map(static function (Model $model) {
            return (string)$model->getScoutKey();
        })->all();

        $this->documentManager->delete($indexName, $documentIds, $this->refreshDocuments, $routing);
    }

    public function executeSearchRequest(SearchRequest $searchRequest, ModelScope $modelScope): SearchResponse
    {
        $indexName = $modelScope->resolveIndexNames()->join(',');
        return $this->documentManager->search($indexName, $searchRequest);
    }
}
