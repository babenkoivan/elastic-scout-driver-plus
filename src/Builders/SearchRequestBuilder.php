<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticAdapter\Search\SearchRequest;
use ElasticScoutDriverPlus\Decorators\EngineDecorator;
use ElasticScoutDriverPlus\Factories\SearchResultFactory;
use ElasticScoutDriverPlus\SearchResult;
use ElasticScoutDriverPlus\Support\ModelScope;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Traits\ForwardsCalls;
use stdClass;

final class SearchRequestBuilder implements SearchRequestBuilderInterface
{
    use ForwardsCalls;

    /**
     * @var ModelScope
     */
    private $modelScope;
    /**
     * @var EngineDecorator
     */
    private $engine;
    /**
     * @var QueryBuilderInterface
     */
    private $queryBuilder;
    /**
     * @var array
     */
    private $highlight = [];
    /**
     * @var array
     */
    private $sort = [];
    /**
     * @var int|null
     */
    private $from;
    /**
     * @var int|null
     */
    private $size;
    /**
     * @var array
     */
    private $suggest = [];
    /**
     * @var bool|string|array|null
     */
    private $source;
    /**
     * @var array
     */
    private $collapse = [];
    /**
     * @var array
     */
    private $aggregations = [];
    /**
     * @var array
     */
    private $postFilter = [];
    /**
     * @var int|bool|null
     */
    private $trackTotalHits;

    public function __construct(Model $model, QueryBuilderInterface $queryBuilder)
    {
        $this->modelScope = new ModelScope(get_class($model));
        $this->engine = $model->searchableUsing();
        $this->queryBuilder = $queryBuilder;
    }

    public function highlightRaw(array $highlight): self
    {
        $this->highlight = $highlight;
        return $this;
    }

    public function highlight(string $field, array $parameters = []): self
    {
        if (!isset($this->highlight['fields'])) {
            $this->highlight['fields'] = [];
        }

        $this->highlight['fields'][$field] = count($parameters) > 0 ? $parameters : new stdClass();
        return $this;
    }

    public function sortRaw(array $sort): self
    {
        $this->sort = $sort;
        return $this;
    }

    public function sort(string $field, string $direction = 'asc'): self
    {
        $this->sort[] = [$field => $direction];
        return $this;
    }

    public function from(int $from): self
    {
        $this->from = $from;
        return $this;
    }

    public function size(int $size): self
    {
        $this->size = $size;
        return $this;
    }

    public function suggestRaw(array $suggest): self
    {
        $this->suggest = $suggest;
        return $this;
    }

    public function suggest(string $suggestion, array $parameters): self
    {
        $this->suggest[$suggestion] = $parameters;
        return $this;
    }

    /**
     * @param bool|string|array $source
     */
    public function sourceRaw($source): self
    {
        $this->source = $source;
        return $this;
    }

    public function source(array $fields): self
    {
        $this->source = $fields;
        return $this;
    }

    public function collapseRaw(array $collapse): self
    {
        $this->collapse = $collapse;
        return $this;
    }

    public function collapse(string $field): self
    {
        $this->collapse = ['field' => $field];
        return $this;
    }

    public function aggregateRaw(array $aggregations): self
    {
        $this->aggregations = $aggregations;
        return $this;
    }

    public function aggregate(string $aggregation, array $parameters): self
    {
        $this->aggregations[$aggregation] = $parameters;
        return $this;
    }

    public function join(string ...$modelClasses): self
    {
        $this->modelScope->push(...$modelClasses);
        return $this;
    }

    public function postFilter(string $type, array $query): self
    {
        $this->postFilter[$type] = $query;
        return $this;
    }

    public function postFilterRaw(array $filter): self
    {
        $this->postFilter = $filter;
        return $this;
    }

    public function load(array $relations, string $modelClass = null): self
    {
        $this->modelScope->with($relations, $modelClass);
        return $this;
    }

    /**
     * @param int|bool $trackTotalHits
     */
    public function trackTotalHits($trackTotalHits): self
    {
        $this->trackTotalHits = $trackTotalHits;
        return $this;
    }

    public function buildSearchRequest(): SearchRequest
    {
        $searchRequest = new SearchRequest($this->queryBuilder->buildQuery());

        if (count($this->highlight) > 0) {
            $searchRequest->setHighlight($this->highlight);
        }

        if (count($this->sort) > 0) {
            $searchRequest->setSort($this->sort);
        }

        if (isset($this->from)) {
            $searchRequest->setFrom($this->from);
        }

        if (isset($this->size)) {
            $searchRequest->setSize($this->size);
        }

        if (count($this->suggest) > 0) {
            $searchRequest->setSuggest($this->suggest);
        }

        if (isset($this->source)) {
            $searchRequest->setSource($this->source);
        }

        if (count($this->collapse) > 0) {
            $searchRequest->setCollapse($this->collapse);
        }

        if (count($this->aggregations) > 0) {
            $searchRequest->setAggregations($this->aggregations);
        }

        if (count($this->postFilter) > 0) {
            $searchRequest->setPostFilter($this->postFilter);
        }

        if (isset($this->trackTotalHits)) {
            $searchRequest->setTrackTotalHits($this->trackTotalHits);
        }

        return $searchRequest;
    }

    public function execute(): SearchResult
    {
        $searchResponse = $this->engine->executeSearchRequest($this->buildSearchRequest(), $this->modelScope);
        return SearchResultFactory::makeFromSearchResponseUsingModelScope($searchResponse, $this->modelScope);
    }

    public function raw(): array
    {
        return $this->engine
            ->executeSearchRequest($this->buildSearchRequest(), $this->modelScope)
            ->getRaw();
    }

    public function __call(string $method, array $parameters): self
    {
        $this->forwardCallTo($this->queryBuilder, $method, $parameters);
        return $this;
    }

    public function paginate(
        int $perPage = self::DEFAULT_PAGE_SIZE,
        string $pageName = 'page',
        int $page = null
    ): LengthAwarePaginatorInterface {
        $page = $page ?? Paginator::resolveCurrentPage($pageName);

        $builder = clone $this;
        $builder->from(($page - 1) * $perPage);
        $builder->size($perPage);

        $searchResult = $builder->execute();

        return new LengthAwarePaginator(
            $searchResult->matches()->all(),
            $searchResult->total(),
            $perPage,
            $page,
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ]
        );
    }
}
