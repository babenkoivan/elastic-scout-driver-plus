<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\QueryParameters\Shared;

trait RelationParameter
{
    public function relation(string $relation): self
    {
        $this->parameters->put('relation', $relation);
        return $this;
    }
}
