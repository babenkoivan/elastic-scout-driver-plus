<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\SharedParameters;

trait PrefixLengthParameter
{
    /**
     * @var int|null
     */
    private $prefixLength;

    public function prefixLength(int $prefixLength): self
    {
        $this->prefixLength = $prefixLength;
        return $this;
    }
}
