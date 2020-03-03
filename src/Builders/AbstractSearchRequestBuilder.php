<?php
declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticAdapter\Search\SearchRequest;
use ElasticScoutDriverPlus\Decorators\EngineDecorator;
use ElasticScoutDriverPlus\Output\SearchResult;
use Illuminate\Database\Eloquent\Model;
use stdClass;

abstract class AbstractSearchRequestBuilder implements SearchRequestBuilderInterface
{
    /**
     * @var Model
     */
    protected $model;
    /**
     * @var EngineDecorator
     */
    protected $engine;
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

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->engine = $this->model->searchableUsing();
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

    public function buildSearchRequest(): SearchRequest
    {
        $searchRequest = new SearchRequest($this->buildQuery());

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

    abstract protected function buildQuery(): array;
}
