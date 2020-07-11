<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Decorators;

use ElasticAdapter\Documents\DocumentManager;
use ElasticAdapter\Search\SearchRequest;
use ElasticAdapter\Search\SearchResponse;
use ElasticScoutDriver\Engine;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilderInterface;
use ElasticScoutDriverPlus\Factories\SearchResultFactory;
use ElasticScoutDriverPlus\SearchResult;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Traits\ForwardsCalls;

final class EngineDecorator
{
    use ForwardsCalls;

    /**
     * @var Engine
     */
    private $engine;
    /**
     * @var DocumentManager
     */
    private $documentManager;
    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    public function __construct(
        Engine $engine,
        DocumentManager $documentManager,
        SearchResultFactory $searchResultFactory
    ) {
        $this->engine = $engine;
        $this->documentManager = $documentManager;
        $this->searchResultFactory = $searchResultFactory;
    }

    public function executeSearchRequest(
        Model $model,
        SearchRequestBuilderInterface $searchRequestBuilder
    ): SearchResult {
        $searchResponse = $this->documentManager->search(
            $model->searchableAs(),
            $searchRequestBuilder->buildSearchRequest()
        );

        return $this->searchResultFactory->makeFromSearchResponseForModel($searchResponse, $model);
    }

    public function rawSearchRequest(
        Model $model,
        SearchRequestBuilderInterface $searchRequestBuilder
    ): array {
        $searchResponse = $this->documentManager->search(
            $model->searchableAs(),
            $searchRequestBuilder->buildSearchRequest()
        );

        return $searchResponse->getRaw();
    }

    public function performSearchRequest(string $indexName, SearchRequest $searchRequest): SearchResponse
    {
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
