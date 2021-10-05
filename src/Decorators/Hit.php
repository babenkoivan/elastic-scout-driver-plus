<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Decorators;

use ElasticAdapter\Search\Hit as BaseHit;
use ElasticScoutDriverPlus\Factories\LazyModelFactory;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Traits\ForwardsCalls;

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

    /**
     * @{@inheritDoc}
     */
    public function toArray()
    {
        $model = $this->model();
        $document = $this->document();
        $highlight = $this->highlight();

        return [
            'model' => isset($model) ? $model->toArray() : null,
            'index_name' => $this->indexName(),
            'document' => $document->toArray(),
            'highlight' => isset($highlight) ? $highlight->raw() : null,
            'score' => $this->score(),
        ];
    }
}
