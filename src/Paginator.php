<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as BaseCollection;
use RuntimeException;

/**
 * @method BaseCollection aggregations()
 * @method BaseCollection documents()
 * @method BaseCollection highlights()
 * @method BaseCollection matches()
 * @method BaseCollection suggestions()
 * @method EloquentCollection models()
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
            $searchResult->matches()->all(),
            $searchResult->total(),
            $perPage,
            $currentPage,
            $options
        );

        $this->searchResult = $searchResult;
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
