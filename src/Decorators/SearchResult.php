<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Decorators;

use ArrayIterator;
use Elastic\Adapter\Search\Hit as BaseHit;
use Elastic\Adapter\Search\SearchResult as BaseSearchResult;
use Elastic\ScoutDriverPlus\Factories\LazyModelFactory;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Traits\ForwardsCalls;
use IteratorAggregate;
use Traversable;

/**
 * @mixin BaseSearchResult
 * @mixin BaseCollection
 *
 * @implements IteratorAggregate<int, Hit>
 */
final class SearchResult implements IteratorAggregate
{
    use ForwardsCalls;

    private BaseSearchResult $baseSearchResult;
    private LazyModelFactory $lazyModelFactory;

    public function __construct(BaseSearchResult $baseSearchResult, LazyModelFactory $lazyModelFactory)
    {
        $this->baseSearchResult = $baseSearchResult;
        $this->lazyModelFactory = $lazyModelFactory;
    }

    public function hits(): BaseCollection
    {
        return $this->baseSearchResult->hits()->map(
            fn (BaseHit $hit) => new Hit($hit, $this->lazyModelFactory)
        );
    }

    public function models(): EloquentCollection
    {
        $models = $this->hits()->map(
            static fn (Hit $hit) => $hit->model()
        )->filter()->values();

        return new EloquentCollection($models);
    }

    public function documents(): BaseCollection
    {
        return $this->hits()->map(
            static fn (Hit $hit) => $hit->document()
        );
    }

    public function highlights(): BaseCollection
    {
        return $this->hits()->map(
            static fn (Hit $hit) => $hit->highlight()
        )->filter()->values();
    }

    /**
     * @return ArrayIterator|Traversable
     */
    public function getIterator(): Traversable
    {
        return $this->hits()->getIterator();
    }

    /**
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->forwardCallTo($this->baseSearchResult, $method, $parameters);
    }
}
