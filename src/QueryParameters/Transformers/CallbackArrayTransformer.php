<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\QueryParameters\Transformers;

use Closure;
use Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection;

final class CallbackArrayTransformer implements ArrayTransformerInterface
{
    private Closure $callback;

    public function __construct(Closure $callback)
    {
        $this->callback = $callback;
    }

    public function transform(ParameterCollection $parameters): array
    {
        return call_user_func($this->callback, $parameters);
    }
}
