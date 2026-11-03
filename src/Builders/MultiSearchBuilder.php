<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Builders;

use Elastic\Adapter\Search\SearchResult as BaseSearchResult;
use Elastic\ScoutDriverPlus\Decorators\SearchResult;
use Elastic\ScoutDriverPlus\Engine;
use Elastic\ScoutDriverPlus\Exceptions\ConnectionMismatchException;
use Elastic\ScoutDriverPlus\Support\Arr;
use Elastic\ScoutDriverPlus\Support\Conditionable;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;

class MultiSearchBuilder
{
    use Conditionable;
    use Macroable;

    /**
     * @var array<int|string, SearchParametersBuilder>
     */
    private array $builders = [];

    /**
     * @param int|string|null $key
     */
    public function add(SearchParametersBuilder $builder, $key = null): self
    {
        if (isset($key)) {
            $this->builders[$key] = $builder;
        } else {
            $this->builders[] = $builder;
        }

        return $this;
    }

    /**
     * @return Collection<int|string, SearchResult>
     */
    public function execute(): Collection
    {
        $baseSearchResults = $this->executeSearches();

        return $baseSearchResults->map(
            fn (BaseSearchResult $baseSearchResult, $key) => new SearchResult(
                $baseSearchResult,
                $this->builders[$key]->buildModelFactory()
            )
        );
    }

    /**
     * @return Collection<int|string, array>
     */
    public function raw(): Collection
    {
        return $this->executeSearches()->map(
            static fn (BaseSearchResult $baseSearchResult) => $baseSearchResult->raw()
        );
    }

    /**
     * @return Collection<int|string, BaseSearchResult>
     */
    private function executeSearches(): Collection
    {
        if (count($this->builders) === 0) {
            return collect();
        }

        $searchParameters = array_map(
            static fn (SearchParametersBuilder $builder) => $builder->buildSearchParameters(),
            array_values($this->builders)
        );

        $baseSearchResults = $this->resolveEngine()->searchManyWithParameters($searchParameters);

        // the engine returns the results in the order of the given search parameters,
        // so that they can be mapped back to the keys of the corresponding builders
        /** @var Collection<int|string, BaseSearchResult> $combined */
        $combined = collect(array_combine(array_keys($this->builders), $baseSearchResults->all()));
        return $combined;
    }

    /**
     * A multi search is a single request, therefore every search has to use the same connection.
     */
    private function resolveEngine(): Engine
    {
        $engines = array_map(
            static fn (SearchParametersBuilder $builder) => $builder->engine(),
            $this->builders
        );

        $connectionNames = array_unique(array_map(
            static fn (Engine $engine) => $engine->connectionName(),
            $engines
        ));

        if (count($connectionNames) > 1) {
            throw new ConnectionMismatchException($connectionNames);
        }

        // $this->builders is never empty here: executeSearches() returns early before calling
        // resolveEngine(), so $engines always has at least one element.
        /** @var Engine $engine */
        $engine = Arr::first($engines);
        return $engine;
    }
}
