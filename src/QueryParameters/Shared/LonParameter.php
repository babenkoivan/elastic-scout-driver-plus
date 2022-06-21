<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\QueryParameters\Shared;

trait LonParameter
{
    public function lon(float $lon): self
    {
        $this->parameters->put('lon', $lon);
        return $this;
    }
}
