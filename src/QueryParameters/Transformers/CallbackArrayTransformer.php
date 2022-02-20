<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\QueryParameters\Transformers;

use ElasticScoutDriverPlus\QueryParameters\ParameterCollection;

final class CallbackArrayTransformer implements ArrayTransformerInterface
{
    /**
     * @var callable
     */
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function transform(ParameterCollection $parameters): array
    {
        return call_user_func($this->callback, $parameters);
    }
}
