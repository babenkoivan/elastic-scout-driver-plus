<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Support;

use Closure;

/**
 * This trait is similar to \Illuminate\Support\Traits\Conditionable,
 * which is available in Laravel since version 9.
 */
trait Conditionable
{
    /**
     * @param mixed $value
     */
    public function when($value, callable $callback, callable $default = null): self
    {
        $value = $value instanceof Closure ? $value($this) : $value;

        if ($value) {
            return $callback($this, $value) ?? $this;
        }

        if ($default) {
            return $default($this, $value) ?? $this;
        }

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function unless($value, callable $callback, callable $default = null): self
    {
        $value = $value instanceof Closure ? $value($this) : $value;

        if (!$value) {
            return $callback($this, $value) ?? $this;
        }

        if ($default) {
            return $default($this, $value) ?? $this;
        }

        return $this;
    }
}
