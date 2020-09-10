<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\QueryParameters\Shared;

trait QueryStringParameter
{
    public function query(string $query): self
    {
        $this->parameters->put('query', $query);
        return $this;
    }
}
