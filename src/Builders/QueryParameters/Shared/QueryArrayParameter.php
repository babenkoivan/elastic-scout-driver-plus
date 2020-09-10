<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\QueryParameters\Shared;

trait QueryArrayParameter
{
    public function query(array $query): self
    {
        $this->parameters->put('query', $query);
        return $this;
    }
}
