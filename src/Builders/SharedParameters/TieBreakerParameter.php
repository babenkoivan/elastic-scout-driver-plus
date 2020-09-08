<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\SharedParameters;

trait TieBreakerParameter
{
    /**
     * @var float|null
     */
    private $tieBreaker;

    public function tieBreaker(float $tieBreaker): self
    {
        $this->tieBreaker = $tieBreaker;
        return $this;
    }
}
