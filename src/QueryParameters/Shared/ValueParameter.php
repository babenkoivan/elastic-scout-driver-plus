<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\QueryParameters\Shared;

trait ValueParameter
{
    public function value(string $value): self
    {
        $this->parameters->put('value', $value);
        return $this;
    }
}
