<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\QueryParameters\Shared;

trait FlagsParameter
{
    public function flags(string $flags): self
    {
        $this->parameters->put('flags', $flags);
        return $this;
    }
}
