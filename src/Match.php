<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus;

use ElasticAdapter\Documents\Document;
use ElasticAdapter\Search\Highlight;
use ElasticAdapter\Search\Hit;
use ElasticScoutDriverPlus\Factories\LazyModelFactory;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;

final class Match implements Arrayable
{
    /**
     * @var LazyModelFactory
     */
    private $lazyModelFactory;
    /**
     * @var Hit
     */
    private $hit;

    public function __construct(LazyModelFactory $lazyModelFactory, Hit $hit)
    {
        $this->lazyModelFactory = $lazyModelFactory;
        $this->hit = $hit;
    }

    public function model(): ?Model
    {
        return $this->lazyModelFactory->makeByIndexNameAndDocumentId(
            $this->indexName(),
            $this->document()->getId()
        );
    }

    public function indexName(): string
    {
        return $this->hit->getIndexName();
    }

    public function document(): Document
    {
        return $this->hit->getDocument();
    }

    public function highlight(): ?Highlight
    {
        return $this->hit->getHighlight();
    }

    public function score(): ?float
    {
        return $this->hit->getScore();
    }

    public function raw(): array
    {
        return $this->hit->getRaw();
    }

    /**
     * @return array
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
            'highlight' => isset($highlight) ? $highlight->getRaw() : null,
            'score' => $this->score(),
        ];
    }
}
