<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\QueryParameters\Transformers;

use ElasticScoutDriverPlus\QueryParameters\ParameterCollection;

interface ArrayTransformerInterface
{
    public function transform(ParameterCollection $parameters): array;
}
