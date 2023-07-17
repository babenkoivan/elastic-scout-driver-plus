<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Decorators;

use Elastic\Adapter\Search\SearchParameters;
use Elastic\Adapter\Search\SearchResult as BaseSearchResult;
use Elastic\ScoutDriverPlus\Engine;
use Illuminate\Support\Traits\ForwardsCalls;
use Laravel\Scout\Engines\NullEngine as BaseNullEngine;

final class NullEngine extends Engine
{
    use ForwardsCalls;

    private BaseNullEngine $baseNullEngine;

    public function searchWithParameters(SearchParameters $searchParameters): BaseSearchResult
    {
        return new BaseSearchResult([
            'hits' => [
                'total' => ['value' => 0],
                'hits' => [],
            ],
        ]);
    }

    public function connection(string $connection): self
    {
        return $this;
    }

    public function openPointInTime(string $indexName, ?string $keepAlive = null): string
    {
        return '';
    }

    public function closePointInTime(string $pointInTimeId): void
    {
    }

    /**
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->forwardCallTo($this->baseNullEngine, $method, $parameters);
    }
}
