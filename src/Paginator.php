<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus;

use ElasticScoutDriverPlus\Decorators\SearchResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use RuntimeException;

/**
 * @mixin SearchResponse
 */
final class Paginator extends LengthAwarePaginator
{
    /**
     * @var SearchResponse
     */
    private $searchResponse;

    public function __construct(
        SearchResponse $searchResponse,
        int $perPage,
        ?int $currentPage = null,
        array $options = []
    ) {
        if (is_null($searchResponse->total())) {
            throw new RuntimeException(
                'Search result does not contain the total hits number. ' .
                'Please, make sure that total hits are tracked.'
            );
        }

        parent::__construct(
            $searchResponse->hits()->all(),
            $searchResponse->total(),
            $perPage,
            $currentPage,
            $options
        );

        $this->searchResponse = $searchResponse;
    }

    /**
     * @{@inheritDoc}
     */
    public function __call($method, $parameters)
    {
        if (method_exists($this->getCollection(), $method)) {
            return $this->forwardCallTo($this->getCollection(), $method, $parameters);
        }

        return $this->forwardCallTo($this->searchResponse, $method, $parameters);
    }
}
