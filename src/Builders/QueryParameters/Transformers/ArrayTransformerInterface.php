<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\QueryParameters\Transformers;

use ElasticScoutDriverPlus\Builders\QueryParameters\Collection;

interface ArrayTransformerInterface
{
    public function transform(Collection $parameters): array;
}
