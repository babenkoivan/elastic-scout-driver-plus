<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Decorators;

use ElasticAdapter\Search\Hit as BaseHit;
use ElasticScoutDriverPlus\Factories\LazyModelFactory;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Support\Collection;

/**
 * @mixin BaseHit
 */
final class Hit implements Arrayable
{
    use ForwardsCalls;

    /**
     * @var BaseHit
     */
    private $hit;
    /**
     * @var LazyModelFactory
     */
    private $lazyModelFactory;

    public function __construct(BaseHit $hit, LazyModelFactory $lazyModelFactory)
    {
        $this->hit = $hit;
        $this->lazyModelFactory = $lazyModelFactory;
    }

    public function model(): ?Model
    {
        return $this->lazyModelFactory->makeByIndexNameAndDocumentId(
            $this->indexName(),
            $this->document()->id()
        );
    }

    /**
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->forwardCallTo($this->hit, $method, $parameters);
    }

    public function inner_hits()
    {
        $final_inner_hits = [];
        $raw_hit = $this->hit->raw();
        if (isset($raw_hit['inner_hits'])) {
            foreach ($raw_hit['inner_hits'] as $key => $inner_hits) {
                $final_inner_hits[$key] = $final_inner_hits[$key] ?? collect();
                if ($inner_hits['hits']['total']['value'] > 0) {
                    foreach ($inner_hits['hits']['hits'] as $inner_hit) {
                        $final_inner_hits[$key]->push(new BaseHit($inner_hit));
                    }
                }
            }
        }

        return $final_inner_hits;
    }

    /**
     * @{@inheritDoc}
     */
    public function toArray()
    {
        $model = $this->model();
        $document = $this->document();
        $highlight = $this->highlight();

        return [
            'model'      => isset($model) ? $model->toArray() : null,
            'index_name' => $this->indexName(),
            'document'   => $document->toArray(),
            'highlight'  => isset($highlight) ? $highlight->raw() : null,
            'score'      => $this->score(),
        ];
    }
}
