<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Decorators;

use ArrayIterator;
use ElasticAdapter\Search\Hit as BaseHit;
use ElasticAdapter\Search\SearchResponse as BaseSearchResponse;
use ElasticScoutDriverPlus\Factories\LazyModelFactory;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ForwardsCalls;
use IteratorAggregate;

/**
 * @mixin BaseSearchResponse
 * @mixin Collection
 *
 * @implements IteratorAggregate<int, Hit>
 */
final class SearchResponse implements IteratorAggregate
{
    use ForwardsCalls;

    /**
     * @var BaseSearchResponse
     */
    private $searchResponse;
    /**
     * @var LazyModelFactory
     */
    private $lazyModelFactory;

    public function __construct(BaseSearchResponse $searchResponse, LazyModelFactory $lazyModelFactory)
    {
        $this->searchResponse = $searchResponse;
        $this->lazyModelFactory = $lazyModelFactory;
    }

    public function hits(): Collection
    {
        return $this->searchResponse->hits()->map(function (BaseHit $hit) {
            return new Hit($hit, $this->lazyModelFactory);
        });
    }

    public function models(): Collection
    {
        return $this->hits()->map(static function (Hit $hit) {
            return $hit->model();
        })->filter()->values();
    }

    public function documents(): Collection
    {
        return $this->hits()->map(static function (Hit $hit) {
            return $hit->document();
        })->filter()->values();
    }

    public function highlights(): Collection
    {
        return $this->hits()->map(static function (Hit $hit) {
            return $hit->highlight();
        })->filter()->values();
    }

    /**
     * @return ArrayIterator<int, Hit>
     */
    public function getIterator()
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
