<?php
declare(strict_types=1);

namespace Elastic\ScoutDriverPlus;

use Closure;
use Elastic\ScoutDriverPlus\Builders\QueryBuilderInterface;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection as BaseCollection;
use Laravel\Scout\Searchable as BaseSearchable;

trait Searchable
{
    use BaseSearchable {
        searchableUsing as baseSearchableUsing;
        registerSearchableMacros as baseRegisterSearchableMacros;
    }

    /**
     * @param Closure|QueryBuilderInterface|array|null $query
     */
    public static function searchQuery($query = null): SearchParametersBuilder
    {
        $builder = new SearchParametersBuilder(new static());

        if (isset($query)) {
            $builder->query($query);
        }

        return $builder;
    }

    /**
     * @return string|int|null
     */
    public function searchableRouting()
    {
        return null;
    }

    /**
     * @return array|string|null
     */
    public function searchableWith()
    {
        return null;
    }

    public function searchableConnection(): ?string
    {
        return null;
    }

    /**
     * @return Engine
     */
    public function searchableUsing()
    {
        /** @var Engine $engine */
        $engine = $this->baseSearchableUsing();
        $connection = $this->searchableConnection();

        return isset($connection) ? $engine->connection($connection) : $engine;
    }

    public static function openPointInTime(?string $keepAlive = null): string
    {
        $self = new static();
        $engine = $self->searchableUsing();
        $indexName = $self->searchableAs();

        return $engine->openPointInTime($indexName, $keepAlive);
    }

    public static function closePointInTime(string $pointInTimeId): void
    {
        $self = new static();
        $engine = $self->searchableUsing();

        $engine->closePointInTime($pointInTimeId);
    }

    /**
     * @return void
     */
    public function registerSearchableMacros()
    {
        $this->baseRegisterSearchableMacros();

        BaseCollection::macro('withSearchableRelations', function () {
            $models = new EloquentCollection($this);

            if ($searchableWith = $models->first()->searchableWith()) {
                $models->loadMissing($searchableWith);
            }

            return $models;
        });
    }
}
