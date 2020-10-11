<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Decorators;

use ElasticAdapter\Documents\DocumentManager;
use ElasticAdapter\Search\SearchRequest;
use ElasticAdapter\Search\SearchResponse;
use ElasticScoutDriver\Engine;
use ElasticScoutDriverPlus\Support\ModelScope;
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

    public function __construct(Engine $engine, DocumentManager $documentManager)
    {
        $this->engine = $engine;
        $this->documentManager = $documentManager;
    }

    public function executeSearchRequest(SearchRequest $searchRequest, ModelScope $modelScope): SearchResponse
    {
        $indexName = $modelScope->getIndexNames()->join(',');
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
