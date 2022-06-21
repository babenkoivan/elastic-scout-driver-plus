<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\QueryParameters\Shared;

trait MaxExpansionsParameter
{
    public function maxExpansions(int $maxExpansions): self
    {
        $this->parameters->put('max_expansions', $maxExpansions);
        return $this;
    }
}
