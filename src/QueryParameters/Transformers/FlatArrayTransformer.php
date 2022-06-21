<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\QueryParameters\Transformers;

use Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection;

final class FlatArrayTransformer implements ArrayTransformerInterface
{
    public function transform(ParameterCollection $parameters): array
    {
        return $parameters->excludeEmpty()->toArray();
    }
}
