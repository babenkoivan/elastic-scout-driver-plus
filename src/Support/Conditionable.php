<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Support;

use Closure;
use Illuminate\Support\HigherOrderWhenProxy;

/**
 * This trait duplicates \Illuminate\Support\Traits\Conditionable,
 * which is available in Laravel since version 9.
 */
trait Conditionable
{
    /**
     * @param mixed $value
     *
     * @return $this|HigherOrderWhenProxy
     */
    public function when($value, callable $callback = null, callable $default = null)
    {
        $value = $value instanceof Closure ? $value($this) : $value;

        if (func_num_args() === 1) {
            return new HigherOrderWhenProxy($this, $value);
        }

        if ($value) {
            return $callback($this, $value) ?? $this;
        } elseif ($default) {
            return $default($this, $value) ?? $this;
        }

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return $this|HigherOrderWhenProxy
     */
    public function unless($value, callable $callback = null, callable $default = null)
    {
        $value = $value instanceof Closure ? $value($this) : $value;

        if (func_num_args() === 1) {
            return new HigherOrderWhenProxy($this, ! $value);
        }

        if (! $value) {
            return $callback($this, $value) ?? $this;
        } elseif ($default) {
            return $default($this, $value) ?? $this;
        }

        return $this;
    }
}
