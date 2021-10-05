<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\QueryParameters\Shared;

trait ValuesParameter
{
    public function values(array $values): self
    {
        $this->parameters->put('values', $values);
        return $this;
    }
}
