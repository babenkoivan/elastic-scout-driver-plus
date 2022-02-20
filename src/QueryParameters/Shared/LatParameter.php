<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\QueryParameters\Shared;

trait LatParameter
{
    public function lat(float $lat): self
    {
        $this->parameters->put('lat', $lat);
        return $this;
    }
}
