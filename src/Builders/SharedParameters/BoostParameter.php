<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\SharedParameters;

trait BoostParameter
{
    /**
     * @var float|null
     */
    private $boost;

    private function boost(float $boost): self
    {
        $this->boost = $boost;
        return $this;
    }
}
