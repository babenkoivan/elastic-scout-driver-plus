<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\QueryParameters\Transformers;

use Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection;

interface ArrayTransformerInterface
{
    public function transform(ParameterCollection $parameters): array;
}
