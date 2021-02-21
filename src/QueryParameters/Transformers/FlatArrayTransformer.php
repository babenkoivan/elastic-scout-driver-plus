<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\QueryParameters\Transformers;

use ElasticScoutDriverPlus\QueryParameters\Collection;

final class FlatArrayTransformer implements ArrayTransformerInterface
{
    public function transform(Collection $parameters): array
    {
        return $parameters->excludeEmpty()->toArray();
    }
}
