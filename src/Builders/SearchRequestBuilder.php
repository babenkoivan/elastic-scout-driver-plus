<?php
declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticAdapter\Search\SearchRequest;
use ElasticScoutDriverPlus\Decorators\EngineDecorator;
use ElasticScoutDriverPlus\SearchResult;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Traits\ForwardsCalls;
use stdClass;

final class SearchRequestBuilder implements SearchRequestBuilderInterface
{
    use ForwardsCalls;

    /**
     * @var Model
     */
    protected $model;
    /**
     * @var EngineDecorator
     */
    protected $engine;
    /**
     * @var QueryBuilderInterface
     */
    private $queryBuilder;
    /**
     * @var array
     */
    protected $highlight = [];
    /**
     * @var array
     */
    protected $sort = [];
    /**
     * @var int|null
     */
    protected $from;
    /**
     * @var int|null
     */
    protected $size;
    /**
     * @var array
     */
    protected $suggest = [];

    public function __construct(Model $model, QueryBuilderInterface $queryBuilder)
    {
        $this->model = $model;
        $this->engine = $this->model->searchableUsing();
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

    public function suggest(string $suggestion, array $parameters): self
    {
        $this->suggest[$suggestion] = $parameters;
        return $this;
    }

    public function suggestRaw(array $suggest): self
    {
        $this->suggest = $suggest;
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

        if (count($this->suggest) > 0) {
            $searchRequest->setSuggest($this->suggest);
        }

        if (isset($this->from)) {
            $searchRequest->setFrom($this->from);
        }

        if (isset($this->size)) {
            $searchRequest->setSize($this->size);
        }

        return $searchRequest;
    }

    public function execute(): SearchResult
    {
        return $this->engine->executeSearchRequest($this->model, $this);
    }

    public function raw(): array
    {
        return $this->engine->rawSearchRequest($this->model, $this);
    }

    public function __call($method, $parameters): self
    {
        $this->forwardCallTo($this->queryBuilder, $method, $parameters);
        return $this;
    }
}
