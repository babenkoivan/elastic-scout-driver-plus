<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\QueryParameters\Transformers;

use ElasticScoutDriverPlus\Builders\QueryParameters\Collection;

final class FlatArrayTransformer implements ArrayTransformerInterface
{
    public function transform(Collection $parameters): array
    {
        return $parameters->excludeEmpty()->toArray();
    }
}
