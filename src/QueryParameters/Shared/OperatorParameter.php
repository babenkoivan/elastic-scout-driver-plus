<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\QueryParameters\Shared;

trait OperatorParameter
{
    public function operator(string $operator): self
    {
        $this->parameters->put('operator', $operator);
        return $this;
    }
}
