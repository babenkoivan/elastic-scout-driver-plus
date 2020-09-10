<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\QueryParameters\Shared;

trait OperatorParameter
{
    public function operator(string $operator): self
    {
        $this->parameters->put('operator', $operator);
        return $this;
    }
}
