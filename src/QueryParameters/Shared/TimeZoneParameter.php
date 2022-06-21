<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\QueryParameters\Shared;

trait TimeZoneParameter
{
    public function timeZone(string $timeZone): self
    {
        $this->parameters->put('time_zone', $timeZone);
        return $this;
    }
}
