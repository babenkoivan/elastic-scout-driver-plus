<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\QueryParameters\Shared;

trait OperatorParameter
{
    public function operator(string $operator): self
    {
        $this->parameters->put('operator', $operator);
        return $this;
    }
}
