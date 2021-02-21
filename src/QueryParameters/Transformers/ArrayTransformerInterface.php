<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\QueryParameters\Transformers;

use ElasticScoutDriverPlus\QueryParameters\Collection;

interface ArrayTransformerInterface
{
    public function transform(Collection $parameters): array;
}
