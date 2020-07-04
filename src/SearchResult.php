<?php
declare(strict_types=1);

namespace ElasticScoutDriverPlus;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection as BaseCollection;

final class SearchResult
{
    /**
     * @var BaseCollection
     */
    private $matches;
    /**
     * @var int
     */
    private $total;
    /**
     * @var BaseCollection
     */
    private $suggestions;
    /**
     * @var BaseCollection
     */
    private $aggregations;

    public function __construct(
        BaseCollection $matches,
        int $total,
        BaseCollection $suggestions,
        BaseCollection $aggregations
    ) {
        $this->matches = $matches;
        $this->total = $total;
        $this->suggestions = $suggestions;
        $this->aggregations = $aggregations;
    }

    public function matches(): BaseCollection
    {
        return $this->matches;
    }

    public function models(): EloquentCollection
    {
        $models = new EloquentCollection();

        $this->matches->each(function (Match $match) use ($models) {
            $models->push($match->model());
        });

        return $models->filter()->values();
    }

    public function documents(): BaseCollection
    {
        $documents = $this->matches->map(function (Match $match) {
            return $match->document();
        });

        return $documents->filter()->values();
    }

    public function highlights(): BaseCollection
    {
        $highlights = $this->matches->map(function (Match $match) {
            return $match->highlight();
        });

        return $highlights->filter()->values();
    }

    public function total(): int
    {
        return $this->total;
    }

    public function suggestions(): BaseCollection
    {
        return $this->suggestions;
    }

    public function aggregations(): BaseCollection
    {
        return $this->aggregations;
    }
}
