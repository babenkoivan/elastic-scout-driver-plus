<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\SharedParameters;

trait MaxExpansionsParameter
{
    /**
     * @var int|null
     */
    private $maxExpansions;

    public function maxExpansions(int $maxExpansions): self
    {
        $this->maxExpansions = $maxExpansions;
        return $this;
    }
}
