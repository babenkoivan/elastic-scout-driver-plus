<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\SharedParameters;

trait IgnoreUnmappedParameter
{
    /**
     * @var bool|null
     */
    private $ignoreUnmapped;

    public function ignoreUnmapped(bool $ignoreUnmapped): self
    {
        $this->ignoreUnmapped = $ignoreUnmapped;
        return $this;
    }
}
