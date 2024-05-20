<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus;

use Elastic\ScoutDriverPlus\Decorators\SearchResult;
use Illuminate\Pagination\Paginator;
use RuntimeException;

/**
 * @mixin SearchResult
 */
final class SimplePaginator extends Paginator
{
    private SearchResult $searchResult;

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
            $perPage,
            $currentPage,
            $options
        );

        $this->hasMorePagesWhen($this->lastItem() < $searchResult->total());

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
     * {@inheritDoc}
     */
    public function __call($method, $parameters)
    {
        if (method_exists($this->getCollection(), $method)) {
            return $this->forwardCallTo($this->getCollection(), $method, $parameters);
        }

        return $this->forwardCallTo($this->searchResult, $method, $parameters);
    }
}
