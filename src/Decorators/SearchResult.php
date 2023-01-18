<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Decorators;

use ArrayIterator;
use ElasticAdapter\Search\Hit as BaseHit;
use ElasticAdapter\Search\SearchResponse;
use ElasticScoutDriverPlus\Factories\LazyModelFactory;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Traits\ForwardsCalls;
use IteratorAggregate;

/**
 * @mixin SearchResponse
 * @mixin BaseCollection
 *
 * @implements IteratorAggregate<int, Hit>
 */
final class SearchResult implements IteratorAggregate
{
    use ForwardsCalls;

    /**
     * @var SearchResponse
     */
    private $searchResponse;
    /**
     * @var LazyModelFactory
     */
    private $lazyModelFactory;

    public function __construct(SearchResponse $searchResponse, LazyModelFactory $lazyModelFactory)
    {
        $this->searchResponse = $searchResponse;
        $this->lazyModelFactory = $lazyModelFactory;
    }

    public function hits(): BaseCollection
    {
        return $this->searchResponse->hits()->map(function (BaseHit $hit) {
            return new Hit($hit, $this->lazyModelFactory);
        });
    }

    public function models(): EloquentCollection
    {
        $models = $this->hits()->map(static function (Hit $hit) {
            return $hit->model();
        })->filter()->values();

        return new EloquentCollection($models);
    }

    public function documents(): BaseCollection
    {
        return $this->hits()->map(static function (Hit $hit) {
            return $hit->document();
        })->filter()->values();
    }

    public function highlights(): BaseCollection
    {
        return $this->hits()->map(static function (Hit $hit) {
            return $hit->highlight();
        })->filter()->values();
    }

    /**
     * @return ArrayIterator<int, Hit>
     */
    public function getIterator(): ArrayIterator
    {
        return $this->hits()->getIterator();
    }

    /**
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->forwardCallTo($this->searchResponse, $method, $parameters);
    }
}
