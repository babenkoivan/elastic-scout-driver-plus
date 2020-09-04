<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\SharedParameters;

trait FuzzyTranspositionsParameter
{
    /**
     * @var bool|null
     */
    private $fuzzyTranspositions;

    public function fuzzyTranspositions(bool $fuzzyTranspositions): self
    {
        $this->fuzzyTranspositions = $fuzzyTranspositions;
        return $this;
    }
}
