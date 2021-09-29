<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\QueryParameters;

use ElasticScoutDriverPlus\Support\Arr;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection as BaseCollection;

final class ParameterCollection implements Arrayable
{
    /**
     * @var BaseCollection
     */
    private $items;

    public function __construct(array $items = [])
    {
        $this->items = collect($items);
    }

    /**
     * @param mixed $value
     */
    public function put(string $key, $value): self
    {
        $this->items->put($key, $value);
        return $this;
    }

    /**
     * @param mixed $value
     */
    public function push(string $key, $value): self
    {
        $array = Arr::wrap($this->items->get($key));
        $array = Arr::wrapAssoc($array);
        $array[] = $value;

        $this->items->put($key, $array);
        return $this;
    }

    /**
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->items->get($key);
    }

    /**
     * @param BaseCollection|array|string $keys
     */
    public function except($keys): self
    {
        $items = $this->items->except($keys)->all();
        return new static($items);
    }

    /**
     * @param BaseCollection|array|string $keys
     */
    public function only($keys): self
    {
        $items = $this->items->only($keys)->all();
        return new static($items);
    }

    public function excludeEmpty(): self
    {
        $items = $this->items->filter(static function ($value) {
            return
                isset($value) &&
                $value !== '' &&
                $value !== [];
        })->all();

        return new static($items);
    }

    public function count(): int
    {
        return $this->items->count();
    }

    public function toArray(): array
    {
        return $this->items->toArray();
    }
}
