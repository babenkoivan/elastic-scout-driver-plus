<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\QueryParameters\Shared;

trait LenientParameter
{
    public function lenient(bool $lenient): self
    {
        $this->parameters->put('lenient', $lenient);
        return $this;
    }
}
