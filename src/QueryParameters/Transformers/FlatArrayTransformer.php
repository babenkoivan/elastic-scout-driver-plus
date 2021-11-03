<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\QueryParameters\Transformers;

use ElasticScoutDriverPlus\QueryParameters\ParameterCollection;

final class FlatArrayTransformer implements ArrayTransformerInterface
{
    public function transform(ParameterCollection $parameters): array
    {
        return $parameters->excludeEmpty()->toArray();
    }
}
