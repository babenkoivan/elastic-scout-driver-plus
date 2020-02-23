<?php
declare(strict_types=1);

namespace ElasticScoutDriverPlus\Output;

use ElasticAdapter\Documents\Document;
use ElasticAdapter\Search\Highlight;
use ElasticScoutDriverPlus\Factories\LazyModelFactoryInterface;
use Illuminate\Database\Eloquent\Model;

final class Match
{
    /**
     * @var LazyModelFactoryInterface
     */
    private $lazyModelFactory;
    /**
     * @var Document
     */
    private $document;
    /**
     * @var Highlight|null
     */
    private $highlight;

    public function __construct(
        LazyModelFactoryInterface $lazyModelFactory,
        Document $document,
        ?Highlight $highlight = null
    ) {
        $this->lazyModelFactory = $lazyModelFactory;
        $this->document = $document;
        $this->highlight = $highlight;
    }

    public function model(): ?Model
    {
        $documentId = $this->document()->getId();
        return $this->lazyModelFactory->makeById($documentId);
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
