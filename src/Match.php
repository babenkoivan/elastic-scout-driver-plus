<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus;

use ElasticAdapter\Documents\Document;
use ElasticAdapter\Search\Highlight;
use ElasticScoutDriverPlus\Factories\LazyModelFactory;
use Illuminate\Database\Eloquent\Model;

final class Match
{
    /**
     * @var LazyModelFactory
     */
    private $lazyModelFactory;
    /**
     * @var string
     */
    private $indexName;
    /**
     * @var Document
     */
    private $document;
    /**
     * @var Highlight|null
     */
    private $highlight;
    /**
     * @var float|null
     */
    private $score;

    public function __construct(
        LazyModelFactory $lazyModelFactory,
        string $indexName,
        Document $document,
        ?Highlight $highlight,
        ?float $score
    ) {
        $this->lazyModelFactory = $lazyModelFactory;
        $this->indexName = $indexName;
        $this->document = $document;
        $this->highlight = $highlight;
        $this->score = $score;
    }

    public function model(): ?Model
    {
        return $this->lazyModelFactory->makeByIndexNameAndDocumentId($this->indexName, $this->document->getId());
    }

    public function indexName(): string
    {
        return $this->indexName;
    }

    public function score(): ?float
    {
        return $this->score;
    }

    public function document(): Document
    {
        return $this->document;
    }

    public function highlight(): ?Highlight
    {
        return $this->highlight;
    }
}
