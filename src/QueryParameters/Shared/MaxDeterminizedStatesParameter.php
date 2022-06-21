<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\QueryParameters\Shared;

trait MaxDeterminizedStatesParameter
{
    public function maxDeterminizedStates(int $maxDeterminizedStates): self
    {
        $this->parameters->put('max_determinized_states', $maxDeterminizedStates);
        return $this;
    }
}
