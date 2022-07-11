<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Builders;

use Closure;
use Elastic\Adapter\Search\SearchParameters;
use Elastic\ScoutDriverPlus\Decorators\SearchResult;
use Elastic\ScoutDriverPlus\Engine;
use Elastic\ScoutDriverPlus\Exceptions\ModelNotJoinedException;
use Elastic\ScoutDriverPlus\Exceptions\NotSearchableModelException;
use Elastic\ScoutDriverPlus\Factories\ModelFactory;
use Elastic\ScoutDriverPlus\Factories\ParameterFactory;
use Elastic\ScoutDriverPlus\Paginator;
use Elastic\ScoutDriverPlus\Searchable;
use Elastic\ScoutDriverPlus\Support\Arr;
use Elastic\ScoutDriverPlus\Support\Conditionable;
use Illuminate\Database\Eloquent\Model;
use stdClass;

class SearchParametersBuilder
{
    use Conditionable;

    public const DEFAULT_PAGE_SIZE = 10;

    private Engine $engine;
    /**
     * @var array<string, DatabaseQueryBuilder>
     */
    private array $databaseQueryBuilders;
    /**
     * @var array<string, string>
     */
    private array $indexNames;
    private ?array $query;
    private array $highlight = [];
    private array $sort = [];
    private array $rescore = [];
    private ?int $from;
    private ?int $size;
    private array $suggest = [];
    /**
     * @var bool|string|array|null
     */
    private $source;
    private array $collapse = [];
    private array $aggregations = [];
    private array $postFilter = [];
    /**
     * @var int|bool|null
     */
    private $trackTotalHits;
    private ?bool $trackScores;
    private ?float $minScore;
    private array $indicesBoost = [];
    private ?string $searchType;
    private ?string $preference;

    public function __construct(Model $model)
    {
        $this->engine = $model->searchableUsing();
        $this->join(get_class($model));
    }

    /**
     * @param Closure|QueryBuilderInterface|array|null $query
     */
    public function query($query): self
    {
        $this->query = isset($query) ? ParameterFactory::makeQuery($query) : null;
        return $this;
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
        $this->rescore['window_size'] = $windowSize;
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

    public function join(string $modelClass, float $boost = null): self
    {
        if (
            !is_a($modelClass, Model::class, true) ||
            !in_array(Searchable::class, class_uses_recursive($modelClass), true)
        ) {
            throw new NotSearchableModelException($modelClass);
        }

        /** @var Model $model */
        $model = new $modelClass();
        /** @var string $indexName */
        $indexName = $model->searchableAs();

        $this->indexNames[$modelClass] = $indexName;
        $this->databaseQueryBuilders[$indexName] = new DatabaseQueryBuilder($model);

        if (isset($boost)) {
            $this->indicesBoost[] = [$indexName => $boost];
        }

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
        $indexName = $this->resolveJoinedIndexName($modelClass);
        $this->databaseQueryBuilders[$indexName]->with($relations);

        return $this;
    }

    public function refineModels(Closure $callback, string $modelClass = null): self
    {
        $indexName = $this->resolveJoinedIndexName($modelClass);
        $this->databaseQueryBuilders[$indexName]->callback($callback);

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

    public function buildSearchParameters(): SearchParameters
    {
        $searchParameters = new SearchParameters();

        if (isset($this->query)) {
            $searchParameters->query($this->query);
        }

        if (!empty($this->highlight)) {
            $searchParameters->highlight($this->highlight);
        }

        if (!empty($this->sort)) {
            $searchParameters->sort($this->sort);
        }

        if (!empty($this->rescore)) {
            $searchParameters->rescore($this->rescore);
        }

        if (isset($this->from)) {
            $searchParameters->from($this->from);
        }

        if (isset($this->size)) {
            $searchParameters->size($this->size);
        }

        if (!empty($this->suggest)) {
            $searchParameters->suggest($this->suggest);
        }

        if (isset($this->source)) {
            $searchParameters->source($this->source);
        }

        if (!empty($this->collapse)) {
            $searchParameters->collapse($this->collapse);
        }

        if (!empty($this->aggregations)) {
            $searchParameters->aggregations($this->aggregations);
        }

        if (!empty($this->postFilter)) {
            $searchParameters->postFilter($this->postFilter);
        }

        if (isset($this->trackTotalHits)) {
            $searchParameters->trackTotalHits($this->trackTotalHits);
        }

        if (isset($this->trackScores)) {
            $searchParameters->trackScores($this->trackScores);
        }

        if (isset($this->minScore)) {
            $searchParameters->minScore($this->minScore);
        }

        if (!empty($this->indicesBoost)) {
            $searchParameters->indicesBoost($this->indicesBoost);
        }

        if (isset($this->searchType)) {
            $searchParameters->searchType($this->searchType);
        }

        if (isset($this->preference)) {
            $searchParameters->preference($this->preference);
        }

        return $searchParameters;
    }

    public function execute(): SearchResult
    {
        $baseSearchResult = $this->engine->searchWithParameters($this->indexNames, $this->buildSearchParameters());
        $modelFactory = new ModelFactory($this->databaseQueryBuilders);
        return new SearchResult($baseSearchResult, $modelFactory);
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
            ->searchWithParameters($this->indexNames, $this->buildSearchParameters())
            ->raw();
    }

    private function resolveJoinedIndexName(?string $modelClass): string
    {
        if (isset($modelClass)) {
            if (isset($this->indexNames[$modelClass])) {
                return $this->indexNames[$modelClass];
            }

            throw new ModelNotJoinedException($modelClass);
        }

        return Arr::first($this->indexNames);
    }
}
