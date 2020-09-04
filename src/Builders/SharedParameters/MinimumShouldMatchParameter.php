<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\SharedParameters;

trait MinimumShouldMatchParameter
{
    /**
     * @var int|string|null
     */
    private $minimumShouldMatch;

    /**
     * @param int|string $minimumShouldMatch
     */
    public function minimumShouldMatch($minimumShouldMatch): self
    {
        $this->minimumShouldMatch = $minimumShouldMatch;
        return $this;
    }
}
