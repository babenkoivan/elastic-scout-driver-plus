<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Decorators;

use ElasticAdapter\Documents\DocumentManager;
use ElasticAdapter\Search\SearchRequest;
use ElasticAdapter\Search\SearchResponse;
use ElasticScoutDriver\Engine;
use ElasticScoutDriverPlus\Factories\SearchResultFactory;
use ElasticScoutDriverPlus\SearchResult;
use Illuminate\Support\Collection;
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

    public function executeSearchRequest(Collection $models, SearchRequest $searchRequest): SearchResult
    {
        $searchResponse = $this->performSearchRequest($models, $searchRequest);
        return $this->searchResultFactory->makeFromSearchResponseUsingModels($searchResponse, $models);
    }

    public function rawSearchRequest(Collection $models, SearchRequest $searchRequest): array
    {
        $searchResponse = $this->performSearchRequest($models, $searchRequest);
        return $searchResponse->getRaw();
    }

    private function performSearchRequest(Collection $models, SearchRequest $searchRequest): SearchResponse
    {
        $indexName = $models->map->searchableAs()->join(',');
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
