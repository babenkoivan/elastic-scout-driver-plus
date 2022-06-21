<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\QueryParameters\Shared;

trait QueryStringParameter
{
    public function query(string $query): self
    {
        $this->parameters->put('query', $query);
        return $this;
    }
}
