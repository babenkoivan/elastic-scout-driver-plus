<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Decorators;

use ElasticAdapter\Documents\DocumentManager;
use ElasticAdapter\Search\SearchRequest;
use ElasticAdapter\Search\SearchResponse;
use ElasticScoutDriver\Engine;
use ElasticScoutDriver\Factories\DocumentFactoryInterface;
use ElasticScoutDriverPlus\ShardRouting;
use ElasticScoutDriverPlus\Support\ModelScope;
use Illuminate\Support\Traits\ForwardsCalls;

final class EngineDecorator
{
    use ForwardsCalls;

    /**
     * @var bool
     */
    private $refreshDocuments;
    /**
     * @var Engine
     */
    private $engine;
    /**
     * @var DocumentManager
     */
    private $documentManager;
    /**
     * @var DocumentFactoryInterface
     */
    private $documentFactory;

    public function __construct(
        Engine $engine,
        DocumentManager $documentManager,
        DocumentFactoryInterface $documentFactory
    ) {
        $this->refreshDocuments = config('elastic.scout_driver.refresh_documents');

        $this->engine = $engine;
        $this->documentManager = $documentManager;
        $this->documentFactory = $documentFactory;
    }

    /**
     * Update the given model in the index.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $models
     * @return void
     */
    public function update($models)
    {
        if ($models->isEmpty()) {
            return;
        }

        $model = $models->first();
        $index = $model->searchableAs();
        $routingPath = in_array(ShardRouting::class, class_uses_recursive($model)) ? $model->getRoutingPath() : null;
        $documents = $this->documentFactory->makeFromModels($models);

        $this->documentManager->index($index, $documents->all(), $this->refreshDocuments, $routingPath);
    }

    /**
     * Remove the given model from the index.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $models
     * @return void
     */
    public function delete($models)
    {
        if ($models->isEmpty()) {
            return;
        }

        $model = $models->first();
        $index = $model->searchableAs();
        $routingPath = in_array(ShardRouting::class, class_uses_recursive($model)) ? $model->getRoutingPath() : null;
        $documents = $this->documentFactory->makeFromModels($models);

        $this->documentManager->delete($index, $documents->all(), $this->refreshDocuments, $routingPath);
    }

    public function executeSearchRequest(SearchRequest $searchRequest, ModelScope $modelScope): SearchResponse
    {
        $indexName = $modelScope->resolveIndexNames()->join(',');
        return $this->documentManager->search($indexName, $searchRequest);
    }

    /**
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->forwardCallTo($this->engine, $method, $parameters);
    }
}
