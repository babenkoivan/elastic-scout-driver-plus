<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\SharedParameters;

trait ScoreModeParameter
{
    /**
     * @var string|null
     */
    private $scoreMode;

    public function scoreMode(string $scoreMode): self
    {
        $this->scoreMode = $scoreMode;
        return $this;
    }
}
