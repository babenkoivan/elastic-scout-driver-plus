<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\QueryParameters\Shared;

trait FlagsParameter
{
    public function flags(string $flags): self
    {
        $this->parameters->put('flags', $flags);
        return $this;
    }
}
