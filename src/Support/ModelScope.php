<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Support;

use Elastic\ScoutDriverPlus\Exceptions\ModelClassNotFoundInScopeException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Laravel\Scout\Searchable;

final class ModelScope
{
    private string $baseModelClass;
    private Collection $modelClasses;
    private Collection $relations;
    private Collection $queryCallbacks;

    public function __construct(string $modelClass)
    {
        $this->baseModelClass = $modelClass;
        $this->modelClasses = collect();
        $this->relations = collect();
        $this->queryCallbacks = collect();

        $this->push($modelClass);
    }

    public function push(string ...$modelClasses): self
    {
        foreach ($modelClasses as $modelClass) {
            $model = new $modelClass();

            if (
                !$model instanceof Model ||
                !in_array(Searchable::class, class_uses_recursive($modelClass), true)
            ) {
                throw new InvalidArgumentException(sprintf(
                    '%s must extend %s class and use %s trait',
                    $modelClass,
                    Model::class,
                    Searchable::class
                ));
            }

            $this->modelClasses->put($model->searchableAs(), $modelClass);
        }

        return $this;
    }

    public function contains(string $modelClass): bool
    {
        return $this->modelClasses->contains($modelClass);
    }

    public function with(array $relations, ?string $modelClass = null): self
    {
        $modelClass = $modelClass ?? $this->baseModelClass;

        if (!$this->contains($modelClass)) {
            throw new ModelClassNotFoundInScopeException($modelClass);
        }

        $this->relations->put($modelClass, $relations);

        return $this;
    }

    /**
     * Set the callback that should have an opportunity to modify the database query.
     */
    public function modifyQuery(callable $callback, ?string $modelClass = null): self
    {
        $modelClass = $modelClass ?? $this->baseModelClass;

        if (!$this->contains($modelClass)) {
            throw new ModelClassNotFoundInScopeException($modelClass);
        }

        $this->queryCallbacks->put($modelClass, $callback);

        return $this;
    }

    public function resolveIndexNames(): Collection
    {
        return $this->modelClasses->keys();
    }

    public function resolveIndexName(string $modelClass): ?string
    {
        return $this->modelClasses->search($modelClass);
    }

    public function resolveModelClass(string $indexName): ?string
    {
        return $this->modelClasses->get($indexName);
    }

    public function resolveRelations(string $modelClass): ?array
    {
        return $this->relations->get($modelClass);
    }

    public function resolveQueryCallback(string $modelClass): ?callable
    {
        return $this->queryCallbacks->get($modelClass);
    }
}
