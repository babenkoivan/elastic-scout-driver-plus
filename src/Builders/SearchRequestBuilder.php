<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use Closure;
use ElasticAdapter\Search\SearchRequest;
use ElasticScoutDriverPlus\Decorators\SearchResult;
use ElasticScoutDriverPlus\Engine;
use ElasticScoutDriverPlus\Exceptions\ModelClassNotFoundInScopeException;
use ElasticScoutDriverPlus\Factories\LazyModelFactory;
use ElasticScoutDriverPlus\Factories\ParameterFactory;
use ElasticScoutDriverPlus\Paginator;
use ElasticScoutDriverPlus\Support\ModelScope;
use Illuminate\Database\Eloquent\Model;
use stdClass;

class SearchRequestBuilder
{
    public const DEFAULT_PAGE_SIZE = 10;

    /**
     * @var array|null
     */
    private $query;
    /**
     * @var ModelScope
     */
    private $modelScope;
    /**
     * @var Engine
     */
    private $engine;
    /**
     * @var array
     */
    private $highlight = [];
    /**
     * @var array
     */
    private $sort = [];
    /**
     * @var array
     */
    private $rescore = [];
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
    /**
     * @var bool|null
     */
    private $trackScores;
    /**
     * @var float|null
     */
    private $minScore;
    /**
     * @var array
     */
    private $indicesBoost = [];
    /**
     * @var string|null
     */
    private $searchType;
    /**
     * @var string|null
     */
    private $preference;

    /**
     * @param Closure|QueryBuilderInterface|array|null $query
     */
    public function __construct($query, Model $model)
    {
        $this->query = isset($query) ? ParameterFactory::makeQuery($query) : null;
        $this->modelScope = new ModelScope(get_class($model));
        $this->engine = $model->searchableUsing();
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

    public function rescoreRaw(array $rescore): self
    {
        $this->rescore = $rescore;
        return $this;
    }

    /**
     * @param Closure|QueryBuilderInterface|array $query
     */
    public function rescoreQuery($query): self
    {
        $this->rescore['query']['rescore_query'] = ParameterFactory::makeQuery($query);
        return $this;
    }

    public function rescoreWindowSize(int $windowSize): self
    {
        $this->rescore['window_size'] =  $windowSize;
        return $this;
    }

    public function rescoreWeights(float $queryWeight, float $rescoreQueryWeight): self
    {
        $this->rescore['query']['query_weight'] = $queryWeight;
        $this->rescore['query']['rescore_query_weight'] = $rescoreQueryWeight;
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

    /**
     * @param Closure|QueryBuilderInterface|array $query
     */
    public function postFilter($query): self
    {
        $this->postFilter = ParameterFactory::makeQuery($query);
        return $this;
    }

    public function load(array $relations, string $modelClass = null): self
    {
        $this->modelScope->with($relations, $modelClass);
        return $this;
    }

    public function refineModels(callable $callback, string $modelClass = null): self
    {
        $this->modelScope->modifyQuery($callback, $modelClass);
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

    public function trackScores(bool $trackScores): self
    {
        $this->trackScores = $trackScores;
        return $this;
    }

    public function minScore(float $minScore): self
    {
        $this->minScore = $minScore;
        return $this;
    }

    public function boostIndex(string $modelClass, float $boost): self
    {
        if (!$this->modelScope->contains($modelClass)) {
            throw new ModelClassNotFoundInScopeException($modelClass);
        }

        $indexName = $this->modelScope->resolveIndexName($modelClass);
        $this->indicesBoost[] = [$indexName => $boost];

        return $this;
    }

    public function searchType(string $searchType): self
    {
        $this->searchType = $searchType;
        return $this;
    }

    public function preference(string $preference): self
    {
        $this->preference = $preference;
        return $this;
    }

    /**
     * @param mixed         $value
     * @param callable      $callback
     * @param callable|null $default
     *
     * @return mixed
     */
    public function when($value, $callback, $default = null)
    {
        if ($value) {
            return $callback($this, $value) ?? $this;
        }

        if ($default) {
            return $default($this, $value) ?? $this;
        }

        return $this;
    }

    public function buildSearchRequest(): SearchRequest
    {
        $searchRequest = new SearchRequest($this->query);

        if (!empty($this->highlight)) {
            $searchRequest->highlight($this->highlight);
        }

        if (!empty($this->sort)) {
            $searchRequest->sort($this->sort);
        }

        if (!empty($this->rescore)) {
            $searchRequest->rescore($this->rescore);
        }

        if (isset($this->from)) {
            $searchRequest->from($this->from);
        }

        if (isset($this->size)) {
            $searchRequest->size($this->size);
        }

        if (!empty($this->suggest)) {
            $searchRequest->suggest($this->suggest);
        }

        if (isset($this->source)) {
            $searchRequest->source($this->source);
        }

        if (!empty($this->collapse)) {
            $searchRequest->collapse($this->collapse);
        }

        if (!empty($this->aggregations)) {
            $searchRequest->aggregations($this->aggregations);
        }

        if (!empty($this->postFilter)) {
            $searchRequest->postFilter($this->postFilter);
        }

        if (isset($this->trackTotalHits)) {
            $searchRequest->trackTotalHits($this->trackTotalHits);
        }

        if (isset($this->trackScores)) {
            $searchRequest->trackScores($this->trackScores);
        }

        if (isset($this->minScore)) {
            $searchRequest->minScore($this->minScore);
        }

        if (!empty($this->indicesBoost)) {
            $searchRequest->indicesBoost($this->indicesBoost);
        }

        if (isset($this->searchType)) {
            $searchRequest->searchType($this->searchType);
        }

        if (isset($this->preference)) {
            $searchRequest->preference($this->preference);
        }

        return $searchRequest;
    }

    public function execute(): SearchResult
    {
        $searchResponse = $this->engine->executeSearchRequest($this->buildSearchRequest(), $this->modelScope);
        $lazyModelFactory = new LazyModelFactory($searchResponse, $this->modelScope);

        return new SearchResult($searchResponse, $lazyModelFactory);
    }

    public function paginate(
        int $perPage = self::DEFAULT_PAGE_SIZE,
        string $pageName = 'page',
        int $page = null
    ): Paginator {
        $page = $page ?? Paginator::resolveCurrentPage($pageName);

        $builder = clone $this;
        $builder->from(($page - 1) * $perPage);
        $builder->size($perPage);
        $searchResult = $builder->execute();

        return new Paginator(
            $searchResult,
            $perPage,
            $page,
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ]
        );
    }

    public function raw(): array
    {
        return $this->engine
            ->executeSearchRequest($this->buildSearchRequest(), $this->modelScope)
            ->raw();
    }
}
