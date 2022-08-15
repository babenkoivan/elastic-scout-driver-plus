<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus;

use Elastic\Adapter\Documents\DocumentManager;
use Elastic\Adapter\Indices\IndexManager;
use Elastic\Adapter\Search\PointInTimeManager;
use Elastic\Adapter\Search\SearchParameters;
use Elastic\Adapter\Search\SearchResult;
use Elastic\ScoutDriver\Engine as BaseEngine;
use Elastic\ScoutDriver\Factories\DocumentFactoryInterface;
use Elastic\ScoutDriver\Factories\ModelFactoryInterface;
use Elastic\ScoutDriver\Factories\SearchParametersFactoryInterface;
use Elastic\ScoutDriverPlus\Factories\RoutingFactoryInterface;
use Illuminate\Database\Eloquent\Model;

class Engine extends BaseEngine
{
    private RoutingFactoryInterface $routingFactory;
    private PointInTimeManager $pointInTimeManager;

    public function __construct(
        DocumentManager $documentManager,
        IndexManager $indexManager,
        PointInTimeManager $pointInTimeManager,
        DocumentFactoryInterface $documentFactory,
        SearchParametersFactoryInterface $searchParametersFactory,
        ModelFactoryInterface $modelFactory,
        RoutingFactoryInterface $routingFactory
    ) {
        parent::__construct($documentManager, $documentFactory, $searchParametersFactory, $modelFactory, $indexManager);

        $this->routingFactory = $routingFactory;
        $this->pointInTimeManager = $pointInTimeManager;
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

    public function searchWithParameters(SearchParameters $searchParameters): SearchResult
    {
        return $this->documentManager->search($searchParameters);
    }

    public function connection(string $connection): self
    {
        $self = clone $this;
        $self->documentManager = $self->documentManager->connection($connection);
        $self->indexManager = $self->indexManager->connection($connection);
        return $self;
    }

    public function openPointInTime(string $indexName, ?string $keepAlive = null): string
    {
        return $this->pointInTimeManager->open($indexName, $keepAlive);
    }

    public function closePointInTime(string $pointInTimeId): void
    {
        $this->pointInTimeManager->close($pointInTimeId);
    }
}
