<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\QueryParameters\Shared;

trait ScoreModeParameter
{
    public function scoreMode(string $scoreMode): self
    {
        $this->parameters->put('score_mode', $scoreMode);
        return $this;
    }
}
