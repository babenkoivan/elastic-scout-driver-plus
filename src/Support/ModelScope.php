<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Laravel\Scout\Searchable;

final class ModelScope
{
    /**
     * @var string
     */
    private $default;
    /**
     * @var Collection
     */
    private $queries;

    public function __construct(string $modelClass)
    {
        $this->default = $modelClass;
        $this->queries = collect();

        $this->push($modelClass);
    }

    public function push(string ...$modelClasses): self
    {
        foreach ($modelClasses as $modelClass) {
            $model = new $modelClass();

            if (!$model instanceof Model || !in_array(Searchable::class, class_uses_recursive($modelClass), true)) {
                throw new InvalidArgumentException(sprintf(
                    '%s must extend %s class and use %s trait',
                    $modelClass,
                    Model::class,
                    Searchable::class
                ));
            }

            $query = in_array(SoftDeletes::class, class_uses_recursive($model), true)
                ? $model->withTrashed()
                : $model->newQuery();

            $this->queries->put($modelClass, $query);
        }

        return $this;
    }

    public function has(string $modelClass): bool
    {
        return $this->queries->has($modelClass);
    }

    public function getDefaultQuery(): Builder
    {
        return $this->queries->get($this->default);
    }

    public function getQuery(string $modelClass): Builder
    {
        if (!$this->has($modelClass)) {
            throw new InvalidArgumentException(sprintf(
                '%s is not found in the model scope',
                $modelClass
            ));
        }

        return $this->queries->get($modelClass);
    }

    public function keyQueriesByIndexName(): Collection
    {
        return $this->queries->keyBy(static function (Builder $query): string {
            return $query->getModel()->searchableAs();
        });
    }

    public function resolveIndexNames(): Collection
    {
        return $this->queries->map(static function (Builder $query): string {
            return $query->getModel()->searchableAs();
        })->values();
    }
}
