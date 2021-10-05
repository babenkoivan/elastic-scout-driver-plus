<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus;

use ElasticScoutDriverPlus\Decorators\SearchResult;
use Illuminate\Pagination\LengthAwarePaginator;
use RuntimeException;

/**
 * @mixin SearchResult
 */
final class Paginator extends LengthAwarePaginator
{
    /**
     * @var SearchResult
     */
    private $searchResult;

    public function __construct(
        SearchResult $searchResult,
        int $perPage,
        ?int $currentPage = null,
        array $options = []
    ) {
        if (is_null($searchResult->total())) {
            throw new RuntimeException(
                'Search result does not contain the total hits number. ' .
                'Please, make sure that total hits are tracked.'
            );
        }

        parent::__construct(
            $searchResult->hits(),
            $searchResult->total(),
            $perPage,
            $currentPage,
            $options
        );

        $this->searchResult = $searchResult;
    }

    public function onlyModels(): self
    {
        $models = $this->models();
        return $this->setCollection($models);
    }

    public function onlyDocuments(): self
    {
        $documents = $this->documents();
        return $this->setCollection($documents);
    }

    /**
     * @{@inheritDoc}
     */
    public function __call($method, $parameters)
    {
        if (method_exists($this->getCollection(), $method)) {
            return $this->forwardCallTo($this->getCollection(), $method, $parameters);
        }

        return $this->forwardCallTo($this->searchResult, $method, $parameters);
    }
}
