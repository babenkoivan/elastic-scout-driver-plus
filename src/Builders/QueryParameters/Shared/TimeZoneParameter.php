<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\QueryParameters\Shared;

trait TimeZoneParameter
{
    public function timeZone(string $timeZone): self
    {
        $this->parameters->put('time_zone', $timeZone);
        return $this;
    }
}
