<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\SharedParameters;

trait SlopParameter
{
    /**
     * @var int|null
     */
    private $slop;

    public function slop(int $slop): self
    {
        $this->slop = $slop;
        return $this;
    }
}
